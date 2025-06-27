@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Daftar Bimbingan</h3>
                        @if (auth()->user()->isMahasiswa())
                            <a href="{{ route('bimbingan.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Ajukan Bimbingan
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

                        @if ($bimbingans->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Mahasiswa</th>
                                            <th>Judul Tugas Akhir</th>
                                            <th>Dosen</th>
                                            <th>Jabatan</th>
                                            <th>Status</th>
                                            <th>Progress</th>
                                            <th>Tanggal Mulai</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($bimbingans as $index => $bimbingan)
                                            <tr>
                                                <td>{{ $bimbingans->firstItem() + $index }}</td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $bimbingan->pengajuanTugasAkhir->mahasiswa->user->name }}</strong><br>
                                                        <small
                                                            class="text-muted">{{ $bimbingan->pengajuanTugasAkhir->mahasiswa->user->nrp }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-wrap" style="max-width: 200px;">
                                                        {{ Str::limit($bimbingan->pengajuanTugasAkhir->judul, 100) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $bimbingan->dosen->user->name }}</strong><br>
                                                        <small class="text-muted">{{ $bimbingan->dosen->nidn }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">
                                                        {{ $bimbingan->jabatan_pembimbing_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $bimbingan->status_color }}">
                                                        {{ $bimbingan->status_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($bimbingan->isActive())
                                                        <div class="progress" style="height: 20px;">
                                                            <div class="progress-bar" role="progressbar"
                                                                style="width: {{ $bimbingan->getProgressPercentage() }}%">
                                                                {{ $bimbingan->getProgressPercentage() }}%
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>{{ $bimbingan->tanggal_mulai->format('d/m/Y') }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('bimbingan.show', $bimbingan) }}"
                                                            class="btn btn-sm btn-info" title="Lihat Detail">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        @if ($bimbingan->isActive())
                                                            <a href="{{ route('log-bimbingan.index', $bimbingan) }}"
                                                                class="btn btn-sm btn-primary" title="Log Bimbingan">
                                                                <i class="fas fa-list"></i>
                                                            </a>
                                                        @endif

                                                        @if (auth()->user()->isDosen() && auth()->user()->dosen->id == $bimbingan->dosen_id)
                                                            @if ($bimbingan->canBeApproved())
                                                                <form action="{{ route('bimbingan.approve', $bimbingan) }}"
                                                                    method="POST" class="d-inline"
                                                                    onsubmit="return confirm('Apakah Anda yakin ingin menyetujui bimbingan ini?')">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit" class="btn btn-sm btn-success"
                                                                        title="Setujui">
                                                                        <i class="fas fa-check"></i>
                                                                    </button>
                                                                </form>

                                                                <button type="button" class="btn btn-sm btn-danger"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#rejectModal{{ $bimbingan->id }}"
                                                                    title="Tolak">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            @endif

                                                            @if ($bimbingan->isActive())
                                                                <form
                                                                    action="{{ route('bimbingan.complete', $bimbingan) }}"
                                                                    method="POST" class="d-inline"
                                                                    onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan bimbingan ini?')">
                                                                    @csrf
                                                                    @method('PUT')
                                                                    <button type="submit" class="btn btn-sm btn-secondary"
                                                                        title="Selesaikan">
                                                                        <i class="fas fa-flag-checkered"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>

                                            <!-- Modal Penolakan -->
                                            @if (auth()->user()->isDosen() && auth()->user()->dosen->id == $bimbingan->dosen_id && $bimbingan->canBeRejected())
                                                <div class="modal fade" id="rejectModal{{ $bimbingan->id }}"
                                                    tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('bimbingan.reject', $bimbingan) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Tolak Bimbingan</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="form-group">
                                                                        <label for="alasan_penolakan">Alasan Penolakan <span
                                                                                class="text-danger">*</span></label>
                                                                        <textarea class="form-control" id="alasan_penolakan" name="alasan_penolakan" rows="4" required
                                                                            placeholder="Masukkan alasan penolakan bimbingan..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary"
                                                                        data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-danger">Tolak
                                                                        Bimbingan</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center">
                                {{ $bimbingans->links() }}
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Belum ada data bimbingan</h5>
                                @if (auth()->user()->isMahasiswa())
                                    <p class="text-muted">Klik tombol "Ajukan Bimbingan" untuk mengajukan bimbingan baru.
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
