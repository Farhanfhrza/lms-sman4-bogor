<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Imports\StudentsImport;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;

class AdminStudentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display all students.
     */
    public function index(Request $request): View
    {
        $query = Student::with(['user', 'studentClasses.schoolClass'])->latest();

        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('login_identifier', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('nisn', 'like', "%{$search}%");
        }

        if ($year = $request->input('enrollment_year')) {
            $query->where('enrollment_year', $year);
        }

        if ($classId = $request->input('class_id')) {
            $query->whereHas('studentClasses', function ($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        $perPage = (int) $request->input('per_page', 25);
        $students = $query->paginate($perPage)->withQueryString();

        // Get distinct enrollment years for filter dropdown
        $enrollmentYears = Student::select('enrollment_year')
            ->distinct()
            ->orderByDesc('enrollment_year')
            ->pluck('enrollment_year');

        // Get all school classes for filter dropdown
        $schoolClasses = SchoolClass::orderBy('name')->get();

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Data Siswa'],
        ];

        return view('admin.students.index', compact('students', 'breadcrumbs', 'enrollmentYears', 'schoolClasses'));
    }

    /**
     * Show form to create a student.
     */
    public function create(): View
    {
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Data Siswa', 'url' => route('admin.students.index')],
            ['label' => 'Tambah Siswa'],
        ];

        return view('admin.students.create', compact('breadcrumbs'));
    }

    /**
     * Store a new student.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'full_name'       => 'required|string|max:255',
            'email'           => 'nullable|email|max:255|unique:users,email',
            'nisn'            => 'required|string|max:20|unique:students,nisn',
            'enrollment_year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'password'        => 'required|string|min:8',
        ]);

        DB::transaction(function () use ($request) {
            // Check if a soft-deleted user with same login_identifier exists
            $existingUser = User::withTrashed()->where('login_identifier', $request->nisn)->first();

            if ($existingUser) {
                // Restore and update the soft-deleted user
                $existingUser->restore();
                $existingUser->update([
                    'full_name' => $request->full_name,
                    'email'     => $request->email,
                    'password'  => Hash::make($request->password),
                ]);
                $user = $existingUser;

                if (!$user->hasRole('student')) {
                    $user->assignRole('student');
                }
            } else {
                $user = User::create([
                    'full_name'        => $request->full_name,
                    'login_identifier' => $request->nisn,
                    'email'            => $request->email,
                    'password'         => Hash::make($request->password),
                ]);

                $user->assignRole('student');
            }

            $student = Student::create([
                'user_id'         => $user->id,
                'nisn'            => $request->nisn,
                'enrollment_year' => $request->enrollment_year,
            ]);

            ActivityLogger::log(null, 'created', $student, 'Menambahkan siswa: ' . $request->full_name);
        });

        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil ditambahkan.');
    }

    /**
     * Show form to edit a student.
     */
    public function edit(Student $student): View
    {
        $student->load('user');

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Data Siswa', 'url' => route('admin.students.index')],
            ['label' => 'Edit: ' . ($student->user->full_name ?? '')],
        ];

        return view('admin.students.edit', compact('student', 'breadcrumbs'));
    }

    /**
     * Update a student.
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        $student->load('user');

        $request->validate([
            'full_name'       => 'required|string|max:255',
            'email'           => 'nullable|email|max:255|unique:users,email,' . $student->user_id,
            'nisn'            => 'required|string|max:20|unique:students,nisn,' . $student->id,
            'enrollment_year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'password'        => 'nullable|string|min:8',
        ]);

        DB::transaction(function () use ($request, $student) {
            $userData = [
                'full_name'        => $request->full_name,
                'login_identifier' => $request->nisn,
                'email'            => $request->email,
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            $student->user->update($userData);

            $student->update([
                'nisn'            => $request->nisn,
                'enrollment_year' => $request->enrollment_year,
            ]);

            ActivityLogger::log(null, 'updated', $student, 'Memperbarui data siswa: ' . $request->full_name);
        });

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Delete a student.
     */
    public function destroy(Student $student): RedirectResponse
    {
        $student->load('user');
        $name = $student->user->full_name ?? 'Siswa';

        ActivityLogger::log(null, 'deleted', $student, 'Menghapus siswa: ' . $name);

        DB::transaction(function () use ($student) {
            $student->user->delete();
            $student->delete();
        });

        return redirect()->route('admin.students.index')
            ->with('success', 'Siswa berhasil dihapus.');
    }

    /**
     * Import students from an Excel/CSV file.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file'            => 'required|file|mimes:xlsx,csv,xls|max:5120',
            'enrollment_year' => 'required|integer|min:2000|max:' . (date('Y') + 1),
        ]);

        $import = new StudentsImport($request->enrollment_year);
        Excel::import($import, $request->file('file'));

        $count = $import->getImportedCount();
        $skipped = $import->getSkippedCount();

        $message = "{$count} siswa berhasil diimpor.";
        if ($skipped > 0) {
            $message .= " {$skipped} baris dilewati (duplikat/kosong).";
        }

        ActivityLogger::log(null, 'created', null, "Import Excel: {$count} siswa baru (Angkatan {$request->enrollment_year})");

        return redirect()->route('admin.students.index')
            ->with('success', $message);
    }
}
