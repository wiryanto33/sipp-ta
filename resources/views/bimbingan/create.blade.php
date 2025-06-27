@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Ajukan Bimbingan</h3>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Info Pengajuan Tugas Akhir -->
                        <div class="card mb-4">
                            <div class="card-header bg-light mb-3">
                                <h5 class="card-title mb-0">Informasi Tugas Akhir</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Judul:</strong><br>
                                        {{ $pengajuanTugasAkhir->judul }}
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Bidang Penelitian:</strong><br>
                                        {{ $pengajuanTugasAkhir->bidang_penelitian }}
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <strong>Status:</strong><br>
                                        <span class="badge bg-{{ $pengajuanTugasAkhir->status_color }}">
                                            {{ $pengajuanTugasAkhir->status_label }}
                                        </span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Tanggal Diterima:</strong><br>
                                        {{ $pengajuanTugasAkhir->tanggal_acc ? $pengajuanTugasAkhir->tanggal_acc->format('d/m/Y') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('bimbingan.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="pengajuan_tugas_akhir_id" value="{{ $pengajuanTugasAkhir->id }}">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="dosen_id" class="form-label">Pilih Dosen <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('dosen_id') is-invalid @enderror" id="dosen_id"
                                            name="dosen_id" required>
                                            <option value="">-- Pilih Dosen --</option>
                                            @foreach ($dosens as $dosen)
                                                <option value="{{ $dosen->id }}"
                                                    {{ old('dosen_id') == $dosen->id ? 'selected' : '' }}>
                                                    {{ $dosen->user->name }} - {{ $dosen->nidn }}
                                                    @if ($dosen->bidang_studi)
                                                        ({{ $dosen->bidang_studi }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('dosen_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Pilih dosen yang akan menjadi pembimbing Anda
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="jabatan_pembimbing" class="form-label">Jabatan Pembimbing <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select @error('jabatan_pembimbing') is-invalid @enderror"
                                            id="jabatan_pembimbing" name="jabatan_pembimbing" required>
                                            <option value="">-- Pilih Jabatan --</option>
                                            <option value="pembimbing_1"
                                                {{ old('jabatan_pembimbing') == 'pembimbing_1' ? 'selected' : '' }}>
                                                Pembimbing 1
                                            </option>
                                            <option value="pembimbing_2"
                                                {{ old('jabatan_pembimbing') == 'pembimbing_2' ? 'selected' : '' }}>
                                                Pembimbing 2
                                            </option>
                                        </select>
                                        @error('jabatan_pembimbing')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Tentukan posisi dosen sebagai pembimbing 1 atau 2
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Info Tambahan -->
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle"></i> Informasi Penting:</h6>
                                <ul class="mb-0 ps-3">
                                    <li>Pastikan dosen yang Anda pilih sesuai dengan bidang penelitian tugas akhir Anda</li>
                                    <li>Pembimbing 1 biasanya adalah dosen utama, sedangkan Pembimbing 2 adalah dosen
                                        pendamping</li>
                                    <li>Pengajuan ini akan dikirim ke dosen untuk persetujuan</li>
                                    <li>Dosen dapat menyetujui atau menolak pengajuan bimbingan Anda</li>
                                </ul>
                            </div>

                            <div class="form-group d-flex justify-content-between">
                                <a href="{{ route('bimbingan.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Ajukan Bimbingan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Optional: Add any JavaScript for form validation or enhancement
                const form = document.querySelector('form');
                const dosenSelect = document.getElementById('dosen_id');
                const jabatanSelect = document.getElementById('jabatan_pembimbing');

                // You can add custom validation or dynamic behavior here
                form.addEventListener('submit', function(e) {
                    if (!dosenSelect.value || !jabatanSelect.value) {
                        e.preventDefault();
                        alert('Mohon lengkapi semua field yang wajib diisi.');
                    }
                });
            });
        </script>
    @endpush
@endsection
