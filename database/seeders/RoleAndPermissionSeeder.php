<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            'manage-users',
            'input-score',
            'manage-materials',
            'view-materials',
            'submit-assignment',
            'view-classes',
            'manage-classes',
            'enroll-classes',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // Create Roles and Assign Permissions
        $roleAdmin = Role::findOrCreate('admin');
        $roleAdmin->givePermissionTo(['manage-users', 'manage-classes', 'view-classes']);

        $roleTeacher = Role::findOrCreate('teacher');
        $roleTeacher->givePermissionTo(['input-score', 'manage-materials', 'view-classes']);

        $roleStudent = Role::findOrCreate('student');
        $roleStudent->givePermissionTo(['view-materials', 'submit-assignment', 'view-classes', 'enroll-classes']);

        $rolePiket = Role::findOrCreate('piket');
        
        $roleWaliKelas = Role::findOrCreate('wali_kelas');
        
        // Create Dummy Users using Factory States if possible, or manual assignment logic
        // We will demonstrate the use of the Factory states we are about to create.
        
        // Admin
        if (!User::where('email', 'admin@sman4bogor.sch.id')->exists()) {
             User::factory()->admin()->create([
                'full_name' => 'Administrator',
                'login_identifier' => 'admin',
                'email' => 'admin@sman4bogor.sch.id',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }

        // Teacher
        if (!User::where('email', 'guru@sman4bogor.sch.id')->exists()) {
            $user = User::factory()->teacher()->create([
                'full_name' => 'Guru Teladan',
                'login_identifier' => 'guru',
                'email' => 'guru@sman4bogor.sch.id',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
            \App\Models\Teacher::factory()->create([
                'user_id' => $user->id,
                'nip' => '198001012024011001',
            ]);
        }

        // Student
        if (!User::where('email', 'siswa@sman4bogor.sch.id')->exists()) {
            $user = User::factory()->student()->create([
                'full_name' => 'Siswa Berprestasi',
                'login_identifier' => 'siswa',
                'email' => 'siswa@sman4bogor.sch.id',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
            \App\Models\Student::factory()->create([
                'user_id' => $user->id,
                'nisn' => '0012345678',
                'enrollment_year' => 2024,
            ]);
        }


        // Piket
        if (!User::where('email', 'piket@sman4bogor.sch.id')->exists()) {
            User::factory()->piket()->create([
                'full_name' => 'Petugas Piket',
                'login_identifier' => 'piket',
                'email' => 'piket@sman4bogor.sch.id',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }

        // Wali Kelas
        if (!User::where('email', 'walikelas@sman4bogor.sch.id')->exists()) {
            User::factory()->waliKelas()->create([
                'full_name' => 'Wali Kelas X-1',
                'login_identifier' => 'walikelas',
                'email' => 'walikelas@sman4bogor.sch.id',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
        }
    }
}
