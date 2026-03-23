<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use App\Models\QuizProgress;
use App\Models\ClassSubject;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class StudentQuizController extends Controller
{
    /**
     * Show quiz preview / info page.
     */
    public function show(ClassSubject $course, Quiz $quiz): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !$user->student) {
            abort(403);
        }

        $quiz->load([
            'questions',
            'section.classSubject.subject',
            'section.classSubject.schoolClass',
        ]);

        // Get past attempts for this student
        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $user->student->id)
            ->orderByDesc('created_at')
            ->get();

        $attemptsUsed = $attempts->count();
        $maxAttempts = $quiz->max_attempt ?? 1;
        $canAttempt = $attemptsUsed < $maxAttempts;

        // Check if quiz is still open
        $isOpen = true;
        if ($quiz->end_at && now()->isAfter($quiz->end_at)) {
            $isOpen = false;
        }

        // Check if there is an active (unsubmitted) attempt
        $activeAttempt = $attempts->where('is_submitted', false)->first();

        $breadcrumbs = [
            ['label' => $course->subject->name ?? 'Course', 'url' => route('courses.show', $course)],
            ['label' => $quiz->title],
        ];

        return view('student.quiz.show', compact(
            'quiz', 'course', 'attempts', 'attemptsUsed', 'maxAttempts',
            'canAttempt', 'isOpen', 'activeAttempt', 'breadcrumbs'
        ));
    }

    /**
     * Start a new quiz attempt.
     */
    public function start(ClassSubject $course, Quiz $quiz): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !$user->student) {
            abort(403);
        }

        // Check for existing active attempt
        $activeAttempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $user->student->id)
            ->where('is_submitted', false)
            ->first();

        if ($activeAttempt) {
            return redirect()->route('student.quiz.take', $activeAttempt);
        }

        // Validate attempt limit
        $attemptsUsed = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $user->student->id)
            ->count();

        if ($attemptsUsed >= ($quiz->max_attempt ?? 1)) {
            return redirect()->route('student.quiz.show', [$course, $quiz])
                ->with('error', 'Anda telah mencapai batas percobaan kuis ini.');
        }

        // Check if quiz is still open
        if ($quiz->end_at && now()->isAfter($quiz->end_at)) {
            return redirect()->route('student.quiz.show', [$course, $quiz])
                ->with('error', 'Kuis sudah ditutup.');
        }

        // Create new attempt
        $attempt = QuizAttempt::create([
            'quiz_id'    => $quiz->id,
            'student_id' => $user->student->id,
            'started_at' => now(),
        ]);

        return redirect()->route('student.quiz.take', $attempt);
    }

    /**
     * Show the quiz taking interface (fullscreen).
     */
    public function take(QuizAttempt $attempt): View|RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !$user->student || $attempt->student_id !== $user->student->id) {
            abort(403);
        }

        $quiz = $attempt->quiz;
        $classSubject = $quiz->section->classSubject;

        // If already submitted, redirect to preview
        if ($attempt->is_submitted) {
            return redirect()->route('student.quiz.show', [$classSubject, $quiz])
                ->with('info', 'Kuis ini sudah dikumpulkan.');
        }

        $quiz->load(['questions' => fn($q) => $q->orderBy('order_number'), 'questions.options']);

        // Check server-side time expiry
        if ($quiz->time_limit && $attempt->started_at) {
            $deadline = $attempt->started_at->copy()->addMinutes($quiz->time_limit);
            if (now()->isAfter($deadline)) {
                // Auto-submit if time expired
                $this->performSubmit($attempt, 'Waktu habis (server-side)');
                return redirect()->route('student.quiz.show', [$classSubject, $quiz])
                    ->with('info', 'Waktu kuis habis. Jawaban Anda telah otomatis dikumpulkan.');
            }
        }

        // Get already-saved answers for this attempt
        $savedAnswers = QuizAnswer::where('attempt_id', $attempt->id)
            ->pluck('selected_option_id', 'question_id')
            ->toArray();

        // Calculate remaining time in seconds
        $remainingSeconds = null;
        if ($quiz->time_limit && $attempt->started_at) {
            $deadline = $attempt->started_at->copy()->addMinutes($quiz->time_limit);
            $remainingSeconds = (int) max(0, now()->diffInSeconds($deadline, false));
        }

        return view('student.quiz.take', compact(
            'attempt', 'quiz', 'savedAnswers', 'remainingSeconds'
        ));
    }

    /**
     * Save a single answer (autosave via AJAX).
     */
    public function saveAnswer(Request $request, QuizAttempt $attempt): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !$user->student || $attempt->student_id !== $user->student->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($attempt->is_submitted) {
            return response()->json(['error' => 'Kuis sudah dikumpulkan'], 422);
        }

        $request->validate([
            'question_id'      => 'required|exists:quiz_questions,id',
            'selected_option_id' => 'required|exists:quiz_options,id',
        ]);

        QuizAnswer::updateOrCreate(
            [
                'attempt_id'  => $attempt->id,
                'question_id' => $request->question_id,
            ],
            [
                'selected_option_id' => $request->selected_option_id,
            ]
        );

        return response()->json(['status' => 'saved']);
    }

    /**
     * Submit the quiz attempt.
     */
    public function submit(Request $request, QuizAttempt $attempt): RedirectResponse|JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user || !$user->student || $attempt->student_id !== $user->student->id) {
            abort(403);
        }

        $quiz = $attempt->quiz;
        $classSubject = $quiz->section->classSubject;

        if ($attempt->is_submitted) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Kuis sudah dikumpulkan'], 422);
            }
            return redirect()->route('student.quiz.show', [$classSubject, $quiz])
                ->with('info', 'Kuis sudah dikumpulkan sebelumnya.');
        }

        // Save any final answers from the form submission
        if ($request->has('answers')) {
            foreach ($request->answers as $questionId => $optionId) {
                if ($optionId) {
                    QuizAnswer::updateOrCreate(
                        [
                            'attempt_id'  => $attempt->id,
                            'question_id' => $questionId,
                        ],
                        [
                            'selected_option_id' => $optionId,
                        ]
                    );
                }
            }
        }

        $reason = $request->input('violation_reason', null);
        $this->performSubmit($attempt, $reason);

        if ($request->expectsJson()) {
            return response()->json([
                'status'  => 'submitted',
                'message' => $reason
                    ? 'Kuis otomatis dikumpulkan karena pelanggaran: ' . $reason
                    : 'Kuis berhasil dikumpulkan.',
            ]);
        }

        $message = $reason
            ? 'Kuis otomatis dikumpulkan karena pelanggaran: ' . $reason
            : 'Kuis berhasil dikumpulkan.';

        return redirect()->route('student.quiz.show', [$classSubject, $quiz])
            ->with('success', $message);
    }

    /**
     * Perform the actual grading and submission.
     */
    private function performSubmit(QuizAttempt $attempt, ?string $reason = null): void
    {
        $quiz = $attempt->quiz;
        $quiz->load(['questions.options']);

        DB::transaction(function () use ($attempt, $quiz, $reason) {
            $totalScore = 0;

            foreach ($quiz->questions as $question) {
                $answer = QuizAnswer::where('attempt_id', $attempt->id)
                    ->where('question_id', $question->id)
                    ->first();

                if ($answer && $answer->selected_option_id) {
                    $correctOption = $question->options->where('is_correct', true)->first();
                    if ($correctOption && $answer->selected_option_id == $correctOption->id) {
                        $score = $question->score ?? 0;
                        $answer->update(['score' => $score]);
                        $totalScore += $score;
                    } else {
                        $answer->update(['score' => 0]);
                    }
                }
            }

            $attempt->update([
                'is_submitted' => true,
                'submitted_at' => now(),
                'total_score'  => $totalScore,
            ]);

            // Update quiz progress
            QuizProgress::updateOrCreate(
                [
                    'student_id' => $attempt->student_id,
                    'quiz_id'    => $attempt->quiz_id,
                ],
                [
                    'is_completed' => true,
                    'completed_at' => now(),
                    'score'        => $totalScore,
                ]
            );

            // Log activity
            $desc = 'Mengumpulkan kuis: ' . $quiz->title . ' (Skor: ' . $totalScore . ')';
            if ($reason) {
                $desc .= ' [' . $reason . ']';
            }
            ActivityLogger::log(
                $quiz->section->class_subject_id ?? null,
                'submitted',
                $attempt,
                $desc
            );
        });
    }
}
