<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $fillable = ['user_id', 'nisn', 'enrollment_year'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function studentClasses()
    {
        return $this->hasMany(StudentClass::class);
    }
}
