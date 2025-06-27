@extends('layouts.dashboard')

@section('content')
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>

    {{-- Modal Lengkapi Profil --}}
    @if (session('show_complete_profile_modal'))
        <div class="modal fade show d-block" tabindex="-1" role="dialog" id="completeProfileModal"
            style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Lengkapi Profil Anda</h5>
                    </div>
                    <div class="modal-body">
                        <p>Silakan lengkapi data profil Anda untuk dapat menggunakan aplikasi secara penuh.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('users.edit', Auth::id()) }}" class="btn btn-primary">Lengkapi Sekarang</a>
                    </div>
                </div>
            </div>
        </div>

        <style>
            body {
                overflow: hidden;
            }
        </style>
    @endif

    <div class="page-heading">
        <h3>Dashboard</h3>
        <p class="text-muted">Selamat datang, {{ auth()->user()->name }}!</p>
    </div>

    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-9">
                {{-- Status Cards --}}
                <div class="row mb-3">
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-4 d-flex justify-content-start">
                                        <div class="stats-icon blue mb-2">
                                            <i class="iconly-boldBookmark"></i>
                                        </div>
                                    </div>
                                    <div class="col-8">
                                        <h5 class="text-muted font-semibold">Progress TA</h5>
                                        <h5 class="font-extrabold mb-0">{{ $progressTA ?? 0 }}%</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-4 d-flex justify-content-start">
                                        <div class="stats-icon green mb-2">
                                            <i class="iconly-boldChat"></i>
                                        </div>
                                    </div>
                                    <div class="col-8">
                                        <h5 class="text-muted font-semibold">Total Bimbingan</h5>
                                        <h5 class="font-extrabold mb-0">{{ number_format($totalBimbingan ?? 0) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-4 d-flex justify-content-start">
                                        <div class="stats-icon purple mb-2">
                                            <i class="iconly-boldDocument"></i>
                                        </div>
                                    </div>
                                    <div class="col-8">
                                        <h5 class="text-muted font-semibold">Pengajuan</h5>
                                        <h5 class="font-extrabold mb-0">

                                            @if ($pengajuanTugasAkhir && $pengajuanTugasAkhir->status == 'diajukan')
                                                <span class="badge bg-warning">Diajukan</span>
                                            @elseif ($pengajuanTugasAkhir && $pengajuanTugasAkhir->status == 'diterima')
                                                <span class="badge bg-success">Diterima</span>
                                            @elseif ($pengajuanTugasAkhir && $pengajuanTugasAkhir->status == 'ditolak')
                                                <span class="badge bg-danger">Ditolak</span>
                                            @else
                                                <span class="badge bg-secondary">Belum Diajukan</span>
                                            @endif
                                        </h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-4 d-flex justify-content-start">
                                        <div class="stats-icon red mb-2">
                                            <i class="iconly-boldCalendar"></i>
                                        </div>
                                    </div>
                                    <div class="col-8">
                                        <h5 class="text-muted font-semibold">Bimbingan Bulan Ini</h5>
                                        <h5 class="font-extrabold mb-0">{{ number_format($bimbinganBulanIni ?? 0) }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Progress Chart --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Progress Bimbingan Saya</h4>
                            </div>
                            <div class="card-body">
                                <div id="chart-progress-mahasiswa"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Jadwal & Log Bimbingan --}}
                <div class="row">
                    <div class="col-12 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Jadwal Bimbingan Mendatang</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Dosen</th>
                                                <th>Tanggal</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($jadwalBimbingan ?? [] as $jadwal)
                                                <tr>
                                                    <td>{{ $jadwal->dosen->user->name ?? 'N/A' }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($jadwal->tanggal)->format('d/m/Y H:i') }}
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $jadwal->status == 'confirmed' ? 'success' : 'warning' }}">
                                                            {{ ucfirst($jadwal->status) }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Belum ada jadwal bimbingan</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Log Bimbingan Terbaru</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Dosen</th>
                                                <th>Progress</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($logBimbingan ?? [] as $log)
                                                <tr>
                                                    <td>{{ $log->bimbingan->dosen->user->name ?? 'N/A' }}</td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $log->progress >= 70 ? 'success' : ($log->progress >= 30 ? 'warning' : 'danger') }}">
                                                            {{ $log->progress }}%
                                                        </span>
                                                    </td>
                                                    <td>{{ $log->created_at->format('d/m/Y') }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3" class="text-center">Belum ada log bimbingan</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-12 col-lg-3">
                <div class="card">
                    <div class="card-body py-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl">
                                <img src="{{ auth()->user()->image ?? asset('mazer/dist/assets/compiled/jpg/1.jpg') }}"
                                    alt="Face 1">
                            </div>
                            <div class="ms-3 name">
                                <h5 class="font-bold">{{ auth()->user()->name }}</h5>
                                <h6 class="text-muted mb-0">{{ auth()->user()->mahasiswa->user->nrp ?? 'N/A' }}</h6>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Info Tugas Akhir --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Info Tugas Akhir</h4>
                    </div>
                    <div class="card-body">
                        @if ($pengajuanTA ?? null)
                            <h6 class="font-bold">{{ $pengajuanTA->judul ?? 'Belum ada judul' }}</h6>
                            <p class="text-muted mb-2">
                                <strong>Pembimbing 1:</strong><br>
                                {{ $pengajuanTA->pembimbing1->user->name ?? 'Belum ditentukan' }}
                            </p>
                            @if ($pengajuanTA->pembimbing2)
                                <p class="text-muted mb-2">
                                    <strong>Pembimbing 2:</strong><br>
                                    {{ $pengajuanTA->pembimbing2->user->name }}
                                </p>
                            @endif
                            <p class="text-muted mb-0">
                                <strong>Status:</strong>
                                <span
                                    class="badge bg-{{ $pengajuanTA->status == 'approved' ? 'success' : ($pengajuanTA->status == 'pending' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($pengajuanTA->status) }}
                                </span>
                            </p>
                        @else
                            <p class="text-muted">Belum ada pengajuan tugas akhir</p>
                            <a href="{{ route('pengajuan-tugas-akhir.create') }}" class="btn btn-primary btn-sm">Ajukan
                                Sekarang</a>
                        @endif
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('bimbingan.create') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-plus-circle me-2"></i>Ajukan Bimbingan
                            </a>
                            <a href="{{ route('bimbingan.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-list me-2"></i>Lihat Semua Bimbingan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- Scripts untuk Chart --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.3/apexcharts.min.js"></script>
    <script>
        // Chart Progress Mahasiswa
        const progressData = @json($progressBimbingan ?? []);
        const chartProgressMahasiswa = new ApexCharts(document.querySelector("#chart-progress-mahasiswa"), {
            series: [{
                name: 'Progress',
                data: progressData.map(item => item.progress)
            }],
            chart: {
                type: 'area',
                height: 350
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                categories: progressData.map(item => item.tanggal)
            },
            colors: ['#435ebe'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.9,
                    stops: [0, 90, 100]
                }
            }
        });
        chartProgressMahasiswa.render();
    </script>
@endsection
