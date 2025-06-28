@extends('layouts.dashboard')

@section('title', 'Daftar Penilaian Sidang')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Daftar Penilaian Sidang</h3>
                    <p class="text-subtitle text-muted">Kelola penilaian sidang tugas akhir mahasiswa</p>
                </div>
            </div>
        </div>
    </div>

    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title">Data Penilaian Sidang</h4>
                            </div>
                            <div class="col-auto">
                                <!-- Filter Status jika diperlukan -->
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle"
                                        data-bs-toggle="dropdown">
                                        <i class="bi bi-funnel"></i> Filter
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('penilaian-sidang.index') }}">Semua</a>
                                        </li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('penilaian-sidang.index', ['filter' => 'terbaru']) }}">Terbaru</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($penilaianSidangs->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Mahasiswa</th>
                                            <th>NRP</th>
                                            <th>Judul Tugas Akhir</th>
                                            <th>Penguji</th>
                                            <th>Nilai Akhir</th>
                                            <th>Grade</th>
                                            <th>Tanggal Penilaian</th>
                                            <th class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($penilaianSidangs as $index => $penilaian)
                                            <tr>
                                                <td>{{ $penilaianSidangs->firstItem() + $index }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <h6 class="mb-0">
                                                                {{ $penilaian->jadwalSidang->tugasAkhir->mahasiswa->user->name }}
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge bg-light-primary">{{ $penilaian->jadwalSidang->tugasAkhir->mahasiswa->user->nrp }}</span>
                                                </td>
                                                <td>
                                                    <div class="text-truncate" style="max-width: 250px;"
                                                        title="{{ $penilaian->jadwalSidang->tugasAkhir->judul }}">
                                                        {{ $penilaian->jadwalSidang->tugasAkhir->judul }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-xs me-2">
                                                            <div class="avatar-img">
                                                                <img src="{{ $penilaian->pengujiSidang->dosen->user->image ?? asset('images/faces/default-avatar.jpg') }}"
                                                                    alt="Avatar" class="rounded-circle">
                                                            </div>
                                                        </div>
                                                        <small>{{ $penilaian->pengujiSidang->dosen->user->name }}</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge
                                                    @if ($penilaian->nilai_akhir >= 80) bg-success
                                                    @elseif($penilaian->nilai_akhir >= 70) bg-primary
                                                    @elseif($penilaian->nilai_akhir >= 60) bg-warning
                                                    @elseif($penilaian->nilai_akhir >= 50) bg-info
                                                    @else bg-danger @endif
                                                ">
                                                        {{ number_format($penilaian->nilai_akhir, 2) }}
                                                    </span>
                                                </td>
                                                <td>

                                                    @php
                                                        $grade = '';
                                                        $badgeClass = '';

                                                        if ($penilaian->nilai_akhir >= 90) {
                                                            $grade = 'A';
                                                            $badgeClass = 'bg-success';
                                                        } elseif ($penilaian->nilai_akhir >= 80) {
                                                            $grade = 'A-';
                                                            $badgeClass = 'bg-success';
                                                        } elseif ($penilaian->nilai_akhir >= 75) {
                                                            $grade = 'B+';
                                                            $badgeClass = 'bg-primary';
                                                        } elseif ($penilaian->nilai_akhir >= 70) {
                                                            $grade = 'B';
                                                            $badgeClass = 'bg-primary';
                                                        } elseif ($penilaian->nilai_akhir >= 65) {
                                                            $grade = 'B-';
                                                            $badgeClass = 'bg-primary';
                                                        } elseif ($penilaian->nilai_akhir >= 60) {
                                                            $grade = 'C+';
                                                            $badgeClass = 'bg-warning text-dark';
                                                        } elseif ($penilaian->nilai_akhir >= 55) {
                                                            $grade = 'C';
                                                            $badgeClass = 'bg-warning text-dark';
                                                        } elseif ($penilaian->nilai_akhir >= 50) {
                                                            $grade = 'D';
                                                            $badgeClass = 'bg-info text-dark';
                                                        } else {
                                                            $grade = 'E';
                                                            $badgeClass = 'bg-danger';
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $badgeClass }}">{{ $grade }}</span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $penilaian->created_at->format('d/m/Y H:i') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <!-- Tombol Detail -->
                                                        @can('show penilaian-sidang')
                                                            <a href="{{ route('penilaian-sidang.show', $penilaian) }}"
                                                                class="btn btn-sm btn-outline-info" title="Detail Penilaian">
                                                                <i class="bi bi-eye"></i>
                                                            </a>
                                                        @endcan


                                                        <!-- Tombol Edit -->
                                                        @can('edit penilaian-sidang')
                                                            <a href="{{ route('penilaian-sidang.edit', $penilaian) }}"
                                                                class="btn btn-sm btn-outline-warning" title="Edit Penilaian">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                        @endcan

                                                        <!-- Tombol Hapus -->
                                                        @can('delete penilaian-sidang')
                                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                                title="Hapus Penilaian"
                                                                onclick="confirmDelete('{{ $penilaian->id }}', '{{ $penilaian->jadwalSidang->tugasAkhir->mahasiswa->user->name }}')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        @endcan

                                                        <!-- Tombol Rekapitulasi -->
                                                        <a href="{{ route('penilaian-sidang.preview', $penilaian) }}" class="btn btn-sm btn-outline-success"
                                                            title="Cetak Nilai">
                                                            <i class="bi bi-printer"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div class="text-muted">
                                    Menampilkan {{ $penilaianSidangs->firstItem() }} - {{ $penilaianSidangs->lastItem() }}
                                    dari {{ $penilaianSidangs->total() }} data
                                </div>
                                {{ $penilaianSidangs->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-clipboard-data" style="font-size: 4rem; color: #ddd;"></i>
                                </div>
                                <h5 class="text-muted">Belum Ada Data Penilaian</h5>
                                <p class="text-muted">Data penilaian sidang akan muncul setelah dosen memberikan penilaian.
                                </p>
                            </div>
                        @endif
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
                    <p>Apakah Anda yakin ingin menghapus penilaian untuk mahasiswa <strong id="studentName"></strong>?</p>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
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
            function confirmDelete(penilaianId, studentName) {
                document.getElementById('studentName').textContent = studentName;
                document.getElementById('deleteForm').action = `{{ route('penilaian-sidang.index') }}/${penilaianId}`;

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
            .table-hover tbody tr:hover {
                background-color: rgba(0, 0, 0, 0.02);
            }

            .avatar-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .text-truncate {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .btn-group .btn {
                border-radius: 0.375rem;
                margin-right: 2px;
            }

            .btn-group .btn:last-child {
                margin-right: 0;
            }

            .badge {
                font-size: 0.75em;
                font-weight: 500;
            }

            @media (max-width: 768px) {
                .table-responsive {
                    font-size: 0.875rem;
                }

                .btn-group .btn {
                    padding: 0.25rem 0.5rem;
                }

                .btn-group .btn i {
                    font-size: 0.875rem;
                }
            }
        </style>
    @endpush
@endsection
