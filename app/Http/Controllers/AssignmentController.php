<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\ClassSubject;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache;

class AssignmentController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the specified assignment.
     */
    public function show(Assignment $assignment): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user) {
            abort(403);
        }

        // Authorization check
        $this->authorize('view', $assignment);

        // Load relationships with caching
        $cacheKey = "assignment_detail_{$assignment->id}";
        $assignment = Cache::remember($cacheKey, 3600, function () use ($assignment) {
            return Assignment::with([
                'section.classSubject.subject',
                'section.classSubject.schoolClass',
                'section.classSubject.teacher.user',
            ])->find($assignment->id);
        });

        // Get student's submission if exists
        $submission = null;
        $canSubmit = false;
        
        if ($user->hasRole('student') && $user->student) {
            $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
                ->where('student_id', $user->student->id)
                ->first();
            
            // Check if can submit (authorized and before due date)
            try {
                $this->authorize('submit', $assignment);
                $canSubmit = true;
            } catch (\Exception $e) {
                $canSubmit = false;
            }
        }

        // Check if assignment is overdue
        $isOverdue = $assignment->due_date && now()->isAfter($assignment->due_date);

        // Breadcrumb data
        $classSubject = $assignment->section->classSubject;
        $breadcrumbs = [
            ['label' => $classSubject->subject->name ?? 'Course', 'url' => route('courses.show', $classSubject)],
            ['label' => $assignment->section->title ?? 'Section', 'url' => route('courses.show', $classSubject) . '#section-' . $assignment->section_id],
            ['label' => $assignment->title ?? 'Assignment'],
        ];

        return view('assignments.show', compact('assignment', 'submission', 'canSubmit', 'isOverdue', 'breadcrumbs'));
    }

    /**
     * Submit assignment.
     */
    public function submit(Request $request, Assignment $assignment)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if (!$user || !$user->student) {
            abort(403);
        }

        // Authorization check
        $this->authorize('submit', $assignment);

        $request->validate([
            'submission_text' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB max
            'link' => 'nullable|url',
        ]);

        // Check if submission already exists
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $user->student->id)
            ->first();

        $fileUrl = null;
        
        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('assignments', $filename, 'public');
            $fileUrl = $path; // Store relative path
        }

        if ($submission) {
            // Update existing submission
            $submission->update([
                'submission_text' => $request->submission_text,
                'file_url' => $fileUrl ?? $submission->file_url,
                'link_url' => $request->link,
                'submitted_at' => now(),
                'status' => 'submitted',
            ]);
        } else {
            // Create new submission
            AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'student_id' => $user->student->id,
                'submission_text' => $request->submission_text,
                'file_url' => $fileUrl,
                'link_url' => $request->link,
                'submitted_at' => now(),
                'status' => 'submitted',
            ]);
        }

        // Clear cache
        Cache::forget("assignment_detail_{$assignment->id}");

        return back()->with('success', 'Assignment submitted successfully!');
    }

    /**
     * Show create assignment form.
     */
    public function create(ClassSubject $course, Request $request): View
    {
        $this->authorize('update', $course);

        $course->load(['subject', 'schoolClass', 'sections' => fn($q) => $q->orderBy('order_number')]);

        $breadcrumbs = [
            ['label' => $course->subject->name ?? 'Course', 'url' => route('manage.courses.show', $course)],
            ['label' => 'Tambah Penugasan'],
        ];

        $sectionId = $request->query('section_id');

        return view('teacher.assignment-form', compact('course', 'breadcrumbs', 'sectionId'));
    }

    /**
     * Store a new assignment.
     */
    public function store(Request $request, ClassSubject $course)
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'section_id' => 'required|exists:class_subject_sections,id',
            'due_date' => 'nullable|date',
            'due_time' => 'nullable|date_format:H:i',
            'max_score' => 'nullable|integer|min:0|max:100',
            'file' => 'nullable|file|max:20480',
            'link_url' => 'nullable|url|max:2048',
        ]);

        // Verify section belongs to course
        $section = $course->sections()->findOrFail($request->section_id);

        $fileUrl = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('assignments', $filename, 'public');
            $fileUrl = $path; // Store relative path
        }

        // Combine date and time
        $dueDate = null;
        if ($request->due_date) {
            $dueDate = $request->due_date;
            if ($request->due_time) {
                $dueDate .= ' ' . $request->due_time . ':00';
            } else {
                $dueDate .= ' 23:59:00';
            }
        }

        $maxOrder = $section->assignments()->max('order_number') ?? 0;

        $assignment = Assignment::create([
            'section_id' => $section->id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $dueDate,
            'max_score' => $request->max_score ?? 100,
            'allow_late_submission' => $request->boolean('allow_late_submission'),
            'file_url' => $fileUrl,
            'order_number' => $maxOrder + 1,
            'created_by' => Auth::id(),
        ]);

        ActivityLogger::log($course->id, 'created', $assignment, 'Menambahkan penugasan: ' . $assignment->title);

        Cache::forget("class_detail_{$course->id}");

        return redirect()->route('manage.courses.show', $course)
            ->with('success', 'Penugasan berhasil ditambahkan.');
    }

    /**
     * Show edit assignment form.
     */
    public function edit(ClassSubject $course, Assignment $assignment): View
    {
        $this->authorize('update', $course);

        $course->load(['subject', 'schoolClass', 'sections' => fn($q) => $q->orderBy('order_number')]);

        $breadcrumbs = [
            ['label' => $course->subject->name ?? 'Course', 'url' => route('manage.courses.show', $course)],
            ['label' => 'Edit Penugasan'],
        ];

        return view('teacher.assignment-form', compact('course', 'assignment', 'breadcrumbs'));
    }

    /**
     * Update an assignment.
     */
    public function update(Request $request, ClassSubject $course, Assignment $assignment)
    {
        $this->authorize('update', $course);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'section_id' => 'required|exists:class_subject_sections,id',
            'due_date' => 'nullable|date',
            'due_time' => 'nullable|date_format:H:i',
            'max_score' => 'nullable|integer|min:0|max:100',
            'file' => 'nullable|file|max:20480',
            'link_url' => 'nullable|url|max:2048',
        ]);

        $fileUrl = $assignment->file_url;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('assignments', $filename, 'public');
            $fileUrl = $path; // Store relative path
        }

        // Combine date and time
        $dueDate = null;
        if ($request->due_date) {
            $dueDate = $request->due_date;
            if ($request->due_time) {
                $dueDate .= ' ' . $request->due_time . ':00';
            } else {
                $dueDate .= ' 23:59:00';
            }
        }

        $assignment->update([
            'title' => $request->title,
            'description' => $request->description,
            'section_id' => $request->section_id,
            'due_date' => $dueDate,
            'max_score' => $request->max_score ?? $assignment->max_score,
            'allow_late_submission' => $request->boolean('allow_late_submission'),
            'file_url' => $fileUrl,
        ]);

        // Regenerate slug if title changed
        if ($assignment->wasChanged('title')) {
            $assignment->update(['slug' => $assignment->generateSlug()]);
        }

        ActivityLogger::log($course->id, 'updated', $assignment, 'Memperbarui penugasan: ' . $assignment->title);

        Cache::forget("assignment_detail_{$assignment->id}");
        Cache::forget("class_detail_{$course->id}");

        return redirect()->route('manage.courses.show', $course)
            ->with('success', 'Penugasan berhasil diperbarui.');
    }

    /**
     * Delete an assignment.
     */
    public function destroy(ClassSubject $course, Assignment $assignment)
    {
        $this->authorize('update', $course);

        ActivityLogger::log($course->id, 'deleted', $assignment, 'Menghapus penugasan: ' . $assignment->title);

        $assignment->delete();

        Cache::forget("assignment_detail_{$assignment->id}");
        Cache::forget("class_detail_{$course->id}");

        return redirect()->route('manage.courses.show', $course)
            ->with('success', 'Penugasan berhasil dihapus.');
    }

    /**
     * Show assignment submissions / grading page (Teacher).
     */
    public function submissions(ClassSubject $course, Assignment $assignment): View
    {
        $this->authorize('update', $course);

        // Load assignment context
        $assignment->load([
            'section.classSubject.subject',
            'section.classSubject.schoolClass',
        ]);

        // Get the class_id from the course
        $classId = $course->class_id;

        // Get active academic year
        $activeAcademicYearId = \App\Models\AcademicYear::where('is_active', true)->value('id');

        // Get all students enrolled in this class (with user info and pivot for attendance_number)
        $students = \App\Models\Student::whereHas('studentClasses', function ($q) use ($classId, $activeAcademicYearId) {
                $q->where('class_id', $classId);
                if ($activeAcademicYearId) {
                    $q->where('academic_year_id', $activeAcademicYearId);
                }
            })
            ->with([
                'user' => function ($q) {
                    $q->select('id', 'full_name');
                },
                'studentClasses' => function($q) use ($classId, $activeAcademicYearId) {
                    $q->where('class_id', $classId);
                    if ($activeAcademicYearId) {
                        $q->where('academic_year_id', $activeAcademicYearId);
                    }
                }
            ])
            ->get()
            ->sortBy(function($student) {
                $sc = $student->studentClasses->first();
                $num = $sc ? ($sc->attendance_number ?? 999) : 999;
                return sprintf('%03d-%s', $num, $student->user->full_name);
            });

        // Fetch all submissions for this assignment at once (keyed by student_id)
        $submissionsByStudent = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->get()
            ->keyBy('student_id');

        // Build rows with computed status
        $rows = $students->map(function ($student, $index) use ($assignment, $submissionsByStudent, $course) {
            $submission = $submissionsByStudent->get($student->id);

            // Determine status (KETERANGAN)
            if ($submission) {
                if ($assignment->due_date && $submission->submitted_at && $submission->submitted_at->gt($assignment->due_date)) {
                    $keterangan = 'Telat Mengumpulkan';
                    $keteranganColor = 'red';
                } else {
                    $keterangan = 'Mengumpulkan';
                    $keteranganColor = 'green';
                }
            } else {
                $keterangan = 'Belum Mengumpulkan';
                $keteranganColor = 'red';
            }

            // Determine score display (NILAI)
            if (!$submission) {
                $nilai = 0;
                $nilaiLabel = 'Kosong';
                $nilaiColor = 'red';
            } elseif ($submission->status === 'draft') {
                $nilai = null;
                $nilaiLabel = 'Draf';
                $nilaiColor = 'gray';
            } elseif ($submission->score === null) {
                $nilai = null;
                $nilaiLabel = 'Belum dinilai';
                $nilaiColor = 'yellow';
            } else {
                $nilai = $submission->score;
                $nilaiLabel = (string) $submission->score;
                $nilaiColor = 'default';
            }

            return [
                'no' => $index + 1,
                'name' => $student->user->full_name ?? '-',
                'nisn' => $student->nisn ?? '-',
                'keterangan' => $keterangan,
                'keteranganColor' => $keteranganColor,
                'nilai' => $nilai,
                'nilaiLabel' => $nilaiLabel,
                'nilaiColor' => $nilaiColor,
                'hasSubmission' => $submission !== null,
                'previewUrl' => route('manage.courses.assignments.submissions.show', [$course, $assignment, $student]),
                'student_id' => $student->id,
            ];
        });

        $course->load(['subject', 'schoolClass']);

        $breadcrumbs = [
            ['label' => $course->subject->name ?? 'Course', 'url' => route('manage.courses.show', $course)],
            ['label' => $assignment->title ?? 'Penugasan'],
        ];

        return view('teacher.assignment-grading', compact('course', 'assignment', 'rows', 'breadcrumbs'));
    }

    /**
     * Show a specific student's submission for grading (Teacher).
     */
    public function showSubmission(ClassSubject $course, Assignment $assignment, \App\Models\Student $student): View
    {
        $this->authorize('update', $course);

        $assignment->load([
            'section.classSubject.subject',
            'section.classSubject.schoolClass',
        ]);

        $student->load('user');

        // Get submission for this student + assignment
        $submission = AssignmentSubmission::where('assignment_id', $assignment->id)
            ->where('student_id', $student->id)
            ->first();

        $course->load(['subject', 'schoolClass']);

        $breadcrumbs = [
            ['label' => $course->subject->name ?? 'Course', 'url' => route('manage.courses.show', $course)],
            ['label' => $assignment->title ?? 'Penugasan', 'url' => route('manage.courses.assignments.submissions', [$course, $assignment])],
            ['label' => $student->user->full_name ?? 'Siswa'],
        ];

        return view('teacher.assignment-grading-detail', compact(
            'course', 'assignment', 'student', 'submission', 'breadcrumbs'
        ));
    }

    /**
     * Save the grade (score + feedback) for a student's submission (Teacher).
     */
    public function gradeSubmission(Request $request, ClassSubject $course, Assignment $assignment, \App\Models\Student $student)
    {
        $this->authorize('update', $course);

        $request->validate([
            'score'    => 'required|integer|min:0|max:' . ($assignment->max_score ?? 100),
            'feedback' => 'nullable|string|max:5000',
        ]);

        $submission = AssignmentSubmission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'student_id'    => $student->id,
            ],
            [
                'score'     => $request->score,
                'feedback'  => $request->feedback,
                'graded_at' => now(),
            ]
        );

        ActivityLogger::log($course->id, 'graded', $submission, 'Menilai tugas ' . $assignment->title . ' untuk ' . ($student->user->full_name ?? 'siswa'));

        Cache::forget("assignment_detail_{$assignment->id}");

        return redirect()
            ->route('manage.courses.assignments.submissions', [$course, $assignment])
            ->with('success', 'Nilai berhasil disimpan untuk ' . ($student->user->full_name ?? 'siswa') . '.');
    }
}
