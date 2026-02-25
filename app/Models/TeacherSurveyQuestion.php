<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TeacherSurveyPeriod;

class TeacherSurveyQuestion extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherSurveyQuestionFactory> */
    use HasFactory;

    protected $fillable = ['period_id', 'question_text', 'order_number'];

    public function period()
    {
        return $this->belongsTo(TeacherSurveyPeriod::class, 'period_id');
    }
}
