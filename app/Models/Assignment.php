<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\ClassSubjectSection;
use App\Models\User;

class Assignment extends Model
{
    /** @use HasFactory<\Database\Factories\AssignmentFactory> */
    use HasFactory;

    protected $fillable = [
        'section_id', 'title', 'slug', 'description', 'file_url', 'due_date', 
        'max_score', 'allow_late_submission', 'order_number', 'created_by'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'allow_late_submission' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($assignment) {
            if (empty($assignment->slug)) {
                $assignment->slug = $assignment->generateSlug();
            }
        });

        static::updating(function ($assignment) {
            if ($assignment->isDirty('file_url')) {
                $oldFile = $assignment->getOriginal('file_url');
                if ($oldFile && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldFile)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldFile);
                }
            }
        });

        static::deleting(function ($assignment) {
            // Hapus setiap submisi secara manual via Eloquent agar trigger 'deleted' memakan file-file siswanya
            $assignment->submissions->each(function($submission) {
                $submission->delete();
            });
        });

        static::deleted(function ($assignment) {
            if ($assignment->file_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($assignment->file_url)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($assignment->file_url);
            }
        });
    }

    /**
     * Use slug for route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Generate a unique slug for the assignment.
     */
    public function generateSlug(): string
    {
        // Professional Hybrid Format: descriptive-slug-random
        return Str::slug($this->title) . '-' . strtolower(Str::random(5));
    }

    public function section()
    {
        return $this->belongsTo(ClassSubjectSection::class, 'section_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'assignment_id');
    }
}
