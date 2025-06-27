<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JadwalSidang extends Model
{
    use HasFactory;


    protected $fillable = [
        'pengajuan_tugas_akhir_id',
        'jenis_sidang',
        'file_sidang',
        'tanggal_sidang',
        'tempat_sidang',
        'ruang_sidang',
        'status',
        'catatan'
    ];

    protected $casts = [
        'tanggal_sidang' => 'datetime',
    ];

    // Relationships
    public function tugasAkhir()
    {
        return $this->belongsTo(PengajuanTugasAkhir::class, 'pengajuan_tugas_akhir_id');
    }

    public function pengujiSidangs()
    {
        return $this->hasMany(PengujiSidang::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_sidang', $jenis);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tanggal_sidang', Carbon::today());
    }

    public function scopeUpcoming($query)
    {
        return $query->whereDate('tanggal_sidang', '>=', Carbon::today())
            ->where('status', '!=', 'selesai');
    }

    // Accessors
    public function getFormattedTanggalAttribute()
    {
        return $this->tanggal_sidang->format('d F Y');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'dijadwalkan' => 'bg-info',
            'berlangsung' => 'bg-warning',
            'selesai' => 'bg-success',
            'ditunda' => 'bg-secondary',
            'dibatalkan' => 'bg-danger'
        ];

        return $badges[$this->status] ?? 'badge-secondary';
    }

    // Methods
    public function canBeEdited()
    {
        return in_array($this->status, ['dijadwalkan', 'ditunda']);
    }

    public function canBeStarted()
    {
        return $this->status === 'dijadwalkan' &&
            $this->tanggal_sidang->isToday();
    }

    // In App\Models\JadwalSidang.php

    public function penilaianSidangs()
    {
        return $this->hasMany(PenilaianSidang::class, 'jadwal_sidang_id');
    }
}
