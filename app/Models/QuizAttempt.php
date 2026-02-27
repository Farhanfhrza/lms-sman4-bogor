<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Quiz;
use App\Models\Student;
use App\Models\QuizAnswer;

class QuizAttempt extends Model
{
    /** @use HasFactory<\Database\Factories\QuizAttemptFactory> */
    use HasFactory;

    protected $fillable = [
        'quiz_id', 'student_id', 'started_at', 
        'submitted_at', 'total_score', 'is_submitted', 'uuid'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'is_submitted' => 'boolean',
    ];

    /**
     * Boot the model — auto-generate UUID on creation.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

    /**
     * Use UUID for route model binding instead of ID.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class, 'attempt_id');
    }
}

