<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Role;

class Dosen extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'prodi_id',
        'role_id',
        'nidn',
        'jabatan_akademik',
        'bidang_studi',
        'phone',
        'alamat',
    ];

    // Relasi ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Relasi ke Bimbingan (untuk dashboard stats)
    public function bimbingans(): HasMany
    {
        return $this->hasMany(Bimbingan::class, 'dosen_id');
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

    // Scope untuk filter berdasarkan prodi
    public function scopeProdi($query, $prodiId)
    {
        return $query->where('prodi_id', $prodiId);
    }

    // Scope untuk filter berdasarkan jabatan akademik
    public function scopeJabatan($query, $jabatan)
    {
        return $query->where('jabatan_akademik', $jabatan);
    }

    public function getBimbinganAktifCountAttribute()
    {
        return $this->bimbingans()->where('status', Bimbingan::STATUS_AKTIF)->count();
    }
}
