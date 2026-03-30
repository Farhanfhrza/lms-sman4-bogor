<?php

namespace App\Http\Controllers;

use App\Models\AcademicEvent;
use App\Services\AcademicEventService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AcademicEventController extends Controller
{
    use AuthorizesRequests;

    protected AcademicEventService $eventService;

    public function __construct(AcademicEventService $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * Display the academic calendar page.
     */
    public function index(Request $request): View
    {
        $year  = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $events = $this->eventService->getEventsForMonth($year, $month);

        // Normalize events with type field
        $normalizedEvents = $events->map(fn ($e) => [
            'id'          => $e->id,
            'title'       => $e->title,
            'description' => $e->description,
            'event_date'  => $e->event_date->toDateString(),
            'day'         => $e->event_date->day,
            'type'        => 'event',
            'status'      => null,
            'slug'        => null,
            'subject'     => null,
            'due_date'    => null,
            'created_by'  => $e->creator->full_name ?? $e->creator->name ?? '-',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $canCreate = $user->hasRole('admin');

        // Merge student assignments if the user is a student
        $isStudent = $user->hasRole('student');
        if ($isStudent) {
            $assignments = $this->eventService->getStudentAssignmentsForMonth($user, $year, $month);
            $normalizedEvents = $normalizedEvents->concat($assignments);
        }

        // Group all items by day number
        $eventsByDay = $normalizedEvents->groupBy('day');

        return view('academic-calendar.index', compact(
            'year', 'month', 'eventsByDay', 'canCreate', 'isStudent'
        ));
    }

    /**
     * Return events for a specific date (AJAX).
     */
    public function eventsForDate(Request $request): JsonResponse
    {
        $request->validate(['date' => 'required|date']);

        $events = $this->eventService->getEventsForDate($request->date);

        return response()->json([
            'events' => $events->map(fn ($e) => [
                'id'          => $e->id,
                'title'       => $e->title,
                'description' => $e->description,
                'event_date'  => $e->event_date->toDateString(),
                'time'        => $e->event_date->format('H:i'),
                'type'        => 'event',
                'status'      => null,
                'slug'        => null,
                'subject'     => null,
                'due_date'    => null,
                'created_by'  => $e->creator->full_name ?? $e->creator->name ?? '-',
            ]),
        ]);
    }

    /**
     * Return events for a month (AJAX, used when navigating months).
     */
    public function eventsForMonth(Request $request): JsonResponse
    {
        $request->validate([
            'year'  => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $year  = (int) $request->year;
        $month = (int) $request->month;

        $events = $this->eventService->getEventsForMonth($year, $month);

        $normalizedEvents = $events->map(fn ($e) => [
            'id'          => $e->id,
            'title'       => $e->title,
            'description' => $e->description,
            'event_date'  => $e->event_date->toDateString(),
            'day'         => $e->event_date->day,
            'type'        => 'event',
            'status'      => null,
            'slug'        => null,
            'subject'     => null,
            'due_date'    => null,
            'created_by'  => $e->creator->full_name ?? $e->creator->name ?? '-',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Merge student assignments if the user is a student
        if ($user && $user->hasRole('student')) {
            $assignments = $this->eventService->getStudentAssignmentsForMonth($user, $year, $month);
            $normalizedEvents = $normalizedEvents->concat($assignments);
        }

        return response()->json([
            'events' => $normalizedEvents->values(),
        ]);
    }

    /**
     * Store a new academic event.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', AcademicEvent::class);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'event_date'  => 'required|date',
            'target_type' => 'nullable|string|in:school,class',
            'target_id'   => 'nullable|integer',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $event = $this->eventService->createEvent($validated, $user);

        ActivityLogger::log(null, 'created', $event, 'Menambahkan event kalender: ' . $event->title);

        return redirect()->route('academic-calendar.index', [
            'year'  => \Carbon\Carbon::parse($validated['event_date'])->year,
            'month' => \Carbon\Carbon::parse($validated['event_date'])->month,
        ])->with('success', 'Event berhasil ditambahkan.');
    }

    /**
     * Update an existing academic event.
     */
    public function update(Request $request, AcademicEvent $event): RedirectResponse
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'event_date'  => 'required|date',
            'target_type' => 'nullable|string|in:school,class',
            'target_id'   => 'nullable|integer',
        ]);

        $this->eventService->updateEvent($event, $validated);

        ActivityLogger::log(null, 'updated', $event, 'Memperbarui event kalender: ' . $event->title);

        return redirect()->route('academic-calendar.index', [
            'year'  => \Carbon\Carbon::parse($validated['event_date'])->year,
            'month' => \Carbon\Carbon::parse($validated['event_date'])->month,
        ])->with('success', 'Event berhasil diperbarui.');
    }

    /**
     * Delete an academic event.
     */
    public function destroy(AcademicEvent $event): RedirectResponse
    {
        $this->authorize('delete', $event);

        $year  = $event->event_date->year;
        $month = $event->event_date->month;

        ActivityLogger::log(null, 'deleted', $event, 'Menghapus event kalender: ' . $event->title);

        $this->eventService->deleteEvent($event);

        return redirect()->route('academic-calendar.index', [
            'year'  => $year,
            'month' => $month,
        ])->with('success', 'Event berhasil dihapus.');
    }
}
