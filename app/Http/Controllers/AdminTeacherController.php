<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Teacher;
use App\Models\Subject;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminTeacherController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display all teachers.
     */
    public function index(Request $request): View
    {
        $query = Teacher::with('user')->latest();

        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('login_identifier', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('nip', 'like', "%{$search}%");
        }

        $perPage = (int) $request->input('per_page', 25);
        $teachers = $query->paginate($perPage)->withQueryString();

        $breadcrumbs = [
            ['label' => 'Data Guru'],
        ];

        return view('admin.teachers.index', compact('teachers', 'breadcrumbs'));
    }

    /**
     * Show form to create a teacher.
     */
    public function create(): View
    {
        $breadcrumbs = [
            ['label' => 'Data Guru', 'url' => route('admin.teachers.index')],
            ['label' => 'Tambah Guru'],
        ];

        return view('admin.teachers.create', compact('breadcrumbs'));
    }

    /**
     * Store a new teacher.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'full_name'      => 'required|string|max:255',
            'login_id'       => 'required|string|max:50|unique:users,login_identifier',
            'email'          => 'nullable|email|max:255|unique:users,email',
            'gender'         => 'required|in:L,P',
            'nip'            => 'nullable|string|max:30|unique:teachers,nip',
            'specialization' => 'nullable|string|max:255',
            'password'       => 'required|string|min:8',
            'profile_photo'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $photoPath = null;
            if ($request->hasFile('profile_photo')) {
                $photoPath = $request->file('profile_photo')->store('profiles', env('UPLOAD_DISK', 'public'));
            }

            $user = User::create([
                'full_name'        => $request->full_name,
                'login_identifier' => $request->login_id,
                'email'            => $request->email,
                'gender'           => $request->gender,
                'password'         => Hash::make($request->password),
                'profile_photo_path' => $photoPath,
            ]);

            $user->assignRole('teacher');

            $teacher = Teacher::create([
                'user_id'        => $user->id,
                'nip'            => $request->nip,
                'specialization' => $request->specialization,
            ]);

            ActivityLogger::log(null, 'created', $teacher, 'Menambahkan guru: ' . $request->full_name);
        });

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Guru berhasil ditambahkan.');
    }

    /**
     * Show form to edit a teacher.
     */
    public function edit(Teacher $teacher): View
    {
        $teacher->load('user', 'subjects');

        $subjects = Subject::orderBy('name')->get();
        $assignedSubjectIds = $teacher->subjects->pluck('id')->toArray();

        $breadcrumbs = [
            ['label' => 'Data Guru', 'url' => route('admin.teachers.index')],
            ['label' => 'Edit: ' . ($teacher->user?->full_name ?? '')],
        ];

        return view('admin.teachers.edit', compact('teacher', 'subjects', 'assignedSubjectIds', 'breadcrumbs'));
    }

    /**
     * Update a teacher.
     */
    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $teacher->load('user');

        $request->validate([
            'full_name'      => 'required|string|max:255',
            'login_id'       => 'required|string|max:50|unique:users,login_identifier,' . $teacher->user_id,
            'email'          => 'nullable|email|max:255|unique:users,email,' . $teacher->user_id,
            'gender'         => 'required|in:L,P',
            'nip'            => 'nullable|string|max:30|unique:teachers,nip,' . $teacher->id,
            'specialization' => 'nullable|string|max:255',
            'password'       => 'nullable|string|min:8',
            'profile_photo'  => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        DB::transaction(function () use ($request, $teacher) {
            $userData = [
                'full_name'        => $request->full_name,
                'login_identifier' => $request->login_id,
                'email'            => $request->email,
                'gender'           => $request->gender,
            ];

            if ($request->hasFile('profile_photo')) {
                if ($teacher->user?->profile_photo_path) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($teacher->user->profile_photo_path);
                }
                $userData['profile_photo_path'] = $request->file('profile_photo')->store('profiles', env('UPLOAD_DISK', 'public'));
            }

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $teacher->user?->update($userData);

            $teacher->update([
                'nip'            => $request->nip,
                'specialization' => $request->specialization,
            ]);

            // Sync teacher-subject assignments
            $teacher->subjects()->sync($request->input('subject_ids', []));

            ActivityLogger::log(null, 'updated', $teacher, 'Memperbarui data guru: ' . $request->full_name);
        });

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Data guru berhasil diperbarui.');
    }

    /**
     * Delete a teacher.
     */
    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->load('user');
        $name = $teacher->user?->full_name ?? 'Guru';

        ActivityLogger::log(null, 'deleted', $teacher, 'Menghapus guru: ' . $name);

        DB::transaction(function () use ($teacher) {
            $teacher->user?->delete();
            $teacher->delete();
        });

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Guru berhasil dihapus.');
    }
}
