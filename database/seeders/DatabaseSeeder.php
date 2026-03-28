<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Run Roles & Permissions Seeder
        $this->call(RoleAndPermissionSeeder::class);

        // 2. Academic Year
        $academicYear = \App\Models\AcademicYear::factory()->create([
            'name' => '2024/2025',
            'is_active' => true,
        ]);

        // 3. Admin User (Already seeded in RoleAndPermissionSeeder, but let's check/update or skip)
        // Since RoleAndPermissionSeeder creates specific admins, we might duplicate if we run this.
        // But RoleAndPermissionSeeder uses firstOrCreate/checks. 
        // Let's assume we want to create EXTRA users here or keep consistent.
        
        // Actually, RoleAndPermissionSeeder creates 'admin@sman4bogor.sch.id'.
        // Here we create 'admin@sman4bogor.sch.id'. 
        // We should skip creating if it exists, or just rely on Seeder.
        
        // Let's modify this to only create what's NOT in the other seeder, OR just rely on the other seeder for base users.
        // We will keep the generation of mass users (Teachers/Students).

        // 4. Teachers (5 Users)
        for ($i = 0; $i < 5; $i++) {
            $nip = fake()->unique()->numerify('19########');
            // Check if user exists to avoid collision w/ seeder
            $user = \App\Models\User::factory()->create([
                'login_identifier' => $nip,
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'is_active' => true,
                'gender' => fake()->randomElement(['L', 'P']),
            ]);
            $user->assignRole('teacher');
            \App\Models\Teacher::factory()->create([
                'user_id' => $user->id,
                'nip' => $nip,
            ]);
        }
        // ... (rest logic)
        $teachers = \App\Models\User::role('teacher')->get(); // Use Spatie scope

        // 5. Students (20 Users)
        for ($i = 0; $i < 20; $i++) {
            $nisn = fake()->unique()->numerify('00########');
            $user = \App\Models\User::factory()->create([
                'login_identifier' => $nisn,
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'is_active' => true,
                'gender' => fake()->randomElement(['L', 'P']),
            ]);
            $user->assignRole('student');
            \App\Models\Student::factory()->create([
                'user_id' => $user->id,
                'nisn' => $nisn,
                'enrollment_year' => 2024,
            ]);
        }
        $students = \App\Models\User::role('student')->get(); // Spatie scope

        // 6. Classes
        $classNames = ['X IPA 1', 'XI IPA 1', 'XII IPA 1'];
        $classes = collect();
        foreach ($classNames as $index => $name) {
            $classes->push(\App\Models\SchoolClass::factory()->create([
                'name' => $name,
                'grade' => 10 + $index,
                'major' => 'IPA',
                'academic_year_id' => $academicYear->id,
            ]));
        }

        // 7. Assign Students to Classes (Distribute evenly)
        $students->chunk(ceil($students->count() / $classes->count()))->each(function ($chunk, $index) use ($classes, $academicYear) {
            if (isset($classes[$index])) {
                $class = $classes[$index];
                $attendanceNumber = 1;
                foreach ($chunk as $studentUser) {
                    \App\Models\StudentClass::create([
                        'student_id'        => $studentUser->student->id,
                        'class_id'          => $class->id,
                        'academic_year_id'  => $academicYear->id,
                        'attendance_number' => $attendanceNumber++,
                    ]);
                }
            }
        });

        // 8. Subjects
        $subjectNames = ['Matematika', 'Fisika', 'Kimia', 'Biologi', 'Bahasa Inggris'];
        $subjects = collect();
        foreach ($subjectNames as $name) {
            $subjects->push(\App\Models\Subject::factory()->create([
                'name' => $name,
                'code' => strtoupper(substr($name, 0, 3)) . '101',
            ]));
        }

        // 9. Class Subjects
        foreach ($classes as $class) {
            foreach ($subjects as $index => $subject) {
                // Assign a random teacher to this subject in this class
                $teacherUser = $teachers->random();

                // Ensure teacher is assigned to this master subject
                if (!$subject->teachers->contains($teacherUser->teacher->id)) {
                    $subject->teachers()->attach($teacherUser->teacher->id);
                }
                
                $cs = \App\Models\ClassSubject::create([
                    'subject_id' => $subject->id,
                    'class_id' => $class->id,
                    'teacher_id' => $teacherUser->teacher->id,
                    'academic_year_id' => $academicYear->id,
                    // general_info will be added by ClassContentSeeder
                ]);

                // Create a schedule for this
                \App\Models\ClassSchedule::create([
                    'class_subject_id' => $cs->id,
                    'day_of_week' => collect(['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'])->random(),
                    'start_time' => '07:30',
                    'end_time' => '09:00',
                    'room' => 'Lab ' . rand(1, 4),
                ]);
            }
        }

        // 10. Call ClassContentSeeder to populate sections, materials, assignments, quizzes
        $this->call(ClassContentSeeder::class);
    }

    private function getGeneralInfo(string $subjectName): string
    {
        return "**Deskripsi Mata Pelajaran**\n\n" .
               "Mata pelajaran {$subjectName} memberikan pembelajaran tentang konsep-konsep fundamental yang penting untuk dipahami. " .
               "Melalui berbagai metode pembelajaran yang interaktif, siswa akan mengembangkan pemahaman mendalam tentang materi yang diajarkan. " .
               "Dengan pendekatan yang sistematis dan terstruktur, siswa diharapkan dapat menguasai kompetensi yang telah ditetapkan.\n\n" .
               
               "**Capaian Pembelajaran Mata Pelajaran**\n\n" .
               "1. Memahami konsep dan prinsip dasar secara komprehensif\n" .
               "2. Menganalisis hubungan antar konsep yang telah dipelajari\n" .
               "3. Menerapkan pengetahuan dalam konteks kehidupan sehari-hari\n" .
               "4. Mengembangkan keterampilan berpikir kritis dan analitis\n" .
               "5. Menyelesaikan permasalahan dengan pendekatan ilmiah\n\n" .
               
               "**Pokok Bahasan Mata Pelajaran**\n\n" .
               "Materi pembelajaran disusun secara sistematis dalam beberapa bab yang saling berkaitan, " .
               "dimulai dari konsep dasar hingga aplikasi lanjutan. Setiap bab dirancang untuk membangun " .
               "pemahaman secara bertahap dan progresif.\n\n" .
               
               "**Pustaka**\n\n" .
               "1. Kementerian Pendidikan dan Kebudayaan. Buku Teks Pelajaran {$subjectName}. Jakarta: Kemendikbud.\n" .
               "2. Referensi tambahan dan sumber pembelajaran yang relevan dengan materi.\n" .
               "3. Sumber digital dan multimedia pembelajaran interaktif.";
    }
}
