<?php

namespace App\Services;

use App\Models\AcademicEvent;
use App\Models\AcademicYear;
use App\Models\Assignment;
use App\Models\StudentClass;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AcademicEventService
{
    /**
     * Get events for a specific month.
     *
     * @param int $year
     * @param int $month
     * @return Collection
     */
    public function getEventsForMonth(int $year, int $month): Collection
    {
        try {
            $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            return AcademicEvent::whereBetween('event_date', [$startOfMonth, $endOfMonth])
                ->with('creator')
                ->orderBy('event_date', 'asc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error fetching monthly events: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get events for a specific date.
     *
     * @param string $date  Y-m-d format
     * @return Collection
     */
    public function getEventsForDate(string $date): Collection
    {
        try {
            return AcademicEvent::whereDate('event_date', $date)
                ->with('creator')
                ->orderBy('event_date', 'asc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error fetching daily events: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get assignments for a student in a specific month.
     * Uses constrained eager loading to avoid N+1 queries.
     *
     * Performance: Only 2-3 SQL queries total regardless of assignment count.
     *   1. Fetch student's class IDs
     *   2. Fetch assignments with constrained submissions eager load
     *
     * @param User $user  Must have ->student relation
     * @param int $year
     * @param int $month
     * @return Collection  Each item is an array with assignment data + status
     */
    public function getStudentAssignmentsForMonth(User $user, int $year, int $month): Collection
    {
        try {
            if (!$user->student) {
                return collect();
            }

            $studentId = $user->student->id;
            $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            // Step 1: Get the student's active class IDs (single query)
            $activeAcademicYearId = AcademicYear::where('is_active', true)->value('id');
            $classIds = StudentClass::where('student_id', $studentId)
                ->when($activeAcademicYearId, fn ($q) => $q->where('academic_year_id', $activeAcademicYearId))
                ->pluck('class_id');

            if ($classIds->isEmpty()) {
                return collect();
            }

            // Step 2: Fetch all assignments for those classes in the month
            // with constrained eager loading for only THIS student's submissions
            $assignments = Assignment::whereHas('section.classSubject', function ($q) use ($classIds) {
                    $q->whereIn('class_id', $classIds);
                })
                ->whereBetween('due_date', [$startOfMonth, $endOfMonth])
                ->with([
                    'section.classSubject.subject',
                    // Constrained eager load: only load this student's submission (0 or 1 row)
                    'submissions' => function ($q) use ($studentId) {
                        $q->where('student_id', $studentId);
                    },
                ])
                ->orderBy('due_date', 'asc')
                ->get();

            // Step 3: Map to a normalized structure with status computed in-memory
            $now = Carbon::now();

            return $assignments->map(function ($assignment) use ($now) {
                $submission = $assignment->submissions->first(); // already eager loaded

                if ($submission) {
                    $status = 'submitted';
                } elseif ($assignment->due_date->lt($now)) {
                    $status = 'missing'; // late / not submitted
                } else {
                    $status = 'upcoming';
                }

                return [
                    'id'          => $assignment->id,
                    'title'       => $assignment->title,
                    'slug'        => $assignment->slug,
                    'description' => $assignment->description,
                    'event_date'  => $assignment->due_date->toDateString(),
                    'day'         => $assignment->due_date->day,
                    'due_date'    => $assignment->due_date->toDateTimeString(),
                    'subject'     => $assignment->section->classSubject->subject->name ?? '-',
                    'type'        => 'assignment',
                    'status'      => $status,
                ];
            });

        } catch (\Exception $e) {
            Log::error('Error fetching student assignments for calendar: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'trace'   => $e->getTraceAsString(),
            ]);
            return collect();
        }
    }

    /**
     * Create a new academic event.
     *
     * @param array $data
     * @param User $user
     * @return AcademicEvent
     */
    public function createEvent(array $data, User $user): AcademicEvent
    {
        return AcademicEvent::create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'event_date'  => $data['event_date'],
            'target_type' => $data['target_type'] ?? 'school',
            'target_id'   => $data['target_id'] ?? null,
            'created_by'  => $user->id,
        ]);
    }

    /**
     * Update an existing academic event.
     *
     * @param AcademicEvent $event
     * @param array $data
     * @return AcademicEvent
     */
    public function updateEvent(AcademicEvent $event, array $data): AcademicEvent
    {
        $event->update([
            'title'       => $data['title'],
            'description' => $data['description'] ?? $event->description,
            'event_date'  => $data['event_date'],
            'target_type' => $data['target_type'] ?? $event->target_type,
            'target_id'   => $data['target_id'] ?? $event->target_id,
        ]);

        return $event->fresh();
    }

    /**
     * Delete an academic event.
     *
     * @param AcademicEvent $event
     * @return bool
     */
    public function deleteEvent(AcademicEvent $event): bool
    {
        return $event->delete();
    }
}
