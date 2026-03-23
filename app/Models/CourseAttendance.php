<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_meeting_id',
        'student_id',
        'status',
        'note',
        'recorded_by'
    ];

    /**
     * Get the meeting this attendance record belongs to.
     */
    public function meeting()
    {
        return $this->belongsTo(CourseMeeting::class, 'course_meeting_id');
    }

    /**
     * Get the student associated with this attendance record.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user who recorded this attendance.
     */
    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
