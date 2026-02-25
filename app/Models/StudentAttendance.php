<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;

class StudentAttendance extends Model
{
    /** @use HasFactory<\Database\Factories\StudentAttendanceFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id', 'class_id', 'attendance_date', 
        'status', 'note', 'recorded_by'
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
