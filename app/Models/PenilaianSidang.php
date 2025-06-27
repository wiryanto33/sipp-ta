<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianSidang extends Model
{
    use HasFactory;

    protected $fillable = [
        'jadwal_sidang_id',
        'penguji_sidang_id',
        'originalitas_materi',
        'analisa_metodologi',
        'tingkat_aplikasi_materi',
        'pengembangan_kreativitas',
        'tata_tulis',
        'penguasaan_materi',
        'sikap_dan_penampilan',
        'penyajian_sarana_sistematika',
        'hasil_yang_dicapai',
        'penguasaan_materi_diskusi',
        'objektivitas_tanggapan',
        'kemampuan_mempertahankan_ide',
        'nilai_rata_rata',
        'nilai_akhir',
    ];

    public function jadwalSidang()
    {
        return $this->belongsTo(JadwalSidang::class, 'jadwal_sidang_id');
    }

    public function pengujiSidang()
    {
        return $this->belongsTo(PengujiSidang::class, 'penguji_sidang_id');
    }

}
