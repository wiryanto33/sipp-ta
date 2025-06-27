<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bimbingan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'pengajuan_tugas_akhir_id',
        'dosen_id',
        'jabatan_pembimbing',
        'kuota',
        'status',
        'tanggal_mulai',
        'tanggal_selesai',
        'alasan_penolakan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    // Status constants
    const STATUS_DIAJUKAN = 'diajukan';
    const STATUS_DISETUJUI = 'disetujui';
    const STATUS_DITOLAK = 'ditolak';
    const STATUS_AKTIF = 'aktif';
    const STATUS_SELESAI = 'selesai';
    const STATUS_BERHENTI = 'berhenti';

    // Jabatan Pembimbing constants
    const JABATAN_PEMBIMBING_1 = 'pembimbing_1';
    const JABATAN_PEMBIMBING_2 = 'pembimbing_2';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_DIAJUKAN => 'Diajukan',
            self::STATUS_DISETUJUI => 'Disetujui',
            self::STATUS_DITOLAK => 'Ditolak',
            self::STATUS_AKTIF => 'Aktif',
            self::STATUS_SELESAI => 'Selesai',
            self::STATUS_BERHENTI => 'Berhenti',
        ];
    }

    public static function getJabatanPembimbingOptions()
    {
        return [
            self::JABATAN_PEMBIMBING_1 => 'Pembimbing 1',
            self::JABATAN_PEMBIMBING_2 => 'Pembimbing 2',
        ];
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    public function getJabatanPembimbingLabelAttribute()
    {
        return self::getJabatanPembimbingOptions()[$this->jabatan_pembimbing] ?? $this->jabatan_pembimbing;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_DIAJUKAN => 'warning',
            self::STATUS_DISETUJUI => 'info',
            self::STATUS_DITOLAK => 'danger',
            self::STATUS_AKTIF => 'success',
            self::STATUS_SELESAI => 'secondary',
            self::STATUS_BERHENTI => 'dark',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    // Relationships
    public function pengajuanTugasAkhir()
    {
        return $this->belongsTo(PengajuanTugasAkhir::class, 'pengajuan_tugas_akhir_id');
    }


    public function dosen(): BelongsTo
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    public function logBimbingans(): HasMany
    {
        return $this->hasMany(LogBimbingan::class, 'bimbingan_id');
    }

    // Accessor untuk mendapatkan mahasiswa melalui pengajuan tugas akhir
    public function getMahasiswaAttribute()
    {
        return $this->pengajuanTugasAkhir->mahasiswa ?? null;
    }

    // Scopes
    public function scopeByDosen($query, $dosenId)
    {
        return $query->where('dosen_id', $dosenId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByJabatanPembimbing($query, $jabatan)
    {
        return $query->where('jabatan_pembimbing', $jabatan);
    }

    public function scopeAktif($query)
    {
        return $query->where('status', self::STATUS_AKTIF);
    }

    public function scopeDiajukan($query)
    {
        return $query->where('status', self::STATUS_DIAJUKAN);
    }

    // Helper methods
    public function canBeApproved()
    {
        return $this->status === self::STATUS_DIAJUKAN;
    }

    public function canBeRejected()
    {
        return $this->status === self::STATUS_DIAJUKAN;
    }

    public function isActive()
    {
        return $this->status === self::STATUS_AKTIF;
    }

    public function isDiajukan()
    {
        return $this->status === self::STATUS_DIAJUKAN;
    }

    public function getProgressPercentage()
    {
        $totalLogs = $this->logBimbingans()->count();
        if ($totalLogs === 0) return 0;

        $averageProgress = $this->logBimbingans()->avg('progress');
        return round($averageProgress, 2);
    }
}
