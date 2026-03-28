<?php

namespace App\Http\Controllers;

use App\Models\TeacherSurveyPeriod;
use App\Models\TeacherSurveyQuestion;
use App\Models\TeacherSurveyResponse;
use App\Models\TeacherSurveyResponseDetail;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class TeacherSurveyController extends Controller
{
    /**
     * Display a list of survey periods where the teacher has responses.
     */
    public function index(): View
    {
        $teacher = auth()->user()->teacher;

        // Get survey periods where this teacher has responses
        $periods = TeacherSurveyPeriod::whereHas('responses', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })
        ->with('academicYear')
        ->orderBy('end_date', 'desc')
        ->get();

        $breadcrumbs = [
            ['label' => 'Hasil Survei Mengajar'],
        ];

        return view('teacher.surveys.index', compact('periods', 'breadcrumbs'));
    }

    /**
     * Display detailed survey results for a specific period for the active teacher.
     */
    public function show(TeacherSurveyPeriod $survey): View
    {
        $teacher = auth()->user()->teacher;

        // Ensure this teacher has responses in this survey
        $totalResponses = TeacherSurveyResponse::where('period_id', $survey->id)
            ->where('teacher_id', $teacher->id)
            ->count();

        abort_if($totalResponses === 0, 404, 'Tidak ada data hasil survei untuk Anda pada periode ini.');

        // Get the questions
        $questions = TeacherSurveyQuestion::where('period_id', $survey->id)
            ->orderBy('order_number')
            ->orderBy('id')
            ->get();

        // Calculate average score for each question
        $questionAverages = [];
        $overallScore = 0;
        
        foreach ($questions as $question) {
            $avg = TeacherSurveyResponseDetail::where('question_id', $question->id)
                ->whereHas('response', function ($query) use ($survey, $teacher) {
                    $query->where('period_id', $survey->id)
                          ->where('teacher_id', $teacher->id);
                })
                ->avg('score');
            
            $questionAverages[$question->id] = round($avg ?? 0, 1);
            $overallScore += $avg ?? 0;
        }

        $overallAverage = count($questions) > 0 ? round($overallScore / count($questions), 2) : 0;

        // Get all written comments (anonymously)
        $comments = TeacherSurveyResponse::where('period_id', $survey->id)
            ->where('teacher_id', $teacher->id)
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->pluck('comment');

        $breadcrumbs = [
            ['label' => 'Hasil Survei Mengajar', 'url' => route('teacher.surveys.index')],
            ['label' => $survey->title],
        ];

        return view('teacher.surveys.show', compact('survey', 'totalResponses', 'questions', 'questionAverages', 'overallAverage', 'comments', 'breadcrumbs'));
    }
}
