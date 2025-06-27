@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('bimbingan.index') }}">Bimbingan</a></li>
                        <li class="breadcrumb-item active">Log Bimbingan</li>
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
                                    <tr>
                                        <td><strong>Progress:</strong></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar"
                                                    style="width: {{ $bimbingan->getProgressPercentage() }}%">
                                                    {{ $bimbingan->getProgressPercentage() }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <strong>Judul Tugas Akhir:</strong>
                                <p class="mt-2">{{ $bimbingan->pengajuanTugasAkhir->judul }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Log Bimbingan -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Log Bimbingan</h3>
                        @if (auth()->user()->isMahasiswa())
                            <a href="{{ route('log-bimbingan.create', $bimbingan) }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Log Bimbingan
                            </a>
                        @endif
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

                        @if ($logBimbingans->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="10%">Tanggal</th>
                                            <th width="35%">Materi Bimbingan</th>
                                            <th width="25%">Saran Dosen</th>
                                            <th width="8%">Progress</th>
                                            <th width="8%">File</th>
                                            <th width="9%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($logBimbingans as $index => $log)
                                            <tr>
                                                <td>{{ $logBimbingans->firstItem() + $index }}</td>
                                                <td>{{ $log->tanggal_bimbingan->format('d/m/Y') }}</td>
                                                <td>
                                                    <div class="text-wrap">
                                                        {{ Str::limit($log->materi_bimbingan, 100) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ($log->saran_dosen)
                                                        <div class="text-wrap">
                                                            {{ Str::limit($log->saran_dosen, 80) }}
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Belum ada saran</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        <div class="progress-bar" role="progressbar"
                                                            style="width: {{ $log->progress }}%">
                                                            {{ $log->progress }}%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    @if ($log->hasFile())
                                                        <a href="{{ route('log-bimbingan.download', [$bimbingan, $log]) }}"
                                                            class="btn btn-sm btn-outline-primary" title="Download File">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('log-bimbingan.show', [$bimbingan, $log]) }}"
                                                            class="btn btn-sm btn-info" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        @if (auth()->user()->isMahasiswa() && empty($log->saran_dosen))
                                                            <a href="{{ route('log-bimbingan.edit', [$bimbingan, $log]) }}"
                                                                class="btn btn-sm btn-warning" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>

                                                            <button type="button" class="btn btn-sm btn-danger"
                                                                onclick="deleteLog({{ $log->id }})" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif

                                                        @if (auth()->user()->isDosen() && empty($log->saran_dosen))
                                                            <button type="button" class="btn btn-sm btn-success"
                                                                onclick="addSaran({{ $log->id }})"
                                                                title="Tambah Saran">
                                                                <i class="fas fa-comment"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center">
                                {{ $logBimbingans->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada log bimbingan.</p>
                                @if (auth()->user()->isMahasiswa())
                                    <a href="{{ route('log-bimbingan.create', $bimbingan) }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Tambah Log Bimbingan Pertama
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Saran -->
    <div class="modal fade" id="saranModal" tabindex="-1" aria-labelledby="saranModalLabel" aria-hidden="true">
        <div class="modal-dialog">
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
                            <textarea class="form-control" id="saran_dosen" name="saran_dosen" rows="5"
                                placeholder="Masukkan saran untuk mahasiswa..." required></textarea>
                            <div class="form-text">Maksimal 2000 karakter</div>
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
