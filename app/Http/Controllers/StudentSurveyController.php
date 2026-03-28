<?php

namespace App\Http\Controllers;

use App\Models\TeacherSurveyPeriod;
use App\Models\TeacherSurveyQuestion;
use App\Models\TeacherSurveyResponse;
use App\Models\TeacherSurveyResponseDetail;
use App\Models\StudentClass;
use App\Models\ClassSubject;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class StudentSurveyController extends Controller
{
    /**
     * Get the list of unique teachers for the student in the active academic year.
     */
    private function getTeachersForStudent($student): \Illuminate\Support\Collection
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return collect();
        }

        $studentClassIds = StudentClass::where('student_id', $student->id)
            ->where('academic_year_id', $activeYear->id)
            ->pluck('class_id');

        if ($studentClassIds->isEmpty()) {
            return collect();
        }

        // Get all unique teachers per subject for these classes
        $classSubjects = ClassSubject::with(['teacher.user', 'subject'])
            ->whereIn('class_id', $studentClassIds)
            ->where('academic_year_id', $activeYear->id)
            ->whereNotNull('teacher_id')
            ->get();

        $teachersMap = [];
        foreach ($classSubjects as $cs) {
            $tid = $cs->teacher_id;
            if (!isset($teachersMap[$tid])) {
                $teachersMap[$tid] = [
                    'teacher'  => $cs->teacher,
                    'subjects' => collect([$cs->subject->name]),
                ];
            } else {
                if (!$teachersMap[$tid]['subjects']->contains($cs->subject->name)) {
                    $teachersMap[$tid]['subjects']->push($cs->subject->name);
                }
            }
        }

        return collect($teachersMap);
    }

    /**
     * Display a listing of active surveys and the teachers to evaluate.
     */
    public function index(): View
    {
        $user    = auth()->user();
        $student = $user->student;

        // Get all active survey periods (is_active toggle only, no date filter)
        $activePeriods = TeacherSurveyPeriod::where('is_active', true)
            ->with('academicYear')
            ->orderBy('created_at', 'desc')
            ->get();

        $teachersMap = $this->getTeachersForStudent($student);
        $surveysData = [];

        foreach ($activePeriods as $period) {
            $teachersList = $teachersMap->map(function ($item) use ($period, $student) {
                $hasResponded = TeacherSurveyResponse::where('period_id', $period->id)
                    ->where('student_id', $student->id)
                    ->where('teacher_id', $item['teacher']->id)
                    ->exists();

                return array_merge($item, ['has_responded' => $hasResponded]);
            })
            ->sortBy('has_responded')
            ->values();

            $surveysData[] = [
                'period'          => $period,
                'teachers'        => $teachersList,
                'total_teachers'  => $teachersList->count(),
                'completed_count' => $teachersList->where('has_responded', true)->count(),
            ];
        }

        $breadcrumbs = [
            ['label' => 'Survei Guru'],
        ];

        return view('student.surveys.index', compact('surveysData', 'breadcrumbs'));
    }

    /**
     * Show the form for filling out the survey for a specific teacher.
     */
    public function fill(TeacherSurveyPeriod $survey, \App\Models\Teacher $teacher): View
    {
        abort_if(!$survey->is_active, 403, 'Survei ini sudah tidak aktif atau ditutup.');

        $student = auth()->user()->student;

        // Check if student already responded
        abort_if(
            TeacherSurveyResponse::where('period_id', $survey->id)
                ->where('student_id', $student->id)
                ->where('teacher_id', $teacher->id)
                ->exists(),
            403,
            'Anda sudah mengisi survei untuk guru ini pada periode ini.'
        );

        $questions = TeacherSurveyQuestion::where('period_id', $survey->id)
            ->orderBy('order_number')
            ->orderBy('id')
            ->get();

        abort_if($questions->isEmpty(), 404, 'Pertanyaan survei belum diatur oleh admin.');

        $teacher->load('user');

        $breadcrumbs = [
            ['label' => 'Survei Guru', 'url' => route('student.surveys.index')],
            ['label' => 'Evaluasi: ' . ($teacher->user->full_name ?? $teacher->user->name)],
        ];

        return view('student.surveys.fill', compact('survey', 'teacher', 'questions', 'breadcrumbs'));
    }

    /**
     * Store the survey response.
     */
    public function store(Request $request, TeacherSurveyPeriod $survey, \App\Models\Teacher $teacher): RedirectResponse
    {
        abort_if(!$survey->is_active, 403, 'Survei ini sudah tidak aktif.');

        $student = auth()->user()->student;

        if (TeacherSurveyResponse::where('period_id', $survey->id)->where('student_id', $student->id)->where('teacher_id', $teacher->id)->exists()) {
            return redirect()->route('student.surveys.index')->with('error', 'Anda sudah menyelesaikan survei untuk guru tersebut.');
        }

        $questions = TeacherSurveyQuestion::where('period_id', $survey->id)->pluck('id')->toArray();

        $rules = ['comment' => 'nullable|string'];
        foreach ($questions as $qId) {
            $rules["answers.{$qId}"] = 'required|integer|min:1|max:5';
        }

        $validated = $request->validate($rules, [
            'answers.*.required' => 'Pilih salah satu nilai (1-5) untuk setiap pertanyaan.',
        ]);

        try {
            DB::transaction(function () use ($survey, $teacher, $student, $validated, $questions) {
                $response = TeacherSurveyResponse::create([
                    'period_id'  => $survey->id,
                    'teacher_id' => $teacher->id,
                    'student_id' => $student->id,
                    'comment'    => $validated['comment'] ?? null,
                ]);

                $details = [];
                foreach ($questions as $qId) {
                    $details[] = [
                        'response_id' => $response->id,
                        'question_id' => $qId,
                        'score'       => (int) $validated['answers'][$qId],
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }
                TeacherSurveyResponseDetail::insert($details);
            });

            return redirect()->route('student.surveys.index')->with('success', 'Terima kasih, survei untuk guru tersebut berhasil disimpan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.')->withInput();
        }
    }
}
