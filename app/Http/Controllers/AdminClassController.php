<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\StudentClass;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminClassController extends Controller
{
    public function index(Request $request): View
    {
        $academicYear = AcademicYear::where('is_active', true)->first();

        // If no active academic year, return right away
        if (!$academicYear) {
            $classes = collect();
        } else {
            // Search criteria
            $search = $request->input('search');
            $gradeFilter = $request->input('grade');
            $majorFilter = $request->input('major');

            $query = SchoolClass::where('academic_year_id', $academicYear->id);

            if ($search) {
                $query->where('name', 'ilike', '%' . $search . '%');
            }
            if ($gradeFilter) {
                $query->where('grade', $gradeFilter);
            }
            if ($majorFilter) {
                $query->where('major', $majorFilter);
            }

            $classes = $query->with('homeroomTeacher.user')->orderBy('grade')->orderBy('major')->orderBy('name')->paginate(15)->appends($request->all());
        }

        // Distinct majors for filtering
        $majors = SchoolClass::select('major')->distinct()->orderBy('major')->pluck('major');
        
        // Fetch teachers for the Walikelas dropdown
        $teachers = Teacher::with('user')->get()->sortBy(fn($t) => $t->user->full_name ?? '');

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Data Kelas'],
        ];

        return view('admin.classes.index', compact('classes', 'academicYear', 'majors', 'teachers', 'breadcrumbs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $academicYear = AcademicYear::where('is_active', true)->firstOrFail();

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:50',
                Rule::unique('classes')->where(function ($query) use ($academicYear) {
                    return $query->where('academic_year_id', $academicYear->id);
                })
            ],
            'grade' => 'required|integer|in:10,11,12',
            'major' => 'required|string|max:50',
        ], [
            'name.required' => 'Nama Rombel wajib diisi.',
            'name.unique' => 'Nama Rombel sudah ada pada tahun ajaran ini.',
            'grade.required' => 'Tingkat Kelas wajib dipilih.',
            'major.required' => 'Jurusan wajib diisi.',
            'teacher_id.exists' => 'Guru tidak ditemukan di sistem.',
        ]);

        $validated['academic_year_id'] = $academicYear->id;
        $validated['teacher_id'] = $request->input('teacher_id') ?: null;

        $schoolClass = SchoolClass::create($validated);

        ActivityLogger::log(null, 'created', $schoolClass, "Menambahkan kelas baru: {$schoolClass->name}");

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, SchoolClass $schoolClass): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required', 'string', 'max:50',
                Rule::unique('classes')->where(function ($query) use ($schoolClass) {
                    return $query->where('academic_year_id', $schoolClass->academic_year_id);
                })->ignore($schoolClass->id)
            ],
            'grade' => 'required|integer|in:10,11,12',
            'major' => 'required|string|max:50',
            'teacher_id' => 'nullable|exists:teachers,id',
        ], [
            'name.required' => 'Nama Rombel wajib diisi.',
            'name.unique' => 'Nama Rombel sudah ada pada tahun ajaran ini.',
            'teacher_id.exists' => 'Guru tidak ditemukan di sistem.',
        ]);

        $validated['teacher_id'] = $request->input('teacher_id') ?: null;

        $oldName = $schoolClass->name;
        $schoolClass->update($validated);

        ActivityLogger::log(null, 'updated', $schoolClass, "Memperbarui kelas {$oldName} menjadi {$schoolClass->name}");

        return redirect()->route('admin.classes.index')->with('success', 'Data Kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $schoolClass): RedirectResponse
    {
        if ($schoolClass->studentClasses()->exists() || $schoolClass->classSubjects()->exists()) {
            return redirect()->route('admin.classes.index')->with('error', 'Tidak dapat menghapus Kelas ini karena sudah memiliki siswa atau daftar mata pelajaran.');
        }

        $name = $schoolClass->name;
        $schoolClass->delete();

        ActivityLogger::log(null, 'deleted', null, "Menghapus kelas: {$name}");

        return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil dihapus.');
    }

    public function show(SchoolClass $schoolClass): View
    {
        $schoolClass->load([
            'homeroomTeacher.user',
            'studentClasses.student.user',
            'classSubjects.subject',
            'classSubjects.teacher.user',
            'classSubjects.schedules',
            'academicYear'
        ]);

        $activeYearId = $schoolClass->academic_year_id;
        
        $unenrolledStudents = Student::with('user')
            ->whereDoesntHave('studentClasses', function($query) use ($activeYearId) {
                $query->where('academic_year_id', $activeYearId);
            })
            ->get()
            ->sortBy(fn($s) => $s->user->full_name ?? '');

        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('dashboard')],
            ['label' => 'Data Kelas', 'url' => route('admin.classes.index')],
            ['label' => 'Detail ' . $schoolClass->name],
        ];

        return view('admin.classes.show', compact('schoolClass', 'unenrolledStudents', 'breadcrumbs'));
    }

    public function enrollStudents(Request $request, SchoolClass $schoolClass): RedirectResponse
    {
        $validated = $request->validate([
            'enrolled_students' => 'array',
            'enrolled_students.*' => 'exists:students,id',
        ]);

        $studentIds = $validated['enrolled_students'] ?? [];

        DB::transaction(function () use ($schoolClass, $studentIds) {
            // Remove existing students from THIS class
            StudentClass::where('class_id', $schoolClass->id)
                ->where('academic_year_id', $schoolClass->academic_year_id)
                ->delete();

            // Insert new students
            $inserts = [];
            foreach ($studentIds as $id) {
                $inserts[] = [
                    'student_id' => $id,
                    'class_id' => $schoolClass->id,
                    'academic_year_id' => $schoolClass->academic_year_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($inserts)) {
                StudentClass::insert($inserts);
            }
        });

        ActivityLogger::log(null, 'updated', $schoolClass, "Memperbarui daftar siswa di kelas {$schoolClass->name}");

        return redirect()->route('admin.classes.show', $schoolClass)->with('success', 'Daftar siswa berhasil diperbarui.');
    }
}
