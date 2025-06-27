@extends('layouts.dashboard')

@section('content')
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white mb-3">
                        <h4><i class="fas fa-plus-circle"></i> Ajukan Tugas Akhir</h4>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('pengajuan-tugas-akhir.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-3">
                                <label for="judul" class="form-label">Judul Tugas Akhir <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('judul') is-invalid @enderror"
                                    id="judul" name="judul" value="{{ old('judul') }}"
                                    placeholder="Masukkan judul tugas akhir">
                                @error('judul')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="bidang_penelitian" class="form-label">Bidang Penelitian <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('bidang_penelitian') is-invalid @enderror"
                                    id="bidang_penelitian" name="bidang_penelitian" value="{{ old('bidang_penelitian') }}"
                                    placeholder="Contoh: Jaringan Komputer, Artificial Intelligence, dll">
                                @error('bidang_penelitian')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="sinopsis" class="form-label">Sinopsis <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('sinopsis') is-invalid @enderror" id="sinopsis" name="sinopsis" rows="6"
                                    placeholder="Jelaskan latar belakang, tujuan, dan metodologi penelitian (minimal 100 karakter)">{{ old('sinopsis') }}</textarea>
                                <div class="form-text">Minimal 100 karakter</div>
                                @error('sinopsis')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="file_proposal" class="form-label">File Proposal <span
                                        class="text-danger">*</span></label>
                                <input type="file" class="form-control @error('file_proposal') is-invalid @enderror"
                                    id="file_proposal" name="file_proposal" accept=".pdf">
                                <div class="form-text">Format: PDF, Maksimal: 5MB</div>
                                @error('file_proposal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Informasi:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Pastikan semua data yang diisi sudah benar</li>
                                    <li>File proposal harus dalam format PDF</li>
                                    <li>Setelah disubmit, pengajuan akan menunggu persetujuan koordinator</li>
                                </ul>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('pengajuan-tugas-akhir.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Submit Pengajuan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Character counter for sinopsis
        document.getElementById('sinopsis').addEventListener('input', function() {
            const length = this.value.length;
            const min = 100;
            const feedback = this.parentElement.querySelector('.form-text');

            if (length < min) {
                feedback.textContent = `Minimal 100 karakter (${length}/${min})`;
                feedback.className = 'form-text text-warning';
            } else {
                feedback.textContent = `${length} karakter`;
                feedback.className = 'form-text text-success';
            }
        });
    </script>
@endsection
