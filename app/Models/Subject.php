<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    /** @use HasFactory<\Database\Factories\SubjectFactory> */
    use HasFactory;

    protected $fillable = ['name', 'code', 'cover_image_path'];

    /**
     * Get the URL to the subject's cover image.
     *
     * @return string
     */
    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image_path) {
            return asset('storage/' . $this->cover_image_path);
        }
        return 'https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=1200&h=400&fit=crop';
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subjects');
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }
}
