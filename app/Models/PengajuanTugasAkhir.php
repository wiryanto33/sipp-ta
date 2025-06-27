<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PengajuanTugasAkhir extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mahasiswa_id',
        'judul',
        'sinopsis',
        'bidang_penelitian',
        'status',
        'tanggal_pengajuan',
        'tanggal_acc',
        'file_proposal',
        'file_skripsi',
        'message',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_acc' => 'date',
    ];

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_DIAJUKAN = 'diajukan';
    const STATUS_DITERIMA = 'diterima';
    const STATUS_DITOLAK = 'ditolak';
    const STATUS_SEDANG_BIMBINGAN = 'sedang_bimbingan';
    const STATUS_SIAP_SIDANG = 'siap_sidang';
    const STATUS_LULUS = 'lulus';
    const STATUS_TIDAK_LULUS = 'tidak_lulus';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_DIAJUKAN => 'Diajukan',
            self::STATUS_DITERIMA => 'Diterima',
            self::STATUS_DITOLAK => 'Ditolak',
            self::STATUS_SEDANG_BIMBINGAN => 'Sedang Bimbingan',
            self::STATUS_SIAP_SIDANG => 'Siap Sidang',
            self::STATUS_LULUS => 'Lulus',
            self::STATUS_TIDAK_LULUS => 'Tidak Lulus',
        ];
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_DRAFT => 'secondary',
            self::STATUS_DIAJUKAN => 'warning',
            self::STATUS_DITERIMA => 'success',
            self::STATUS_DITOLAK => 'danger',
            self::STATUS_SEDANG_BIMBINGAN => 'info',
            self::STATUS_SIAP_SIDANG => 'primary',
            self::STATUS_LULUS => 'success',
            self::STATUS_TIDAK_LULUS => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    // Relationships
    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    // Scopes
    // Di model PengajuanTugasAkhir
    public function scopeByMahasiswa($query, $mahasiswaId)
    {
        return $query->where('mahasiswa_id', $mahasiswaId);
    }

    // Di Model PengajuanTugasAkhir
    public function scopeByProdi($query, $prodiId)
    {
        return $query->whereHas('mahasiswa', function ($q) use ($prodiId) {
            $q->where('prodi_id', $prodiId);
        });
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function bimbingans()
    {
        return $this->hasMany(Bimbingan::class, 'pengajuan_tugas_akhir_id');
    }
}
