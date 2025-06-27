<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengujiSidang extends Model
{
    use HasFactory;

    protected $fillable = [
        'jadwal_sidang_id',
        'dosen_id',
        'peran',
        'hadir'
    ];

    protected $casts = [
        'hadir' => 'boolean'
    ];

    // Relationships
    public function jadwalSidang()
    {
        return $this->belongsTo(JadwalSidang::class);
    }

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }


    // Scopes
    public function scopeByPeran($query, $peran)
    {
        return $query->where('peran', $peran);
    }

    public function scopeHadir($query)
    {
        return $query->where('hadir', true);
    }

    // Accessors
    public function getPeranLabelAttribute()
    {
        $labels = [
            'ketua' => 'Ketua Penguji',
            'sekretaris' => 'Sekretaris',
            'penguji_1' => 'Penguji 1',
            'penguji_2' => 'Penguji 2',
            'pembimbing_1' => 'Pembimbing 1',
            'pembimbing_2' => 'Pembimbing 2'
        ];

        return $labels[$this->peran] ?? $this->peran;
    }
}
