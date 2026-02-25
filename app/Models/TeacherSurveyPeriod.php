<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSurveyPeriod extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherSurveyPeriodFactory> */
    use HasFactory;

    protected $fillable = [
        'academic_year_id', 'semester', 'title', 
        'start_date', 'end_date', 'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
