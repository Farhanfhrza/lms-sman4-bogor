<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSubjectSection extends Model
{
    /** @use HasFactory<\Database\Factories\ClassSubjectSectionFactory> */
    use HasFactory;

    protected $fillable = ['class_subject_id', 'title', 'description', 'order_number', 'is_published'];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($section) {
            // Hapus isi BAB secara manual via Eloquent agar trigger 'deleted' memakan file-file siswanya
            $section->materials->each->delete();
            $section->assignments->each->delete();
            $section->quizzes->each->delete();
        });
    }

    public function classSubject()
    {
        return $this->belongsTo(ClassSubject::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'section_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'section_id');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'section_id');
    }
}
