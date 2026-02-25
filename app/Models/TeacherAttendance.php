<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherAttendanceFactory> */
    use HasFactory;

    protected $fillable = [
        'teacher_id', 'attendance_date', 'status', 'note', 'recorded_by'
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
