<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassSubject;
use App\Models\CourseMeeting;
use App\Models\CourseAttendance;
use Illuminate\Support\Facades\Auth;

class CourseAttendanceController extends Controller
{
    /**
     * Display the global attendance management dashboard for the teacher 
     * listing all their taught courses.
     */
    public function dashboard()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // This assumes the user is a teacher. 
        // We get courses where they are the assigned teacher.
        // In this LMS, teacher is usually linked to user.
        $teacher = $user->teacher;
        
        $courses = collect();
        if ($teacher) {
            $courses = ClassSubject::where('teacher_id', $teacher->id)
                ->with(['subject', 'schoolClass'])
                ->get();
        }

        return view('teacher.attendances.dashboard', compact('courses'));
    }

    /**
     * Display the main attendance management page for a course.
     */
    public function index(ClassSubject $course)
    {
        // Load meetings ordered by latest
        $meetings = $course->courseMeetings()->orderBy('meeting_date', 'desc')->get();
        
        // Calculate basic attendance stats per meeting
        foreach ($meetings as $meeting) {
            $attendances = $meeting->attendances()->get();
            $meeting->stats = [
                'hadir' => $attendances->where('status', 'Hadir')->count(),
                'izin' => $attendances->where('status', 'Izin')->count(),
                'sakit' => $attendances->where('status', 'Sakit')->count(),
                'alpha' => $attendances->where('status', 'Alpha')->count(),
            ];
        }

        return view('teacher.attendances.index', compact('course', 'meetings'));
    }

    /**
     * Store a newly created course meeting in storage.
     */
    public function storeMeeting(Request $request, ClassSubject $course)
    {
        $validated = $request->validate([
            'meeting_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $validated['class_subject_id'] = $course->id;
        $validated['created_by'] = Auth::id();

        $meeting = CourseMeeting::create($validated);

        // Pre-fill student attendances with 'Alpha' or default status?
        // Let's pre-fill them all with default 'Alpha' or null?
        // "Presensi default" usually starts empty or default. If we don't prefill, the inline radio buttons 
        // can be empty until chosen. But creating them makes it easier to track.
        $students = $course->schoolClass->studentClasses()->with('student')->get()->pluck('student');
        
        $attendanceData = [];
        foreach ($students as $student) {
            $attendanceData[] = [
                'course_meeting_id' => $meeting->id,
                'student_id' => $student->id,
                'status' => 'Alpha', // Default to Alpha if not changed
                'note' => null,
                'recorded_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if (count($attendanceData) > 0) {
            CourseAttendance::insert($attendanceData);
        }

        return redirect()->route('manage.courses.attendances.index', $course)
                         ->with('success', 'Pertemuan berhasil ditambahkan.');
    }

    /**
     * Update the details of a specific course meeting.
     */
    public function updateMeeting(Request $request, ClassSubject $course, CourseMeeting $meeting)
    {
        // Ensure meeting belongs to course
        if ($meeting->class_subject_id !== $course->id) {
            abort(404);
        }

        $validated = $request->validate([
            'meeting_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $meeting->update($validated);

        return redirect()->route('manage.courses.attendances.showMeeting', [$course, $meeting])
                         ->with('success', 'Detail pertemuan berhasil diperbarui.');
    }

    /**
     * Display the detailed roster for a specific meeting.
     */
    public function showMeeting(ClassSubject $course, CourseMeeting $meeting)
    {
        // Ensure meeting belongs to course
        if ($meeting->class_subject_id !== $course->id) {
            abort(404);
        }

        // Load attendances with student data and their pivot class data for sorting
        $attendances = $meeting->attendances()
            ->with(['student.user', 'student.studentClasses' => function($query) use ($course) {
                $query->where('class_id', $course->class_id);
            }])
            ->get()
            ->sortBy(function($attendance) {
                $sc = $attendance->student->studentClasses->first();
                $num = $sc ? ($sc->attendance_number ?? 999) : 999;
                return sprintf('%03d-%s', $num, $attendance->student->user->full_name);
            });

        return view('teacher.attendances.show-meeting', compact('course', 'meeting', 'attendances'));
    }

    /**
     * Update the attendance roster for a specific meeting.
     */
    public function updateRoster(Request $request, ClassSubject $course, CourseMeeting $meeting)
    {
        // Ensure meeting belongs to course
        if ($meeting->class_subject_id !== $course->id) {
            abort(404);
        }

        $validated = $request->validate([
            'attendances' => 'required|array',
            'attendances.*.status' => 'required|in:Hadir,Sakit,Izin,Alpha',
            'attendances.*.note' => 'nullable|string',
        ]);

        foreach ($validated['attendances'] as $attendanceId => $data) {
            CourseAttendance::where('id', $attendanceId)
                ->where('course_meeting_id', $meeting->id)
                ->update([
                    'status' => $data['status'],
                    'note' => $data['note'] ?? null,
                    'recorded_by' => Auth::id(),
                    'updated_at' => now()
                ]);
        }

        return redirect()->route('manage.courses.attendances.showMeeting', [$course, $meeting])
                         ->with('success', 'Rekap absensi berhasil diperbarui.');
    }

    /**
     * Display the overall attendance recap for the course.
     */
    public function recap(ClassSubject $course)
    {
        $meetings = $course->courseMeetings()->orderBy('meeting_date', 'asc')->get();
        $students = $course->schoolClass->studentClasses()->with('student.user')->get()->pluck('student');

        // Prepare a matrix mapping student_id to an array of meeting attendances
        $matrix = [];
        $totals = [];

        // Preload all attendances for these meetings
        $allAttendances = CourseAttendance::whereIn('course_meeting_id', $meetings->pluck('id'))
            ->get()
            ->groupBy('student_id');

        foreach ($students as $student) {
            $studentAttendances = $allAttendances->get($student->id, collect());
            
            $meetingsData = [];
            $hadirCount = 0;

            foreach ($meetings as $meeting) {
                $att = $studentAttendances->firstWhere('course_meeting_id', $meeting->id);
                $status = $att ? $att->status : '-';
                $meetingsData[$meeting->id] = $status;
                
                if ($status === 'Hadir') {
                    $hadirCount++;
                }
            }

            $percentage = $meetings->count() > 0 ? round(($hadirCount / $meetings->count()) * 100) : 0;

            $matrix[$student->id] = [
                'student' => $student,
                'meetings' => $meetingsData,
                'percentage' => $percentage
            ];
        }

        // Sort matrix by attendance number, then by student name alphabetically
        uasort($matrix, function($a, $b) use ($course) {
            $scA = $a['student']->studentClasses->firstWhere('class_id', $course->class_id);
            $scB = $b['student']->studentClasses->firstWhere('class_id', $course->class_id);
            $numA = $scA ? ($scA->attendance_number ?? 999) : 999;
            $numB = $scB ? ($scB->attendance_number ?? 999) : 999;

            if ($numA === $numB) {
                return strcasecmp($a['student']->user->full_name, $b['student']->user->full_name);
            }
            return $numA <=> $numB;
        });

        return view('teacher.attendances.recap', compact('course', 'meetings', 'matrix'));
    }
}
