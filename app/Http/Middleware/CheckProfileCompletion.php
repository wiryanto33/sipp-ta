<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileCompletion
{
    use HasRoles;

    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $isIncomplete = false;

            // Cek apakah user adalah mahasiswa
            if ($user->hasRole('mahasiswa') && $user->mahasiswa) {
                $mhs = $user->mahasiswa;
                $isIncomplete = empty($mhs->prodi_id)
                    || empty($mhs->angkatan)
                    || empty($mhs->semester)
                    || empty($mhs->phone)
                    || empty($mhs->alamat);
            }

            // Cek apakah user adalah dosen
            if ($user->hasRole('dosen') && $user->dosen) {
                $dsn = $user->dosen;
                $isIncomplete = empty($dsn->prodi_id)
                    || empty($dsn->nidn)
                    || empty($dsn->jabatan_akademik)
                    || empty($dsn->bidang_studi)
                    || empty($dsn->phone)
                    || empty($dsn->alamat);
            }

            // Set session jika belum lengkap
            if ($isIncomplete && !$request->session()->has('show_complete_profile_modal')) {
                session(['show_complete_profile_modal' => true]);
            }

            // Hapus session jika sudah lengkap
            if (!$isIncomplete) {
                session()->forget('show_complete_profile_modal');
            }
        }

        return $next($request);
    }
}
