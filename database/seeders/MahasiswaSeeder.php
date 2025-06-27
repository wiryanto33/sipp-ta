<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'mahasiswa']);
        $user = User::where('email', 'andi@example.com')->first();
        $prodi = Prodi::where('name', 'Teknik Informatika')->first();

        // $user->assignRole($role);

        Mahasiswa::create([
            'role_id' => $role->id,
            'user_id' => $user->id,
            'prodi_id' => $prodi->id,
            'angkatan' => '2022',
            'semester' => 6,
            'ipk' => 3.75,
            'phone' => '081234567890',
            'alamat' => 'Jl. Contoh No. 123, Surabaya',
        ]);
    }
}
