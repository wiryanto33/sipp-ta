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
                        <li class="breadcrumb-item active">Edit Log</li>
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

                <!-- Form Edit Log Bimbingan -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Edit Log Bimbingan</h3>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('log-bimbingan.update', [$bimbingan, $logBimbingan]) }}" method="POST"
                            enctype="multipart/form-data" id="logBimbinganForm">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal_bimbingan" class="form-label">
                                            Tanggal Bimbingan <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                            class="form-control @error('tanggal_bimbingan') is-invalid @enderror"
                                            id="tanggal_bimbingan" name="tanggal_bimbingan"
                                            value="{{ old('tanggal_bimbingan', $logBimbingan->tanggal_bimbingan->format('Y-m-d')) }}"
                                            max="{{ date('Y-m-d') }}" required>
                                        @error('tanggal_bimbingan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="progress" class="form-label">
                                            Progress (%) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="number"
                                                class="form-control @error('progress') is-invalid @enderror" id="progress"
                                                name="progress" value="{{ old('progress', $logBimbingan->progress) }}"
                                                min="0" max="100" required>
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <div class="progress mt-2" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ old('progress', $logBimbingan->progress) }}%"
                                                id="progressBar">
                                                {{ old('progress', $logBimbingan->progress) }}%
                                            </div>
                                        </div>
                                        @error('progress')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="materi_bimbingan" class="form-label">
                                    Materi Bimbingan <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('materi_bimbingan') is-invalid @enderror" id="materi_bimbingan"
                                    name="materi_bimbingan" rows="6" placeholder="Jelaskan materi yang dibahas dalam bimbingan ini..." required>{{ old('materi_bimbingan', $logBimbingan->materi_bimbingan) }}</textarea>
                                <div class="form-text">
                                    <span
                                        id="charCount">{{ strlen(old('materi_bimbingan', $logBimbingan->materi_bimbingan)) }}</span>/2000
                                    karakter
                                </div>
                                @error('materi_bimbingan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if ($logBimbingan->hasFile())
                                <div class="mb-3">
                                    <label class="form-label">File Saat Ini:</label>
                                    <div class="border rounded p-3 bg-light">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-file text-primary me-2"></i>
                                            <span>{{ $logBimbingan->file_name }}</span>
                                            <span class="text-muted ms-2">({{ $logBimbingan->getFileSizeHuman() }})</span>
                                            <a href="{{ route('log-bimbingan.download', [$bimbingan, $logBimbingan]) }}"
                                                class="btn btn-sm btn-outline-primary ms-auto">
                                                <i class="fas fa-download"></i> Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="file_bimbingan" class="form-label">
                                    {{ $logBimbingan->hasFile() ? 'Ganti File Pendukung' : 'File Pendukung' }}
                                </label>
                                <input type="file" class="form-control @error('file_bimbingan') is-invalid @enderror"
                                    id="file_bimbingan" name="file_bimbingan"
                                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip,.rar,.jpg,.jpeg,.png">
                                @error('file_bimbingan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Maksimal ukuran file 5MB. Format yang didukung: pdf, doc, docx, xls, xlsx, ppt, pptx,
                                    zip, rar, jpg, jpeg, png.
                                </div>
                            </div>

                            <div class="text-end">
                                <a href="{{ route('log-bimbingan.index', $bimbingan) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Update progress bar secara real-time saat input berubah
        document.getElementById('progress').addEventListener('input', function() {
            const progress = this.value;
            const progressBar = document.getElementById('progressBar');
            progressBar.style.width = `${progress}%`;
            progressBar.textContent = `${progress}%`;
        });

        // Update jumlah karakter materi bimbingan
        document.getElementById('materi_bimbingan').addEventListener('input', function() {
            const count = this.value.length;
            document.getElementById('charCount').textContent = count;
        });
    </script>
@endpush

