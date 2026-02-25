<?php

namespace App\Http\Controllers;

use App\Models\ClassSubject;
use App\Models\ClassSubjectSection;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;

class TeacherCourseController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the teacher course management page.
     */
    public function manage(ClassSubject $course): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $this->authorize('update', $course);

        // Load relationships
        $course->load([
            'subject',
            'schoolClass',
            'teacher.user',
            'academicYear',
            'sections' => function ($q) {
                $q->orderBy('order_number');
            },
            'sections.materials' => function ($q) {
                $q->orderBy('order_number');
            },
            'sections.assignments' => function ($q) {
                $q->orderBy('order_number');
            },
            'sections.quizzes',
        ]);

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Manajemen Kelas', 'url' => route('courses.index')],
            ['label' => $course->subject->name ?? 'Course'],
        ];

        return view('teacher.course-manage', compact('course', 'breadcrumbs'));
    }

    /**
     * Update the general info of a course.
     */
    public function updateInfo(Request $request, ClassSubject $course)
    {
        $this->authorize('update', $course);

        $request->validate([
            'general_info' => 'nullable|string|max:10000',
        ]);

        $course->update([
            'general_info' => $request->general_info,
        ]);

        Cache::forget("class_detail_{$course->id}");

        return back()->with('success', 'Informasi umum berhasil diperbarui.');
    }

    /**
     * Store a new section (BAB).
     */
    public function storeSection(Request $request, ClassSubject $course)
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $maxOrder = $course->sections()->max('order_number') ?? 0;

        ClassSubjectSection::create([
            'class_subject_id' => $course->id,
            'title' => $request->title,
            'description' => $request->description,
            'order_number' => $maxOrder + 1,
            'is_published' => true,
        ]);

        Cache::forget("class_detail_{$course->id}");

        return back()->with('success', 'BAB baru berhasil ditambahkan.');
    }

    /**
     * Update a section (BAB).
     */
    public function updateSection(Request $request, ClassSubject $course, ClassSubjectSection $section)
    {
        $this->authorize('update', $course);

        // Verify section belongs to course
        if ($section->class_subject_id !== $course->id) {
            abort(404);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $section->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        Cache::forget("class_detail_{$course->id}");

        return back()->with('success', 'BAB berhasil diperbarui.');
    }

    /**
     * Delete a section (BAB) and all its content.
     */
    public function deleteSection(ClassSubject $course, ClassSubjectSection $section)
    {
        $this->authorize('update', $course);

        if ($section->class_subject_id !== $course->id) {
            abort(404);
        }

        $section->delete();

        Cache::forget("class_detail_{$course->id}");

        return back()->with('success', 'BAB berhasil dihapus.');
    }
}
