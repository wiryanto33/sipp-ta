<?php

namespace App\Http\Controllers;

use App\Models\PengajuanTugasAkhir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PengajuanTugasAkhirController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('permission:view tugas-akhir', only: ['index']),
            new Middleware('permission:edit tugas-akhir', only: ['edit']),
            new Middleware('permission:create tugas-akhir', only: ['create']),
            new Middleware('permission:delete tugas-akhir', only: ['destroy']),
        ];
    }

    public function index()
    {
        $user = Auth::user(); // object User

        if ($user->isMahasiswa()) {
            // Mahasiswa hanya bisa melihat pengajuan sendiri
            $pengajuans = PengajuanTugasAkhir::with(['mahasiswa.user', 'mahasiswa.prodi'])
                ->byMahasiswa($user->mahasiswa->id) // ambil ID mahasiswa dari relasi
                ->latest()
                ->paginate(10);
        } elseif ($user->isKaprodi()) {
            //Kaprodi hanya bisa melihat pengajuan di prodi sendiri
            $pengajuans = PengajuanTugasAkhir::with(['mahasiswa.user', 'mahasiswa.prodi'])
                ->byProdi($user->kaprodi->prodi->id) // ambil ID prodi dari relasi mahasiswa
                ->latest()
                ->paginate(10);
        } else {
            // Admin/Koordinator/Dosen bisa melihat semua
            $pengajuans = PengajuanTugasAkhir::with(['mahasiswa.user', 'mahasiswa.prodi'])
                ->latest()
                ->paginate(10);
        }

        return view('pengajuan.index', compact('pengajuans'));
    }



    public function create()
    {
        $user = Auth::user();

        // Hanya mahasiswa yang bisa mengajukan
        if (!$user->isMahasiswa()) {
            abort(403, 'Hanya mahasiswa yang dapat mengajukan tugas akhir');
        }

        // Validasi relasi mahasiswa
        if (!$user->mahasiswa) {
            return redirect()->route('dashboard')
                ->with('error', 'Data mahasiswa tidak ditemukan. Silakan hubungi administrator.');
        }

        // Cek apakah sudah pernah mengajukan
        $existingPengajuan = PengajuanTugasAkhir::byMahasiswa($user->mahasiswa->id)
            ->whereIn('status', [
                PengajuanTugasAkhir::STATUS_DIAJUKAN,
                PengajuanTugasAkhir::STATUS_DITERIMA,
                PengajuanTugasAkhir::STATUS_SEDANG_BIMBINGAN,
                PengajuanTugasAkhir::STATUS_SIAP_SIDANG,
                PengajuanTugasAkhir::STATUS_LULUS
            ])->first();

        if ($existingPengajuan) {
            return redirect()->route('pengajuan-tugas-akhir.index')
                ->with('error', 'Anda sudah memiliki pengajuan tugas akhir yang aktif');
        }

        return view('pengajuan.create');
    }


    public function store(Request $request)
    {
        // Debug: Tambahkan logging untuk debugging
        $user = Auth::user();

        if (!$user->isMahasiswa()) {
            abort(403);
        }

        // Validasi tambahan: pastikan user memiliki relasi mahasiswa
        if (!$user->mahasiswa) {
            return redirect()->back()
                ->with('error', 'Data mahasiswa tidak ditemukan untuk user ini')
                ->withInput();
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'sinopsis' => 'required|string|min:100',
            'bidang_penelitian' => 'required|string|max:100',
            'file_proposal' => 'required|file|mimes:pdf|max:5120', // 5MB
        ], [
            'judul.required' => 'Judul tugas akhir harus diisi',
            'sinopsis.required' => 'Sinopsis harus diisi',
            'sinopsis.min' => 'Sinopsis minimal 100 karakter',
            'bidang_penelitian.required' => 'Bidang penelitian harus diisi',
            'file_proposal.required' => 'File proposal harus diupload',
            'file_proposal.mimes' => 'File proposal harus berformat PDF',
            'file_proposal.max' => 'File proposal maksimal 5MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->only(['judul', 'sinopsis', 'bidang_penelitian']);
            $data['mahasiswa_id'] = $user->mahasiswa->id;
            $data['status'] = PengajuanTugasAkhir::STATUS_DIAJUKAN;
            $data['tanggal_pengajuan'] = now();

            // Upload file proposal
            if ($request->hasFile('file_proposal')) {
                $file = $request->file('file_proposal');
                $filename = 'proposal_' . $user->mahasiswa->id . '_' . time() . '.pdf';
                $path = $file->storeAs('proposals', $filename, 'public');
                $data['file_proposal'] = $path;
            }

            $pengajuan = PengajuanTugasAkhir::create($data);

            return redirect()->route('pengajuan-tugas-akhir.index')
                ->with('success', 'Pengajuan tugas akhir berhasil disubmit');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }


    // In your controller
    public function show($id)
    {
        $pengajuan = PengajuanTugasAkhir::with(['mahasiswa.user', 'mahasiswa.prodi'])
            ->findOrFail($id);

        return view('pengajuan.show', compact('pengajuan'));
    }

    public function edit(PengajuanTugasAkhir $pengajuan)
    {
        // $this->authorize('update', $pengajuan);

        // Hanya bisa edit jika status draft atau ditolak
        if (!in_array($pengajuan->status, [PengajuanTugasAkhir::STATUS_DRAFT, PengajuanTugasAkhir::STATUS_DITOLAK])) {
            return redirect()->route('pengajuan-tugas-akhir.index')
                ->with('error', 'Pengajuan tidak dapat diedit pada status ini');
        }

        return view('pengajuan-tugas-akhir.edit', compact('pengajuan'));
    }

    public function update(Request $request, PengajuanTugasAkhir $pengajuan)
    {
        // $this->authorize('update', $pengajuan);

        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'sinopsis' => 'required|string|min:100',
            'bidang_penelitian' => 'required|string|max:100',
            'file_proposal' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = $request->only(['judul', 'sinopsis', 'bidang_penelitian']);

            // Upload file proposal baru jika ada
            if ($request->hasFile('file_proposal')) {
                // Hapus file lama
                if ($pengajuan->file_proposal) {
                    Storage::disk('public')->delete($pengajuan->file_proposal);
                }

                $file = $request->file('file_proposal');
                $filename = 'proposal_' . $pengajuan->mahasiswa_id . '_' . time() . '.pdf';
                $path = $file->storeAs('proposals', $filename, 'public');
                $data['file_proposal'] = $path;
            }

            // Update status menjadi diajukan jika sebelumnya ditolak
            if ($pengajuan->status === PengajuanTugasAkhir::STATUS_DITOLAK) {
                $data['status'] = PengajuanTugasAkhir::STATUS_DIAJUKAN;
                $data['tanggal_pengajuan'] = now();
                $data['message'] = null; // Reset pesan penolakan
            }

            $pengajuan->update($data);

            return redirect()->route('pengajuan.index')
                ->with('success', 'Pengajuan berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function updateStatus(Request $request, PengajuanTugasAkhir $pengajuan)
    {
        // Hanya admin/koordinator yang bisa update status
        if (!Auth::user()->isAdmin() && !Auth::user()->isKaprodi()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:' . implode(',', array_keys(PengajuanTugasAkhir::getStatusOptions())),
            'message' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            $data = ['status' => $request->status];

            if ($request->status === PengajuanTugasAkhir::STATUS_DITERIMA) {
                $data['tanggal_acc'] = now();
            }

            if ($request->filled('message')) {
                $data['message'] = $request->message;
            }

            $pengajuan->update($data);

            return redirect()->back()
                ->with('success', 'Status pengajuan berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function uploadSkripsi(Request $request, PengajuanTugasAkhir $pengajuan)
    {
        // $this->authorize('update', $pengajuan);

        // Hanya bisa upload jika status sedang bimbingan
        if ($pengajuan->status !== PengajuanTugasAkhir::STATUS_SEDANG_BIMBINGAN) {
            return redirect()->back()
                ->with('error', 'Upload skripsi hanya bisa dilakukan pada status sedang bimbingan');
        }

        $validator = Validator::make($request->all(), [
            'file_skripsi' => 'required|file|mimes:pdf|max:10240', // 10MB
        ], [
            'file_skripsi.required' => 'File skripsi harus diupload',
            'file_skripsi.mimes' => 'File skripsi harus berformat PDF',
            'file_skripsi.max' => 'File skripsi maksimal 10MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            // Hapus file skripsi lama jika ada
            if ($pengajuan->file_skripsi) {
                Storage::disk('public')->delete($pengajuan->file_skripsi);
            }

            // Upload file skripsi baru
            $file = $request->file('file_skripsi');
            $filename = 'skripsi_' . $pengajuan->mahasiswa_id . '_' . time() . '.pdf';
            $path = $file->storeAs('skripsi', $filename, 'public');

            $pengajuan->update([
                'file_skripsi' => $path,
                'status' => PengajuanTugasAkhir::STATUS_SIAP_SIDANG
            ]);

            return redirect()->back()
                ->with('success', 'File skripsi berhasil diupload. Status diubah menjadi Siap Sidang');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function downloadFile(PengajuanTugasAkhir $pengajuan, $type)
    {
        // $this->authorize('view', $pengajuan);

        $filePath = null;
        $fileName = null;

        if ($type === 'proposal' && $pengajuan->file_proposal) {
            $filePath = $pengajuan->file_proposal;
            $fileName = 'Proposal_' . $pengajuan->mahasiswa->user->name . '.pdf';
        } elseif ($type === 'skripsi' && $pengajuan->file_skripsi) {
            $filePath = $pengajuan->file_skripsi;
            $fileName = 'Skripsi_' . $pengajuan->mahasiswa->user->name . '.pdf';
        }

        if (!$filePath || !Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return Storage::disk('public')->download($filePath, $fileName);
    }
}
