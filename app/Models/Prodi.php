<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Prodi extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'jenjang',
        'kaprodi',
    ];

    // Relasi ke Mahasiswa - Diperbaiki
    public function mahasiswas(): HasMany
    {
        return $this->hasMany(Mahasiswa::class, 'prodi_id', 'id');
    }

    // Relasi ke Dosen
    public function dosens(): HasMany
    {
        return $this->hasMany(Dosen::class, 'prodi_id', 'id');
    }

    // Accessor untuk mendapatkan jumlah mahasiswa
    public function getMahasiswaCountAttribute()
    {
        return $this->mahasiswas()->count();
    }

    // Accessor untuk mendapatkan jumlah dosen
    public function getDosenCountAttribute()
    {
        return $this->dosens()->count();
    }
}
