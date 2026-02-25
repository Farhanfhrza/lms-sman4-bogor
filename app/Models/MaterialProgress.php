<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaterialProgress extends Model
{
    /** @use HasFactory<\Database\Factories\MaterialProgressFactory> */
    use HasFactory;

    protected $table = 'material_progresses';

    protected $fillable = [
        'student_id', 'material_id', 'is_completed', 'completed_at'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
