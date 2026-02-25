<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Illuminate\Support\Str;

class ClassSubject extends Model
{
    /** @use HasFactory<\Database\Factories\ClassSubjectFactory> */
    use HasFactory;

    protected $fillable = ['subject_id', 'teacher_id', 'class_id', 'academic_year_id', 'general_info', 'slug'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($classSubject) {
            if (empty($classSubject->slug)) {
                $classSubject->slug = $classSubject->generateSlug();
            }
        });

        static::updating(function ($classSubject) {
            if ($classSubject->isDirty(['subject_id', 'class_id']) && empty($classSubject->getOriginal('slug'))) {
                $classSubject->slug = $classSubject->generateSlug();
            }
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Generate a unique slug for the class subject
     */
    public function generateSlug(): string
    {
        $baseSlug = Str::slug(
            ($this->subject->name ?? 'subject') . '-' . 
            ($this->schoolClass->name ?? 'class')
        );

        $slug = $baseSlug;
        $count = 1;

        while (self::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function sections()
    {
        return $this->hasMany(ClassSubjectSection::class, 'class_subject_id');
    }

    public function classSubjectProgress()
    {
        return $this->hasMany(ClassSubjectProgress::class, 'class_subject_id');
    }
}
