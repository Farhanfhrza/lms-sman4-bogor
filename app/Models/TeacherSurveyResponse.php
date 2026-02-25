<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TeacherSurveyPeriod;
use App\Models\Teacher;
use App\Models\Student;

class TeacherSurveyResponse extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherSurveyResponseFactory> */
    use HasFactory;

    protected $fillable = [
        'period_id', 'teacher_id', 'student_id', 'comment', 'submitted_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function period()
    {
        return $this->belongsTo(TeacherSurveyPeriod::class, 'period_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
