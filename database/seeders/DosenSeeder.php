<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Dosen;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get dosen role
        $dosenRole = Role::where('name', 'dosen')->first();

        $dosenData = [
            [
                'name' => 'Dr. Budi Santoso, M.Kom',
                'email' => 'budi.santoso@univ.ac.id',
                'pangkat' => 'Letkol',
                'korps' => 'CKM',
                'nrp' => '11750123456789',
                'nidn' => '0123456789',
                'jabatan_akademik' => 'Lektor Kepala',
                'bidang_studi' => 'Teknik Informatika',
                'phone' => '081987654321',
                'alamat' => 'Jl. Profesor No. 45, Surabaya',
            ],
            [
                'name' => 'Sari Wijayanti, S.Kom, M.T',
                'email' => 'sari.wijayanti@univ.ac.id',
                'pangkat' => 'Mayor',
                'korps' => 'CKM',
                'nrp' => '11800234567890',
                'nidn' => '0234567890',
                'jabatan_akademik' => 'Lektor',
                'bidang_studi' => 'Sistem Informasi',
                'phone' => '081876543210',
                'alamat' => 'Jl. Pendidikan No. 78, Yogyakarta',
            ],
            [
                'name' => 'Prof. Dr. Agus Setiawan, M.Sc',
                'email' => 'agus.setiawan@univ.ac.id',
                'pangkat' => 'Kolonel',
                'korps' => 'CKM',
                'nrp' => '11700345678901',
                'nidn' => '0345678901',
                'jabatan_akademik' => 'Guru Besar',
                'bidang_studi' => 'Computer Science',
                'phone' => '081765432109',
                'alamat' => 'Jl. Akademik No. 99, Bandung',
            ]
        ];

        foreach ($dosenData as $data) {
            // Create User first
            $user = User::create([
                'name' => $data['name'],
                'pangkat' => $data['pangkat'],
                'korps' => $data['korps'],
                'nrp' => $data['nrp'],
                'image' => null,
                'email' => $data['email'],
                'email_verified_at' => now(),
                'status' => 'aktif',
                'password' => Hash::make('password'),
            ]);

            // Assign dosen role
            $user->assignRole('dosen');

            // Create Dosen profile
            Dosen::create([
                'role_id' => $dosenRole->id,
                'user_id' => $user->id,
                'prodi_id' => 1, // Sesuaikan dengan ID prodi yang ada
                'nidn' => $data['nidn'],
                'jabatan_akademik' => $data['jabatan_akademik'],
                'bidang_studi' => $data['bidang_studi'],
                'phone' => $data['phone'],
                'alamat' => $data['alamat'],
            ]);
        }
    }
}
