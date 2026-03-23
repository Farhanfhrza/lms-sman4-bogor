<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AdminLogController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the activity logs page (Admin only).
     */
    public function index(Request $request): View
    {
        $query = ActivityLog::with(['user', 'course.subject'])->latest();

        // Filter: Search by user name or description
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('login_identifier', 'like', "%{$search}%");
                  });
            });
        }

        // Filter: By course
        if ($courseId = $request->input('course_id')) {
            $query->where('course_id', $courseId);
        }

        // Filter: By action
        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        $perPage = (int) $request->input('per_page', 25);
        $logs = $query->paginate($perPage)->withQueryString();

        $breadcrumbs = [
            ['label' => 'Log Aktivitas'],
        ];

        return view('admin.activity-logs', compact('logs', 'breadcrumbs'));
    }
}
