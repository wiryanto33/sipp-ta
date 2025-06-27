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

        // Koordinator Role - Limited Admin Access
        $koordinatorRole = Role::create(['name' => 'kaprodi']);
        $koordinatorRole->givePermissionTo([
            'approve tugas-akhir',
            'reject tugas-akhir',
            'view tugas-akhir',
            'download tugas-akhir',
            'delete tugas-akhir',
            'create dosens',
            'edit dosens',
            'view dosens',
            'delete dosens',
            'edit jadwal-sidang',
            'view jadwal-sidang',
            'show jadwal-sidang',
            'delete jadwal-sidang',
            'create mahasiswas',
            'edit mahasiswas',
            'view mahasiswas',
            'delete mahasiswas',
            'create users',
            'edit users',
            'view users',
            'delete users',
            'show users',
            'edit profile',
            'view profile',
            'edit update-kehadiran',
            'edit update-status',
            'view reports',
        ]);

        // Dosen Role - View and limited edit access
        $dosenRole = Role::create(['name' => 'dosen']);
        $dosenRole->givePermissionTo([
            'view jadwal-sidang',
            'show jadwal-sidang',
            'view penilaian-sidang',
            'edit penilaian-sidang',
            'create penilaian-sidang',
            'show penilaian-sidang',
            'edit users',
            'view users',
            'show users',
            'edit profile',
            'view profile',
            'edit update-kehadiran',
            'edit update-status',
        ]);

        // Mahasiswa Role - Very limited access
        $mahasiswaRole = Role::create(['name' => 'mahasiswa']);
        $mahasiswaRole->givePermissionTo([
            'view tugas-akhir',
            'create tugas-akhir',
            'edit tugas-akhir',
            'delete tugas-akhir',
            'download tugas-akhir',
            'submit tugas-akhir',
            'create jadwal-sidang',
            'view jadwal-sidang',
            'show jadwal-sidang',
            'edit jadwal-sidang',
            'delete jadwal-sidang',
            'view penilaian-sidang',
            'show penilaian-sidang',
            'edit users',
            'show users',
            'edit profile',
            'view profile',
            'edit update-kehadiran',
            'edit update-status',
        ]);
    }
}
