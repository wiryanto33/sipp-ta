@extends('layouts.dashboard')

@section('content')
    <!-- Informasi Mahasiswa -->
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white mb-3">
                        <h4><i class="fas fa-file-alt"></i> Detail Pengajuan Tugas Akhir</h4>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Informasi Mahasiswa -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">INFORMASI MAHASISWA</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td width="40%">Nama</td>
                                        <td>: {{ $pengajuan->mahasiswa->user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>PANGKAT</td>
                                        <td>: {{ $pengajuan->mahasiswa->user->pangkat }}</td>
                                    </tr>
                                    <tr>
                                        <td>KORPS</td>
                                        <td>: {{ $pengajuan->mahasiswa->user->korps }}</td>
                                    </tr>
                                    <tr>
                                        <td>NRP</td>
                                        <td>: {{ $pengajuan->mahasiswa->user->nrp }}</td>
                                    </tr>
                                    <tr>
                                        <td>Program Studi</td>
                                        <td>: {{ $pengajuan->mahasiswa->prodi->name ?? '-' }} -
                                            {{ $pengajuan->mahasiswa->prodi->jenjang ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Angkatan</td>
                                        <td>: {{ $pengajuan->mahasiswa->angkatan }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">STATUS PENGAJUAN</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td width="40%">Status</td>
                                        <td>: <span
                                                class="badge bg-{{ $pengajuan->status_color }}">{{ $pengajuan->status_label }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal Pengajuan</td>
                                        <td>: {{ $pengajuan->tanggal_pengajuan->format('d F Y') }}</td>
                                    </tr>
                                    @if ($pengajuan->tanggal_acc)
                                        <tr>
                                            <td>Tanggal Diterima</td>
                                            <td>: {{ $pengajuan->tanggal_acc->format('d F Y') }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <hr>

                        <!-- Detail Pengajuan -->
                        <div class="mb-4">
                            <h6 class="text-muted">DETAIL PENGAJUAN</h6>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Judul Tugas Akhir:</label>
                                <p class="form-control-plaintext border rounded p-2 bg-light">{{ $pengajuan->judul }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Bidang Penelitian:</label>
                                <p class="form-control-plaintext border rounded p-2 bg-light">
                                    {{ $pengajuan->bidang_penelitian }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Sinopsis:</label>
                                <div class="form-control-plaintext border rounded p-3 bg-light" style="min-height: 120px;">
                                    {{ $pengajuan->sinopsis }}
                                </div>
                            </div>
                        </div>

                        <!-- Files -->
                        <div class="mb-4">
                            <h6 class="text-muted">FILE DOKUMEN</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fa-solid fa-file-pdf fa-3x text-danger mb-2"></i>
                                            <h6>File Proposal</h6>
                                            @if ($pengajuan->file_proposal)
                                                <a href="{{ route('pengajuan.download-file', [$pengajuan, 'proposal']) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            @else
                                                <span class="text-muted">Belum diupload</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                {{-- <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <i class="fas fa-file-pdf fa-3x text-success mb-2"></i>
                                            <h6>File Skripsi</h6>
                                            @if ($pengajuan->file_skripsi)
                                                <a href="{{ route('pengajuan.download-file', [$pengajuan, 'skripsi']) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            @else
                                                <span class="text-muted">Belum diupload</span>
                                            @endif
                                        </div>
                                    </div>
                                </div> --}}
                            </div>
                        </div>

                        <!-- Message -->
                        @if ($pengajuan->message)
                            <div class="alert alert-info">
                                <h6><i class="fas fa-comment"></i> Pesan:</h6>
                                <p class="mb-0">{{ $pengajuan->message }}</p>
                            </div>
                        @endif

                        <!-- Upload Skripsi (untuk mahasiswa) -->
                        @if (auth()->user()->isMahasiswa() && $pengajuan->status === 'sedang_bimbingan' && !$pengajuan->file_skripsi)
                            <div class="card border-success">
                                <div class="card-header bg-success text-white mb-3">
                                    <h6><i class="fas fa-upload"></i> Upload File Skripsi</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('pengajuan.upload-skripsi', $pengajuan) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        <div class="mb-3">
                                            <input type="file"
                                                class="form-control @error('file_skripsi') is-invalid @enderror"
                                                name="file_skripsi" accept=".pdf" required>
                                            <div class="form-text">Format: PDF, Maksimal: 10MB</div>
                                            @error('file_skripsi')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-upload"></i> Upload Skripsi
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('pengajuan-tugas-akhir.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>

                            @if (auth()->user()->isMahasiswa() && in_array($pengajuan->status, ['draft', 'ditolak']))
                                <a href="{{ route('pengajuan-tugas-akhir.edit', $pengajuan) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit Pengajuan
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar untuk Admin/Koordinator -->
            @if (auth()->user()->isAdmin() || auth()->user()->isKaprodi())
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-warning text-dark mb-3">
                            <h6><i class="fas fa-cogs"></i> Kelola Pengajuan</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('pengajuan.update-status', $pengajuan) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="status" class="form-label">Update Status:</label>
                                    <select class="form-select" name="status" id="status" required>
                                        @foreach (\App\Models\PengajuanTugasAkhir::getStatusOptions() as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ $pengajuan->status == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="message" class="form-label">Pesan (Opsional):</label>
                                    <textarea class="form-control" name="message" id="message" rows="3"
                                        placeholder="Tambahkan catatan atau alasan...">{{ $pengajuan->message }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Update Status
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Progress Timeline -->
                    <div class="card mt-3">
                        <div class="card-header bg-info text-white mb-3">
                            <h6><i class="fas fa-timeline"></i> Progress</h6>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @php
                                    $statuses = [
                                        'diajukan' => 'Diajukan',
                                        'diterima' => 'Diterima',
                                        'sedang_bimbingan' => 'Sedang Bimbingan',
                                        'siap_sidang' => 'Siap Sidang',
                                        'lulus' => 'Lulus',
                                    ];
                                    $currentIndex = array_search($pengajuan->status, array_keys($statuses));
                                @endphp

                                @foreach ($statuses as $key => $label)
                                    @php
                                        $index = array_search($key, array_keys($statuses));
                                        $isActive = $index <= $currentIndex;
                                        $isCurrent = $key === $pengajuan->status;
                                    @endphp

                                    <div class="d-flex align-items-center mb-2">
                                        <div class="timeline-icon {{ $isActive ? 'bg-success' : 'bg-secondary' }} rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 30px; height: 30px; min-width: 30px;">
                                            @if ($isActive)
                                                <i class="fas fa-check text-white" style="font-size: 12px;"></i>
                                            @else
                                                <i class="fas fa-circle text-white" style="font-size: 8px;"></i>
                                            @endif
                                        </div>
                                        <div class="ms-3">
                                            <span
                                                class="{{ $isCurrent ? 'fw-bold text-primary' : ($isActive ? 'text-success' : 'text-muted') }}">
                                                {{ $label }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
@endsection
