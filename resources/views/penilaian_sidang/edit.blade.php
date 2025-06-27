@extends('layouts.dashboard')

@section('title', 'Edit Penilaian Sidang')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Penilaian Sidang</h3>
                    <p class="text-subtitle text-muted">Edit penilaian sidang tugas akhir</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('penilaian-sidang.index') }}">Penilaian Sidang</a>
                            </li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('penilaian-sidang.show', $penilaianSidang) }}">Detail</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <!-- Informasi Mahasiswa -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Informasi Mahasiswa</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-lg me-3">
                                        <img src="{{ $penilaianSidang->jadwalSidang->tugasAkhir->mahasiswa->user->image ?? asset('images/faces/default-avatar.jpg') }}"
                                            alt="Avatar" class="rounded-circle">
                                    </div>
                                    <div>
                                        <h5 class="mb-1">
                                            {{ $penilaianSidang->jadwalSidang->tugasAkhir->mahasiswa->user->name }}</h5>
                                        <span
                                            class="badge bg-light-primary">{{ $penilaianSidang->jadwalSidang->tugasAkhir->mahasiswa->nrp }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <strong>Tanggal Sidang:</strong>
                                    <span
                                        class="text-muted">{{ $penilaianSidang->jadwalSidang->tanggal_sidang->format('d F Y') }}</span>
                                </div>
                                <div class="mb-2">
                                    <strong>Penguji:</strong>
                                    <span class="text-muted">{{ $penilaianSidang->pengujiSidang->dosen->user->name }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <strong>Judul Tugas Akhir:</strong>
                                <p class="text-muted mb-0">{{ $penilaianSidang->jadwalSidang->tugasAkhir->judul }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Edit Penilaian -->
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Form Edit Penilaian</h4>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('penilaian-sidang.show', $penilaianSidang) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <form action="{{ route('penilaian-sidang.update', $penilaianSidang) }}" method="POST"
                            id="penilaianForm">
                            @csrf
                            @method('PUT')

                            <!-- Materi Skripsi (Bobot 50%) -->
                            <div class="card mb-4 border-primary">
                                <div class="card-header bg-light-primary mb-3">
                                    <h5 class="text-primary mb-0">
                                        <i class="bi bi-book me-2"></i>Materi Skripsi (Bobot 50%)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="originalitas_materi" class="form-label fw-bold">
                                                Originalitas Materi <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control range-input @error('originalitas_materi') is-invalid @enderror"
                                                    id="originalitas_materi" name="originalitas_materi" min="0"
                                                    max="100" step="0.01"
                                                    value="{{ old('originalitas_materi', $penilaianSidang->originalitas_materi) }}"
                                                    placeholder="0-100" onchange="updateNilaiAkhir()">
                                                <span class="input-group-text">/ 100</span>
                                                @error('originalitas_materi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="analisa_metodologi" class="form-label fw-bold">
                                                Analisa Metodologi <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control range-input @error('analisa_metodologi') is-invalid @enderror"
                                                    id="analisa_metodologi" name="analisa_metodologi" min="0"
                                                    max="100" step="0.01"
                                                    value="{{ old('analisa_metodologi', $penilaianSidang->analisa_metodologi) }}"
                                                    placeholder="0-100" onchange="updateNilaiAkhir()">
                                                <span class="input-group-text">/ 100</span>
                                                @error('analisa_metodologi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="tingkat_aplikasi_materi" class="form-label fw-bold">
                                                Tingkat Aplikasi Materi <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control range-input @error('tingkat_aplikasi_materi') is-invalid @enderror"
                                                    id="tingkat_aplikasi_materi" name="tingkat_aplikasi_materi"
                                                    min="0" max="100" step="0.01"
                                                    value="{{ old('tingkat_aplikasi_materi', $penilaianSidang->tingkat_aplikasi_materi) }}"
                                                    placeholder="0-100" onchange="updateNilaiAkhir()">
                                                <span class="input-group-text">/ 100</span>
                                                @error('tingkat_aplikasi_materi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="pengembangan_kreativitas" class="form-label fw-bold">
                                                Pengembangan Kreativitas <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control range-input @error('pengembangan_kreativitas') is-invalid @enderror"
                                                    id="pengembangan_kreativitas" name="pengembangan_kreativitas"
                                                    min="0" max="100" step="0.01"
                                                    value="{{ old('pengembangan_kreativitas', $penilaianSidang->pengembangan_kreativitas) }}"
                                                    placeholder="0-100" onchange="updateNilaiAkhir()">
                                                <span class="input-group-text">/ 100</span>
                                                @error('pengembangan_kreativitas')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="tata_tulis" class="form-label fw-bold">
                                                Tata Tulis <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control range-input @error('tata_tulis') is-invalid @enderror"
                                                    id="tata_tulis" name="tata_tulis" min="0" max="100"
                                                    step="0.01"
                                                    value="{{ old('tata_tulis', $penilaianSidang->tata_tulis) }}"
                                                    placeholder="0-100" onchange="updateNilaiAkhir()">
                                                <span class="input-group-text">/ 100</span>
                                                @error('tata_tulis')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Penyajian (Bobot 30%) -->
                            <div class="card mb-4 border-success">
                                <div class="card-header bg-light-success mb-3">
                                    <h5 class="text-success mb-0">
                                        <i class="bi bi-presentation me-2"></i>Penyajian (Bobot 30%)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="penguasaan_materi" class="form-label fw-bold">
                                                Penguasaan Materi <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control range-input @error('penguasaan_materi') is-invalid @enderror"
                                                    id="penguasaan_materi" name="penguasaan_materi" min="0"
                                                    max="100" step="0.01"
                                                    value="{{ old('penguasaan_materi', $penilaianSidang->penguasaan_materi) }}"
                                                    placeholder="0-100" onchange="updateNilaiAkhir()">
                                                <span class="input-group-text">/ 100</span>
                                                @error('penguasaan_materi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="sikap_dan_penampilan" class="form-label fw-bold">
                                                Sikap dan Penampilan <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control range-input @error('sikap_dan_penampilan') is-invalid @enderror"
                                                    id="sikap_dan_penampilan" name="sikap_dan_penampilan" min="0"
                                                    max="100" step="0.01"
                                                    value="{{ old('sikap_dan_penampilan', $penilaianSidang->sikap_dan_penampilan) }}"
                                                    placeholder="0-100" onchange="updateNilaiAkhir()">
                                                <span class="input-group-text">/ 100</span>
                                                @error('sikap_dan_penampilan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="penyajian_sarana_sistematika" class="form-label fw-bold">
                                                Penyajian Sarana Sistematika <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control range-input @error('penyajian_sarana_sistematika') is-invalid @enderror"
                                                    id="penyajian_sarana_sistematika" name="penyajian_sarana_sistematika"
                                                    min="0" max="100" step="0.01"
                                                    value="{{ old('penyajian_sarana_sistematika', $penilaianSidang->penyajian_sarana_sistematika) }}"
                                                    placeholder="0-100" onchange="updateNilaiAkhir()">
                                                <span class="input-group-text">/ 100</span>
                                                @error('penyajian_sarana_sistematika')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="hasil_yang_dicapai" class="form-label fw-bold">
                                                Hasil yang Dicapai <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control range-input @error('hasil_yang_dicapai') is-invalid @enderror"
                                                    id="hasil_yang_dicapai" name="hasil_yang_dicapai" min="0"
                                                    max="100" step="0.01"
                                                    value="{{ old('hasil_yang_dicapai', $penilaianSidang->hasil_yang_dicapai) }}"
                                                    placeholder="0-100" onchange="updateNilaiAkhir()">
                                                <span class="input-group-text">/ 100</span>
                                                @error('hasil_yang_dicapai')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Diskusi & Tanya Jawab (Bobot 20%) -->
                            <div class="card mb-4 border-warning">
                                <div class="card-header bg-light-warning mb-3">
                                    <h5 class="text-warning mb-0">
                                        <i class="bi bi-chat-dots me-2"></i>Diskusi & Tanya Jawab (Bobot 20%)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="penguasaan_materi_diskusi" class="form-label fw-bold">
                                                Penguasaan Materi Diskusi <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control range-input @error('penguasaan_materi_diskusi') is-invalid @enderror"
                                                    id="penguasaan_materi_diskusi" name="penguasaan_materi_diskusi"
                                                    min="0" max="100" step="0.01"
                                                    value="{{ old('penguasaan_materi_diskusi', $penilaianSidang->penguasaan_materi_diskusi) }}"
                                                    placeholder="0-100" onchange="updateNilaiAkhir()">
                                                <span class="input-group-text">/ 100</span>
                                                @error('penguasaan_materi_diskusi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="objektivitas_tanggapan" class="form-label fw-bold">
                                                Objektivitas Tanggapan <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control range-input @error('objektivitas_tanggapan') is-invalid @enderror"
                                                    id="objektivitas_tanggapan" name="objektivitas_tanggapan"
                                                    min="0" max="100" step="0.01"
                                                    value="{{ old('objektivitas_tanggapan', $penilaianSidang->objektivitas_tanggapan) }}"
                                                    placeholder="0-100" onchange="updateNilaiAkhir()">
                                                <span class="input-group-text">/ 100</span>
                                                @error('objektivitas_tanggapan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="kemampuan_mempertahankan_ide" class="form-label fw-bold">
                                                Kemampuan Mempertahankan Ide <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input type="number"
                                                    class="form-control range-input @error('kemampuan_mempertahankan_ide') is-invalid @enderror"
                                                    id="kemampuan_mempertahankan_ide" name="kemampuan_mempertahankan_ide"
                                                    min="0" max="100" step="0.01"
                                                    value="{{ old('kemampuan_mempertahankan_ide', $penilaianSidang->kemampuan_mempertahankan_ide) }}"
                                                    placeholder="0-100" onchange="updateNilaiAkhir()">
                                                <span class="input-group-text">/ 100</span>
                                                @error('kemampuan_mempertahankan_ide')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Nilai Akhir -->
                            <div class="card mb-4 border-info">
                                <div class="card-header bg-light-info">
                                    <h5 class="text-info mb-0">
                                        <i class="bi bi-calculator me-2"></i>Hasil Perhitungan Nilai Akhir
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="row text-center">
                                                <div class="col-md-4">
                                                    <div class="p-3 bg-light rounded">
                                                        <h6 class="text-primary fw-bold mb-1">Materi Skripsi (50%)</h6>
                                                        <span id="nilai-materi"
                                                            class="fs-4 fw-bold text-primary">0.00</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="p-3 bg-light rounded">
                                                        <h6 class="text-success fw-bold mb-1">Penyajian (30%)</h6>
                                                        <span id="nilai-penyajian"
                                                            class="fs-4 fw-bold text-success">0.00</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="p-3 bg-light rounded">
                                                        <h6 class="text-info fw-bold mb-1">Diskusi (20%)</h6>
                                                        <span id="nilai-diskusi"
                                                            class="fs-4 fw-bold text-info">0.00</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center p-4 bg-gradient-info rounded">
                                                <label for="nilai_akhir" class="form-label fw-bold text-gray mb-2">
                                                    Nilai Akhir <span class="text-warning">*</span>
                                                </label>
                                                <input type="number"
                                                    class="form-control form-control-lg text-center fw-bold border-0 shadow @error('nilai_akhir') is-invalid @enderror"
                                                    id="nilai_akhir" name="nilai_akhir" min="0" max="100"
                                                    step="0.01" readonly
                                                    value="{{ old('nilai_akhir', $penilaianSidang->nilai_akhir) }}"
                                                    style="font-size: 2rem; background: white;">
                                                @error('nilai_akhir')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="bi bi-sticky me-2"></i>Catatan
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="4"
                                        placeholder="Masukkan catatan atau komentar penilaian...">{{ old('catatan', $penilaianSidang->catatan) }}</textarea>
                                    <div class="form-text">Opsional - Catatan atau komentar untuk mahasiswa</div>
                                    @error('catatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-end gap-3">
                                        <a href="{{ route('penilaian-sidang.show', $penilaianSidang) }}"
                                            class="btn btn-outline-secondary btn-lg">
                                            <i class="bi bi-x-circle me-2"></i>Batal
                                        </a>
                                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                            <i class="bi bi-check-circle me-2"></i>Update Penilaian
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize nilai akhir calculation
            updateNilaiAkhir();

            // Add input validation
            const rangeInputs = document.querySelectorAll('.range-input');
            rangeInputs.forEach(input => {
                input.addEventListener('input', function() {
                    validateInput(this);
                    updateNilaiAkhir();
                });
            });

            // Form submission validation
            document.getElementById('penilaianForm').addEventListener('submit', function(e) {
                let isValid = true;
                rangeInputs.forEach(input => {
                    if (!validateInput(input)) {
                        isValid = false;
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    alert(
                        'Mohon periksa kembali nilai yang dimasukkan. Pastikan semua nilai berada dalam rentang 0-100.');
                }
            });
        });

        function updateNilaiAkhir() {
            // Ambil semua nilai dari form
            const materiFields = [
                'originalitas_materi',
                'analisa_metodologi',
                'tingkat_aplikasi_materi',
                'pengembangan_kreativitas',
                'tata_tulis'
            ];

            const penyajianFields = [
                'penguasaan_materi',
                'sikap_dan_penampilan',
                'penyajian_sarana_sistematika',
                'hasil_yang_dicapai'
            ];

            const diskusiFields = [
                'penguasaan_materi_diskusi',
                'objektivitas_tanggapan',
                'kemampuan_mempertahankan_ide'
            ];

            // Hitung rata-rata materi skripsi
            let totalMateri = 0;
            let countMateri = 0;
            materiFields.forEach(field => {
                const value = parseFloat(document.getElementById(field).value) || 0;
                totalMateri += value;
                countMateri++;
            });
            const avgMateri = countMateri > 0 ? totalMateri / countMateri : 0;
            const nilaiMateri = avgMateri * 0.5;

            // Hitung rata-rata penyajian
            let totalPenyajian = 0;
            let countPenyajian = 0;
            penyajianFields.forEach(field => {
                const value = parseFloat(document.getElementById(field).value) || 0;
                totalPenyajian += value;
                countPenyajian++;
            });
            const avgPenyajian = countPenyajian > 0 ? totalPenyajian / countPenyajian : 0;
            const nilaiPenyajian = avgPenyajian * 0.3;

            // Hitung rata-rata diskusi
            let totalDiskusi = 0;
            let countDiskusi = 0;
            diskusiFields.forEach(field => {
                const value = parseFloat(document.getElementById(field).value) || 0;
                totalDiskusi += value;
                countDiskusi++;
            });
            const avgDiskusi = countDiskusi > 0 ? totalDiskusi / countDiskusi : 0;
            const nilaiDiskusi = avgDiskusi * 0.2;

            // Hitung nilai akhir
            const nilaiAkhir = nilaiMateri + nilaiPenyajian + nilaiDiskusi;

            // Update tampilan
            document.getElementById('nilai-materi').textContent = nilaiMateri.toFixed(2);
            document.getElementById('nilai-penyajian').textContent = nilaiPenyajian.toFixed(2);
            document.getElementById('nilai-diskusi').textContent = nilaiDiskusi.toFixed(2);
            document.getElementById('nilai_akhir').value = nilaiAkhir.toFixed(2);
        }

        function validateInput(input) {
            const value = parseFloat(input.value);
            const isValid = !isNaN(value) && value >= 0 && value <= 100;

            if (!isValid) {
                input.classList.add('is-invalid');
                input.parentElement.classList.add('has-error');
                return false;
            } else {
                input.classList.remove('is-invalid');
                input.parentElement.classList.remove('has-error');
                return true;
            }
        }
    </script>

@endsection
