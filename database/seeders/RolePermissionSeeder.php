<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Mahasiswa Management
            'view mahasiswas',
            'create mahasiswas',
            'edit mahasiswas',
            'delete mahasiswas',

            // Dosen Management
            'view dosens',
            'create dosens',
            'edit dosens',
            'delete dosens',

            // Prodi Management
            'view prodi',
            'create prodi',
            'edit prodi',
            'delete prodi',

            // Dashboard
            'view dashboard',
            'view reports',

            // Profile
            'view profile',
            'edit profile',

            //tugas akhir
            'view tugas-akhir',
            'create tugas-akhir',
            'edit tugas-akhir',
            'delete tugas-akhir',
            'submit tugas-akhir',
            'reject tugas-akhir',
            'approve tugas-akhir',
            'download tugas-akhir',

            //jadwal sidang
            'view jadwal-sidang',
            'create jadwal-sidang',
            'edit jadwal-sidang',
            'delete jadwal-sidang',
            'show jadwal-sidang',

            //update kehadiran
            'edit update-kehadiran',
            'edit update-status',

            //penilaian sidang
            'view penilaian-sidang',
            'create penilaian-sidang',
            'edit penilaian-sidang',
            'delete penilaian-sidang',
            'show penilaian-sidang',

            'view kaprodis'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Admin Role - Full Access
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());
    }
}
