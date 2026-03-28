<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\ClassSubject;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the specified material.
     */
    public function show(Material $material): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            abort(403);
        }

        // Authorization check
        $this->authorize('view', $material);

        // Load relationships with caching
        $cacheKey = "material_detail_{$material->id}";
        $material = Cache::remember($cacheKey, 3600, function () use ($material) {
            return Material::with([
                'section.classSubject.subject',
                'section.classSubject.schoolClass',
                'section.classSubject.teacher.user',
            ])->find($material->id);
        });

        // Check if student has completed this material
        $isCompleted = false;
        if ($user->hasRole('student') && $user->student) {
            $isCompleted = $material->materialProgress()
                ->where('student_id', $user->student->id)
                ->where('is_completed', true)
                ->exists();
        }

        // Breadcrumb data
        $classSubject = $material->section->classSubject;
        $breadcrumbs = [
            ['label' => $classSubject->subject->name ?? 'Course', 'url' => route('courses.show', $classSubject)],
            ['label' => $material->section->title ?? 'Section', 'url' => route('courses.show', $classSubject) . '#section-' . $material->section_id],
            ['label' => $material->title ?? 'Material'],
        ];

        return view('materials.show', compact('material', 'isCompleted', 'breadcrumbs'));
    }

    /**
     * Mark material as completed.
     */
    public function markComplete(Request $request, Material $material)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user || !$user->student) {
            abort(403);
        }

        // Authorization check
        $this->authorize('markComplete', $material);

        // Toggle completion status
        $progress = $material->materialProgress()
            ->where('student_id', $user->student->id)
            ->first();

        if ($progress) {
            $progress->update(['is_completed' => !$progress->is_completed]);
        } else {
            $material->materialProgress()->create([
                'student_id' => $user->student->id,
                'is_completed' => true,
            ]);
        }

        // Clear cache
        Cache::forget("material_detail_{$material->id}");

        return back()->with('success', 'Material progress updated successfully.');
    }

    /**
     * Show create material form.
     */
    public function create(ClassSubject $course, Request $request): View
    {
        $this->authorize('update', $course);

        $course->load(['subject', 'schoolClass', 'sections' => fn($q) => $q->orderBy('order_number')]);

        $breadcrumbs = [
            ['label' => $course->subject->name ?? 'Course', 'url' => route('manage.courses.show', $course)],
            ['label' => 'Tambah Materi'],
        ];

        $sectionId = $request->query('section_id');

        return view('teacher.material-form', compact('course', 'breadcrumbs', 'sectionId'));
    }

    /**
     * Store a new material.
     */
    public function store(Request $request, ClassSubject $course)
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'section_id' => 'required|exists:class_subject_sections,id',
            'content_type' => 'required|in:pdf,video,image,link,document',
            'file' => 'nullable|file|max:20480',
            'link_url' => 'nullable|url|max:2048',
        ]);

        // Verify section belongs to course
        $section = $course->sections()->findOrFail($request->section_id);

        $fileUrl = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('materials', $filename, 'public');
            $fileUrl = $path; // Store relative path, not URL
        }

        $maxOrder = $section->materials()->max('order_number') ?? 0;

        $material = Material::create([
            'section_id' => $section->id,
            'title' => $request->title,
            'description' => $request->description,
            'content_type' => $request->content_type,
            'file_url' => $fileUrl,
            'link_url' => $request->link_url,
            'order_number' => $maxOrder + 1,
            'published_at' => $request->action === 'draft' ? null : now(),
            'created_by' => Auth::id(),
        ]);

        ActivityLogger::log($course->id, 'created', $material, 'Menambahkan materi: ' . $material->title);

        Cache::forget("class_detail_{$course->id}");

        return redirect()->route('manage.courses.show', $course)
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    /**
     * Show edit material form.
     */
    public function edit(ClassSubject $course, Material $material): View
    {
        $this->authorize('update', $course);

        $course->load(['subject', 'schoolClass', 'sections' => fn($q) => $q->orderBy('order_number')]);

        $breadcrumbs = [
            ['label' => $course->subject->name ?? 'Course', 'url' => route('manage.courses.show', $course)],
            ['label' => 'Edit Materi'],
        ];

        return view('teacher.material-form', compact('course', 'material', 'breadcrumbs'));
    }

    /**
     * Update a material.
     */
    public function update(Request $request, ClassSubject $course, Material $material)
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'section_id' => 'required|exists:class_subject_sections,id',
            'content_type' => 'required|in:pdf,video,image,link,document',
            'file' => 'nullable|file|max:20480',
            'link_url' => 'nullable|url|max:2048',
        ]);

        $fileUrl = $material->file_url;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('materials', $filename, 'public');
            $fileUrl = $path; // Store relative path, not URL
        }

        $material->update([
            'title' => $request->title,
            'description' => $request->description,
            'section_id' => $request->section_id,
            'content_type' => $request->content_type,
            'file_url' => $fileUrl,
            'link_url' => $request->link_url,
            'published_at' => $request->action === 'draft' ? null : ($material->published_at ?? now()),
        ]);

        // Regenerate slug if title changed
        if ($material->wasChanged('title')) {
            $material->update(['slug' => $material->generateSlug()]);
        }

        ActivityLogger::log($course->id, 'updated', $material, 'Memperbarui materi: ' . $material->title);

        Cache::forget("material_detail_{$material->id}");
        Cache::forget("class_detail_{$course->id}");

        return redirect()->route('manage.courses.show', $course)
            ->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * Delete a material.
     */
    public function destroy(ClassSubject $course, Material $material)
    {
        $this->authorize('update', $course);

        ActivityLogger::log($course->id, 'deleted', $material, 'Menghapus materi: ' . $material->title);

        $material->delete();

        Cache::forget("material_detail_{$material->id}");
        Cache::forget("class_detail_{$course->id}");

        return redirect()->route('manage.courses.show', $course)
            ->with('success', 'Materi berhasil dihapus.');
    }
}
