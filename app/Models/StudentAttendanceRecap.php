<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\AcademicYear;

class StudentAttendanceRecap extends Model
{
    /** @use HasFactory<\Database\Factories\StudentAttendanceRecapFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id', 'academic_year_id', 'total_hadir', 
        'total_izin', 'total_sakit', 'total_alpha', 'last_calculated_at'
    ];

    protected $casts = [
        'last_calculated_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
