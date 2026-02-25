<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\ClassSubject;

class ClassSubjectProgress extends Model
{
    /** @use HasFactory<\Database\Factories\ClassSubjectProgressFactory> */
    use HasFactory, \App\Traits\CalculatesProgress;

    protected $table = 'class_subject_progresses';

    protected $fillable = [
        'student_id', 'class_subject_id', 'completion_percentage', 'is_completed'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classSubject()
    {
        return $this->belongsTo(ClassSubject::class);
    }
}
