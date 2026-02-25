<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\ClassSubjectSection;
use App\Models\User;

class Material extends Model
{
    /** @use HasFactory<\Database\Factories\MaterialFactory> */
    use HasFactory;

    protected $fillable = [
        'section_id', 'title', 'slug', 'description', 'content_type', 'content_url', 
        'file_url', 'link_url', 'order_number', 'published_at', 'created_by'
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($material) {
            if (empty($material->slug)) {
                $material->slug = $material->generateSlug();
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
     * Generate a unique slug for the material.
     */
    public function generateSlug(): string
    {
        // Professional Hybrid Format: descriptive-slug-random (e.g. 'materi-biologi-a7x9z')
        // Combines readability (SEO) with guaranteed uniqueness and a clear "id" feel.
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

    public function materialProgress()
    {
        return $this->hasMany(MaterialProgress::class, 'material_id');
    }
}
