<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log an activity directly to the database.
     *
     * @param int|null   $courseId    ClassSubject ID (null for global events)
     * @param string     $action     Action verb: 'created', 'updated', 'deleted', 'submitted', 'graded'
     * @param Model|null $target     The Eloquent model that was acted upon (polymorphic)
     * @param string     $description Human-readable description (max 255 chars)
     */
    public static function log(
        ?int $courseId,
        string $action,
        ?Model $target,
        string $description
    ): void {
        try {
            ActivityLog::create([
                'user_id'     => Auth::id(),
                'course_id'   => $courseId,
                'action'      => $action,
                'description' => mb_substr($description, 0, 255),
                'target_type' => $target ? get_class($target) : null,
                'target_id'   => $target?->getKey(),
                'ip_address'  => Request::ip(),
            ]);
        } catch (\Throwable $e) {
            // Silently fail – logging should never crash the main request
            report($e);
        }
    }
}

