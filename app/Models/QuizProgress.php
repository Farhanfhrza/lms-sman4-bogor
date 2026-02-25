<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\Quiz;

class QuizProgress extends Model
{
    /** @use HasFactory<\Database\Factories\QuizProgressFactory> */
    use HasFactory;

    protected $table = 'quiz_progresses';

    protected $fillable = [
        'student_id', 'quiz_id', 'is_completed', 'completed_at', 'score'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}
