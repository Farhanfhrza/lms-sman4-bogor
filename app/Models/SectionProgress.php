<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\ClassSubjectSection;

class SectionProgress extends Model
{
    /** @use HasFactory<\Database\Factories\SectionProgressFactory> */
    use HasFactory, \App\Traits\CalculatesProgress;

    protected $table = 'section_progresses';

    protected $fillable = [
        'student_id', 'section_id', 'completion_percentage', 'is_completed'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function section()
    {
        return $this->belongsTo(ClassSubjectSection::class, 'section_id');
    }
}
