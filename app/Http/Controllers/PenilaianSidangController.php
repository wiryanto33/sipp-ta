<?php

namespace App\Http\Controllers;

use App\Models\JadwalSidang;
use App\Models\PenilaianSidang;
use App\Models\PengujiSidang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PenilaianSidangController extends Controller implements HasMiddleware
{


    public static function middleware(): array
    {
        return [
            new Middleware('permission:view penilaian-sidang', only: ['index']),
            new Middleware('permission:view penilaian-sidang', only: ['show']),
            new Middleware('permission:edit penilaian-sidang', only: ['edit']),
            new Middleware('permission:create penilaian-sidang', only: ['create']),
            new Middleware('permission:delete penilaian-sidang', only: ['destroy']),
        ];
    }

    public function index()
    {
        $user = Auth::user();

        $query = PenilaianSidang::with(['jadwalSidang.tugasAkhir.mahasiswa.user', 'pengujiSidang.dosen.user']);

        // Jika user adalah dosen, hanya tampilkan penilaian yang dia buat
        if ($user->hasRole('dosen')) {
            $query->whereHas('pengujiSidang', function ($q) {
                $q->whereHas('dosen', function ($q2) {
                    $q2->where('user_id', Auth::id());
                });
            });
        }
        // Jika user adalah kaprodi, hanya tampilkan penilaian dari prodi yang dia pimpin
        elseif ($user->isKaprodi()) {
            $query->whereHas('jadwalSidang.tugasAkhir.mahasiswa', function ($q) use ($user) {
                $q->where('prodi_id', $user->kaprodi->prodi->id);
            });
        } elseif ($user->hasRole('mahasiswa')) {
            // Jika user adalah mahasiswa, hanya tampilkan penilaian untuk tugas akhir miliknya
            $query->whereHas('jadwalSidang.tugasAkhir.mahasiswa', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        $penilaianSidangs = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('penilaian_sidang.index', compact('penilaianSidangs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Cek apakah ada parameter jadwal_sidang
        $jadwalSidangId = $request->get('jadwal_sidang');
        // dd($jadwalSidangId);

        if (!$jadwalSidangId) {
            return redirect()->route('jadwal-sidang.index')
                ->with('error', 'Jadwal sidang tidak ditemukan');
        }

        // Ambil jadwal sidang dengan relasi yang diperlukan
        $jadwalSidang = JadwalSidang::with([
            'tugasAkhir.mahasiswa.user',
            'pengujiSidangs.dosen.user'
        ])->findOrFail($jadwalSidangId);

        // Cek apakah user adalah dosen penguji untuk sidang ini
        $pengujiSidang = $jadwalSidang->pengujiSidangs
            ->firstWhere('dosen.user_id', Auth::id());

            // dd($pengujiSidang);

        if (!$pengujiSidang) {
            return redirect()->route('jadwal-sidang.show', $jadwalSidang)
                ->with('error', 'Anda tidak terdaftar sebagai penguji untuk sidang ini');
        }

        // Cek apakah sudah ada penilaian dari dosen ini untuk sidang ini
        $existingPenilaian = PenilaianSidang::where('jadwal_sidang_id', $jadwalSidang->id)
            ->where('penguji_sidang_id', $pengujiSidang->id)
            ->first();

        if ($existingPenilaian) {
            return redirect()->route('jadwal-sidang.show', $jadwalSidang)
                ->with('error', 'Anda sudah memberikan penilaian untuk sidang ini');
        }

        if ($jadwalSidang->status !== 'berlangsung') {
            return redirect()->route('jadwal-sidang.show', $jadwalSidang->id)
                ->with('error', 'Penilaian hanya dapat diberikan ketika sidang sedang berlangsung. Status saat ini: ' . $jadwalSidang->status);
        }

        return view('penilaian_sidang.create', compact('jadwalSidang', 'pengujiSidang'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'jadwal_sidang_id' => 'required|exists:jadwal_sidangs,id',
            'penguji_sidang_id' => 'required|exists:penguji_sidangs,id',

            // Materi Skripsi (Bobot 0.5)
            'originalitas_materi' => 'required|numeric|min:0|max:100',
            'analisa_metodologi' => 'required|numeric|min:0|max:100',
            'tingkat_aplikasi_materi' => 'required|numeric|min:0|max:100',
            'pengembangan_kreativitas' => 'required|numeric|min:0|max:100',
            'tata_tulis' => 'required|numeric|min:0|max:100',

            // Penyajian (Bobot 0.3)
            'penguasaan_materi' => 'required|numeric|min:0|max:100',
            'sikap_dan_penampilan' => 'required|numeric|min:0|max:100',
            'penyajian_sarana_sistematika' => 'required|numeric|min:0|max:100',

            // Diskusi & Tanya Jawab (Bobot 0.2)
            'hasil_yang_dicapai' => 'required|numeric|min:0|max:100',
            'penguasaan_materi_diskusi' => 'required|numeric|min:0|max:100',
            'objektivitas_tanggapan' => 'required|numeric|min:0|max:100',
            'kemampuan_mempertahankan_ide' => 'required|numeric|min:0|max:100',
        ]);

        // Validasi tambahan
        $jadwalSidang = JadwalSidang::findOrFail($request->jadwal_sidang_id);
        $pengujiSidang = PengujiSidang::findOrFail($request->penguji_sidang_id);

        // Cek apakah user adalah dosen yang bersangkutan
        if ($pengujiSidang->dosen->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk memberikan penilaian ini');
        }

        // Cek apakah sudah ada penilaian
        $existingPenilaian = PenilaianSidang::where('jadwal_sidang_id', $request->jadwal_sidang_id)
            ->where('penguji_sidang_id', $request->penguji_sidang_id)
            ->first();

        if ($existingPenilaian) {
            return back()->with('error', 'Anda sudah memberikan penilaian untuk sidang ini');
        }

        DB::beginTransaction();

        try {
            // Hitung nilai akhir berdasarkan bobot
            $materiSkripsi = (
                $request->originalitas_materi +
                $request->analisa_metodologi +
                $request->tingkat_aplikasi_materi +
                $request->pengembangan_kreativitas +
                $request->tata_tulis
            ) / 5 * 0.5;

            $penyajian = (
                $request->penguasaan_materi +
                $request->sikap_dan_penampilan +
                $request->penyajian_sarana_sistematika
            ) / 3 * 0.3;

            $diskusi = (
                $request->hasil_yang_dicapai +
                $request->penguasaan_materi_diskusi +
                $request->objektivitas_tanggapan +
                $request->kemampuan_mempertahankan_ide
            ) / 4 * 0.2;

            $nilaiAkhir = $materiSkripsi + $penyajian + $diskusi;

            // Simpan penilaian
            PenilaianSidang::create([
                'jadwal_sidang_id' => $request->jadwal_sidang_id,
                'penguji_sidang_id' => $request->penguji_sidang_id,

                // Materi Skripsi
                'originalitas_materi' => $request->originalitas_materi,
                'analisa_metodologi' => $request->analisa_metodologi,
                'tingkat_aplikasi_materi' => $request->tingkat_aplikasi_materi,
                'pengembangan_kreativitas' => $request->pengembangan_kreativitas,
                'tata_tulis' => $request->tata_tulis,

                // Penyajian
                'penguasaan_materi' => $request->penguasaan_materi,
                'sikap_dan_penampilan' => $request->sikap_dan_penampilan,
                'penyajian_sarana_sistematika' => $request->penyajian_sarana_sistematika,

                // Diskusi
                'hasil_yang_dicapai' => $request->hasil_yang_dicapai,
                'penguasaan_materi_diskusi' => $request->penguasaan_materi_diskusi,
                'objektivitas_tanggapan' => $request->objektivitas_tanggapan,
                'kemampuan_mempertahankan_ide' => $request->kemampuan_mempertahankan_ide,

                'nilai_akhir' => $nilaiAkhir
            ]);

            DB::commit();

            return redirect()->route('jadwal-sidang.show', $jadwalSidang)
                ->with('success', 'Penilaian berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PenilaianSidang $penilaianSidang)
    {
        $penilaianSidang->load([
            'jadwalSidang.tugasAkhir.mahasiswa.user',
            'pengujiSidang.dosen.user'
        ]);

        return view('penilaian_sidang.show', compact('penilaianSidang'));
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PenilaianSidang $penilaianSidang)
    {
        // Cek apakah user adalah dosen yang memberikan penilaian ini
        // if ($penilaianSidang->pengujiSidang->dosen->user_id !== Auth::id()) {
        //     return redirect()->route('penilaian-sidang.index')
        //         ->with('error', 'Anda tidak memiliki akses untuk mengedit penilaian ini');
        // }

        $penilaianSidang->load([
            'jadwalSidang.tugasAkhir.mahasiswa.user',
            'pengujiSidang.dosen.user'
        ]);

        return view('penilaian_sidang.edit', compact('penilaianSidang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PenilaianSidang $penilaianSidang)
    {
        // Cek apakah user adalah dosen yang memberikan penilaian ini
        // if ($penilaianSidang->pengujiSidang->dosen->user_id !== Auth::id()) {
        //     return redirect()->route('penilaian-sidang.index')
        //         ->with('error', 'Anda tidak memiliki akses untuk mengedit penilaian ini');
        // }

        $request->validate([
            // Materi Skripsi (Bobot 0.5)
            'originalitas_materi' => 'required|numeric|min:0|max:100',
            'analisa_metodologi' => 'required|numeric|min:0|max:100',
            'tingkat_aplikasi_materi' => 'required|numeric|min:0|max:100',
            'pengembangan_kreativitas' => 'required|numeric|min:0|max:100',
            'tata_tulis' => 'required|numeric|min:0|max:100',

            // Penyajian (Bobot 0.3)
            'penguasaan_materi' => 'required|numeric|min:0|max:100',
            'sikap_dan_penampilan' => 'required|numeric|min:0|max:100',
            'penyajian_sarana_sistematika' => 'required|numeric|min:0|max:100',

            // Diskusi & Tanya Jawab (Bobot 0.2)
            'hasil_yang_dicapai' => 'required|numeric|min:0|max:100',
            'penguasaan_materi_diskusi' => 'required|numeric|min:0|max:100',
            'objektivitas_tanggapan' => 'required|numeric|min:0|max:100',

            // Catatan (opsional)
            'catatan' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Hitung nilai akhir berdasarkan bobot
            $materiSkripsi = (
                $request->originalitas_materi +
                $request->analisa_metodologi +
                $request->tingkat_aplikasi_materi +
                $request->pengembangan_kreativitas +
                $request->tata_tulis
            ) / 5 * 0.5;

            $penyajian = (
                $request->penguasaan_materi +
                $request->sikap_dan_penampilan +
                $request->penyajian_sarana_sistematika +
                $request->hasil_yang_dicapai
            ) / 4 * 0.3;

            $diskusi = (
                $request->penguasaan_materi_diskusi +
                $request->objektivitas_tanggapan +
                $request->kemampuan_mempertahankan_ide
            ) / 3 * 0.2; // Ubah dari /4 menjadi /3 karena hanya ada 3 field

            $nilaiAkhir = $materiSkripsi + $penyajian + $diskusi;

            // Update penilaian
            $penilaianSidang->update([
                // Materi Skripsi
                'originalitas_materi' => $request->originalitas_materi,
                'analisa_metodologi' => $request->analisa_metodologi,
                'tingkat_aplikasi_materi' => $request->tingkat_aplikasi_materi,
                'pengembangan_kreativitas' => $request->pengembangan_kreativitas,
                'tata_tulis' => $request->tata_tulis,

                // Penyajian
                'penguasaan_materi' => $request->penguasaan_materi,
                'sikap_dan_penampilan' => $request->sikap_dan_penampilan,
                'penyajian_sarana_sistematika' => $request->penyajian_sarana_sistematika,
                'hasil_yang_dicapai' => $request->hasil_yang_dicapai,

                // Diskusi
                'penguasaan_materi_diskusi' => $request->penguasaan_materi_diskusi,
                'objektivitas_tanggapan' => $request->objektivitas_tanggapan,
                'kemampuan_mempertahankan_ide' => $request->kemampuan_mempertahankan_ide,

                // Nilai akhir dan catatan
                'nilai_akhir' => $nilaiAkhir,
                'catatan' => $request->catatan
            ]);

            DB::commit();

            return redirect()->route('penilaian-sidang.show', $penilaianSidang)
                ->with('success', 'Penilaian berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Gagal memperbarui penilaian: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PenilaianSidang $penilaianSidang)
    {
        // Cek apakah user adalah dosen yang memberikan penilaian ini atau admin
        if (
            $penilaianSidang->pengujiSidang->dosen->user_id !== Auth::id()
            && !Auth::user()->hasRole('admin')
        ) {
            return redirect()->route('penilaian-sidang.index')
                ->with('error', 'Anda tidak memiliki akses untuk menghapus penilaian ini');
        }

        try {
            $penilaianSidang->delete();

            return redirect()->route('penilaian-sidang.index')
                ->with('success', 'Penilaian berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus penilaian: ' . $e->getMessage());
        }
    }

   

    public function printPDF(PenilaianSidang $penilaianSidang)
    {
        // Load relasi yang diperlukan
        $penilaianSidang->load([
            'jadwalSidang.tugasAkhir.mahasiswa.user',
            'pengujiSidang.dosen.user'
        ]);


        // Generate PDF
        $pdf = Pdf::loadView('penilaian-sidang.pdf', compact('penilaianSidang'));

        // Set paper size dan orientation
        $pdf->setPaper('A4', 'portrait');

        // Set options untuk PDF
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ]);

        // Generate filename
        $mahasiswaNama = str_replace(' ', '_', $penilaianSidang->jadwalSidang->tugasAkhir->mahasiswa->user->name);
        $tanggalSidang = $penilaianSidang->jadwalSidang->tanggal_sidang->format('Y-m-d');
        $filename = "Penilaian_Sidang_{$mahasiswaNama}_{$tanggalSidang}.pdf";

        // Return PDF sebagai download
        return $pdf->download($filename);
    }

    /**
     * Stream PDF untuk preview
     */
    public function previewPDF(PenilaianSidang $penilaianSidang)
    {

        // Load relasi yang diperlukan
        $penilaianSidang->load([
            'jadwalSidang.tugasAkhir.mahasiswa.user',
            'pengujiSidang.dosen.user'
        ]);

        // Generate PDF
        $pdf = Pdf::loadView('penilaian_sidang.pdf', compact('penilaianSidang'));

        // Set paper size dan orientation
        $pdf->setPaper('A4', 'portrait');

        // Set options untuk PDF
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'Arial',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ]);

        // Return PDF untuk di-stream (preview di browser)
        return $pdf->stream('penilaian_sidang_preview.pdf');
    }
}
