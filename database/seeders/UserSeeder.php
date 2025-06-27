<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Prodi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Get roles and prodis
        $adminRole = Role::where('name', 'admin')->first();
        $kaprodiRole = Role::where('name', 'kaprodi')->first();
        $dosenRole = Role::where('name', 'dosens')->first();
        $mahasiswaRole = Role::where('name', 'mahasiswas')->first();

        $prodis = Prodi::all();

        // Create Admin Users
        $this->createAdminUsers($adminRole, $kaprodiRole);

        // Create Dosen Users
        $this->createDosenUsers($dosenRole, $prodis);

        // Create Mahasiswa Users
        $this->createMahasiswaUsers($mahasiswaRole, $prodis);
    }

    private function createAdminUsers($adminRole, $koordinatorRole)
    {
        // Super Admin
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'pangkat' => 'Kolonel',
            'korps' => 'TNI AD',
            'nrp' => '11223344',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'status' => 'aktif',
        ]);
        $superAdmin->assignRole($adminRole);

        // Koordinator
        $kaprodiRole = User::create([
            'name' => 'Dr. Koordinator Prodi',
            'pangkat' => 'Letnan Kolonel',
            'korps' => 'TNI AD',
            'nrp' => '22334455',
            'email' => 'koordinator@example.com',
            'password' => Hash::make('password'),
        ]);
        $kaprodiRole->assignRole($kaprodiRole);
    }

    private function createDosenUsers($dosenRole, $prodis)
    {
        $dosenData = [
            [
                'name' => 'Dr. Ahmad Suhendra, S.T., M.T.',
                'pangkat' => 'Mayor',
                'korps' => 'TNI AD',
                'nrp' => '33445566',
                'email' => 'ahmad.suhendra@example.com',
                'nidn' => '0123456789',
                'jabatan_akademik' => 'Lektor Kepala',
                'bidang_studi' => 'Rekayasa Perangkat Lunak',
                'phone' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 123, Jakarta',
                'prodi_id' => $prodis->where('name', 'Teknik Informatika')->where('jenjang', 'D3')->first()->id,
            ],
            [
                'name' => 'Dr. Budi Raharjo, S.Kom., M.Kom.',
                'pangkat' => 'Kapten',
                'korps' => 'TNI AD',
                'nrp' => '44556677',
                'email' => 'budi.raharjo@example.com',
                'nidn' => '0234567890',
                'jabatan_akademik' => 'Lektor',
                'bidang_studi' => 'Sistem Informasi Manajemen',
                'phone' => '081234567891',
                'alamat' => 'Jl. Proklamasi No. 456, Jakarta',
                'prodi_id' => $prodis->where('name', 'Teknik Mesin')->where('jenjang', 'D3')->first()->id,
            ],
            [
                'name' => 'Prof. Dr. Candra Wijaya, S.T., M.T.',
                'pangkat' => 'Letnan Kolonel',
                'korps' => 'TNI AD',
                'nrp' => '55667788',
                'email' => 'candra.wijaya@example.com',
                'nidn' => '0345678901',
                'jabatan_akademik' => 'Guru Besar',
                'bidang_studi' => 'Arsitektur Komputer',
                'phone' => '081234567892',
                'alamat' => 'Jl. Diponegoro No. 789, Jakarta',
                'prodi_id' => $prodis->where('name', 'Teknik Elektro')->where('jenjang', 'S1')->first()->id,
            ],
            [
                'name' => 'Drs. Dedi Kusuma, M.M.',
                'pangkat' => 'Mayor',
                'korps' => 'TNI AD',
                'nrp' => '66778899',
                'email' => 'dedi.kusuma@example.com',
                'nidn' => '0456789012',
                'jabatan_akademik' => 'Asisten Ahli',
                'bidang_studi' => 'Manajemen Sistem Informasi',
                'phone' => '081234567893',
                'alamat' => 'Jl. Sudirman No. 321, Jakarta',
                'prodi_id' => $prodis->where('name', 'Teknik Manajemen Industri')->where('jenjang', 'S1')->first()->id,
            ],
            [
                'name' => 'Ir. Eko Prasetyo, M.T.',
                'pangkat' => 'Kapten',
                'korps' => 'TNI AD',
                'nrp' => '77889900',
                'email' => 'eko.prasetyo@example.com',
                'nidn' => '0567890123',
                'jabatan_akademik' => 'Lektor',
                'bidang_studi' => 'Jaringan Komputer',
                'phone' => '081234567894',
                'alamat' => 'Jl. Thamrin No. 654, Jakarta',
                'prodi_id' => $prodis->where('name', 'Teknik Informatika')->where('jenjang', 'D3')->first()->id,
            ],
        ];

        foreach ($dosenData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'pangkat' => $data['pangkat'],
                'korps' => $data['korps'],
                'nrp' => $data['nrp'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
            ]);

            $user->assignRole($dosenRole);

            Dosen::create([
                'user_id' => $user->id,
                'role_id' => $dosenRole->id,
                'prodi_id' => $data['prodi_id'],
                'nidn' => $data['nidn'],
                'jabatan_akademik' => $data['jabatan_akademik'],
                'bidang_studi' => $data['bidang_studi'],
                'phone' => $data['phone'],
                'alamat' => $data['alamat'],
            ]);
        }
    }

    private function createMahasiswaUsers($mahasiswaRole, $prodis)
    {
        $mahasiswaData = [
            [
                'name' => 'Serda Andi Pratama',
                'pangkat' => 'Serda',
                'korps' => 'TNI AD',
                'nrp' => '12345678',
                'email' => 'andi.pratama@example.com',
                'angkatan' => 2023,
                'semester' => 3,
                'ipk' => 3.75,
                'phone' => '081987654321',
                'alamat' => 'Jl. Pahlawan No. 111, Bandung',
                'prodi_id' => $prodis->where('name', 'Teknik Informatika')->where('jenjang', 'D3')->first()->id,
            ],
            [
                'name' => 'Kopda Budi Santoso',
                'pangkat' => 'Kopda',
                'korps' => 'TNI AD',
                'nrp' => '23456789',
                'email' => 'budi.santoso@example.com',
                'angkatan' => 2023,
                'semester' => 3,
                'ipk' => 3.50,
                'phone' => '081987654322',
                'alamat' => 'Jl. Veteran No. 222, Surabaya',
                'prodi_id' => $prodis->where('name', 'Teknik Informatika')->where('jenjang', 'D3')->first()->id,
            ],
            [
                'name' => 'Sertu Citra Dewi',
                'pangkat' => 'Sertu',
                'korps' => 'TNI AD',
                'nrp' => '34567890',
                'email' => 'citra.dewi@example.com',
                'angkatan' => 2022,
                'semester' => 5,
                'ipk' => 3.85,
                'phone' => '081987654323',
                'alamat' => 'Jl. Pemuda No. 333, Yogyakarta',
                'prodi_id' => $prodis->where('name', 'Teknik Manajemen Industri')->where('jenjang', 'S1')->first()->id,
            ],
            [
                'name' => 'Koptu Doni Hermawan',
                'pangkat' => 'Koptu',
                'korps' => 'TNI AD',
                'nrp' => '45678901',
                'email' => 'doni.hermawan@example.com',
                'angkatan' => 2024,
                'semester' => 1,
                'ipk' => null,
                'phone' => '081987654324',
                'alamat' => 'Jl. Kemerdekaan No. 444, Medan',
                'prodi_id' => $prodis->where('name', 'Teknik Informatika')->where('jenjang', 'D3')->first()->id,
            ],
            [
                'name' => 'Serka Eka Putri',
                'pangkat' => 'Serka',
                'korps' => 'TNI AD',
                'nrp' => '56789012',
                'email' => 'eka.putri@example.com',
                'angkatan' => 2022,
                'semester' => 5,
                'ipk' => 3.60,
                'phone' => '081987654325',
                'alamat' => 'Jl. Persatuan No. 555, Makassar',
                'prodi_id' => $prodis->where('name', 'Teknik Informatika')->where('jenjang', 'D3')->first()->id,
            ],
            [
                'name' => 'Kopda Fajar Nugroho',
                'pangkat' => 'Kopda',
                'korps' => 'TNI AD',
                'nrp' => '67890123',
                'email' => 'fajar.nugroho@example.com',
                'angkatan' => 2023,
                'semester' => 3,
                'ipk' => 3.25,
                'phone' => '081987654326',
                'alamat' => 'Jl. Bhayangkara No. 666, Semarang',
                'prodi_id' => $prodis->where('name', 'Teknik Informatika')->where('jenjang', 'D3')->first()->id,
            ],
            [
                'name' => 'Serda Gita Sari',
                'pangkat' => 'Serda',
                'korps' => 'TNI AD',
                'nrp' => '78901234',
                'email' => 'gita.sari@example.com',
                'angkatan' => 2021,
                'semester' => 7,
                'ipk' => 3.90,
                'phone' => '081987654327',
                'alamat' => 'Jl. Kartini No. 777, Denpasar',
                'prodi_id' => $prodis->where('name', 'Teknik Elektro')->where('jenjang', 'S1')->first()->id,
            ],
            [
                'name' => 'Koptu Hendi Wijaya',
                'pangkat' => 'Koptu',
                'korps' => 'TNI AD',
                'nrp' => '89012345',
                'email' => 'hendi.wijaya@example.com',
                'angkatan' => 2024,
                'semester' => 1,
                'ipk' => null,
                'phone' => '081987654328',
                'alamat' => 'Jl. Pancasila No. 888, Palembang',
                'prodi_id' => $prodis->where('name', 'Teknik Elektro')->where('jenjang', 'S1')->first()->id,
            ],
        ];

        foreach ($mahasiswaData as $data) {
            $user = User::create([
                'name' => $data['name'],
                'pangkat' => $data['pangkat'],
                'korps' => $data['korps'],
                'nrp' => $data['nrp'],
                'email' => $data['email'],
                'password' => Hash::make('password'),
            ]);

            $user->assignRole($mahasiswaRole);

            Mahasiswa::create([
                'user_id' => $user->id,
                'role_id' => $mahasiswaRole->id,
                'prodi_id' => $data['prodi_id'],
                'angkatan' => $data['angkatan'],
                'semester' => $data['semester'],
                'ipk' => $data['ipk'],
                'phone' => $data['phone'],
                'alamat' => $data['alamat'],
            ]);
        }
    }
}
