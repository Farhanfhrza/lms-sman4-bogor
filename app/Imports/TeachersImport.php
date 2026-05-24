<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class TeachersImport implements ToArray, WithHeadingRow, WithChunkReading
{
    protected int $importedCount = 0;
    protected int $skippedCount = 0;
    protected int $currentRow = 1; // Heading is row 1
    public array $rowErrors = [];

    /**
     * Process each chunk of rows from the Excel file.
     */
    public function array(array $rows): void
    {
        foreach ($rows as $row) {
            $this->currentRow++;
            $rowNum = $this->currentRow;
            $errors = [];

            // Normalize column keys (support both Indonesian and English)
            $name = $row['nama'] ?? $row['name'] ?? null;
            $loginId = $row['login_id'] ?? $row['username'] ?? null;
            $nip = $row['nip'] ?? null;
            $specialization = $row['spesialisasi'] ?? $row['specialization'] ?? null;

            if (empty($name)) {
                $errors[] = "Nama tidak boleh kosong.";
            }
            if (empty($loginId)) {
                $errors[] = "Login ID tidak boleh kosong.";
            }

            if (!empty($errors)) {
                $this->rowErrors[] = "Baris {$rowNum}: " . implode(' ', $errors);
                $this->skippedCount++;
                continue;
            }

            $loginId = trim((string) $loginId);
            $name = trim((string) $name);
            if ($nip !== null) {
                $nip = trim((string) $nip);
            }
            if ($specialization !== null) {
                $specialization = trim((string) $specialization);
            }

            $genderRaw = $row['jk'] ?? $row['jenis_kelamin'] ?? $row['gender'] ?? null;
            $gender = null;
            if ($genderRaw) {
                $g = strtoupper(trim((string) $genderRaw));
                if (in_array($g, ['L', 'P', 'LAKI-LAKI', 'PEREMPUAN', 'M', 'F'])) {
                    $gender = in_array($g, ['L', 'LAKI-LAKI', 'M']) ? 'L' : 'P';
                } else {
                    $errors[] = "Jenis Kelamin tidak valid ('{$genderRaw}'). Gunakan L atau P.";
                }
            } else {
                $errors[] = "Jenis Kelamin tidak boleh kosong.";
            }

            // Skip if NIP already exists in teachers table
            if (!empty($nip) && Teacher::where('nip', $nip)->exists()) {
                $errors[] = "NIP {$nip} sudah terdaftar.";
            }

            // Check if a user with this login_identifier already exists
            $existingUser = User::withTrashed()->where('login_identifier', $loginId)->first();
            if ($existingUser && !$existingUser->trashed()) {
                $errors[] = "Login ID {$loginId} sudah digunakan.";
            }

            if (!empty($errors)) {
                $this->rowErrors[] = "Baris {$rowNum} ({$name}): " . implode(' ', $errors);
                $this->skippedCount++;
                continue;
            }

            if ($existingUser) {
                // Restore and reuse the soft-deleted user
                DB::transaction(function () use ($existingUser, $name, $loginId, $nip, $gender, $specialization) {
                    $existingUser->restore();
                    $existingUser->update([
                        'full_name' => $name,
                        'gender' => $gender,
                        'password' => Hash::make('Guru' . $loginId),
                    ]);

                    if (!$existingUser->hasRole('teacher')) {
                        $existingUser->assignRole('teacher');
                    }

                    Teacher::create([
                        'user_id'        => $existingUser->id,
                        'nip'            => $nip,
                        'specialization' => $specialization,
                    ]);
                });

                $this->importedCount++;
                continue;
            }

            DB::transaction(function () use ($name, $loginId, $nip, $gender, $specialization) {
                $user = User::create([
                    'full_name'        => $name,
                    'login_identifier' => $loginId,
                    'gender'           => $gender,
                    'password'         => Hash::make('Guru' . $loginId),
                ]);

                $user->assignRole('teacher');

                Teacher::create([
                    'user_id'        => $user->id,
                    'nip'            => $nip,
                    'specialization' => $specialization,
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

    public function getRowErrors(): array
    {
        return $this->rowErrors;
    }
}
