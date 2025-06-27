@extends('layouts.dashboard')

@section('title', 'Detail Penilaian Sidang')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Detail Penilaian Sidang</h3>
                    <p class="text-subtitle text-muted">Detail penilaian sidang tugas akhir</p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('penilaian-sidang.index') }}">Penilaian Sidang</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Detail</li>
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
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Informasi Mahasiswa</h4>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('penilaian-sidang.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar avatar-lg mb-3">
                                        <img src="{{ $penilaianSidang->jadwalSidang->tugasAkhir->mahasiswa->user->image ?? asset('images/faces/default-avatar.jpg') }}"
                                            alt="Avatar" class="rounded-circle">
                                    </div>
                                    <div>
                                        <h5 class="mb-1">
                                            {{ $penilaianSidang->jadwalSidang->tugasAkhir->mahasiswa->user->name }}</h5>
                                        <span class="badge bg-light-primary">NRP:
                                            {{ $penilaianSidang->jadwalSidang->tugasAkhir->mahasiswa->user->nrp }}</span>
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
                                    <strong>Tempat:</strong>
                                    <span class="text-muted">{{ $penilaianSidang->jadwalSidang->tempat_sidang }}</span>

                                </div>

                                <div class="mb-2">
                                    <strong>Ruangan:</strong>
                                    <span class="text-muted">{{ $penilaianSidang->jadwalSidang->ruang_sidang }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-1">
                            <div class="col-12">
                                <strong>Judul Tugas Akhir:</strong>
                                <p class="text-muted mb-0">{{ $penilaianSidang->jadwalSidang->tugasAkhir->judul }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Penguji -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Informasi Penguji</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-md me-3">
                                <img src="{{ $penilaianSidang->pengujiSidang->dosen->user->image ?? asset('images/faces/default-avatar.jpg') }}"
                                    alt="Avatar" class="rounded-circle">
                            </div>
                            <div>
                                <h5 class="mb-1">{{ $penilaianSidang->pengujiSidang->dosen->user->name }}</h5>
                                <small
                                    class="text-muted">{{ $penilaianSidang->pengujiSidang->dosen->nidn ?? 'NIP tidak tersedia' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Penilaian -->
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Detail Penilaian</h4>
                            </div>
                            <div class="col-auto">
                                @if (Auth::user()->hasRole('admin') || $penilaianSidang->pengujiSidang->dosen->user_id == Auth::id())
                                    <a href="{{ route('penilaian-sidang.edit', $penilaianSidang) }}"
                                        class="btn btn-warning">
                                        <i class="bi bi-pencil me-2"></i>Edit Penilaian
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Materi Skripsi (Bobot 50%) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="bi bi-book me-2"></i>Materi Skripsi (Bobot 50%)
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Originalitas Materi</label>
                                            <div class="progress mb-1">
                                                <div class="progress-bar bg-warning"
                                                    style="width: {{ $penilaianSidang->originalitas_materi }}%"></div>
                                            </div>
                                            <small
                                                class="text-muted">{{ $penilaianSidang->originalitas_materi }}/100</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Analisa dan Metodologi</label>
                                            <div class="progress mb-1">
                                                <div class="progress-bar bg-warning"
                                                    style="width: {{ $penilaianSidang->analisa_metodologi }}%">
                                                </div>
                                            </div>
                                            <small
                                                class="text-muted">{{ $penilaianSidang->analisa_metodologi }}/100</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Tingkat aplikasi materi</label>
                                            <div class="progress mb-1">
                                                <div class="progress-bar bg-warning"
                                                    style="width: {{ $penilaianSidang->tingkat_aplikasi_materi }}%">
                                                </div>
                                            </div>
                                            <small
                                                class="text-muted">{{ $penilaianSidang->tingkat_aplikasi_materi }}/100</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Pengembangan dan kreativitas</label>
                                            <div class="progress mb-1">
                                                <div class="progress-bar bg-warning"
                                                    style="width: {{ $penilaianSidang->pengembangan_kreativitas }}%">
                                                </div>
                                            </div>
                                            <small
                                                class="text-muted">{{ $penilaianSidang->pengembangan_kreativitas }}/100</small>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Tata tulis</label>
                                            <div class="progress mb-1">
                                                <div class="progress-bar bg-warning"
                                                    style="width: {{ $penilaianSidang->tata_tulis }}%">
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ $penilaianSidang->tata_tulis }}/100</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Penyajian (Bobot 30%) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="bi bi-book me-2"></i>Penyajian (Bobot 30%)
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Penguasaan Materi</label>
                                            <div class="progress mb-1">
                                                <div class="progress-bar bg-warning"
                                                    style="width: {{ $penilaianSidang->penguasaan_materi }}%"></div>
                                            </div>
                                            <small
                                                class="text-muted">{{ $penilaianSidang->penguasaan_materi }}/100</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Sikap dan penampilan</label>
                                            <div class="progress mb-1">
                                                <div class="progress-bar bg-warning"
                                                    style="width: {{ $penilaianSidang->sikap_dan_penampilan }}%">
                                                </div>
                                            </div>
                                            <small
                                                class="text-muted">{{ $penilaianSidang->sikap_dan_penampilan }}/100</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Penyiapan sarana dan sistematika</label>
                                            <div class="progress mb-1">
                                                <div class="progress-bar bg-warning"
                                                    style="width: {{ $penilaianSidang->penyajian_sarana_sistematika }}%">
                                                </div>
                                            </div>
                                            <small
                                                class="text-muted">{{ $penilaianSidang->penyajian_sarana_sistematika }}/100</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Hasil yang dicapai</label>
                                            <div class="progress mb-1">
                                                <div class="progress-bar bg-warning"
                                                    style="width: {{ $penilaianSidang->hasil_yang_dicapai }}%">
                                                </div>
                                            </div>
                                            <small
                                                class="text-muted">{{ $penilaianSidang->hasil_yang_dicapai }}/100</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Diskusi (Bobot 20%) -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="bi bi-book me-2"></i>Diskusi (Bobot 20%)
                                </h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Penguasaan Materi</label>
                                            <div class="progress mb-1">
                                                <div class="progress-bar bg-warning"
                                                    style="width: {{ $penilaianSidang->penguasaan_materi_diskusi }}%">
                                                </div>
                                            </div>
                                            <small
                                                class="text-muted">{{ $penilaianSidang->penguasaan_materi_diskusi }}/100</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Obyektifitas dalam menanggapi
                                                pertanyaan</label>
                                            <div class="progress mb-1">
                                                <div class="progress-bar bg-warning"
                                                    style="width: {{ $penilaianSidang->objektivitas_tanggapan }}%">
                                                </div>
                                            </div>
                                            <small
                                                class="text-muted">{{ $penilaianSidang->objektivitas_tanggapan }}/100</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Kemampuan menjelaskan dan mempertahankan
                                                ide</label>
                                            <div class="progress mb-1">
                                                <div class="progress-bar bg-warning"
                                                    style="width: {{ $penilaianSidang->kemampuan_mempertahankan_ide }}%">
                                                </div>
                                            </div>
                                            <small
                                                class="text-muted">{{ $penilaianSidang->kemampuan_mempertahankan_ide }}/100</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Nilai Akhir -->
                        <div class="row">
                            <div class="col-12">
                                <div class="text-center">
                                    <h3 class="text-primary mb-2">Nilai Akhir</h3>
                                    @php
                                        $grade = '';
                                        $badgeClass = '';

                                        if ($penilaianSidang->nilai_akhir >= 90) {
                                            $grade = 'A';
                                            $badgeClass = 'bg-success';
                                        } elseif ($penilaianSidang->nilai_akhir >= 80) {
                                            $grade = 'A-';
                                            $badgeClass = 'bg-success';
                                        } elseif ($penilaianSidang->nilai_akhir >= 75) {
                                            $grade = 'B+';
                                            $badgeClass = 'bg-primary';
                                        } elseif ($penilaianSidang->nilai_akhir >= 70) {
                                            $grade = 'B';
                                            $badgeClass = 'bg-primary';
                                        } elseif ($penilaianSidang->nilai_akhir >= 65) {
                                            $grade = 'B-';
                                            $badgeClass = 'bg-primary';
                                        } elseif ($penilaianSidang->nilai_akhir >= 60) {
                                            $grade = 'C+';
                                            $badgeClass = 'bg-warning text-dark';
                                        } elseif ($penilaianSidang->nilai_akhir >= 55) {
                                            $grade = 'C';
                                            $badgeClass = 'bg-warning text-dark';
                                        } elseif ($penilaianSidang->nilai_akhir >= 50) {
                                            $grade = 'D';
                                            $badgeClass = 'bg-info text-dark';
                                        } else {
                                            $grade = 'E';
                                            $badgeClass = 'bg-danger';
                                        }
                                    @endphp


                                    <div class="d-flex justify-content-center align-items-center mb-3">
                                        <div class="me-4">
                                            <div class="display-4 fw-bold text-primary">
                                                {{ number_format($penilaianSidang->nilai_akhir, 2) }}</div>
                                            <small class="text-muted">dari 100</small>
                                        </div>
                                        <div>
                                            <span class="badge {{ $badgeClass }} fs-1 p-3">{{ $grade }}</span>
                                        </div>
                                    </div>

                                    <!-- Breakdown Nilai -->
                                    <div class="row justify-content-center">
                                        <div class="col-md-8">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h6 class="card-title mb-3">Breakdown Perhitungan Nilai</h6>
                                                    @php
                                                        $materiSkripsi =
                                                            ($penilaianSidang->originalitas_materi +
                                                                $penilaianSidang->analisa_metodologi +
                                                                $penilaianSidang->tingkat_aplikasi_materi +
                                                                $penilaianSidang->pengembangan_kreativitas +
                                                                $penilaianSidang->tata_tulis) /
                                                            5;

                                                        $penyajian =
                                                            ($penilaianSidang->penguasaan_materi +
                                                                $penilaianSidang->sikap_dan_penampilan +
                                                                $penilaianSidang->penyajian_sarana_sistematika +
                                                                $penilaianSidang->hasil_yang_dicapai) /
                                                            4;

                                                        $diskusi =
                                                            ($penilaianSidang->penguasaan_materi_diskusi +
                                                                $penilaianSidang->objektivitas_tanggapan +
                                                                $penilaianSidang->kemampuan_mempertahankan_ide) /
                                                            3;
                                                    @endphp

                                                    <div class="row text-start">
                                                        <div class="col-md-4 mb-2">
                                                            <small class="text-primary ">Materi Skripsi (50%)</small>
                                                            <div class="fw-bold">{{ number_format($materiSkripsi, 2) }} ×
                                                                0.5 = {{ number_format($materiSkripsi * 0.5, 2) }}</div>
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <small class="text-primary">Penyajian (30%)</small>
                                                            <div class="fw-bold">{{ number_format($penyajian, 2) }} × 0.3
                                                                = {{ number_format($penyajian * 0.3, 2) }}</div>
                                                        </div>
                                                        <div class="col-md-4 mb-2">
                                                            <small class="text-primary">Diskusi (20%)</small>
                                                            <div class="fw-bold">{{ number_format($diskusi, 2) }} × 0.2 =
                                                                {{ number_format($diskusi * 0.2, 2) }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Informasi Tambahan</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <strong>Tanggal Penilaian:</strong>
                                    <span class="text-muted">{{ $penilaianSidang->created_at->format('d F Y, H:i') }}
                                        WIB</span>
                                </div>
                                <div class="mb-2">
                                    <strong>Terakhir Diupdate:</strong>
                                    <span class="text-muted">{{ $penilaianSidang->updated_at->format('d F Y, H:i') }}
                                        WIB</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('penilaian-sidang.preview', $penilaianSidang) }}"
                                        class="btn btn-outline-success me-2">
                                        <i class="bi bi-printer me-2"></i>Cetak Penilaian
                                    </a>
                                    @if (Auth::user()->hasRole('admin') || $penilaianSidang->pengujiSidang->dosen->user_id == Auth::id())
                                        <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                                            <i class="bi bi-trash me-2"></i>Hapus Penilaian
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
                    <p>Apakah Anda yakin ingin menghapus penilaian untuk mahasiswa
                        <strong>{{ $penilaianSidang->jadwalSidang->tugasAkhir->mahasiswa->user->name }}</strong>?
                    </p>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('penilaian-sidang.destroy', $penilaianSidang) }}" method="POST"
                        style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash me-2"></i>Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete() {
                var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            }

            // Auto hide alerts after 5 seconds
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    var alerts = document.querySelectorAll('.alert');
                    alerts.forEach(function(alert) {
                        var bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    });
                }, 5000);
            });
        </script>
    @endpush

    @push('styles')
        <style>
            .progress {
                height: 8px;
                border-radius: 4px;
            }

            .progress-bar {
                border-radius: 4px;
            }

            .avatar img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .display-4 {
                font-size: 3rem;
            }

            .fs-1 {
                font-size: 2rem !important;
            }

            .card {
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                border: 1px solid rgba(0, 0, 0, 0.125);
            }

            .card-header {
                background-color: rgba(0, 0, 0, 0.03);
                border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            }

            .bg-light {
                background-color: #f8f9fa !important;
            }

            @media (max-width: 768px) {
                .display-4 {
                    font-size: 2rem;
                }

                .fs-1 {
                    font-size: 1.5rem !important;
                }

                .d-flex.justify-content-center.align-items-center {
                    flex-direction: column;
                }

                .d-flex.justify-content-center.align-items-center .me-4 {
                    margin-right: 0 !important;
                    margin-bottom: 1rem;
                }
            }
        </style>
    @endpush
@endsection
