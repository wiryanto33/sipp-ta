@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Detail Bimbingan</h3>
                        <div class="btn-group">
                            <a href="{{ route('bimbingan.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                            @if ($bimbingan->isActive())
                                <a href="{{ route('log-bimbingan.index', $bimbingan) }}" class="btn btn-primary">
                                    <i class="fas fa-list"></i> Log Bimbingan
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="row">
                            <!-- Informasi Bimbingan -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="card-title mb-0">Informasi Bimbingan</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Status:</strong></td>
                                                <td>
                                                    <span class="badge bg-{{ $bimbingan->status_color }} fs-6">
                                                        {{ $bimbingan->status_label }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Jabatan Pembimbing:</strong></td>
                                                <td>
                                                    <span class="badge bg-info fs-6">
                                                        {{ $bimbingan->jabatan_pembimbing_label }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Tanggal Mulai:</strong></td>
                                                <td>{{ $bimbingan->tanggal_mulai->format('d/m/Y') }}</td>
                                            </tr>
                                            @if ($bimbingan->tanggal_selesai)
                                                <tr>
                                                    <td><strong>Tanggal Selesai:</strong></td>
                                                    <td>{{ $bimbingan->tanggal_selesai->format('d/m/Y') }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td><strong>Kuota:</strong></td>
                                                <td>{{ $bimbingan->kuota }} mahasiswa</td>
                                            </tr>
                                            @if ($bimbingan->isActive())
                                                <tr>
                                                    <td><strong>Progress:</strong></td>
                                                    <td>
                                                        <div class="progress" style="height: 25px;">
                                                            <div class="progress-bar" role="progressbar"
                                                                style="width: {{ $bimbingan->getProgressPercentage() }}%">
                                                                {{ $bimbingan->getProgressPercentage() }}%
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        </table>

                                        @if ($bimbingan->status === 'ditolak' && $bimbingan->alasan_penolakan)
                                            <div class="alert alert-danger mt-3">
                                                <h6><i class="fas fa-exclamation-triangle"></i> Alasan Penolakan:</h6>
                                                <p class="mb-0">{{ $bimbingan->alasan_penolakan }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Mahasiswa -->
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="card-title mb-0">Informasi Mahasiswa</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td><strong>Nama:</strong></td>
                                                <td>{{ $bimbingan->pengajuanTugasAkhir->mahasiswa->user->name }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>NRP:</strong></td>
                                                <td>{{ $bimbingan->pengajuanTugasAkhir->mahasiswa->user->nrp }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td>{{ $bimbingan->pengajuanTugasAkhir->mahasiswa->user->email }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Angkatan:</strong></td>
                                                <td>{{ $bimbingan->pengajuanTugasAkhir->mahasiswa->angkatan }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Semester:</strong></td>
                                                <td>{{ $bimbingan->pengajuanTugasAkhir->mahasiswa->semester }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Tugas Akhir -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-warning">
                                        <h5 class="card-title mb-0">Informasi Tugas Akhir</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td><strong>Judul:</strong></td>
                                                        <td>{{ $bimbingan->pengajuanTugasAkhir->judul }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Bidang Penelitian:</strong></td>
                                                        <td>
                                                            <span class="badge bg-secondary fs-6">
                                                                {{ $bimbingan->pengajuanTugasAkhir->bidang_penelitian }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Bidang Penelitian:</strong></td>
                                                        <td>
                                                            <span class="badge bg-primary fs-6">
                                                                {{ $bimbingan->pengajuanTugasAkhir->bidang_penelitian }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td><strong>Tanggal Pengajuan:</strong></td>
                                                        <td>{{ $bimbingan->pengajuanTugasAkhir->created_at->format('d/m/Y H:i') }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Status Pengajuan:</strong></td>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ $bimbingan->pengajuanTugasAkhir->status_color }} fs-6">
                                                                {{ $bimbingan->pengajuanTugasAkhir->status_label }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- Deskripsi -->
                                        @if ($bimbingan->pengajuanTugasAkhir->deskripsi)
                                            <div class="mt-3">
                                                <h6><strong>Deskripsi:</strong></h6>
                                                <div class="p-3 bg-light rounded">
                                                    {!! nl2br(e($bimbingan->pengajuanTugasAkhir->deskripsi)) !!}
                                                </div>
                                            </div>
                                        @endif

                                        <!-- File Proposal -->
                                        @if ($bimbingan->pengajuanTugasAkhir->file_proposal)
                                            <div class="mt-3">
                                                <h6><strong>File Proposal:</strong></h6>
                                                <a href="{{ Storage::url($bimbingan->pengajuanTugasAkhir->file_proposal) }}"
                                                    class="btn btn-outline-primary" target="_blank">
                                                    <i class="fas fa-download"></i> Download Proposal
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Pembimbing -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-dark text-white">
                                        <h5 class="card-title mb-0">Informasi Pembimbing</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td><strong>Nama:</strong></td>
                                                        <td>{{ $bimbingan->dosen->user->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>NIDN:</strong></td>
                                                        <td>{{ $bimbingan->dosen->nidn }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Email:</strong></td>
                                                        <td>{{ $bimbingan->dosen->user->email }}</td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td><strong>Jabatan Akademik:</strong></td>
                                                        <td>{{ $bimbingan->dosen->jabatan_akademik }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Bidang Studi:</strong></td>
                                                        <td>
                                                            @foreach (explode(',', $bimbingan->dosen->bidang_studi) as $bidang)
                                                                <span class="badge bg-info me-1">{{ trim($bidang) }}</span>
                                                            @endforeach

                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons untuk Dosen -->
                        @if (auth()->user()->role === 'dosen' && $bimbingan->status === 'pending')
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h5 class="card-title mb-0">Tindakan</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                                    data-bs-target="#approveModal">
                                                    <i class="fas fa-check"></i> Setuju
                                                </button>
                                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                                    data-bs-target="#rejectModal">
                                                    <i class="fas fa-times"></i> Tolak
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Approve -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Persetujuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyetujui bimbingan ini?</p>
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            Setelah disetujui, mahasiswa dapat memulai proses bimbingan dengan Anda.
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('bimbingan.approve', $bimbingan) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success">Ya, Setujui</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reject -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Bimbingan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('bimbingan.reject', $bimbingan) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="alasan_penolakan" class="form-label">Alasan Penolakan <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" rows="4" required
                                placeholder="Jelaskan alasan penolakan bimbingan..."></textarea>
                        </div>
                        <div class="alert alert-warning">
                            <small>
                                <i class="fas fa-exclamation-triangle"></i>
                                Mahasiswa akan menerima notifikasi penolakan beserta alasan yang Anda berikan.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak Bimbingan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
