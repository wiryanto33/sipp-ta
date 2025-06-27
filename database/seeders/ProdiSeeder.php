<?php

namespace Database\Seeders;

use App\Models\Prodi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProdiSeeder extends Seeder
{
    public function run(): void
    {
        Prodi::insert([
            [
                'name' => 'Teknik Informatika',
                'jenjang' => 'D3',
                'kaprodi' => 'Syahlan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Teknik Elektro',
                'jenjang' => 'D3',
                'kaprodi' => 'Bagus',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Teknik Mesin',
                'jenjang' => 'D3',
                'kaprodi' => 'Pompy',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Teknik Mesin', // PERBAIKAN: Huruf besar di awal
                'jenjang' => 'S1',
                'kaprodi' => 'Riza',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Teknik Elektro',
                'jenjang' => 'S1',
                'kaprodi' => 'Rendi Akbar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Teknik Manajemen Industri',
                'jenjang' => 'S1',
                'kaprodi' => 'Lina Marlina',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
