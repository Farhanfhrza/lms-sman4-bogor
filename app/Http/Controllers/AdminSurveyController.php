<?php

namespace App\Http\Controllers;

use App\Models\TeacherSurveyPeriod;
use App\Models\TeacherSurveyQuestion;
use App\Models\AcademicYear;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AdminSurveyController extends Controller
{
    public function index(): View
    {
        $periods = TeacherSurveyPeriod::with('academicYear')
            ->orderBy('start_date', 'desc')
            ->paginate(15);
            
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        $breadcrumbs = [
            ['label' => 'Manajemen Survei'],
        ];

        return view('admin.surveys.index', compact('periods', 'academicYears', 'breadcrumbs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'title'            => 'required|string|max:255',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
        ]);

        $validated['is_active'] = false; // Always create as inactive
        $validated['semester']  = 1;     // Default, not used in UI anymore

        $period = TeacherSurveyPeriod::create($validated);

        ActivityLogger::log(null, 'created', $period, "Membuat periode survei: {$period->title}");

        return redirect()->route('admin.surveys.show', $period)->with('success', 'Periode survei berhasil ditambahkan. Silakan tambahkan pertanyaan.');
    }

    public function show(TeacherSurveyPeriod $survey): View
    {
        $survey->load('academicYear');
        
        $questions = TeacherSurveyQuestion::where('period_id', $survey->id)
            ->orderBy('order_number')
            ->orderBy('id')
            ->get();

        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        // Count UNIQUE students who have completed the survey
        // (at least one response submitted in this period = they participated)
        $responseCount = \App\Models\TeacherSurveyResponse::where('period_id', $survey->id)
            ->distinct('student_id')
            ->count('student_id');

        // Per-teacher recap: average scores per teacher in this period
        $teacherRecap = \App\Models\TeacherSurveyResponse::where('period_id', $survey->id)
            ->with('teacher.user')
            ->get()
            ->groupBy('teacher_id')
            ->map(function ($responses) use ($questions) {
                $teacher = $responses->first()->teacher;
                $totalScore = 0;
                $questionCount = $questions->count();
                $responderCount = $responses->count();

                // Average score across all questions and all responders for this teacher
                $avgPerQuestion = [];
                foreach ($questions as $q) {
                    $avg = \App\Models\TeacherSurveyResponseDetail::where('question_id', $q->id)
                        ->whereIn('response_id', $responses->pluck('id'))
                        ->avg('score');
                    $avgPerQuestion[$q->id] = round($avg ?? 0, 1);
                    $totalScore += $avg ?? 0;
                }

                $overallAvg = $questionCount > 0 ? round($totalScore / $questionCount, 2) : 0;

                return [
                    'teacher'        => $teacher,
                    'responder_count'=> $responderCount,
                    'overall_avg'    => $overallAvg,
                    'avg_per_q'      => $avgPerQuestion,
                ];
            })
            ->sortByDesc('overall_avg')
            ->values();

        $breadcrumbs = [
            ['label' => 'Manajemen Survei', 'url' => route('admin.surveys.index')],
            ['label' => $survey->title],
        ];

        return view('admin.surveys.show', compact('survey', 'questions', 'responseCount', 'academicYears', 'breadcrumbs', 'teacherRecap'));
    }

    public function update(Request $request, TeacherSurveyPeriod $survey): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'title'            => 'required|string|max:255',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
        ]);

        $survey->update($validated);

        ActivityLogger::log(null, 'updated', $survey, "Memperbarui periode survei: {$survey->title}");

        return redirect()->route('admin.surveys.show', $survey)->with('success', 'Data periode survei berhasil diperbarui.');
    }

    public function destroy(TeacherSurveyPeriod $survey): RedirectResponse
    {
        $title = $survey->title;
        $survey->delete();

        ActivityLogger::log(null, 'deleted', null, "Menghapus periode survei: {$title}");

        return redirect()->route('admin.surveys.index')->with('success', 'Periode survei berhasil dihapus.');
    }

    public function toggleStatus(TeacherSurveyPeriod $survey): RedirectResponse
    {
        // If activating, we might optionally want to deactivate others. For surveys, multiple can technically be active if they don't overlap, 
        // but typically one at a time makes sense. Here we just toggle the specific one.
        if (!$survey->is_active) {
            // Check if it has questions
            if (TeacherSurveyQuestion::where('period_id', $survey->id)->count() === 0) {
                return back()->with('error', 'Tidak dapat mengaktifkan survei yang belum memiliki daftar pertanyaan.');
            }
        }

        $survey->update(['is_active' => !$survey->is_active]);

        $status = $survey->is_active ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLogger::log(null, 'updated', $survey, "Status survei {$survey->title} {$status}");

        return back()->with('success', "Periode survei berhasil {$status}.");
    }

    // --- Questions Management ---

    public function storeQuestion(Request $request, TeacherSurveyPeriod $survey): RedirectResponse
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'order_number'  => 'required|integer|min:1',
        ]);

        // If the order number is already taken, shift existing questions up
        $conflict = TeacherSurveyQuestion::where('period_id', $survey->id)
            ->where('order_number', $validated['order_number'])
            ->first();

        if ($conflict) {
            TeacherSurveyQuestion::where('period_id', $survey->id)
                ->where('order_number', '>=', $validated['order_number'])
                ->increment('order_number');
        }

        TeacherSurveyQuestion::create([
            'period_id'     => $survey->id,
            'question_text' => $validated['question_text'],
            'order_number'  => $validated['order_number'],
        ]);

        return back()->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    public function updateQuestion(Request $request, TeacherSurveyQuestion $question): RedirectResponse
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'order_number'  => 'required|integer|min:1',
        ]);

        // If the order number changed and already taken by another question, shift others
        if ($validated['order_number'] != $question->order_number) {
            $conflict = TeacherSurveyQuestion::where('period_id', $question->period_id)
                ->where('order_number', $validated['order_number'])
                ->where('id', '!=', $question->id)
                ->first();

            if ($conflict) {
                // Swap: give the conflicting one the old order number of the one being updated
                $conflict->update(['order_number' => $question->order_number]);
            }
        }

        $question->update($validated);

        return back()->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    public function destroyQuestion(TeacherSurveyQuestion $question): RedirectResponse
    {
        $question->delete();

        return back()->with('success', 'Pertanyaan berhasil dihapus.');
    }
}
