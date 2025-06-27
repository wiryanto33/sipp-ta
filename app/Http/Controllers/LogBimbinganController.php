<?php

namespace App\Http\Controllers;

use App\Models\Bimbingan;
use App\Models\LogBimbingan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LogBimbinganController extends Controller
{
    public function index(Bimbingan $bimbingan)
    {
        $user = Auth::user();

        // Cek akses
        if ($user->isMahasiswa()) {
            if ($bimbingan->pengajuanTugasAkhir->mahasiswa_id != $user->mahasiswa->id) {
                abort(403, 'Anda tidak memiliki akses ke log bimbingan ini.');
            }
        } elseif ($user->isDosen()) {
            if ($bimbingan->dosen_id != $user->dosen->id) {
                abort(403, 'Anda tidak memiliki akses ke log bimbingan ini.');
            }
        }

        if (!$bimbingan->isActive()) {
            return redirect()->route('bimbingan.index')
                ->with('error', 'Log bimbingan hanya dapat diakses untuk bimbingan yang sedang aktif.');
        }

        $logBimbingans = LogBimbingan::byBimbingan($bimbingan->id)
            ->orderByTanggal()
            ->paginate(10);
            // dd($logBimbingans);

        $bimbingan->load([
            'pengajuanTugasAkhir.mahasiswa.user',
            'dosen.user'
        ]);

        return view('log-bimbingan.index', compact('bimbingan', 'logBimbingans'));
    }

    public function create(Bimbingan $bimbingan)
    {
        $user = Auth::user();

        // Cek akses - hanya mahasiswa yang bisa membuat log bimbingan
        if (!$user->isMahasiswa()) {
            abort(403, 'Hanya mahasiswa yang dapat membuat log bimbingan.');
        }

        if ($bimbingan->pengajuanTugasAkhir->mahasiswa_id != $user->mahasiswa->id) {
            abort(403, 'Anda tidak memiliki akses ke bimbingan ini.');
        }

        if (!$bimbingan->isActive()) {
            return redirect()->route('log-bimbingan.index', $bimbingan)
                ->with('error', 'Log bimbingan hanya dapat dibuat untuk bimbingan yang sedang aktif.');
        }

        $bimbingan->load([
            'pengajuanTugasAkhir.mahasiswa.user',
            'dosen.user'
        ]);

        return view('log-bimbingan.create', compact('bimbingan'));
    }

    public function store(Request $request, Bimbingan $bimbingan)
    {
        $user = Auth::user();

        if (!$user->isMahasiswa()) {
            abort(403, 'Hanya mahasiswa yang dapat membuat log bimbingan.');
        }

        if ($bimbingan->pengajuanTugasAkhir->mahasiswa_id != $user->mahasiswa->id) {
            abort(403, 'Anda tidak memiliki akses ke bimbingan ini.');
        }

        $request->validate([
            'tanggal_bimbingan' => 'required|date|before_or_equal:today',
            'materi_bimbingan' => 'required|string|max:2000',
            'progress' => 'required|integer|min:0|max:100',
            'file_bimbingan' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'bimbingan_id' => $bimbingan->id,
                'tanggal_bimbingan' => $request->tanggal_bimbingan,
                'materi_bimbingan' => $request->materi_bimbingan,
                'progress' => $request->progress,
            ];

            // Handle file upload
            if ($request->hasFile('file_bimbingan')) {
                $file = $request->file('file_bimbingan');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('bimbingan-files', $fileName, 'public');
                $data['file_bimbingan'] = $filePath;
            }

            LogBimbingan::create($data);

            DB::commit();

            return redirect()->route('log-bimbingan.index', $bimbingan)
                ->with('success', 'Log bimbingan berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Bimbingan $bimbingan, LogBimbingan $logBimbingan)
    {
        $user = Auth::user();

        // Cek akses
        if ($user->isMahasiswa()) {
            if ($bimbingan->pengajuanTugasAkhir->mahasiswa_id != $user->mahasiswa->id) {
                abort(403, 'Anda tidak memiliki akses ke log bimbingan ini.');
            }
        } elseif ($user->isDosen()) {
            if ($bimbingan->dosen_id != $user->dosen->id) {
                abort(403, 'Anda tidak memiliki akses ke log bimbingan ini.');
            }
        }

        // Pastikan log bimbingan milik bimbingan yang benar
        if ($logBimbingan->bimbingan_id != $bimbingan->id) {
            abort(404);
        }

        $bimbingan->load([
            'pengajuanTugasAkhir.mahasiswa.user',
            'dosen.user'
        ]);

        return view('log-bimbingan.show', compact('bimbingan', 'logBimbingan'));
    }

    public function edit(Bimbingan $bimbingan, LogBimbingan $logBimbingan)
    {
        $user = Auth::user();

        // Cek akses - hanya mahasiswa yang bisa mengedit log bimbingan yang belum ada saran dosen
        if (!$user->isMahasiswa()) {
            abort(403, 'Hanya mahasiswa yang dapat mengedit log bimbingan.');
        }

        if ($bimbingan->pengajuanTugasAkhir->mahasiswa_id != $user->mahasiswa->id) {
            abort(403, 'Anda tidak memiliki akses ke bimbingan ini.');
        }

        // Pastikan log bimbingan milik bimbingan yang benar
        if ($logBimbingan->bimbingan_id != $bimbingan->id) {
            abort(404);
        }

        // Cek apakah log sudah ada saran dosen (tidak bisa diedit lagi)
        if (!empty($logBimbingan->saran_dosen)) {
            return redirect()->route('log-bimbingan.show', [$bimbingan, $logBimbingan])
                ->with('error', 'Log bimbingan yang sudah ada saran dosen tidak dapat diedit.');
        }

        $bimbingan->load([
            'pengajuanTugasAkhir.mahasiswa.user',
            'dosen.user'
        ]);

        return view('log-bimbingan.edit', compact('bimbingan', 'logBimbingan'));
    }

    public function update(Request $request, Bimbingan $bimbingan, LogBimbingan $logBimbingan)
    {
        $user = Auth::user();

        if (!$user->isMahasiswa()) {
            abort(403, 'Hanya mahasiswa yang dapat mengedit log bimbingan.');
        }

        if ($bimbingan->pengajuanTugasAkhir->mahasiswa_id != $user->mahasiswa->id) {
            abort(403, 'Anda tidak memiliki akses ke bimbingan ini.');
        }

        // Pastikan log bimbingan milik bimbingan yang benar
        if ($logBimbingan->bimbingan_id != $bimbingan->id) {
            abort(404);
        }

        // Cek apakah log sudah ada saran dosen
        if (!empty($logBimbingan->saran_dosen)) {
            return redirect()->back()
                ->with('error', 'Log bimbingan yang sudah ada saran dosen tidak dapat diedit.');
        }

        $request->validate([
            'tanggal_bimbingan' => 'required|date|before_or_equal:today',
            'materi_bimbingan' => 'required|string|max:2000',
            'progress' => 'required|integer|min:0|max:100',
            'file_bimbingan' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'tanggal_bimbingan' => $request->tanggal_bimbingan,
                'materi_bimbingan' => $request->materi_bimbingan,
                'progress' => $request->progress,
            ];

            // Handle file upload
            // Handle file upload
            if ($request->hasFile('file_bimbingan')) {
                // Delete old file if exists - FIX: Use correct disk
                if ($logBimbingan->file_bimbingan && Storage::disk('public')->exists($logBimbingan->file_bimbingan)) {
                    Storage::disk('public')->delete($logBimbingan->file_bimbingan);
                }

                $file = $request->file('file_bimbingan');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('bimbingan-files', $fileName, 'public');
                $data['file_bimbingan'] = $filePath;
            }

            $logBimbingan->update($data);

            DB::commit();

            return redirect()->route('log-bimbingan.show', [$bimbingan, $logBimbingan])
                ->with('success', 'Log bimbingan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function addSaran(Request $request, Bimbingan $bimbingan, LogBimbingan $logBimbingan)
    {
        $user = Auth::user();

        // Cek akses - hanya dosen pembimbing yang bisa menambah saran
        if (!$user->isDosen() || $bimbingan->dosen_id != $user->dosen->id) {
            abort(403, 'Anda tidak memiliki akses untuk menambahkan saran pada log bimbingan ini.');
        }

        // Pastikan log bimbingan milik bimbingan yang benar
        if ($logBimbingan->bimbingan_id != $bimbingan->id) {
            abort(404);
        }

        $request->validate([
            'saran_dosen' => 'required|string|max:2000',
        ]);

        DB::beginTransaction();
        try {
            $logBimbingan->update([
                'saran_dosen' => $request->saran_dosen,
            ]);

            DB::commit();

            return redirect()->route('log-bimbingan.show', [$bimbingan, $logBimbingan])
                ->with('success', 'Saran berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Bimbingan $bimbingan, LogBimbingan $logBimbingan)
    {
        $user = Auth::user();

        // Cek akses - hanya mahasiswa yang bisa menghapus log yang belum ada saran dosen
        if (!$user->isMahasiswa()) {
            abort(403, 'Hanya mahasiswa yang dapat menghapus log bimbingan.');
        }

        if ($bimbingan->pengajuanTugasAkhir->mahasiswa_id != $user->mahasiswa->id) {
            abort(403, 'Anda tidak memiliki akses ke bimbingan ini.');
        }

        // Pastikan log bimbingan milik bimbingan yang benar
        if ($logBimbingan->bimbingan_id != $bimbingan->id) {
            abort(404);
        }

        // Cek apakah log sudah ada saran dosen
        if (!empty($logBimbingan->saran_dosen)) {
            return redirect()->back()
                ->with('error', 'Log bimbingan yang sudah ada saran dosen tidak dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            $logBimbingan->delete();
            DB::commit();

            return redirect()->route('log-bimbingan.index', $bimbingan)
                ->with('success', 'Log bimbingan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function downloadFile(Bimbingan $bimbingan, LogBimbingan $logBimbingan)
    {
        $user = Auth::user();

        // Cek akses
        if ($user->isMahasiswa()) {
            if ($bimbingan->pengajuanTugasAkhir->mahasiswa_id != $user->mahasiswa->id) {
                abort(403, 'Anda tidak memiliki akses ke file ini.');
            }
        } elseif ($user->isDosen()) {
            if ($bimbingan->dosen_id != $user->dosen->id) {
                abort(403, 'Anda tidak memiliki akses ke file ini.');
            }
        }

        // Pastikan log bimbingan milik bimbingan yang benar
        if ($logBimbingan->bimbingan_id != $bimbingan->id) {
            abort(404);
        }

        if (!$logBimbingan->hasFile()) {
            abort(404, 'File tidak ditemukan.');
        }

        // FIX: Use the correct disk for download
        try {
            return Storage::disk('public')->download($logBimbingan->file_bimbingan, $logBimbingan->file_name);
        } catch (\Exception $e) {
            abort(404, 'File tidak dapat diunduh.');
        }
    }
}
