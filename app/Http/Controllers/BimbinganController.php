<?php

namespace App\Http\Controllers;

use App\Models\Bimbingan;
use App\Models\Dosen;
use App\Models\JadwalSidang;
use App\Models\PengajuanTugasAkhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BimbinganController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isDosen()) {
            // Untuk dosen, tampilkan bimbingan yang diajukan ke dosen tersebut
            $bimbingans = Bimbingan::with(['pengajuanTugasAkhir.mahasiswa.user', 'dosen.user'])
                ->byDosen($user->dosen->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($user->isMahasiswa()) {
            // Untuk mahasiswa, tampilkan bimbingan yang diajukan oleh mahasiswa tersebut
            $pengajuanIds = PengajuanTugasAkhir::byMahasiswa($user->mahasiswa->id)->pluck('id');
            $bimbingans = Bimbingan::with(['pengajuanTugasAkhir.mahasiswa.user', 'dosen.user'])
                ->whereIn('pengajuan_tugas_akhir_id', $pengajuanIds)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // Untuk admin/koordinator, tampilkan semua bimbingan
            $bimbingans = Bimbingan::with(['pengajuanTugasAkhir.mahasiswa.user', 'dosen.user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }

        return view('bimbingan.index', compact('bimbingans'));
    }

    public function create()
    {
        $user = Auth::user();

        if (!$user->isMahasiswa()) {
            abort(403, 'Hanya mahasiswa yang dapat mengajukan bimbingan.');
        }

        // Ambil pengajuan tugas akhir yang sudah diterima
        $pengajuanTugasAkhir = PengajuanTugasAkhir::byMahasiswa($user->mahasiswa->id)
            ->where('status', PengajuanTugasAkhir::STATUS_DITERIMA)
            ->first();

        if (!$pengajuanTugasAkhir) {
            return redirect()->route('bimbingan.index')
                ->with('error', 'Anda belum memiliki pengajuan tugas akhir yang diterima.');
        }

        // cek apakah mahasiswa sudah memiliki jadwal sidang proposal dengan status selesai
        $jadwalSidangProposal = JadwalSidang::where('pengajuan_tugas_akhir_id', $pengajuanTugasAkhir->id)
            ->where('jenis_sidang', 'proposal')
            ->where('status', 'selesai')
            ->first();

        if (!$jadwalSidangProposal) {
            return redirect()->route('bimbingan.index')
                ->with('error', 'Anda harus menyelesaikan sidang proposal sebelum mengajukan bimbingan.');
        }

        // Ambil daftar dosen
        $dosens = Dosen::with('user')->get();

        return view('bimbingan.create', compact('pengajuanTugasAkhir', 'dosens'));
    }


    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user->isMahasiswa()) {
            abort(403, 'Hanya mahasiswa yang dapat mengajukan bimbingan.');
        }

        $request->validate([
            'pengajuan_tugas_akhir_id' => 'required|exists:pengajuan_tugas_akhirs,id',
            'dosen_id' => 'required|exists:dosens,id',
            'jabatan_pembimbing' => ['required', Rule::in(['pembimbing_1', 'pembimbing_2'])],
        ]);

        // Cek apakah pengajuan tugas akhir milik mahasiswa yang sedang login
        $pengajuanTugasAkhir = PengajuanTugasAkhir::where('id', $request->pengajuan_tugas_akhir_id)
            ->where('mahasiswa_id', $user->mahasiswa->id)
            ->first();

        if (!$pengajuanTugasAkhir) {
            return redirect()->back()->with('error', 'Pengajuan tugas akhir tidak valid.');
        }

        // Cek apakah sudah ada bimbingan dengan jabatan yang sama untuk pengajuan ini
        $existingBimbingan = Bimbingan::where('pengajuan_tugas_akhir_id', $request->pengajuan_tugas_akhir_id)
            ->where('jabatan_pembimbing', $request->jabatan_pembimbing)
            ->whereIn('status', [Bimbingan::STATUS_DIAJUKAN, Bimbingan::STATUS_DISETUJUI, Bimbingan::STATUS_AKTIF])
            ->first();

        if ($existingBimbingan) {
            return redirect()->back()->with('error', 'Sudah ada pengajuan bimbingan dengan jabatan yang sama.');
        }

        DB::beginTransaction();
        try {
            $bimbingan = Bimbingan::create([
                'pengajuan_tugas_akhir_id' => $request->pengajuan_tugas_akhir_id,
                'dosen_id' => $request->dosen_id,
                'jabatan_pembimbing' => $request->jabatan_pembimbing,
                'status' => Bimbingan::STATUS_DIAJUKAN,
                'tanggal_mulai' => now(),
            ]);

            DB::commit();

            return redirect()->route('bimbingan.index')
                ->with('success', 'Pengajuan bimbingan berhasil diajukan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Bimbingan $bimbingan)
    {
        $user = Auth::user();

        // Cek akses
        if ($user->isMahasiswa()) {
            if ($bimbingan->pengajuanTugasAkhir->mahasiswa_id != $user->mahasiswa->id) {
                abort(403, 'Anda tidak memiliki akses ke bimbingan ini.');
            }
        } elseif ($user->isDosen()) {
            if ($bimbingan->dosen_id != $user->dosen->id) {
                abort(403, 'Anda tidak memiliki akses ke bimbingan ini.');
            }
        }

        $bimbingan->load([
            'pengajuanTugasAkhir.mahasiswa.user',
            'dosen.user',
            'logBimbingans' => function ($query) {
                $query->orderBy('tanggal_bimbingan', 'desc');
            }
        ]);

        return view('bimbingan.show', compact('bimbingan'));
    }

    public function approve(Bimbingan $bimbingan)
    {
        $user = Auth::user();

        if (!$user->isDosen() || $bimbingan->dosen_id != $user->dosen->id) {
            abort(403, 'Anda tidak memiliki akses untuk menyetujui bimbingan ini.');
        }

        if (!$bimbingan->canBeApproved()) {
            return redirect()->back()->with('error', 'Bimbingan tidak dapat disetujui.');
        }

        DB::beginTransaction();
        try {
            $bimbingan->update([
                'status' => Bimbingan::STATUS_AKTIF,
                'tanggal_mulai' => now(),
            ]);

            // Update status pengajuan tugas akhir menjadi sedang bimbingan
            $bimbingan->pengajuanTugasAkhir->update([
                'status' => PengajuanTugasAkhir::STATUS_SEDANG_BIMBINGAN
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Bimbingan berhasil disetujui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, Bimbingan $bimbingan)
    {
        $user = Auth::user();

        if (!$user->isDosen() || $bimbingan->dosen_id != $user->dosen->id) {
            abort(403, 'Anda tidak memiliki akses untuk menolak bimbingan ini.');
        }

        if (!$bimbingan->canBeRejected()) {
            return redirect()->back()->with('error', 'Bimbingan tidak dapat ditolak.');
        }

        $request->validate([
            'alasan_penolakan' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            $bimbingan->update([
                'status' => Bimbingan::STATUS_DITOLAK,
                'alasan_penolakan' => $request->alasan_penolakan,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Bimbingan berhasil ditolak.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function complete(Bimbingan $bimbingan)
    {
        $user = Auth::user();

        if (!$user->isDosen() || $bimbingan->dosen_id != $user->dosen->id) {
            abort(403, 'Anda tidak memiliki akses untuk menyelesaikan bimbingan ini.');
        }

        if (!$bimbingan->isActive()) {
            return redirect()->back()->with('error', 'Bimbingan tidak sedang aktif.');
        }

        DB::beginTransaction();
        try {
            $bimbingan->update([
                'status' => Bimbingan::STATUS_SELESAI,
                'tanggal_selesai' => now(),
            ]);

            // Cek apakah semua bimbingan sudah selesai
            $activeBimbingans = Bimbingan::where('pengajuan_tugas_akhir_id', $bimbingan->pengajuan_tugas_akhir_id)
                ->where('status', Bimbingan::STATUS_AKTIF)
                ->count();

            if ($activeBimbingans == 0) {
                // Jika semua bimbingan sudah selesai, update status pengajuan
                $bimbingan->pengajuanTugasAkhir->update([
                    'status' => PengajuanTugasAkhir::STATUS_SIAP_SIDANG
                ]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Bimbingan berhasil diselesaikan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
