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
            $existingIds = StudentClass::where('class_id', $schoolClass->id)
                ->where('academic_year_id', $schoolClass->academic_year_id)
                ->pluck('student_id')
                ->toArray();

            $toRemove = array_diff($existingIds, $studentIds);
            $toAdd = array_diff($studentIds, $existingIds);

            if (!empty($toRemove)) {
                StudentClass::where('class_id', $schoolClass->id)
                    ->where('academic_year_id', $schoolClass->academic_year_id)
                    ->whereIn('student_id', $toRemove)
                    ->delete();
            }

            if (!empty($toAdd)) {
                $inserts = [];
                foreach ($toAdd as $id) {
                    $inserts[] = [
                        'student_id' => $id,
                        'class_id' => $schoolClass->id,
                        'academic_year_id' => $schoolClass->academic_year_id,
                        'attendance_number' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                StudentClass::insert($inserts);
            }
        });

        ActivityLogger::log(null, 'updated', $schoolClass, "Memperbarui daftar siswa di kelas {$schoolClass->name}");

        return redirect()->route('admin.classes.show', $schoolClass)->with('success', 'Daftar siswa berhasil diperbarui.');
    }

    public function generateAttendanceNumbers(SchoolClass $schoolClass): RedirectResponse
    {
        $activeYearId = $schoolClass->academic_year_id;
        
        // Get all students enrolled in this class for this academic year, sorted by full_name
        $studentClasses = StudentClass::where('class_id', $schoolClass->id)
            ->where('academic_year_id', $activeYearId)
            ->join('students', 'student_classes.student_id', '=', 'students.id')
            ->join('users', 'students.user_id', '=', 'users.id')
            ->orderBy('users.full_name', 'asc')
            ->select('student_classes.*')
            ->get();

        $number = 1;
        foreach ($studentClasses as $sc) {
            $sc->update(['attendance_number' => $number]);
            $number++;
        }

        ActivityLogger::log(null, 'updated', $schoolClass, "Generate otomatis nomor absen kelas {$schoolClass->name}");

        return redirect()->route('admin.classes.show', $schoolClass)->with('success', 'Nomor absen berhasil digenerate berdasarkan abjad.');
    }

    public function updateAttendanceNumbers(Request $request, SchoolClass $schoolClass): RedirectResponse
    {
        $validated = $request->validate([
            'attendance_numbers' => 'required|array',
            'attendance_numbers.*' => 'nullable|integer|min:1',
        ]);

        $activeYearId = $schoolClass->academic_year_id;

        // Cek duplikasi nomor absen (abaikan yang null/kosong)
        $numbers = array_filter($validated['attendance_numbers'], function($val) {
            return !is_null($val) && $val !== '';
        });

        if (count($numbers) !== count(array_unique($numbers))) {
            return back()->with('error', 'Gagal menyimpan: Terdapat nomor absen yang ganda (duplikat). Setiap siswa harus memiliki nomor absen unik.');
        }

        DB::transaction(function () use ($schoolClass, $activeYearId, $validated) {
            foreach ($validated['attendance_numbers'] as $studentId => $number) {
                StudentClass::where('class_id', $schoolClass->id)
                    ->where('academic_year_id', $activeYearId)
                    ->where('student_id', $studentId)
                    ->update(['attendance_number' => $number]);
            }
        });

        ActivityLogger::log(null, 'updated', $schoolClass, "Update manual nomor absen kelas {$schoolClass->name}");

        return redirect()->route('admin.classes.show', $schoolClass)->with('success', 'Nomor absen berhasil diperbarui.');
    }
}
