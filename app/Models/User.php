<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    // Tambahkan trait ini untuk menggunakan fitur Spatie Permission


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'pangkat',
        'korps',
        'nrp',
        'image',
        'email',
        'status',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi ke Mahasiswa (One to One)
    public function mahasiswa()
    {
        return $this->hasOne(Mahasiswa::class);
    }

    // Relasi ke Dosen (One to One)
    public function dosen()
    {
        return $this->hasOne(Dosen::class, 'user_id', 'id');
    }

    // Relasi ke Kaprodi (One to One)
    public function kaprodi()
    {
        return $this->hasOne(Kaprodi::class, 'user_id', 'id');
    }

    // Scope untuk status aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    // Helper methods using Spatie roles
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isKoordinator()
    {
        return $this->hasRole('koordinator');
    }

    public function isDosen()
    {
        return $this->hasRole('dosen');
    }

    public function isMahasiswa()
    {
        return $this->hasRole('mahasiswa');
    }

    public function isKaprodi()
    {
        return $this->hasRole('kaprodi');
    }

    // Accessor untuk mendapatkan detail berdasarkan role
    public function getDetailAttribute()
    {
        if ($this->isDosen()) {
            return $this->dosen;
        } elseif ($this->isMahasiswa()) {
            return $this->mahasiswa;
        }
        return null;
    }

    // Boot method untuk handle cascade delete
    protected static function booted()
    {
        static::deleting(function ($user) {
            // Soft delete relasi
            if ($user->mahasiswa) {
                $user->mahasiswa->delete();
            }
            if ($user->dosen) {
                $user->dosen->delete();
            }
        });

        static::forceDeleting(function ($user) {
            // Force delete relasi
            if ($user->mahasiswa) {
                $user->mahasiswa->forceDelete();
            }
            if ($user->dosen) {
                $user->dosen->forceDelete();
            }
        });
    }

    public function pengajuanTugasAkhir()
    {
        return $this->hasMany(PengajuanTugasAkhir::class, 'mahasiswa_id');
    }
}
