@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('bimbingan.index') }}">Bimbingan</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('log-bimbingan.index', $bimbingan) }}">Log
                                Bimbingan</a></li>
                        <li class="breadcrumb-item active">Detail Log</li>
                    </ol>
                </nav>

                <!-- Informasi Bimbingan -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informasi Bimbingan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Mahasiswa:</strong></td>
                                        <td>{{ $bimbingan->pengajuanTugasAkhir->mahasiswa->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>NRP:</strong></td>
                                        <td>{{ $bimbingan->pengajuanTugasAkhir->mahasiswa->user->nrp }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Dosen:</strong></td>
                                        <td>{{ $bimbingan->dosen->user->name }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>Jabatan:</strong></td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $bimbingan->jabatan_pembimbing_label }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            <span class="badge bg-{{ $bimbingan->status_color }}">
                                                {{ $bimbingan->status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Log Bimbingan -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Detail Log Bimbingan</h3>
                        <div>
                            @if (auth()->user()->isMahasiswa() && empty($logBimbingan->saran_dosen))
                                <a href="{{ route('log-bimbingan.edit', [$bimbingan, $logBimbingan]) }}"
                                    class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endif
                            @if (auth()->user()->isDosen() && empty($logBimbingan->saran_dosen))
                                <button type="button" class="btn btn-success btn-sm"
                                    onclick="addSaran({{ $logBimbingan->id }})">
                                    <i class="fas fa-comment"></i> Tambah Saran
                                </button>
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
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Tanggal Bimbingan:</strong></label>
                                    <p class="form-control-plaintext">
                                        {{ $logBimbingan->tanggal_bimbingan->format('d F Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Progress:</strong></label>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar" role="progressbar"
                                            style="width: {{ $logBimbingan->progress }}%">
                                            {{ $logBimbingan->progress }}%
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label"><strong>Materi Bimbingan:</strong></label>
                            <div class="border rounded p-3 bg-light">
                                {!! nl2br(e($logBimbingan->materi_bimbingan)) !!}
                            </div>
                        </div>

                        @if ($logBimbingan->saran_dosen)
                            <div class="mb-4">
                                <label class="form-label"><strong>Saran Dosen:</strong></label>
                                <div class="border rounded p-3 bg-success bg-opacity-10 border-success">
                                    <i class="fas fa-comment-dots text-success me-2"></i>
                                    {!! nl2br(e($logBimbingan->saran_dosen)) !!}
                                </div>
                            </div>
                        @else
                            <div class="mb-4">
                                <label class="form-label"><strong>Saran Dosen:</strong></label>
                                <div class="border rounded p-3 bg-warning bg-opacity-10 border-warning">
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    <em>Menunggu saran dari dosen pembimbing</em>
                                </div>
                            </div>
                        @endif

                        @if ($logBimbingan->hasFile())
                            <div class="mb-4">
                                <label class="form-label"><strong>File Pendukung:</strong></label>
                                <div class="border rounded p-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file fa-2x text-primary me-3"></i>
                                        <div>
                                            <h6 class="mb-1">{{ $logBimbingan->file_name }}</h6>
                                            <small class="text-muted">{{ $logBimbingan->getFileSizeHuman() }}</small>
                                        </div>
                                        <div class="ms-auto">
                                            <a href="{{ route('log-bimbingan.download', [$bimbingan, $logBimbingan]) }}"
                                                class="btn btn-primary btn-sm">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Dibuat:</strong></label>
                                    <p class="form-control-plaintext">
                                        {{ $logBimbingan->created_at->format('d F Y H:i') }}
                                    </p>
                                </div>
                            </div>
                            @if ($logBimbingan->updated_at != $logBimbingan->created_at)
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><strong>Terakhir Diperbarui:</strong></label>
                                        <p class="form-control-plaintext">
                                            {{ $logBimbingan->updated_at->format('d F Y H:i') }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('log-bimbingan.index', $bimbingan) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Log
                            </a>
                            @if (auth()->user()->isMahasiswa() && empty($logBimbingan->saran_dosen))
                                <button type="button" class="btn btn-danger" onclick="deleteLog({{ $logBimbingan->id }})">
                                    <i class="fas fa-trash"></i> Hapus Log
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Saran -->
    <div class="modal fade" id="saranModal" tabindex="-1" aria-labelledby="saranModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="saranForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="saranModalLabel">Tambah Saran Dosen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="saran_dosen" class="form-label">Saran <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="saran_dosen" name="saran_dosen" rows="6"
                                placeholder="Berikan saran, kritik, atau arahan untuk mahasiswa..." required></textarea>
                            <div class="form-text">
                                <span id="saranCharCount">0</span>/2000 karakter
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="submitSaran">
                            <i class="fas fa-save"></i> Simpan Saran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus log bimbingan ini?</p>
                    <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/log-bimbingan.js') }}"></script>
@endpush
