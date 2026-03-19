<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Assignment;
use App\Models\Student;

class AssignmentSubmission extends Model
{
    /** @use HasFactory<\Database\Factories\AssignmentSubmissionFactory> */
    use HasFactory;

    protected $fillable = [
        'assignment_id', 'student_id', 'submission_url', 
        'submitted_at', 'score', 'feedback', 'graded_at',
        'file_url', 'link_url', 'submission_text', 'status'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($submission) {
            if ($submission->isDirty('file_url')) {
                $oldFile = $submission->getOriginal('file_url');
                if ($oldFile && \Illuminate\Support\Facades\Storage::disk('public')->exists($oldFile)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldFile);
                }
            }
        });

        static::deleted(function ($submission) {
            if ($submission->file_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($submission->file_url)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($submission->file_url);
            }
        });
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
