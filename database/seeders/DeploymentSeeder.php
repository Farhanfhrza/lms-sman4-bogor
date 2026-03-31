<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DeploymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Create Permissions
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
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 3. Create Roles and Assign Permissions
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleAdmin->givePermissionTo(['manage-users', 'manage-classes', 'view-classes']);

        $roleTeacher = Role::firstOrCreate(['name' => 'teacher']);
        $roleTeacher->givePermissionTo(['input-score', 'manage-materials', 'view-classes']);

        $roleStudent = Role::firstOrCreate(['name' => 'student']);
        $roleStudent->givePermissionTo(['view-materials', 'submit-assignment', 'view-classes', 'enroll-classes']);

        Role::firstOrCreate(['name' => 'piket']);
        Role::firstOrCreate(['name' => 'wali_kelas']);

        // 4. Create Active Academic Year
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        $academicYearName = "{$currentYear}/{$nextYear}";
        
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => $academicYearName],
            [
                'is_active' => true
            ]
        );

        // Pastikan hanya satu tahun ajaran yang aktif
        AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);

        // 5. Create Admin Users
        $admins = [
            [
                'full_name' => 'Staff Admin 1',
                'login_identifier' => 'Staff-admin-1',
                'email' => 'admin1@sman4bogor.sch.id',
                'password' => '16132sman4bogor'
            ],
            [
                'full_name' => 'Staff Admin 2',
                'login_identifier' => 'Staff-admin-2',
                'email' => 'admin2@sman4bogor.sch.id',
                'password' => 'adminsman4_bogor'
            ],
            [
                'full_name' => 'Staff Admin 3',
                'login_identifier' => 'Staff-admin-3',
                'email' => 'admin3@sman4bogor.sch.id',
                'password' => 'sman4_bogor_admin'
            ],
            [
                'full_name' => 'Staff Admin 4',
                'login_identifier' => 'Staff-admin-4',
                'email' => 'admin4@sman4bogor.sch.id',
                'password' => 'admin4_sman4_bogor'
            ],
            [
                'full_name' => 'Staff Admin 5',
                'login_identifier' => 'Staff-admin-5',
                'email' => 'admin5@sman4bogor.sch.id',
                'password' => 'admin5_sman4_bogor'
            ]
        ];

        $this->command->info('Creating Deployment Admins...');

        foreach ($admins as $adminData) {
            $user = User::firstOrCreate(
                ['login_identifier' => $adminData['login_identifier']],
                [
                    'full_name' => $adminData['full_name'],
                    'email' => $adminData['email'],
                    'password' => Hash::make($adminData['password']),
                    'gender' => 'L', // Default L
                    'is_active' => true,
                ]
            );

            // Jika role belum admin, tambahkan
            if (!$user->hasRole('admin')) {
                $user->assignRole('admin');
            }
        }

        $this->command->info('Deployment Seeder executed successfully.');
    }
}
