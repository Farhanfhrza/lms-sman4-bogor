<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Fetch active academic year
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if ($user->hasRole('student')) {
            // Load student specifics
            $student = clone $user->student; 
            $studentClass = null;
            $classSubjects = collect();
            $todaySchedules = collect();
            $pendingAssignments = collect();
            
            if ($student && $academicYear) {
                // Find active enrollment
                $studentClass = $student->studentClasses()
                    ->where('academic_year_id', $academicYear->id)
                    ->with('schoolClass')
                    ->first();
                    
                if ($studentClass && $studentClass->schoolClass) {
                    $classId = $studentClass->schoolClass->id;
                    
                    // Get all enrolled subjects
                    $classSubjects = \App\Models\ClassSubject::with(['subject', 'teacher.user'])
                        ->where('class_id', $classId)
                        ->where('academic_year_id', $academicYear->id)
                        ->get();
                        
                    // Filter today's schedules
                    $todayName = now()->format('l'); // 'Monday', 'Tuesday', etc.
                    $todaySchedules = \App\Models\ClassSchedule::with(['classSubject.subject', 'classSubject.teacher.user'])
                        ->whereHas('classSubject', function($query) use ($classId, $academicYear) {
                            $query->where('class_id', $classId)
                                  ->where('academic_year_id', $academicYear->id);
                        })
                        ->where('day_of_week', $todayName)
                        ->orderBy('start_time')
                        ->get();

                    // Get pending assignments
                    $pendingAssignments = \App\Models\Assignment::with(['section.classSubject.subject'])
                        ->whereHas('section.classSubject', function($query) use ($classId, $academicYear) {
                            $query->where('class_id', $classId)
                                  ->where('academic_year_id', $academicYear->id);
                        })
                        ->whereDoesntHave('submissions', function($query) use ($student) {
                            $query->where('student_id', $student->id);
                        })
                        // Include all upcoming or past unsubmitted
                        ->orderBy('due_date', 'asc')
                        ->get();
                }
            }

            return view('dashboard.student', compact(
                'academicYear', 
                'student', 
                'studentClass', 
                'classSubjects', 
                'todaySchedules',
                'pendingAssignments'
            ));
        }
        
        if ($user->hasRole('teacher')) {
            $teacher = $user->teacher;
            $classSubjects = collect();
            $todaySchedules = collect();
            $totalStudents = 0;
            $totalClasses = 0;

            if ($teacher && $academicYear) {
                // Mapel yang diajar oleh guru ini
                $classSubjects = \App\Models\ClassSubject::with(['subject', 'schoolClass'])
                    ->where('teacher_id', $teacher->id)
                    ->where('academic_year_id', $academicYear->id)
                    ->get();
                
                $totalClasses = $classSubjects->pluck('class_id')->unique()->count();
                
                // Hitung jumlah sisa dari kelas-kelas yang diajar
                $classIds = $classSubjects->pluck('class_id')->unique();
                $totalStudents = \App\Models\StudentClass::whereIn('class_id', $classIds)
                    ->where('academic_year_id', $academicYear->id)
                    ->count();

                // Jadwal Hari Ini
                $todayName = now()->format('l');
                $todaySchedules = \App\Models\ClassSchedule::with(['classSubject.subject', 'classSubject.schoolClass'])
                    ->whereHas('classSubject', function($query) use ($teacher, $academicYear) {
                        $query->where('teacher_id', $teacher->id)
                              ->where('academic_year_id', $academicYear->id);
                    })
                    ->where('day_of_week', $todayName)
                    ->orderBy('start_time')
                    ->get();
            }

            return view('dashboard.teacher', compact(
                'academicYear', 'teacher', 'classSubjects', 'todaySchedules', 'totalStudents', 'totalClasses'
            ));
        }
        
        if ($user->hasRole('admin')) {
            $totalStudents = \App\Models\Student::count();
            $totalTeachers = \App\Models\Teacher::count();
            $totalClasses = \App\Models\SchoolClass::where('academic_year_id', $academicYear->id)->count();
            $totalSubjects = \App\Models\Subject::count();

            return view('dashboard.admin', compact('academicYear', 'totalStudents', 'totalTeachers', 'totalClasses', 'totalSubjects'));
        }

        // Fallback
        return view('dashboard');
    }
}
