<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolClass extends Model
{
    /** @use HasFactory<\Database\Factories\SchoolClassFactory> */
    use HasFactory;

    protected $table = 'classes';
    protected $fillable = ['name', 'grade', 'major', 'academic_year_id', 'teacher_id'];

    public function homeroomTeacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class, 'class_id');
    }

    public function studentClasses()
    {
        return $this->hasMany(StudentClass::class, 'class_id');
    }
}
