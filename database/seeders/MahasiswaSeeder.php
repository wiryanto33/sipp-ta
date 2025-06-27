<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Mahasiswa;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get mahasiswa role
        $mahasiswaRole = Role::where('name', 'mahasiswa')->first();

        // Create User first
        $user = User::create([
            'name' => 'popy',
            'pangkat' => null,
            'korps' => null,
            'nrp' => null,
            'image' => null,
            'email' => 'popy@gmail.com',
            'email_verified_at' => now(),
            'status' => 'aktif',
            'password' => Hash::make('password'),
        ]);

        // Assign mahasiswa role
        $user->assignRole('mahasiswa');

        // Create Mahasiswa profile
        Mahasiswa::create([
            'role_id' => $mahasiswaRole->id,
            'user_id' => $user->id,
            'prodi_id' => 1, // Sesuaikan dengan ID prodi yang ada
            'angkatan' => '2021',
            'semester' => 6,
            'ipk' => 3.75,
            'phone' => '081234567890',
            'alamat' => 'Jl. Merdeka No. 123, Jakarta Pusat',
        ]);
    }
}
