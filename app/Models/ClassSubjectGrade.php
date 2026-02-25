<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSubjectGrade extends Model
{
    /** @use HasFactory<\Database\Factories\ClassSubjectGradeFactory> */
    use HasFactory;

    protected $table = 'class_subject_grades';

    protected $fillable = [
        'student_id', 'class_subject_id', 'final_score', 
        'grade_letter', 'note', 'is_finalized', 'finalized_at'
    ];

    protected $casts = [
        'is_finalized' => 'boolean',
        'finalized_at' => 'datetime',
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
