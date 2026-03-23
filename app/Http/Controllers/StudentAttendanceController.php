<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassSubject;
use App\Models\CourseMeeting;
use App\Models\CourseAttendance;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StudentAttendanceController extends Controller
{
    /**
     * Display the student attendance dashboard listing their courses.
     */
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;
        
        if (!$student) {
            abort(403, 'Anda bukan siswa.');
        }

        // Get classes the student is enrolled in
        $studentClasses = $student->studentClasses()->with(['schoolClass.classSubjects.subject', 'schoolClass.classSubjects.teacher.user'])->get();
        $courses = collect();
        
        foreach ($studentClasses as $studentClass) {
            foreach ($studentClass->schoolClass->classSubjects as $course) {
                // Calculate stats
                $meetings = $course->courseMeetings;
                $attendances = CourseAttendance::where('student_id', $student->id)
                    ->whereIn('course_meeting_id', $meetings->pluck('id'))
                    ->get();
                
                $course->stats = [
                    'hadir' => $attendances->where('status', 'Hadir')->count(),
                    'izin' => $attendances->where('status', 'Izin')->count(),
                    'sakit' => $attendances->where('status', 'Sakit')->count(),
                    'alpha' => $attendances->where('status', 'Alpha')->count(),
                    'pertemuan' => $meetings->count(),
                ];
                
                $course->percentage = $meetings->count() > 0 ? round(($course->stats['hadir'] / $meetings->count()) * 100) : 0;
                $courses->push($course);
            }
        }

        return view('student.attendances.index', compact('courses'));
    }

    /**
     * Display the list of meetings for a specific course.
     */
    public function show(ClassSubject $course)
    {
        $user = Auth::user();
        $student = $user->student;
        
        // Ensure student is in the class
        if (!$student->studentClasses()->where('class_id', $course->class_id)->exists()) {
            abort(403);
        }

        $meetings = $course->courseMeetings()->orderBy('meeting_date', 'desc')->orderBy('start_time', 'desc')->get();
        
        // Load attendances mapping by meeting_id for O(1) loop access
        $attendances = CourseAttendance::where('student_id', $student->id)
            ->whereIn('course_meeting_id', $meetings->pluck('id'))
            ->get()
            ->keyBy('course_meeting_id');

        return view('student.attendances.show', compact('course', 'meetings', 'attendances'));
    }

    /**
     * Submit attendance for an active meeting.
     */
    public function submit(Request $request, ClassSubject $course, CourseMeeting $meeting)
    {
        $user = Auth::user();
        $student = $user->student;
        
        if ($meeting->class_subject_id !== $course->id) {
            abort(404);
        }

        // Validate time
        $now = now();
        $meetingDate = Carbon::parse($meeting->meeting_date);
        $startTime = Carbon::parse($meeting->start_time)->setDateFrom($meetingDate);
        $endTime = Carbon::parse($meeting->end_time)->setDateFrom($meetingDate);

        if ($now->lt($startTime) || $now->gt($endTime)) {
            return back()->with('error', 'Waktu absen untuk pertemuan ini sudah ditutup atau belum dibuka.');
        }

        // Fetch attendance record
        $attendance = CourseAttendance::where('student_id', $student->id)
            ->where('course_meeting_id', $meeting->id)
            ->first();

        // Check if already submitted and NOT alpha (if it was alpha, it might be default. Wait, 
        // if they haven't submitted, is it null or Alpha? We prefill with Alpha when meeting is created.
        // So we need to check if they manually submitted. Currently we don't have a 'submitted' flag.
        // But the user said "pilihan akan di lock". So if recorded_by is the student, it's locked.
        // When teacher creates meeting, recorded_by is null.
        if ($attendance && $attendance->recorded_by === $user->id) {
             return back()->with('error', 'Anda sudah melakukan absensi. Pilihan terkunci.');
        }

        $request->validate([
            'status' => 'required|in:Hadir,Sakit,Izin',
        ]);

        if ($attendance) {
            $attendance->update([
                'status' => $request->status,
                'recorded_by' => $user->id
            ]);
        } else {
            // Failsafe if not prefilled
            CourseAttendance::create([
                'course_meeting_id' => $meeting->id,
                'student_id' => $student->id,
                'status' => $request->status,
                'recorded_by' => $user->id
            ]);
        }

        return back()->with('success', 'Berhasil mencatat kehadiran Anda.');
    }
}
