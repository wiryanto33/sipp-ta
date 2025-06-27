<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\JadwalSidang;
use App\Models\PengajuanTugasAkhir;
use App\Models\PengujiSidang;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class JadwalSidangController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view jadwal-sidang', only: ['index']),
            new Middleware('permission:show jadwal-sidang', only: ['show']),
            new Middleware('permission:edit jadwal-sidang', only: ['edit']),
            new Middleware('permission:create jadwal-sidang', only: ['create']),
            new Middleware('permission:delete jadwal-sidang', only: ['destroy']),

            new Middleware('permission:edit update-status', only: ['edit']),
            new Middleware('permission:edit update-kehadiran', only: ['edit']),

        ];
    }

    public function index(Request $request)
    {
        $query = JadwalSidang::with(['tugasAkhir.mahasiswa.user', 'pengujiSidangs.dosen']);

        // Jika user adalah dosen, hanya tampilkan jadwal sidang dimana dia menjadi penguji
        if (Auth::user()->hasRole('dosen')) {
            $dosenId = Auth::user()->dosen->id;
            $query->whereHas('pengujiSidangs', function ($q) use ($dosenId) {
                $q->where('dosen_id', $dosenId);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by jenis
        if ($request->filled('jenis')) {
            $query->byJenis($request->jenis);
        }

        // Filter by date range
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_sidang', '>=', $request->tanggal_dari);
        }

        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_sidang', '<=', $request->tanggal_sampai);
        }

        $jadwalSidangs = $query->orderBy('tanggal_sidang', 'desc')->paginate(10);

        // dd(JadwalSidang::with('tugasAkhir')->first());


        return view('jadwal-sidang.index', compact('jadwalSidangs'));
    }

    public function create()
    {
        $tugasAkhirs = PengajuanTugasAkhir::with('mahasiswa')->get();
        $dosens = Dosen::all();

        // Cari tugas akhir milik mahasiswa yang sedang login
        $defaultTugasAkhirId = null;
        if (auth()->user()->mahasiswa) {
            $currentStudentTugasAkhir = $tugasAkhirs->where('mahasiswa_id', auth()->user()->mahasiswa->id)->first();
            $defaultTugasAkhirId = $currentStudentTugasAkhir ? $currentStudentTugasAkhir->id : null;
        }

        return view('jadwal-sidang.create', compact('tugasAkhirs', 'dosens', 'defaultTugasAkhirId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pengajuan_tugas_akhir_id' => 'required|exists:pengajuan_tugas_akhirs,id',
            'jenis_sidang' => 'required|in:proposal,skripsi,tugas_akhir',
            'tanggal_sidang' => 'required|date|after:now|date_format:Y-m-d\TH:i',
            'tempat_sidang' => 'required|string|max:255',
            'ruang_sidang' => 'nullable|string|max:255',
            'file_sidang' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'catatan' => 'nullable|string',
            'penguji' => 'required|array|min:1',
            'penguji.*.dosen_id' => 'required|exists:dosens,id',
            'penguji.*.peran' => 'required|in:ketua,sekretaris,penguji_1,penguji_2,pembimbing_1,pembimbing_2'
        ]);

        DB::beginTransaction();

        try {
            // Handle file upload
            $filePath = null;
            if ($request->hasFile('file_sidang')) {
                $filePath = $request->file('file_sidang')->store('jadwal-sidang', 'public');
            }

            // Buat jadwal sidang
            $jadwalSidang = JadwalSidang::create([
                'pengajuan_tugas_akhir_id' => $request->pengajuan_tugas_akhir_id,
                'jenis_sidang' => $request->jenis_sidang,
                'file_sidang' => $filePath,
                'tanggal_sidang' => Carbon::parse($request->tanggal_sidang)->format('Y-m-d H:i:s'),
                'tempat_sidang' => $request->tempat_sidang,
                'ruang_sidang' => $request->ruang_sidang,
                'status' => 'dijadwalkan',
                'catatan' => $request->catatan
            ]);

            // Tambah penguji
            foreach ($request->penguji as $penguji) {
                $jadwalSidang->pengujiSidangs()->create([
                    'dosen_id' => $penguji['dosen_id'],
                    'peran' => $penguji['peran'],
                    'hadir' => false
                ]);
            }

            DB::commit();

            return redirect()->route('jadwal-sidang.index')
                ->with('success', 'Jadwal sidang berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Gagal membuat jadwal sidang: ' . $e->getMessage());
        }
    }



    public function show(JadwalSidang $jadwalSidang)
    {
        $jadwalSidang->load(['tugasAkhir.mahasiswa', 'pengujiSidangs.dosen']);

        return view('jadwal-sidang.show', compact('jadwalSidang'));
    }

    public function edit(JadwalSidang $jadwalSidang)
    {
        // if (!$jadwalSidang->canBeEdited()) {
        //     return redirect()->route('jadwal-sidang.index')
        //         ->with('error', 'Jadwal sidang tidak dapat diedit');
        // }

        $tugasAkhirs = PengajuanTugasAkhir::with('mahasiswa')->get();
        $dosens = Dosen::all();
        $jadwalSidang->load('pengujiSidangs');

        return view('jadwal-sidang.edit', compact('jadwalSidang', 'tugasAkhirs', 'dosens'));
    }

    public function update(Request $request, JadwalSidang $jadwalSidang)
    {
        // if (!$jadwalSidang->canBeEdited()) {
        //     return redirect()->route('jadwal-sidang.index')
        //         ->with('error', 'Jadwal sidang tidak dapat diedit');
        // }

        $request->validate([
            // Fixed: Changed validation rule
            'pengajuan_tugas_akhir_id' => 'required|exists:pengajuan_tugas_akhirs,id',
            'jenis_sidang' => 'required|in:proposal,skripsi,tugas_akhir',
            'tanggal_sidang' => 'required|date|after:now|date_format:Y-m-d\TH:i',
            'tempat_sidang' => 'required|string|max:255',
            'ruang_sidang' => 'nullable|string|max:255',
            'file_sidang' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'catatan' => 'nullable|string',
            'penguji' => 'required|array|min:1',
            'penguji.*.dosen_id' => 'required|exists:dosens,id',
            'penguji.*.peran' => 'required|in:ketua,sekretaris,penguji_1,penguji_2,pembimbing_1,pembimbing_2'
        ]);

        DB::beginTransaction();

        try {
            // Handle file upload
            $filePath = $jadwalSidang->file_sidang;
            if ($request->hasFile('file_sidang')) {
                // Delete old file
                if ($filePath) {
                    Storage::disk('public')->delete($filePath);
                }
                $filePath = $request->file('file_sidang')->store('sidang-files', 'public');
            }

            // Update jadwal sidang
            // Fixed: Use pengajuan_tugas_akhir_id instead of tugas_akhir_id
            $jadwalSidang->update([
                'pengajuan_tugas_akhir_id' => $request->pengajuan_tugas_akhir_id,
                'file_sidang' => $filePath,
                'jenis_sidang' => $request->jenis_sidang,
                'tanggal_sidang' => Carbon::parse($request->tanggal_sidang)->format('Y-m-d H:i:s'),
                'tempat_sidang' => $request->tempat_sidang,
                'ruang_sidang' => $request->ruang_sidang,
                'catatan' => $request->catatan
            ]);

            // Delete existing penguji and create new ones
            $jadwalSidang->pengujiSidangs()->delete();

            foreach ($request->penguji as $penguji) {
                $jadwalSidang->pengujiSidangs()->create([
                    'dosen_id' => $penguji['dosen_id'],
                    'peran' => $penguji['peran'],
                    'hadir' => false
                ]);
            }

            DB::commit();

            return redirect()->route('jadwal-sidang.index')
                ->with('success', 'Jadwal sidang berhasil diupdate');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Gagal mengupdate jadwal sidang: ' . $e->getMessage());
        }
    }

    public function destroy(JadwalSidang $jadwalSidang)
    {
        if (!$jadwalSidang->canBeEdited()) {
            return redirect()->route('jadwal-sidang.index')
                ->with('error', 'Jadwal sidang tidak dapat dihapus');
        }

        try {
            // Delete file if exists
            if ($jadwalSidang->file_sidang) {
                Storage::disk('public')->delete($jadwalSidang->file_sidang);
            }

            $jadwalSidang->delete();

            return redirect()->route('jadwal-sidang.index')
                ->with('success', 'Jadwal sidang berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus jadwal sidang: ' . $e->getMessage());
        }
    }

    public function updateStatus(Request $request, JadwalSidang $jadwalSidang)
    {
        $request->validate([
            'status' => 'required|in:dijadwalkan,berlangsung,selesai,ditunda,dibatalkan'
        ]);

        $jadwalSidang->update(['status' => $request->status]);

        return redirect()->route('jadwal-sidang.show', $jadwalSidang)
            ->with('success', 'Status sidang berhasil diupdate');
    }

    public function updateKehadiran(Request $request, JadwalSidang $jadwalSidang)
    {
        $request->validate([
            'kehadiran' => 'required|array',
            'kehadiran.*' => 'boolean'
        ]);

        foreach ($request->kehadiran as $pengujiId => $hadir) {
            $jadwalSidang->pengujiSidangs()
                ->where('id', $pengujiId)
                ->update(['hadir' => $hadir]);
        }

        return redirect()->route('jadwal-sidang.show', $jadwalSidang)
            ->with('success', 'Kehadiran penguji berhasil diupdate');
    }

    public function calendar()
    {
        $jadwalSidangs = JadwalSidang::with(['tugasAkhir.mahasiswa', 'pengujiSidangs'])
            ->upcoming()
            ->orderBy('tanggal_sidang')
            ->get();

        return view('jadwal-sidang.calendar', compact('jadwalSidangs'));
    }

    // public function downloadFile(JadwalSidang $jadwalSidang)
    // {
    //     if (!$jadwalSidang->file_sidang || !Storage::disk('public')->exists($jadwalSidang->file_sidang)) {
    //         return back()->with('error', 'File tidak ditemukan');
    //     }

    //     return Storage::disk('public')->download($jadwalSidang->file_sidang);
    // }


    public function downloadFile(PengujiSidang $pengujiSidang)
    {
        if (!$pengujiSidang->file_sidang || !Storage::disk('public')->exists($pengujiSidang->file_sidang)) {
            return back()->with('error', 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($pengujiSidang->file_sidang);
    }
}
