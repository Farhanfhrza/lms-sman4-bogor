<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class StudentsImport implements ToArray, WithHeadingRow, WithChunkReading
{
    protected int $enrollmentYear;
    protected int $importedCount = 0;
    protected int $skippedCount = 0;

    public function __construct(int $enrollmentYear)
    {
        $this->enrollmentYear = $enrollmentYear;
    }

    /**
     * Process each chunk of rows from the Excel file.
     */
    public function array(array $rows): void
    {
        foreach ($rows as $row) {
            // Normalize column keys (support both Indonesian and English)
            $name = $row['nama'] ?? $row['name'] ?? null;
            $nisn = $row['nisn'] ?? null;

            // Skip empty rows
            if (empty($name) || empty($nisn)) {
                $this->skippedCount++;
                continue;
            }

            $nisn = trim((string) $nisn);
            $name = trim((string) $name);

            $genderRaw = $row['jk'] ?? $row['jenis_kelamin'] ?? $row['gender'] ?? null;
            $gender = null;
            if ($genderRaw) {
                $g = strtoupper(trim((string) $genderRaw));
                if (in_array($g, ['L', 'P', 'LAKI-LAKI', 'PEREMPUAN', 'M', 'F'])) {
                    $gender = in_array($g, ['L', 'LAKI-LAKI', 'M']) ? 'L' : 'P';
                }
            }

            // Skip if NISN already exists in students table (Student has no SoftDeletes)
            if (Student::where('nisn', $nisn)->exists()) {
                $this->skippedCount++;
                continue;
            }

            // Check if a user with this login_identifier exists (soft-deleted)
            $existingUser = User::withTrashed()->where('login_identifier', $nisn)->first();

            // Generate email from NISN
            $email = $nisn . '@student.sman4bogor.sch.id';

            if ($existingUser) {
                // Restore and reuse the soft-deleted user
                DB::transaction(function () use ($existingUser, $name, $nisn, $email, $gender) {
                    $existingUser->restore();
                    $existingUser->update([
                        'full_name' => $name,
                        'email' => $email,
                        'gender' => $gender,
                        'password' => Hash::make('Siswa' . $nisn),
                    ]);

                    if (!$existingUser->hasRole('student')) {
                        $existingUser->assignRole('student');
                    }

                    Student::create([
                        'user_id'         => $existingUser->id,
                        'nisn'            => $nisn,
                        'enrollment_year' => $this->enrollmentYear,
                    ]);
                });

                $this->importedCount++;
                continue;
            }

            // Skip if email already exists (active user)
            if (User::where('email', $email)->exists()) {
                $this->skippedCount++;
                continue;
            }

            DB::transaction(function () use ($name, $nisn, $email, $gender) {
                $user = User::create([
                    'full_name'        => $name,
                    'login_identifier' => $nisn,
                    'email'            => $email,
                    'gender'           => $gender,
                    'password'         => Hash::make('Siswa' . $nisn),
                ]);

                $user->assignRole('student');

                Student::create([
                    'user_id'         => $user->id,
                    'nisn'            => $nisn,
                    'enrollment_year' => $this->enrollmentYear,
                ]);
            });

            $this->importedCount++;
        }
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
}
