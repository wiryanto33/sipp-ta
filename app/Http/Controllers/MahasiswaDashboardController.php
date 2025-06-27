<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Bimbingan;
use App\Models\PengajuanTugasAkhir;
use App\Models\LogBimbingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MahasiswaDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $mahasiswa = $user->mahasiswa;

        if (!$mahasiswa) {
            abort(403, 'Akses ditolak. Anda bukan mahasiswa.');
        }

        // Pengajuan Tugas Akhir mahasiswa
        $pengajuanTugasAkhir = PengajuanTugasAkhir::where('mahasiswa_id', $mahasiswa->id)->orderBy('created_at', 'desc')->first();

        // Bimbingan mahasiswa
        $bimbingan = null;
        $totalLogBimbingan = 0;
        $progressTerakhir = 0;
        $logBimbinganTerbaru = collect();

        if ($pengajuanTugasAkhir) {
            $bimbingan = Bimbingan::where('pengajuan_tugas_akhir_id', $pengajuanTugasAkhir->id)->first();

            if ($bimbingan) {
                $totalLogBimbingan = LogBimbingan::where('bimbingan_id', $bimbingan->id)->count();

                // Progress terakhir
                $logTerakhir = LogBimbingan::where('bimbingan_id', $bimbingan->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($logTerakhir) {
                    $progressTerakhir = $logTerakhir->progress;
                }

                // Log bimbingan terbaru (5 terakhir)
                $logBimbinganTerbaru = LogBimbingan::where('bimbingan_id', $bimbingan->id)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            }
        }

        // Chart progress bimbingan (6 bulan terakhir)
        $progressBulanan = [];
        if ($bimbingan) {
            for ($i = 5; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $avgProgress = LogBimbingan::where('bimbingan_id', $bimbingan->id)
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->avg('progress');

                $progressBulanan[] = [
                    'bulan' => $month->format('M Y'),
                    'progress' => round($avgProgress ?? 0, 1)
                ];
            }
        }

        // Status akademik
        $statusData = [
            'nrp' => $user->nrp,
            'nama' => $user->name,
            'angkatan' => $mahasiswa->angkatan,
            'status_pengajuan' => $pengajuanTugasAkhir ? $pengajuanTugasAkhir->status : 'belum_mengajukan',
            'judul_ta' => $pengajuanTugasAkhir ? $pengajuanTugasAkhir->judul : null,
            'pembimbing' => $bimbingan && $bimbingan->dosen ? $bimbingan->dosen->user->name : null,
            'status_bimbingan' => $bimbingan ? $bimbingan->status : null,
        ];

        // Notifikasi/reminder
        $notifications = [];

        // Cek jika belum mengajukan tugas akhir
        if (!$pengajuanTugasAkhir) {
            $notifications[] = [
                'type' => 'warning',
                'message' => 'Anda belum mengajukan proposal tugas akhir. Silakan ajukan segera.'
            ];
        }

        // Cek jika sudah lama tidak bimbingan
        if ($bimbingan && $logBimbinganTerbaru->isNotEmpty()) {
            $lastBimbingan = $logBimbinganTerbaru->first();
            $daysSinceLastBimbingan = Carbon::parse($lastBimbingan->created_at)->diffInDays(Carbon::now());

            if ($daysSinceLastBimbingan > 14) {
                $notifications[] = [
                    'type' => 'info',
                    'message' => "Bimbingan terakhir sudah $daysSinceLastBimbingan hari yang lalu. Jadwalkan bimbingan selanjutnya."
                ];
            }
        }

        // Cek progress rendah
        if ($progressTerakhir > 0 && $progressTerakhir < 30) {
            $notifications[] = [
                'type' => 'danger',
                'message' => 'Progress tugas akhir Anda masih rendah. Tingkatkan aktivitas bimbingan.'
            ];
        }

        return view('dashboard-mahasiswa', compact(
            'statusData',
            'pengajuanTugasAkhir',
            'bimbingan',
            'totalLogBimbingan',
            'progressTerakhir',
            'logBimbinganTerbaru',
            'progressBulanan',
            'notifications'
        ));

    }
}
