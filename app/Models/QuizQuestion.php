<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    /** @use HasFactory<\Database\Factories\QuizQuestionFactory> */
    use HasFactory;

    protected $fillable = ['quiz_id', 'question_text', 'image_url', 'question_type', 'score', 'order_number'];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($question) {
            if ($question->isDirty('image_url')) {
                $oldImage = $question->getOriginal('image_url');
                if ($oldImage && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldImage)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldImage);
                }
            }
        });

        static::deleted(function ($question) {
            if ($question->image_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($question->image_url)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($question->image_url);
            }
        });
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function options()
    {
        return $this->hasMany(QuizOption::class, 'question_id');
    }
}
