<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\ClassSchedule;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AdminScheduleController extends Controller
{
    /**
     * Display the scheduling matrix page.
     */
    public function index(Request $request): View
    {
        $academicYear = AcademicYear::where('is_active', true)->first();

        // Build a grouped structure: [grade => [major => [rombel => SchoolClass]]]
        $rawClasses = collect();
        if ($academicYear) {
            $rawClasses = SchoolClass::where('academic_year_id', $academicYear->id)
                ->orderBy('grade')
                ->orderBy('major')
                ->orderBy('name')
                ->get();
        }

        $groupedClasses = [];
        foreach ($rawClasses as $class) {
            $groupedClasses[$class->grade][$class->major][] = $class;
        }

        // Resolve selected class from 3 params
        $selectedGrade  = $request->input('grade');
        $selectedMajor  = $request->input('major');
        $selectedRombel = $request->input('rombel_id');

        // Derive available majors for selected grade
        $availableMajors = $selectedGrade ? array_keys($groupedClasses[$selectedGrade] ?? []) : [];
        // Derive available rombels for selected grade+major
        $availableRombels = ($selectedGrade && $selectedMajor)
            ? ($groupedClasses[$selectedGrade][$selectedMajor] ?? [])
            : [];

        $selectedClass = $selectedRombel ? SchoolClass::find($selectedRombel) : null;

        $existingMappings = collect();
        if ($selectedClass && $academicYear) {
            $existingMappings = ClassSubject::with(['teacher.user', 'teacher.subjects', 'schedules', 'subject'])
                ->where('class_id', $selectedClass->id)
                ->where('academic_year_id', $academicYear->id)
                ->get()
                ->keyBy('subject_id');
        }

        $breadcrumbs = [
            ['label' => 'Penjadwalan Kelas'],
        ];

        return view('admin.schedules.index', compact(
            'groupedClasses', 'selectedGrade', 'selectedMajor', 'selectedRombel',
            'availableMajors', 'availableRombels', 'selectedClass',
            'academicYear', 'existingMappings', 'breadcrumbs'
        ));
    }

    /**
     * API: Get teachers who can teach a given subject.
     */
    public function teachersForSubject(Request $request): JsonResponse
    {
        $subjectId = $request->input('subject_id');
        $subject = Subject::find($subjectId);

        $teachers = collect();
        if ($subject) {
            $teachers = $subject->teachers()->with('user')->get()->map(fn($t) => [
                'id'   => $t->id,
                'name' => $t->user->full_name ?? ('Guru #' . $t->id),
            ]);
        }

        return response()->json($teachers);
    }

    /**
     * API: Search subjects by name.
     */
    public function searchSubjects(Request $request): JsonResponse
    {
        $query  = $request->input('q', '');
        $except = $request->input('except', []);

        $subjects = Subject::where('name', 'ilike', "%{$query}%")
            ->whereNotIn('id', $except)
            ->orderBy('name')
            ->limit(15)
            ->get(['id', 'name', 'code']);

        return response()->json($subjects);
    }

    /**
     * Store or update the entire mapping for a class.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'class_id'                                    => 'required|exists:classes,id',
            'mappings'                                    => 'nullable|array',
            'mappings.*.teacher_id'                       => 'required|exists:teachers,id',
            'mappings.*.schedules'                        => 'nullable|array',
            'mappings.*.schedules.*.day_of_week'          => 'required|string|max:15',
            'mappings.*.schedules.*.start_time'           => 'required|date_format:H:i',
            'mappings.*.schedules.*.end_time'             => ['required', 'date_format:H:i'],
            'mappings.*.schedules.*.room'                 => 'nullable|string|max:50',
        ], [
            'class_id.required'                           => 'Kelas harus dipilih.',
            'mappings.*.teacher_id.required'              => 'Guru pengajar harus dipilih untuk setiap mata pelajaran yang diaktifkan.',
            'mappings.*.schedules.*.day_of_week.required' => 'Hari pertemuan harus dipilih.',
            'mappings.*.schedules.*.start_time.required'  => 'Jam mulai harus diisi.',
            'mappings.*.schedules.*.end_time.required'    => 'Jam selesai harus diisi.',
            'mappings.*.schedules.*.start_time.date_format' => 'Format jam mulai tidak valid (HH:MM).',
            'mappings.*.schedules.*.end_time.date_format' => 'Format jam selesai tidak valid (HH:MM).',
        ]);

        $academicYear = AcademicYear::where('is_active', true)->firstOrFail();
        $classId = $request->input('class_id');
        $mappings = $request->input('mappings', []);

        // Validate end_time > start_time
        foreach ($mappings as $subjectId => $data) {
            foreach (($data['schedules'] ?? []) as $idx => $sched) {
                if (isset($sched['start_time'], $sched['end_time']) && $sched['end_time'] <= $sched['start_time']) {
                    return back()->withInput()->withErrors([
                        "jadwal_{$subjectId}_{$idx}" => "Jam selesai harus lebih besar dari jam mulai untuk mata pelajaran ID {$subjectId}."
                    ])->with('error', 'Terdapat jam pelajaran yang tidak valid. Jam selesai harus lebih besar dari jam mulai.');
                }
            }
        }

        // Collision detection
        $collisionError = $this->detectCollisions($classId, $mappings, $academicYear->id);
        if ($collisionError) {
            return back()->withInput()->with('error', $collisionError);
        }

        DB::transaction(function () use ($classId, $academicYear, $mappings) {
            $existingIds = ClassSubject::where('class_id', $classId)
                ->where('academic_year_id', $academicYear->id)
                ->pluck('id', 'subject_id');

            $submittedSubjectIds = array_keys($mappings);
            $toDelete = $existingIds->except($submittedSubjectIds);

            if ($toDelete->isNotEmpty()) {
                ClassSubject::whereIn('id', $toDelete->values())->delete();
            }

            foreach ($mappings as $subjectId => $data) {
                $classSubject = ClassSubject::updateOrCreate(
                    [
                        'class_id'         => $classId,
                        'subject_id'       => $subjectId,
                        'academic_year_id' => $academicYear->id,
                    ],
                    [
                        'teacher_id' => $data['teacher_id'],
                    ]
                );

                $classSubject->schedules()->delete();

                foreach (($data['schedules'] ?? []) as $schedule) {
                    $classSubject->schedules()->create([
                        'day_of_week' => $schedule['day_of_week'],
                        'start_time'  => $schedule['start_time'],
                        'end_time'    => $schedule['end_time'],
                        'room'        => $schedule['room'] ?? null,
                    ]);
                }
            }
        });

        $class = SchoolClass::find($classId);
        ActivityLogger::log(null, 'updated', $class, 'Memperbarui jadwal kelas: ' . ($class->name ?? ''));

        return redirect()
            ->route('admin.schedules.index', [
                'grade'    => $request->input('grade'),
                'major'    => $request->input('major'),
                'rombel_id' => $classId,
            ])
            ->with('success', 'Jadwal kelas berhasil disimpan!');
    }

    /**
     * Detect schedule collisions for teachers and classes.
     * Returns null if no collision, or an error message string.
     */
    private function detectCollisions(int $classId, array $mappings, int $academicYearId): ?string
    {
        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        // Build list of slots from this submission
        $newSlots = []; // [teacherId => [[day, start, end, subjectId]]]
        $classSlots = []; // [[day, start, end, subjectId]] for the class itself

        foreach ($mappings as $subjectId => $data) {
            $teacherId = $data['teacher_id'] ?? null;
            foreach (($data['schedules'] ?? []) as $sched) {
                if (!isset($sched['day_of_week'], $sched['start_time'], $sched['end_time'])) continue;

                $slot = [
                    'day'   => $sched['day_of_week'],
                    'start' => $sched['start_time'],
                    'end'   => $sched['end_time'],
                    'subjectId' => $subjectId,
                ];

                if ($teacherId) {
                    $newSlots[$teacherId][] = $slot;
                }
                $classSlots[] = $slot;
            }
        }

        // 1. Check intra-submission class collision (2 subjects in same class at same time)
        $collision = $this->findOverlap($classSlots);
        if ($collision) {
            return "Jadwal kelas bertabrakan! Terdapat dua mata pelajaran yang dijadwalkan pada hari {$collision['day']} pukul {$collision['start']} - {$collision['end']} untuk kelas yang sama.";
        }

        // 2. Check intra-submission teacher collision (same teacher, 2 subjects at same time)
        foreach ($newSlots as $teacherId => $slots) {
            $collision = $this->findOverlap($slots);
            if ($collision) {
                $teacher = Teacher::with('user')->find($teacherId);
                $name = $teacher?->user?->full_name ?? 'Guru';
                return "Jadwal guru bertabrakan! {$name} sudah dijadwalkan mengajar di hari {$collision['day']} pukul {$collision['start']} - {$collision['end']} lebih dari satu kelas/mapel secara bersamaan.";
            }
        }

        // 3. Check teacher collision with OTHER classes in the same academic year
        foreach ($newSlots as $teacherId => $slots) {
            // Get all other schedules for this teacher (different class)
            $otherSchedules = ClassSchedule::whereHas('classSubject', function ($q) use ($teacherId, $classId, $academicYearId) {
                $q->where('teacher_id', $teacherId)
                  ->where('academic_year_id', $academicYearId)
                  ->where('class_id', '!=', $classId);
            })->get(['day_of_week', 'start_time', 'end_time']);

            foreach ($slots as $newSlot) {
                foreach ($otherSchedules as $existing) {
                    $existStart = substr($existing->start_time, 0, 5);
                    $existEnd   = substr($existing->end_time, 0, 5);
                    if ($existing->day_of_week === $newSlot['day'] &&
                        $newSlot['start'] < $existEnd &&
                        $newSlot['end'] > $existStart) {
                        $teacher = Teacher::with('user')->find($teacherId);
                        $name = $teacher?->user?->full_name ?? 'Guru';
                        return "Jadwal guru bertabrakan! {$name} sudah terjadwal mengajar di kelas lain pada hari {$newSlot['day']} pukul {$existStart} - {$existEnd}. Silakan pilih jam atau hari yang berbeda.";
                    }
                }
            }
        }

        return null;
    }

    /**
     * Check if any two slots in the array overlap. Returns the overlapping slot info or null.
     */
    private function findOverlap(array $slots): ?array
    {
        $n = count($slots);
        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $a = $slots[$i];
                $b = $slots[$j];
                if ($a['day'] === $b['day'] &&
                    $a['start'] < $b['end'] &&
                    $a['end'] > $b['start']) {
                    return $a;
                }
            }
        }
        return null;
    }
}
