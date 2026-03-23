<?php

namespace App\Http\Controllers;

use App\Models\ClassSubject;
use App\Models\ClassSubjectSection;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

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
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $course->update([
            'general_info' => $request->general_info,
        ]);

        if ($request->hasFile('cover_image')) {
            $path = $request->file('cover_image')->store('subjects', 'public');
            if ($course->subject->cover_image_path && Storage::disk('public')->exists($course->subject->cover_image_path)) {
                Storage::disk('public')->delete($course->subject->cover_image_path);
            }
            $course->subject->update(['cover_image_path' => $path]);
        }

        ActivityLogger::log($course->id, 'updated', $course, 'Memperbarui informasi umum kelas');

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

        $section = ClassSubjectSection::create([
            'class_subject_id' => $course->id,
            'title' => $request->title,
            'description' => $request->description,
            'order_number' => $maxOrder + 1,
            'is_published' => true,
        ]);

        ActivityLogger::log($course->id, 'created', $section, 'Menambahkan BAB: ' . $section->title);

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

        ActivityLogger::log($course->id, 'updated', $section, 'Memperbarui BAB: ' . $section->title);

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

        ActivityLogger::log($course->id, 'deleted', $section, 'Menghapus BAB: ' . $section->title);

        $section->delete();

        Cache::forget("class_detail_{$course->id}");

        return back()->with('success', 'BAB berhasil dihapus.');
    }
}
