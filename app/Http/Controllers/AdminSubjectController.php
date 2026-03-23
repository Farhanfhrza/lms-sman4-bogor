<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ClassSubject;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminSubjectController extends Controller
{
    /**
     * Tampilkan data master mapel dan yang terhubung dengan kelas.
     */
    public function index(Request $request): View
    {
        // Data Master Mapel
        $subjects = Subject::withCount('classSubjects')->orderBy('name')->paginate(15, ['*'], 'subjects_page');

        // Data mapel yang spesifik terhubung kelas di tahun ajaran aktif
        $activeClassSubjects = ClassSubject::with(['subject', 'schoolClass', 'teacher.user'])
            ->whereHas('academicYear', function ($q) {
                $q->where('is_active', true);
            })
            ->latest()
            ->paginate(15, ['*'], 'class_subjects_page');

        $breadcrumbs = [
            ['label' => 'Mata Pelajaran'],
        ];

        return view('admin.subjects.index', compact('subjects', 'activeClassSubjects', 'breadcrumbs'));
    }

    /**
     * Simpan mapel baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('covers', 'public');
        }

        $subject = Subject::create([
            'name' => $request->name,
            'code' => $request->code,
            'cover_image_path' => $coverPath,
        ]);

        ActivityLogger::log(null, 'created', $subject, 'Menambahkan mapel baru: ' . $subject->name);

        return redirect()->route('admin.subjects.index')->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    /**
     * Update mapel.
     */
    public function update(Request $request, Subject $subject): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:subjects,code,' . $subject->id,
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $updateData = [
            'name' => $request->name,
            'code' => $request->code,
        ];

        if ($request->hasFile('cover_image')) {
            if ($subject->cover_image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($subject->cover_image_path);
            }
            $updateData['cover_image_path'] = $request->file('cover_image')->store('covers', 'public');
        }

        $subject->update($updateData);

        ActivityLogger::log(null, 'updated', $subject, 'Memperbarui mapel: ' . $subject->name);

        return redirect()->route('admin.subjects.index')->with('success', 'Mata Pelajaran berhasil diubah.');
    }

    /**
     * Tampilkan detail mapel, kelola guru pengajar.
     */
    public function show(Subject $subject): View
    {
        $subject->load(['teachers.user']);

        // Data semua guru untuk dropdown tambah guru
        $allTeachers = Teacher::with('user')->get();
        // Guru yang belum mengajar mapel ini
        $availableTeachers = $allTeachers->reject(function ($t) use ($subject) {
            return $subject->teachers->contains('id', $t->id);
        });

        // Tampilkan kelas kelas yang menggunakan mapel ini (semua TP atau khusus aktif? Khusus aktif saja baiknya)
        $classesUsing = ClassSubject::with(['schoolClass', 'academicYear', 'teacher.user'])
            ->where('subject_id', $subject->id)
            ->latest()
            ->paginate(10);

        $breadcrumbs = [
            ['label' => 'Mata Pelajaran', 'url' => route('admin.subjects.index')],
            ['label' => 'Detail Mapel'],
        ];

        return view('admin.subjects.show', compact('subject', 'availableTeachers', 'classesUsing', 'breadcrumbs'));
    }

    /**
     * Hubungkan guru dengan mapel ini.
     */
    public function assignTeacher(Request $request, Subject $subject): RedirectResponse
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id'
        ]);

        if (!$subject->teachers->contains($request->teacher_id)) {
            $subject->teachers()->attach($request->teacher_id);
            $teacher = Teacher::with('user')->find($request->teacher_id);
            ActivityLogger::log(null, 'updated', $subject, 'Menambahkan guru ' . ($teacher->user->full_name ?? '') . ' ke mapel ' . $subject->name);
        }

        return redirect()->route('admin.subjects.show', $subject->id)->with('success', 'Guru berhasil ditambahkan ke mata pelajaran ini.');
    }

    /**
     * Lepaskan guru dari mapel ini.
     */
    public function removeTeacher(Request $request, Subject $subject, Teacher $teacher): RedirectResponse
    {
        $subject->teachers()->detach($teacher->id);
        ActivityLogger::log(null, 'updated', $subject, 'Melepaskan guru ' . ($teacher->user->full_name ?? '') . ' dari mapel ' . $subject->name);

        return redirect()->route('admin.subjects.show', $subject->id)->with('success', 'Guru berhasil dihapus dari mata pelajaran ini.');
    }

    /**
     * Hapus mapel.
     */
    public function destroy(Subject $subject): RedirectResponse
    {
        ActivityLogger::log(null, 'deleted', $subject, 'Menghapus mapel: ' . $subject->name);
        $subject->delete();

        return redirect()->route('admin.subjects.index')->with('success', 'Mata Pelajaran berhasil dihapus.');
    }
}
