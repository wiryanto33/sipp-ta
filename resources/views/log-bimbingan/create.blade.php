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
                        <li class="breadcrumb-item"><a href="{{ route('log-bimbingan.index', $bimbingan) }}">Log
                                Bimbingan</a></li>
                        <li class="breadcrumb-item active">Tambah Log</li>
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
                        <div class="row">
                            <div class="col-12">
                                <strong>Judul Tugas Akhir:</strong>
                                <p class="mt-2">{{ $bimbingan->pengajuanTugasAkhir->judul }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Tambah Log Bimbingan -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tambah Log Bimbingan</h3>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('log-bimbingan.store', $bimbingan) }}" method="POST"
                            enctype="multipart/form-data" id="logBimbinganForm">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="tanggal_bimbingan" class="form-label">
                                            Tanggal Bimbingan <span class="text-danger">*</span>
                                        </label>
                                        <input type="date"
                                            class="form-control @error('tanggal_bimbingan') is-invalid @enderror"
                                            id="tanggal_bimbingan" name="tanggal_bimbingan"
                                            value="{{ old('tanggal_bimbingan', date('Y-m-d')) }}"
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
                                                name="progress" value="{{ old('progress', 0) }}" min="0"
                                                max="100" required>
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <div class="progress mt-2" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: {{ old('progress', 0) }}%" id="progressBar">
                                                {{ old('progress', 0) }}%
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
                                    name="materi_bimbingan" rows="6" placeholder="Jelaskan materi yang dibahas dalam bimbingan ini..." required>{{ old('materi_bimbingan') }}</textarea>
                                <div class="form-text">
                                    <span id="charCount">0</span>/2000 karakter
                                </div>
                                @error('materi_bimbingan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="file_bimbingan" class="form-label">File Pendukung</label>
                                <input type="file" class="form-control @error('file_bimbingan') is-invalid @enderror"
                                    id="file_bimbingan" name="file_bimbingan" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                <div class="form-text">
                                    Format yang diizinkan: PDF, DOC, DOCX, JPG, JPEG, PNG. Maksimal 10MB.
                                </div>
                                <div id="fileInfo" class="mt-2" style="display: none;">
                                    <div class="alert alert-info">
                                        <i class="fas fa-file"></i>
                                        <span id="fileName"></span>
                                        (<span id="fileSize"></span>)
                                    </div>
                                </div>
                                @error('file_bimbingan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('log-bimbingan.index', $bimbingan) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> Simpan Log Bimbingan
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
    <script src="{{ asset('assets/js/log-bimbingan-form.js') }}"></script>
@endpush
