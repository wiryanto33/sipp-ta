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
        <h3>Dashboard </h3>
    </div>
    <div class="page-content">
        <section class="row">
            <div class="col-12 col-lg-9">
                {{-- Statistik Cards --}}
                <div class="row">
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon purple mb-2">
                                            <i class="iconly-boldProfile"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Siap Sidang</h6>
                                        <h6 class="font-extrabold mb-0">{{ number_format($totalSiapSidang) }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon blue mb-2">
                                            <i class="iconly-boldUser"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Total Dosen</h6>
                                        <h6 class="font-extrabold mb-0">{{ number_format($totalDosen) }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon green mb-2">
                                            <i class="iconly-boldBookmark"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Bimbingan Aktif</h6>
                                        <h6 class="font-extrabold mb-0">{{ number_format($totalBimbinganAktif) }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3 col-md-6">
                        <div class="card">
                            <div class="card-body px-4 py-4-5">
                                <div class="row">
                                    <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start">
                                        <div class="stats-icon red mb-2">
                                            <i class="iconly-boldDanger"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                        <h6 class="text-muted font-semibold">Pengajuan Pending</h6>
                                        <h6 class="font-extrabold mb-0">{{ number_format($totalPengajuanPending) }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Chart Bimbingan Bulanan --}}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4>Trend Bimbingan 6 Bulan Terakhir</h4>
                            </div>
                            <div class="card-body">
                                <div id="chart-bimbingan-bulanan"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Progress Stats & Top Dosen --}}
                <div class="row">
                    <div class="col-12 col-xl-4">
                        <div class="card">
                            <div class="card-header">
                                <h4>Progress Bimbingan</h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-7">
                                        <div class="d-flex align-items-center">
                                            <svg class="bi text-danger" width="32" height="32" fill="red"
                                                style="width:10px">
                                                <use
                                                    xlink:href="{{ asset('mazer/dist/assets/static/images/bootstrap-icons.svg') }}#circle-fill" />
                                            </svg>
                                            <h5 class="mb-0 ms-3">Progress Rendah</h5>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <h5 class="mb-0 text-end">{{ $progressStats['rendah'] ?? 0 }}</h5>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-7">
                                        <div class="d-flex align-items-center">
                                            <svg class="bi text-warning" width="32" height="32" fill="orange"
                                                style="width:10px">
                                                <use
                                                    xlink:href="{{ asset('mazer/dist/assets/static/images/bootstrap-icons.svg') }}#circle-fill" />
                                            </svg>
                                            <h5 class="mb-0 ms-3">Progress Sedang</h5>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <h5 class="mb-0 text-end">{{ $progressStats['sedang'] ?? 0 }}</h5>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-7">
                                        <div class="d-flex align-items-center">
                                            <svg class="bi text-success" width="32" height="32" fill="green"
                                                style="width:10px">
                                                <use
                                                    xlink:href="{{ asset('mazer/dist/assets/static/images/bootstrap-icons.svg') }}#circle-fill" />
                                            </svg>
                                            <h5 class="mb-0 ms-3">Progress Tinggi</h5>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <h5 class="mb-0 text-end">{{ $progressStats['tinggi'] ?? 0 }}</h5>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-7">
                                        <div class="d-flex align-items-center">
                                            <svg class="bi text-secondary" width="32" height="32" fill="gray"
                                                style="width:10px">
                                                <use
                                                    xlink:href="{{ asset('mazer/dist/assets/static/images/bootstrap-icons.svg') }}#circle-fill" />
                                            </svg>
                                            <h5 class="mb-0 ms-3">Tanpa Log</h5>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <h5 class="mb-0 text-end">{{ $progressStats['tanpa_log'] ?? 0 }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-xl-8">
                        <div class="card">
                            <div class="card-header">
                                <h4>Log Bimbingan Terbaru</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-lg">
                                        <thead>
                                            <tr>
                                                <th>Mahasiswa</th>
                                                <th>Dosen</th>
                                                <th>Progress</th>
                                                <th>Tanggal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($logBimbinganTerbaru as $log)
                                                <tr>
                                                    <td class="col-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar avatar-md">
                                                                <img src="{{ $log->bimbingan->pengajuanTugasAkhir->mahasiswa->user->image ?? asset('mazer/dist/assets/compiled/jpg/1.jpg') }}"
                                                                    alt="Avatar">
                                                            </div>
                                                            <p class="font-bold ms-3 mb-0">
                                                                {{ $log->bimbingan->pengajuanTugasAkhir->mahasiswa->user->name ?? 'N/A' }}
                                                            </p>
                                                        </div>
                                                    </td>
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
                                                    <td colspan="4" class="text-center">Belum ada log bimbingan</td>
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
                                <h6 class="text-muted mb-0">{{ auth()->user()->pangkat ?? 'N/A' }}  {{auth()->user()->korps ?? 'N/A'}}</h6>
                                <h6 class="text-muted mb-0">{{ auth()->user()->nrp ?? 'N/A' }}</h6>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Top Dosen --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Top Dosen Pembimbing</h4>
                    </div>
                    <div class="card-content pb-4" style="max-height: 300px; overflow-y: auto;">
                        @forelse($dosenTerbanyak as $dosen)
                            <div class="recent-message d-flex px-4 py-3 border-bottom">
                                <div class="avatar avatar-lg">
                                    <img src="{{ $dosen->user->image ?? asset('mazer/dist/assets/compiled/jpg/1.jpg') }}"
                                        alt="Avatar">
                                </div>
                                <div class="name ms-4">
                                    <h5 class="mb-1">{{ $dosen->user->name }}</h5>
                                    <h6 class="text-muted mb-0">{{ $dosen->bimbingans_count }} bimbingan aktif</h6>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-3">
                                <p class="text-muted">Belum ada data dosen</p>
                            </div>
                        @endforelse
                    </div>
                </div>


                {{-- Mahasiswa per Angkatan --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Mahasiswa per Angkatan</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart-mahasiswa-angkatan"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- Scripts untuk Chart --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.3/apexcharts.min.js"></script>
    <script>
        // Chart Bimbingan Bulanan
        const bimbinganBulananData = @json($bimbinganBulanan);
        const chartBimbinganBulanan = new ApexCharts(document.querySelector("#chart-bimbingan-bulanan"), {
            series: [{
                name: 'Bimbingan',
                data: bimbinganBulananData.map(item => item.total)
            }],
            chart: {
                type: 'line',
                height: 350
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                categories: bimbinganBulananData.map(item => item.bulan)
            },
            colors: ['#435ebe'],
            markers: {
                size: 5
            }
        });
        chartBimbinganBulanan.render();

        // Chart Mahasiswa per Angkatan
        const mahasiswaAngkatanData = @json($mahasiswaPerAngkatan);
        const chartMahasiswaAngkatan = new ApexCharts(document.querySelector("#chart-mahasiswa-angkatan"), {
            series: mahasiswaAngkatanData.map(item => item.total),
            chart: {
                width: 380,
                type: 'donut',
            },
            labels: mahasiswaAngkatanData.map(item => 'Angkatan ' + item.angkatan),
            colors: ['#435ebe', '#55c6e8', '#f39c12', '#e74c3c', '#9b59b6'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        });
        chartMahasiswaAngkatan.render();
    </script>
@endsection
