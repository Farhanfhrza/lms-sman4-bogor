<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ClassSubjectSection;
use App\Models\User;

class Quiz extends Model
{
    /** @use HasFactory<\Database\Factories\QuizFactory> */
    use HasFactory;

    protected $fillable = [
        'section_id', 'title', 'description', 'time_limit', 'max_attempt',
        'is_published', 'start_at', 'end_at', 'created_by'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function section()
    {
        return $this->belongsTo(ClassSubjectSection::class, 'section_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class, 'quiz_id');
    }
}
