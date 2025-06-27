@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Form Penilaian Sidang</h4>
                    </div>
                    <div class="card-body">
                        {{-- Informasi Sidang --}}
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Informasi Sidang</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Mahasiswa:</strong>
                                    {{ $jadwalSidang->tugasAkhir->mahasiswa->user->name ?? 'N/A' }}<br>
                                    <strong>NRP:</strong> {{ $jadwalSidang->tugasAkhir->mahasiswa->user->nrp ?? 'N/A' }}<br>
                                    <strong>Judul TA:</strong> {{ $jadwalSidang->tugasAkhir->judul ?? 'N/A' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Tanggal Sidang:</strong> {{ $jadwalSidang->tanggal_sidang ?? 'N/A' }}<br>
                                    <strong>Jenis Sidang:</strong> {{ $jadwalSidang->jenis_sidang ?? 'N/A' }}<br>
                                    <strong>Penguji:</strong> {{ $pengujiSidang->dosen->user->name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>

                        {{-- Form Penilaian --}}
                        <form action="{{ route('penilaian-sidang.store') }}" method="POST">
                            @csrf

                            {{-- Hidden Fields --}}
                            <input type="hidden" name="jadwal_sidang_id" value="{{ $jadwalSidang->id }}">
                            <input type="hidden" name="penguji_sidang_id" value="{{ $pengujiSidang->id }}">

                            {{-- Materi Skripsi --}}
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white mb-3">
                                    <h5 class="mb-0"><i class="fas fa-book"></i> Materi Skripsi (Bobot 50%)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="originalitas_materi" class="form-label">Originalitas Materi
                                                    <span class="text-danger">*</span></label>
                                                <input type="number" name="originalitas_materi" id="originalitas_materi"
                                                    class="form-control @error('originalitas_materi') is-invalid @enderror"
                                                    min="0" max="100" value="{{ old('originalitas_materi') }}"
                                                    required>
                                                @error('originalitas_materi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="analisa_metodologi" class="form-label">Analisa Metodologi <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="analisa_metodologi" id="analisa_metodologi"
                                                    class="form-control @error('analisa_metodologi') is-invalid @enderror"
                                                    min="0" max="100" value="{{ old('analisa_metodologi') }}"
                                                    required>
                                                @error('analisa_metodologi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="tingkat_aplikasi_materi" class="form-label">Tingkat Aplikasi
                                                    Materi <span class="text-danger">*</span></label>
                                                <input type="number" name="tingkat_aplikasi_materi"
                                                    id="tingkat_aplikasi_materi"
                                                    class="form-control @error('tingkat_aplikasi_materi') is-invalid @enderror"
                                                    min="0" max="100"
                                                    value="{{ old('tingkat_aplikasi_materi') }}" required>
                                                @error('tingkat_aplikasi_materi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="pengembangan_kreativitas" class="form-label">Pengembangan
                                                    Kreativitas <span class="text-danger">*</span></label>
                                                <input type="number" name="pengembangan_kreativitas"
                                                    id="pengembangan_kreativitas"
                                                    class="form-control @error('pengembangan_kreativitas') is-invalid @enderror"
                                                    min="0" max="100"
                                                    value="{{ old('pengembangan_kreativitas') }}" required>
                                                @error('pengembangan_kreativitas')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="tata_tulis" class="form-label">Tata Tulis <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="tata_tulis" id="tata_tulis"
                                                    class="form-control @error('tata_tulis') is-invalid @enderror"
                                                    min="0" max="100" value="{{ old('tata_tulis') }}" required>
                                                @error('tata_tulis')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Penyajian --}}
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white mb-3">
                                    <h5 class="mb-0"><i class="fas fa-presentation"></i> Penyajian (Bobot 30%)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="penguasaan_materi" class="form-label">Penguasaan Materi <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" name="penguasaan_materi" id="penguasaan_materi"
                                                    class="form-control @error('penguasaan_materi') is-invalid @enderror"
                                                    min="0" max="100" value="{{ old('penguasaan_materi') }}"
                                                    required>
                                                @error('penguasaan_materi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="sikap_dan_penampilan" class="form-label">Sikap dan Penampilan
                                                    <span class="text-danger">*</span></label>
                                                <input type="number" name="sikap_dan_penampilan"
                                                    id="sikap_dan_penampilan"
                                                    class="form-control @error('sikap_dan_penampilan') is-invalid @enderror"
                                                    min="0" max="100"
                                                    value="{{ old('sikap_dan_penampilan') }}" required>
                                                @error('sikap_dan_penampilan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group mb-3">
                                                <label for="penyajian_sarana_sistematika" class="form-label">Penyajian
                                                    Sarana Sistematika <span class="text-danger">*</span></label>
                                                <input type="number" name="penyajian_sarana_sistematika"
                                                    id="penyajian_sarana_sistematika"
                                                    class="form-control @error('penyajian_sarana_sistematika') is-invalid @enderror"
                                                    min="0" max="100"
                                                    value="{{ old('penyajian_sarana_sistematika') }}" required>
                                                @error('penyajian_sarana_sistematika')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Diskusi & Tanya Jawab --}}
                            <div class="card mb-4">
                                <div class="card-header bg-warning text-dark mb-3">
                                    <h5 class="mb-0"><i class="fas fa-comments"></i> Diskusi & Tanya Jawab (Bobot 20%)
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="hasil_yang_dicapai" class="form-label">Hasil yang Dicapai
                                                    <span class="text-danger">*</span></label>
                                                <input type="number" name="hasil_yang_dicapai" id="hasil_yang_dicapai"
                                                    class="form-control @error('hasil_yang_dicapai') is-invalid @enderror"
                                                    min="0" max="100"
                                                    value="{{ old('hasil_yang_dicapai') }}" required>
                                                @error('hasil_yang_dicapai')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="penguasaan_materi_diskusi" class="form-label">Penguasaan
                                                    Materi Diskusi <span class="text-danger">*</span></label>
                                                <input type="number" name="penguasaan_materi_diskusi"
                                                    id="penguasaan_materi_diskusi"
                                                    class="form-control @error('penguasaan_materi_diskusi') is-invalid @enderror"
                                                    min="0" max="100"
                                                    value="{{ old('penguasaan_materi_diskusi') }}" required>
                                                @error('penguasaan_materi_diskusi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="objektivitas_tanggapan" class="form-label">Objektivitas
                                                    Tanggapan <span class="text-danger">*</span></label>
                                                <input type="number" name="objektivitas_tanggapan"
                                                    id="objektivitas_tanggapan"
                                                    class="form-control @error('objektivitas_tanggapan') is-invalid @enderror"
                                                    min="0" max="100"
                                                    value="{{ old('objektivitas_tanggapan') }}" required>
                                                @error('objektivitas_tanggapan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="kemampuan_mempertahankan_ide" class="form-label">Kemampuan
                                                    Mempertahankan Ide <span class="text-danger">*</span></label>
                                                <input type="number" name="kemampuan_mempertahankan_ide"
                                                    id="kemampuan_mempertahankan_ide"
                                                    class="form-control @error('kemampuan_mempertahankan_ide') is-invalid @enderror"
                                                    min="0" max="100"
                                                    value="{{ old('kemampuan_mempertahankan_ide') }}" required>
                                                @error('kemampuan_mempertahankan_ide')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tombol Submit --}}
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('jadwal-sidang.show', $jadwalSidang->id) }}"
                                            class="btn btn-secondary">
                                            <i class="fas fa-arrow-left"></i> Kembali
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Simpan Penilaian
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script untuk validasi form --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validasi input number
            const numberInputs = document.querySelectorAll('input[type="number"]');

            numberInputs.forEach(input => {
                input.addEventListener('input', function() {
                    let value = parseInt(this.value);

                    if (value < 0) {
                        this.value = 0;
                    } else if (value > 100) {
                        this.value = 100;
                    }
                });

                // Tambahkan tooltip untuk rentang nilai
                input.setAttribute('title', 'Masukkan nilai antara 0-100');
            });

            // Konfirmasi sebelum submit
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                if (!confirm(
                        'Apakah Anda yakin ingin menyimpan penilaian ini? Pastikan semua nilai sudah benar.'
                        )) {
                    e.preventDefault();
                }
            });
        });
    </script>

    <style>
        .card-header {
            border-bottom: 2px solid rgba(0, 0, 0, 0.125);
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .alert-info {
            border-left: 4px solid #17a2b8;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        input[type="number"]:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .invalid-feedback {
            display: block;
        }
    </style>
@endsection
