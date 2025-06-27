<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Bimbingan;
use App\Models\PengajuanTugasAkhir;
use App\Models\LogBimbingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DashboardController extends Controller
{
   
    public function index()
    {
        // Statistik Dasar
        $totalMahasiswa = Mahasiswa::count();
        $totalDosen = Dosen::count();
        $totalBimbinganAktif = Bimbingan::where('status', Bimbingan::STATUS_AKTIF)->count();
        $totalSiapSidang = PengajuanTugasAkhir::where('status', 'siap_sidang')->count();
        $totalPengajuanPending = PengajuanTugasAkhir::where('status', 'diajukan')->count();

        // Statistik Bimbingan per Status
        $bimbinganStats = Bimbingan::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        // Data untuk chart bimbingan bulanan (6 bulan terakhir)
        $bimbinganBulanan = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = Bimbingan::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $bimbinganBulanan[] = [
                'bulan' => $month->format('M Y'),
                'total' => $count
            ];
        }

        // Statistik Mahasiswa per Angkatan
        $mahasiswaPerAngkatan = Mahasiswa::select('angkatan', DB::raw('count(*) as total'))
            ->groupBy('angkatan')
            ->orderBy('angkatan', 'desc')
            ->limit(5)
            ->get();

        // Top 5 Dosen dengan Bimbingan Terbanyak
        $dosenTerbanyak = Dosen::select('dosens.*', DB::raw('COUNT(bimbingans.id) as bimbingans_count'))
            ->leftJoin('bimbingans', function ($join) {
                $join->on('dosens.id', '=', 'bimbingans.dosen_id')
                    ->where('bimbingans.status', '=', Bimbingan::STATUS_AKTIF)
                    ->whereNull('bimbingans.deleted_at');
            })
            ->with('user')
            ->whereNull('dosens.deleted_at')
            ->groupBy('dosens.id')
            ->orderBy('bimbingans_count', 'desc')
            ->limit(5)
            ->get();

        // Log Bimbingan Terbaru
        $logBimbinganTerbaru = LogBimbingan::with(['bimbingan.pengajuanTugasAkhir.mahasiswa.user', 'bimbingan.dosen.user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Statistik Progress Bimbingan
        $progressQuery = DB::table('bimbingans')
            ->leftJoin('log_bimbingans', 'bimbingans.id', '=', 'log_bimbingans.bimbingan_id')
            ->whereNull('bimbingans.deleted_at')
            ->whereNull('log_bimbingans.deleted_at')
            ->select('bimbingans.id', DB::raw('AVG(log_bimbingans.progress) as avg_progress'))
            ->groupBy('bimbingans.id')
            ->get();

        $progressStats = [
            'rendah' => $progressQuery->where('avg_progress', '<', 30)->count(),
            'sedang' => $progressQuery->whereBetween('avg_progress', [30, 70])->count(),
            'tinggi' => $progressQuery->where('avg_progress', '>', 70)->count(),
            'tanpa_log' => Bimbingan::whereDoesntHave('logBimbingans')->count(),
        ];

        // Pengajuan Tugas Akhir per Status
        $pengajuanStats = PengajuanTugasAkhir::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get()
            ->pluck('total', 'status')
            ->toArray();

        return view('dashboard', compact(
            'totalMahasiswa',
            'totalDosen',
            'totalBimbinganAktif',
            'totalSiapSidang',
            'totalPengajuanPending',
            'bimbinganStats',
            'bimbinganBulanan',
            'mahasiswaPerAngkatan',
            'dosenTerbanyak',
            'logBimbinganTerbaru',
            'progressStats',
            'pengajuanStats'
        ));
    }
}
