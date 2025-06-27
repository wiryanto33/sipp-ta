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
            ],
            [
                'name' => 'Teknik Elektro',
                'jenjang' => 'D3',
                'kaprodi' => 'Bagus',
            ],

            [
                'name' => 'Teknik Mesin',
                'jenjang' => 'D3',
                'kaprodi' => 'Pompy',
            ],
            [
                'name' => 'Teknik mesin',
                'jenjang' => 'S1',
                'kaprodi' => 'Riza',
            ],
            [
                'name' => 'Teknik Elektro',
                'jenjang' => 'S1',
                'kaprodi' => 'Rendi Akbar',
            ],
            [
                'name' => 'Teknik Manajemen Industri',
                'jenjang' => 'S1',
                'kaprodi' => 'Lina Marlina',
            ],
        ]);
    }
}
