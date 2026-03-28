<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\ClassSubject;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;

class QuizController extends Controller
{
    use AuthorizesRequests;
    /**
     * Show create quiz form.
     */
    public function create(ClassSubject $course, Request $request): View
    {
        $this->authorize('update', $course);

        $course->load(['subject', 'schoolClass', 'sections' => fn($q) => $q->orderBy('order_number')]);

        $breadcrumbs = [
            ['label' => $course->subject->name ?? 'Course', 'url' => route('manage.courses.show', $course)],
            ['label' => 'Tambah Kuis'],
        ];

        $sectionId = $request->query('section_id');

        return view('teacher.quiz-form', compact('course', 'breadcrumbs', 'sectionId'));
    }

    /**
     * Store a new quiz with questions and options.
     */
    public function store(Request $request, ClassSubject $course)
    {
        $this->authorize('update', $course);

        $request->validate([
            'title'           => 'required|string|max:255',
            'subtitle'        => 'nullable|string|max:255',
            'description'     => 'nullable|string|max:10000',
            'section_id'      => 'required|exists:class_subject_sections,id',
            'due_date'        => 'nullable|date',
            'due_time'        => 'nullable|date_format:H:i',
            'time_limit'      => 'nullable|integer|min:1|max:600',
            'close_on_deadline' => 'nullable',
            'questions'       => 'required|array|min:1',
            'questions.*.text'           => 'required|string',
            'questions.*.points'         => 'nullable|integer|min:1|max:1000',
            'questions.*.correct_option' => 'required|integer|min:0',
            'questions.*.options'        => 'required|array|min:2',
            'questions.*.options.*.text' => 'required|string|max:1000',
            'questions.*.image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Enforce 2MB limit on server side
        ]);

        // Verify section belongs to this course
        $section = $course->sections()->findOrFail($request->section_id);

        // Build end_at from due_date + due_time
        $endAt = null;
        if ($request->due_date) {
            $endAt = $request->due_date;
            $endAt .= ' ' . ($request->due_time ? $request->due_time . ':00' : '23:59:00');
        }

        // Determine scoring
        $scoringMode = $request->input('scoring_mode', 'auto');
        $questionCount = count($request->questions);
        $baseScore = $questionCount > 0 ? (int) floor(100 / $questionCount) : 0;
        $remainder = $questionCount > 0 ? 100 - ($baseScore * $questionCount) : 0;

        $quiz = DB::transaction(function () use ($request, $section, $endAt, $scoringMode, $baseScore, $remainder, $questionCount) {
            $quiz = Quiz::create([
                'section_id'   => $section->id,
                'title'        => $request->title,
                'description'  => ($request->subtitle ? $request->subtitle . "\n" : '') . ($request->description ?? ''),
                'time_limit'   => $request->time_limit,
                'max_attempt'  => 1,
                'is_published' => true,
                'start_at'     => now(),
                'end_at'       => $endAt,
                'created_by'   => Auth::id(),
            ]);

            foreach ($request->questions as $index => $qData) {
                // Handle image upload
                $imageUrl = null;
                if ($request->hasFile("questions.{$index}.image")) {
                    $file = $request->file("questions.{$index}.image");
                    $filename = time() . '_q' . $index . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('quiz_images', $filename, 'public');
                    $imageUrl = $path;
                }

                // Auto: last question gets the remainder so total = 100
                $autoScore = ($index === $questionCount - 1) ? $baseScore + $remainder : $baseScore;

                $score = $scoringMode === 'manual'
                    ? (int) ($qData['points'] ?? $autoScore)
                    : $autoScore;

                $question = QuizQuestion::create([
                    'quiz_id'       => $quiz->id,
                    'question_text' => $qData['text'],
                    'image_url'     => $imageUrl,
                    'question_type' => 'multiple_choice',
                    'score'         => $score,
                    'order_number'  => $index + 1,
                ]);

                foreach ($qData['options'] as $optIndex => $optData) {
                    QuizOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optData['text'],
                        'is_correct'  => (int) $qData['correct_option'] === $optIndex,
                    ]);
                }
            }

            return $quiz;
        });

        ActivityLogger::log($course->id, 'created', $quiz, 'Menambahkan kuis: ' . $quiz->title);

        Cache::forget("class_detail_{$course->id}");

        return redirect()->route('manage.courses.show', $course)
            ->with('success', 'Kuis berhasil ditambahkan.');
    }

    /**
     * Show edit quiz form.
     */
    public function edit(ClassSubject $course, Quiz $quiz, Request $request): View
    {
        $this->authorize('update', $course);

        $course->load(['subject', 'schoolClass', 'sections' => fn($q) => $q->orderBy('order_number')]);
        $quiz->load(['questions' => fn($q) => $q->orderBy('order_number'), 'questions.options']);

        $breadcrumbs = [
            ['label' => $course->subject->name ?? 'Course', 'url' => route('manage.courses.show', $course)],
            ['label' => 'Edit Kuis'],
        ];

        // Build JSON-ready questions array for Alpine
        $existingQuestions = $quiz->questions->map(function ($q, $idx) {
            $correctIndex = 0;
            $options = $q->options->values()->map(function ($opt, $oIdx) use (&$correctIndex) {
                if ($opt->is_correct) {
                    $correctIndex = $oIdx;
                }
                return [
                    'id' => $oIdx + 1,
                    'text' => $opt->option_text,
                ];
            })->toArray();

            return [
                'id' => $idx + 1,
                'text' => $q->question_text,
                'points' => $q->score,
                'imagePreview' => $q->image_url ? asset('storage/' . $q->image_url) : null,
                'existingImageUrl' => $q->image_url,
                'correctOption' => $correctIndex,
                'options' => $options,
                'nextOptionId' => count($options) + 1,
            ];
        })->toArray();

        // Split description back into subtitle + description
        $descParts = explode("\n", $quiz->description ?? '', 2);
        $subtitle = count($descParts) > 1 ? $descParts[0] : '';
        $descriptionBody = count($descParts) > 1 ? $descParts[1] : ($quiz->description ?? '');

        // Detect scoring mode: if all questions have the same score, it's auto
        $scores = $quiz->questions->pluck('score')->unique();
        $scoringMode = ($scores->count() === 1) ? 'auto' : 'manual';

        return view('teacher.quiz-form', compact(
            'course', 'breadcrumbs', 'quiz', 'existingQuestions', 'subtitle', 'descriptionBody', 'scoringMode'
        ));
    }

    /**
     * Update an existing quiz with questions and options.
     */
    public function update(Request $request, ClassSubject $course, Quiz $quiz)
    {
        $this->authorize('update', $course);

        $request->validate([
            'title'           => 'required|string|max:255',
            'subtitle'        => 'nullable|string|max:255',
            'description'     => 'nullable|string|max:10000',
            'section_id'      => 'required|exists:class_subject_sections,id',
            'due_date'        => 'nullable|date',
            'due_time'        => 'nullable|date_format:H:i',
            'time_limit'      => 'nullable|integer|min:1|max:600',
            'close_on_deadline' => 'nullable',
            'questions'       => 'required|array|min:1',
            'questions.*.text'           => 'required|string',
            'questions.*.points'         => 'nullable|integer|min:1|max:1000',
            'questions.*.correct_option' => 'required|integer|min:0',
            'questions.*.options'        => 'required|array|min:2',
            'questions.*.options.*.text' => 'required|string|max:1000',
            'questions.*.image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Enforce 2MB limit on server side
        ]);

        $section = $course->sections()->findOrFail($request->section_id);

        $endAt = null;
        if ($request->due_date) {
            $endAt = $request->due_date;
            $endAt .= ' ' . ($request->due_time ? $request->due_time . ':00' : '23:59:00');
        }

        // Determine scoring
        $scoringMode = $request->input('scoring_mode', 'auto');
        $questionCount = count($request->questions);
        $baseScore = $questionCount > 0 ? (int) floor(100 / $questionCount) : 0;
        $remainder = $questionCount > 0 ? 100 - ($baseScore * $questionCount) : 0;

        DB::transaction(function () use ($request, $quiz, $section, $endAt, $scoringMode, $baseScore, $remainder, $questionCount) {
            $quiz->update([
                'section_id'  => $section->id,
                'title'       => $request->title,
                'description' => ($request->subtitle ? $request->subtitle . "\n" : '') . ($request->description ?? ''),
                'time_limit'  => $request->time_limit,
                'end_at'      => $endAt,
            ]);

            // Delete old questions + options (cascade)
            $quiz->questions()->delete();

            foreach ($request->questions as $index => $qData) {
                $imageUrl = null;

                // Check for new image upload
                if ($request->hasFile("questions.{$index}.image")) {
                    $file = $request->file("questions.{$index}.image");
                    $filename = time() . '_q' . $index . '_' . $file->getClientOriginalName();
                    $path = $file->storeAs('quiz_images', $filename, 'public');
                    $imageUrl = $path;
                } elseif (!empty($qData['existing_image'])) {
                    // Keep existing image
                    $imageUrl = $qData['existing_image'];
                }

                // Auto: last question gets the remainder so total = 100
                $autoScore = ($index === $questionCount - 1) ? $baseScore + $remainder : $baseScore;

                $score = $scoringMode === 'manual'
                    ? (int) ($qData['points'] ?? $autoScore)
                    : $autoScore;

                $question = QuizQuestion::create([
                    'quiz_id'       => $quiz->id,
                    'question_text' => $qData['text'],
                    'image_url'     => $imageUrl,
                    'question_type' => 'multiple_choice',
                    'score'         => $score,
                    'order_number'  => $index + 1,
                ]);

                foreach ($qData['options'] as $optIndex => $optData) {
                    QuizOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optData['text'],
                        'is_correct'  => (int) $qData['correct_option'] === $optIndex,
                    ]);
                }
            }
        });

        ActivityLogger::log($course->id, 'updated', $quiz, 'Memperbarui kuis: ' . $quiz->title);

        Cache::forget("class_detail_{$course->id}");

        return redirect()->route('manage.courses.show', $course)
            ->with('success', 'Kuis berhasil diperbarui.');
    }

    /**
     * Delete a quiz.
     */
    public function destroy(ClassSubject $course, Quiz $quiz)
    {
        $this->authorize('update', $course);

        ActivityLogger::log($course->id, 'deleted', $quiz, 'Menghapus kuis: ' . $quiz->title);

        $quiz->delete();

        Cache::forget("class_detail_{$course->id}");

        return redirect()->route('manage.courses.show', $course)
            ->with('success', 'Kuis berhasil dihapus.');
    }

    /**
     * Show quiz results / grading page (Teacher).
     */
    public function results(ClassSubject $course, Quiz $quiz): View
    {
        $this->authorize('update', $course);

        $quiz->load(['section.classSubject.subject', 'section.classSubject.schoolClass', 'questions']);

        // Get the class_id from the course
        $classId = $course->class_id;

        // Get active academic year
        $activeAcademicYearId = \App\Models\AcademicYear::where('is_active', true)->value('id');

        // Get all students enrolled in this class (with pivot for sorting)
        $students = \App\Models\Student::whereHas('studentClasses', function ($q) use ($classId, $activeAcademicYearId) {
                $q->where('class_id', $classId);
                if ($activeAcademicYearId) {
                    $q->where('academic_year_id', $activeAcademicYearId);
                }
            })
            ->with(['user' => function ($q) {
                $q->select('id', 'full_name');
            }, 'studentClasses' => function($q) use ($classId, $activeAcademicYearId) {
                $q->where('class_id', $classId);
                if ($activeAcademicYearId) {
                    $q->where('academic_year_id', $activeAcademicYearId);
                }
            }])
            ->get()
            ->sortBy(function($student) {
                $sc = $student->studentClasses->first();
                $num = $sc ? ($sc->attendance_number ?? 999) : 999;
                return sprintf('%03d-%s', $num, $student->user->full_name);
            });

        // Fetch all submitted attempts for this quiz (keyed by student_id, only best/latest)
        $attemptsByStudent = \App\Models\QuizAttempt::where('quiz_id', $quiz->id)
            ->where('is_submitted', true)
            ->get()
            ->groupBy('student_id')
            ->map(function ($attempts) {
                // Pick the attempt with the highest score
                return $attempts->sortByDesc('total_score')->first();
            });

        $totalQuestions = $quiz->questions->count();

        // Build rows
        $rows = $students->map(function ($student, $index) use ($quiz, $attemptsByStudent, $course, $totalQuestions) {
            $attempt = $attemptsByStudent->get($student->id);

            // Determine status
            if ($attempt) {
                $keterangan = 'Sudah Mengerjakan';
                $keteranganColor = 'green';
            } else {
                $keterangan = 'Belum Mengerjakan';
                $keteranganColor = 'red';
            }

            // Determine score display
            if (!$attempt) {
                $nilaiLabel = 'Kosong';
                $nilaiColor = 'red';
            } else {
                $nilaiLabel = (string) round($attempt->total_score, 1);
                $nilaiColor = 'default';
            }

            return [
                'no' => $index + 1,
                'name' => $student->user->full_name ?? '-',
                'nisn' => $student->nisn ?? '-',
                'keterangan' => $keterangan,
                'keteranganColor' => $keteranganColor,
                'nilaiLabel' => $nilaiLabel,
                'nilaiColor' => $nilaiColor,
                'hasAttempt' => $attempt !== null,
                'attemptUuid' => $attempt?->uuid,
                'student_id' => $student->id,
            ];
        });

        $course->load(['subject', 'schoolClass']);

        $breadcrumbs = [
            ['label' => $course->subject->name ?? 'Course', 'url' => route('manage.courses.show', $course)],
            ['label' => $quiz->title ?? 'Kuis'],
        ];

        return view('teacher.quiz-grading', compact('course', 'quiz', 'rows', 'breadcrumbs', 'totalQuestions'));
    }
}
