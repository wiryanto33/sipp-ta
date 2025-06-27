<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class LogBimbingan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'bimbingan_id',
        'tanggal_bimbingan',
        'materi_bimbingan',
        'saran_dosen',
        'progress',
        'file_bimbingan',
    ];

    protected $casts = [
        'tanggal_bimbingan' => 'date',
    ];

    // Relationships
    public function bimbingan(): BelongsTo
    {
        return $this->belongsTo(Bimbingan::class, 'bimbingan_id');
    }

    // PERBAIKAN: Accessor untuk mendapatkan URL file yang dapat diakses browser
    public function getFileUrlAttribute()
    {
        if (!$this->file_bimbingan) {
            return null;
        }

        // Menggunakan Storage::url() untuk mendapatkan URL yang dapat diakses browser
        return Storage::disk('public')->url($this->file_bimbingan);
    }

    // Accessor untuk mendapatkan path absolut file (untuk keperluan internal)
    public function getFilePathAttribute()
    {
        if (!$this->file_bimbingan) {
            return null;
        }

        return Storage::disk('public')->path($this->file_bimbingan);
    }

    // Accessor untuk mendapatkan nama file tanpa path
    public function getFileNameAttribute()
    {
        if (!$this->file_bimbingan) {
            return null;
        }

        return basename($this->file_bimbingan);
    }

    // Accessor untuk progress dengan format persentase
    public function getProgressPercentageAttribute()
    {
        return $this->progress . '%';
    }

    // Scopes
    public function scopeByBimbingan($query, $bimbinganId)
    {
        return $query->where('bimbingan_id', $bimbinganId);
    }

    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal_bimbingan', $tanggal);
    }

    public function scopeByProgress($query, $progress)
    {
        return $query->where('progress', '>=', $progress);
    }

    public function scopeOrderByTanggal($query, $direction = 'desc')
    {
        return $query->orderBy('tanggal_bimbingan', $direction);
    }

    // Helper methods
    public function hasFile()
    {
        return !empty($this->file_bimbingan) && Storage::disk('public')->exists($this->file_bimbingan);
    }

    public function getFileSize()
    {
        if (!$this->hasFile()) {
            return null;
        }

        try {
            return Storage::disk('public')->size($this->file_bimbingan);
        } catch (\Exception $e) {
            return null;
        }
    }


    public function getFileSizeHuman()
    {
        $size = $this->getFileSize();
        if (!$size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    // TAMBAHAN: Method untuk mendapatkan MIME type file

    public function getFileMimeType()
    {
        if (!$this->hasFile()) {
            return null;
        }

        try {
            return Storage::disk('public')->mimeType($this->file_bimbingan);
        } catch (\Exception $e) {
            return null;
        }
    }



    // TAMBAHAN: Method untuk check apakah file bisa di-download
    public function isDownloadable()
    {
        return $this->hasFile();
    }

    // Boot method untuk handle file deletion
    protected static function booted()
    {
        static::deleting(function ($logBimbingan) {
            if ($logBimbingan->file_bimbingan && Storage::disk('public')->exists($logBimbingan->file_bimbingan)) {
                Storage::disk('public')->delete($logBimbingan->file_bimbingan);
            }
        });
    }
}
