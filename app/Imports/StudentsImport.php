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

            // Skip if NISN already exists
            if (Student::where('nisn', $nisn)->exists()) {
                $this->skippedCount++;
                continue;
            }

            // Generate email from NISN
            $email = $nisn . '@student.sman4bogor.sch.id';

            // Skip if email already exists
            if (User::where('email', $email)->exists()) {
                $this->skippedCount++;
                continue;
            }

            DB::transaction(function () use ($name, $nisn, $email) {
                $user = User::create([
                    'full_name'        => $name,
                    'login_identifier' => $nisn,
                    'email'            => $email,
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
