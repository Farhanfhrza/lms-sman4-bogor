<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TeacherSurveyResponse;
use App\Models\TeacherSurveyQuestion;

class TeacherSurveyResponseDetail extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherSurveyResponseDetailFactory> */
    use HasFactory;

    protected $fillable = ['response_id', 'question_id', 'score'];

    public function response()
    {
        return $this->belongsTo(TeacherSurveyResponse::class, 'response_id');
    }

    public function question()
    {
        return $this->belongsTo(TeacherSurveyQuestion::class, 'question_id');
    }
}
