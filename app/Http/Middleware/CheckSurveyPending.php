<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\TeacherSurveyPeriod;
use App\Models\TeacherSurveyResponse;
use App\Models\StudentClass;
use App\Models\ClassSubject;
use App\Models\AcademicYear;

class CheckSurveyPending
{
    /**
     * Handle an incoming request.
     *
     * Only applies to students. If there is an active survey with pending
     * teachers (not yet evaluated), redirect to the survey page and block
     * all other features.
     */
    public function handle(Request $request, Closure $next)
    {
        // Only apply gate to logged-in students
        if (!auth()->check() || !auth()->user()->hasRole('student')) {
            return $next($request);
        }

        // Skip if already on the surveys route
        if ($request->routeIs('student.surveys.*') || $request->routeIs('logout')) {
            return $next($request);
        }

        $student = auth()->user()->student;

        // If for some reason the student record doesn't exist, skip
        if (!$student) {
            return $next($request);
        }

        // Get the current active academic year
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            return $next($request);
        }

        // Check for any active survey period
        $activePeriod = TeacherSurveyPeriod::where('is_active', true)->first();
        if (!$activePeriod) {
            return $next($request);
        }

        // Find student's class in the current active academic year
        $studentClassIds = StudentClass::where('student_id', $student->id)
            ->where('academic_year_id', $activeYear->id)
            ->pluck('class_id');

        if ($studentClassIds->isEmpty()) {
            return $next($request);
        }

        // Find all unique teachers who teach the student's classes
        $teacherIds = ClassSubject::whereIn('class_id', $studentClassIds)
            ->where('academic_year_id', $activeYear->id)
            ->whereNotNull('teacher_id')
            ->pluck('teacher_id')
            ->unique();

        if ($teacherIds->isEmpty()) {
            return $next($request);
        }

        // Check if the student has completed all evaluations
        $completedCount = TeacherSurveyResponse::where('period_id', $activePeriod->id)
            ->where('student_id', $student->id)
            ->whereIn('teacher_id', $teacherIds)
            ->count();

        $pendingCount = $teacherIds->count() - $completedCount;

        if ($pendingCount > 0) {
            // There are still uncompleted surveys – gate the student
            return redirect()->route('student.surveys.index')
                ->with('survey_gate_message', "⚠️ Terdapat <strong>{$pendingCount} guru</strong> yang belum Anda nilai. Selesaikan survei terlebih dahulu untuk mengakses fitur lainnya.");
        }

        return $next($request);
    }
}
