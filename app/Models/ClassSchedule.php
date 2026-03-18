<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSchedule extends Model
{
    protected $fillable = [
        'class_subject_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room',
    ];

    public function classSubject()
    {
        return $this->belongsTo(ClassSubject::class);
    }
}
