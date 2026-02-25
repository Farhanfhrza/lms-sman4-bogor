<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Models\AcademicYear;

class HomeroomTeacher extends Model
{
    /** @use HasFactory<\Database\Factories\HomeroomTeacherFactory> */
    use HasFactory;

    protected $fillable = ['class_id', 'teacher_id', 'academic_year_id'];

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
