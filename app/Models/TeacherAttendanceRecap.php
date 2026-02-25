<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // Added this import

class TeacherAttendanceRecap extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherAttendanceRecapFactory> */
    use HasFactory;

    protected $fillable = [
        'teacher_id', 'academic_year_id', 'total_hadir', 
        'total_izin', 'total_sakit', 'total_alpha', 'last_calculated_at'
    ];

    protected $casts = [
        'last_calculated_at' => 'datetime',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
