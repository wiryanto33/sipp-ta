<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\Bimbingan;
use App\Models\PengajuanTugasAkhir;
use App\Models\LogBimbingan;
use App\Models\JadwalBimbingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DosenDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $dosen = $user->dosen;

        if (!$dosen) {
            abort(403, 'Akses ditolak. Anda bukan dosen.');
        }

        // Statistik untuk cards di dashboard
        $totalMahasiswaBimbingan = Bimbingan::where('dosen_id', $dosen->id)
            ->whereIn('status', [Bimbingan::STATUS_AKTIF, Bimbingan::STATUS_SELESAI])
            ->count();

        $totalBimbinganAktif = Bimbingan::where('dosen_id', $dosen->id)
            ->where('status', Bimbingan::STATUS_AKTIF)
            ->count();

        // Mahasiswa siap sidang
        $mahasiswaSiapSidang = PengajuanTugasAkhir::whereHas('bimbingans', function ($query) use ($dosen) {
            $query->where('dosen_id', $dosen->id);
        })
            ->where('status', 'siap_sidang')
            ->count();

        // Total yang perlu review (pengajuan pending + bimbingan diajukan)
        $pengajuanPendingReview = PengajuanTugasAkhir::whereHas('bimbingans', function ($query) use ($dosen) {
            $query->where('dosen_id', $dosen->id);
        })
            ->where('status', 'diajukan')
            ->count();

        $pengajuanBimbingan = Bimbingan::where('dosen_id', $dosen->id)
            ->where('status', Bimbingan::STATUS_DIAJUKAN)
            ->count();

        $perluReview = $pengajuanPendingReview + $pengajuanBimbingan;

        // Data untuk tabel Progress Mahasiswa Bimbingan
        $mahasiswaProgress = Bimbingan::with(['pengajuanTugasAkhir.mahasiswa.user'])
            ->where('dosen_id', $dosen->id)
            ->where('status', Bimbingan::STATUS_AKTIF)
            ->get()
            ->map(function ($bimbingan) {
                $totalLog = LogBimbingan::where('bimbingan_id', $bimbingan->id)->count();
                $lastLog = LogBimbingan::where('bimbingan_id', $bimbingan->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                // Hitung progress berdasarkan total log dan progress terakhir
                $progress = 0;
                if ($lastLog) {
                    $progress = $lastLog->progress;
                } elseif ($totalLog > 0) {
                    // Jika ada log tapi tidak ada progress, estimasi dari jumlah log
                    $progress = min(($totalLog * 10), 100); // setiap log = 10% progress
                }

                return (object) [
                    'id' => $bimbingan->id,
                    'user' => $bimbingan->pengajuanTugasAkhir->mahasiswa->user,
                    'progress' => $progress,
                    'last_update' => $lastLog ? $lastLog->created_at : $bimbingan->created_at,
                ];
            })
            ->take(5); // Ambil 5 teratas untuk dashboard

        // Jadwal Bimbingan Hari Ini
        $jadwalHariIni = Bimbingan::with(['pengajuanTugasAkhir.mahasiswa.user'])
            ->where('dosen_id', $dosen->id)
            ->where('status', Bimbingan::STATUS_AKTIF)
            ->whereDate('tanggal_mulai', Carbon::today())
            ->orderBy('tanggal_mulai')
            ->get()
            ->map(function ($bimbingan) {
                return (object) [
                    'mahasiswa' => $bimbingan->pengajuanTugasAkhir->mahasiswa,
                    'waktu' => '09:00', // Default waktu atau bisa ditambahkan field waktu
                    'status' => 'scheduled',
                    'keterangan' => 'Bimbingan rutin'
                ];
            });

        // Chart Bimbingan Bulanan (6 bulan terakhir)
        $bimbinganBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = LogBimbingan::whereHas('bimbingan', function ($query) use ($dosen) {
                $query->where('dosen_id', $dosen->id);
            })
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $bimbinganBulanan[] = [
                'bulan' => $month->format('M Y'),
                'total' => $count
            ];
        }

        // Pengajuan Bimbingan Baru (untuk sidebar)
        $pengajuanBaru = Bimbingan::with(['pengajuanTugasAkhir.mahasiswa.user'])
            ->where('dosen_id', $dosen->id)
            ->where('status', Bimbingan::STATUS_DIAJUKAN)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($bimbingan) {
                return (object) [
                    'mahasiswa' => $bimbingan->pengajuanTugasAkhir->mahasiswa,
                    'judul' => $bimbingan->pengajuanTugasAkhir->judul,
                    'created_at' => $bimbingan->created_at,
                ];
            });

        // Distribusi Progress untuk chart donut
        $progressDistribution = [];
        $progressCounts = [
            'Rendah (< 30%)' => 0,
            'Sedang (30-70%)' => 0,
            'Tinggi (> 70%)' => 0
        ];

        foreach ($mahasiswaProgress as $mahasiswa) {
            if ($mahasiswa->progress < 30) {
                $progressCounts['Rendah (< 30%)']++;
            } elseif ($mahasiswa->progress <= 70) {
                $progressCounts['Sedang (30-70%)']++;
            } else {
                $progressCounts['Tinggi (> 70%)']++;
            }
        }

        // Jika tidak ada data progress, berikan data default
        if (empty($progressDistribution)) {
            $progressDistribution = [
                ['category' => 'Belum Ada Data', 'count' => 1]
            ];
        }

        return view('dashboard-dosen', compact(
            'totalMahasiswaBimbingan',
            'totalBimbinganAktif',
            'mahasiswaSiapSidang',
            'perluReview',
            'mahasiswaProgress',
            'jadwalHariIni',
            'bimbinganBulanan',
            'pengajuanBaru',
            'progressDistribution'
        ));
    }
}
