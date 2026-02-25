<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Report extends Model
{
    /** @use HasFactory<\Database\Factories\ReportFactory> */
    use HasFactory;

    protected $fillable = ['reporter_id', 'title', 'description', 'status'];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}
