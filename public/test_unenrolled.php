<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$academicYear = App\Models\AcademicYear::orderBy('id', 'desc')->first();
echo "Latest Academic Year: " . $academicYear->name . " (ID: " . $academicYear->id . ")\n";

$studentsCount = App\Models\Student::count();
echo "Total Students: " . $studentsCount . "\n";

$unenrolledStudents = App\Models\Student::with('user')
    ->whereDoesntHave('studentClasses', function($query) use ($academicYear) {
        $query->where('academic_year_id', $academicYear->id);
    })
    ->get();

echo "Unenrolled Students Count: " . $unenrolledStudents->count() . "\n";
