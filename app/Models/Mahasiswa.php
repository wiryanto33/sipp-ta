<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class Mahasiswa extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = [
        'role_id',
        'user_id',
        'prodi_id',
        'angkatan',
        'semester',
        'ipk',
        'phone',
        'alamat',
    ];
    // Relasi ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Relasi ke Prodi
    public function prodi(): BelongsTo
    {
        return $this->belongsTo(Prodi::class, 'prodi_id', 'id');
    }

    // Relasi ke Role
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    // Scope untuk filter berdasarkan angkatan
    public function scopeAngkatan($query, $angkatan)
    {
        return $query->where('angkatan', $angkatan);
    }

    // Scope untuk filter berdasarkan prodi
    public function scopeProdi($query, $prodiId)
    {
        return $query->where('prodi_id', $prodiId);
    }

    // Accessor untuk status akademik berdasarkan IPK
    public function getStatusAkademikAttribute()
    {
        if (!$this->ipk) return 'Belum Ada Data';

        if ($this->ipk >= 3.5) return 'Sangat Baik';
        if ($this->ipk >= 3.0) return 'Baik';
        if ($this->ipk >= 2.5) return 'Cukup';
        return 'Kurang';
    }

    public function pengajuanTugasAkhir()
    {
        return $this->hasMany(PengajuanTugasAkhir::class, 'mahasiswa_id');
    }

    public function pengajuanTugasAkhirAktif()
    {
        return $this->hasOne(PengajuanTugasAkhir::class, 'mahasiswa_id')
            ->whereIn('status', [
                PengajuanTugasAkhir::STATUS_DIAJUKAN,
                PengajuanTugasAkhir::STATUS_DITERIMA,
                PengajuanTugasAkhir::STATUS_SEDANG_BIMBINGAN,
                PengajuanTugasAkhir::STATUS_SIAP_SIDANG,
                PengajuanTugasAkhir::STATUS_LULUS
            ]);
    }
}
