<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_subject_id',
        'title',
        'description',
        'meeting_date',
        'start_time',
        'end_time',
        'created_by'
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    /**
     * Get the class subject (course) this meeting belongs to.
     */
    public function classSubject()
    {
        return $this->belongsTo(ClassSubject::class);
    }

    /**
     * Get the user who created this meeting.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the attendances for this meeting.
     */
    public function attendances()
    {
        return $this->hasMany(CourseAttendance::class);
    }
}
