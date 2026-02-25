<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class AcademicEvent extends Model
{
    /** @use HasFactory<\Database\Factories\AcademicEventFactory> */
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'event_date', 
        'target_type', 'target_id', 'created_by'
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
