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

    <div class="page-heading mb-3">
        <h3>Dashboard</h3>
        <p class="text-muted">Selamat datang, {{ auth()->user()->name }}!</p>
    </div>

    <div class="page-content">
        <section class="row">
            {{-- Konten Utama --}}
            <div class="col-12 col-lg-9">
                {{-- Statistik --}}
                <div class="row">
                    @foreach ([['label' => 'Mahasiswa Bimbingan', 'value' => $totalMahasiswaBimbingan ?? 0, 'icon' => 'User', 'color' => 'blue'], ['label' => 'Bimbingan Aktif', 'value' => $totalBimbinganAktif ?? 0, 'icon' => 'Bookmark', 'color' => 'green'], ['label' => 'Siap Sidang', 'value' => $mahasiswaSiapSidang ?? 0, 'icon' => 'Profile', 'color' => 'purple'], ['label' => 'Perlu Review', 'value' => $perluReview ?? 0, 'icon' => 'Danger', 'color' => 'red']] as $stat)
                        <div class="col-12 col-md-6 col-lg-6 mb-3">
                            <div class="card">
                                <div class="card-body px-4 py-4-5">
                                    <div class="row">
                                        <div class="col-xxl-5 d-flex justify-content-start">
                                            <div class="stats-icon {{ $stat['color'] }} mb-2">
                                                <i class="iconly-bold{{ $stat['icon'] }}"></i>
                                            </div>
                                        </div>
                                        <div class="col-xxl-7">
                                            <h6 class="text-muted font-semibold">{{ $stat['label'] }}</h6>
                                            <h6 class="font-extrabold mb-0">{{ number_format($stat['value']) }}</h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Chart Bimbingan --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Aktivitas Bimbingan 6 Bulan Terakhir</h4>
                    </div>
                    <div class="card-body">
                        <div id="chart-bimbingan-dosen"></div>
                    </div>
                </div>

                {{-- Tabel Progress & Jadwal --}}
                <div class="row">
                    {{-- Progress --}}
                    <div class="col-12 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Progress Mahasiswa Bimbingan</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Mahasiswa</th>
                                                <th>Progress</th>
                                                <th>Last Update</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($mahasiswaProgress ?? [] as $mahasiswa)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar avatar-sm">
                                                                <img src="{{ $mahasiswa->user->image ?? asset('mazer/dist/assets/compiled/jpg/1.jpg') }}"
                                                                    alt="Avatar">
                                                            </div>
                                                            <div class="ms-2">
                                                                <h6 class="mb-0">{{ $mahasiswa->user->name }}</h6>
                                                                <small class="text-muted">{{ $mahasiswa->user->nrp }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="progress progress-sm">
                                                            <div class="progress-bar bg-{{ $mahasiswa->progress >= 70 ? 'success' : ($mahasiswa->progress >= 30 ? 'warning' : 'danger') }}"
                                                                style="width: {{ $mahasiswa->progress }}%"></div>
                                                        </div>
                                                        <small>{{ $mahasiswa->progress }}%</small>
                                                    </td>
                                                    <td>{{ $mahasiswa->last_update->format('d/m/Y') ?? 'N/A' }}</td>
                                                    <td>
                                                        <a href="{{ route('bimbingan.show', $mahasiswa->id) }}"
                                                            class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center">Belum ada mahasiswa bimbingan
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Jadwal Hari Ini --}}
                    <div class="col-12 col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4>Jadwal Bimbingan Hari Ini</h4>
                            </div>
                            <div class="card-body">
                                @forelse($jadwalHariIni ?? [] as $jadwal)
                                    <div class="d-flex align-items-center mb-3 p-3 border rounded">
                                        <div class="avatar avatar-md">
                                            <img src="{{ $jadwal->mahasiswa->user->image ?? asset('mazer/dist/assets/compiled/jpg/1.jpg') }}"
                                                alt="Avatar">
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <h6 class="mb-1">{{ $jadwal->mahasiswa->user->name }}</h6>
                                            {{-- <p class="text-muted mb-0">
                                                <i
                                                    class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($jadwal->waktu)->format('H:i') }}
                                            </p> --}}
                                        </div>
                                        <div>
                                            <span
                                                class="badge bg-{{ $jadwal->status == 'confirmed' ? 'success' : 'warning' }}">
                                                {{ ucfirst($jadwal->status) }}
                                            </span>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-4">
                                        <i class="bi bi-calendar-x display-4 text-muted"></i>
                                        <p class="text-muted mt-2">Tidak ada jadwal bimbingan hari ini</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-12 col-lg-3">
                {{-- Profil Dosen --}}
                <div class="card">
                    <div class="card-body py-4 px-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xl">
                                <img src="{{ auth()->user()->image ?? asset('mazer/dist/assets/compiled/jpg/1.jpg') }}"
                                    alt="Face">
                            </div>
                            <div class="ms-3 name">
                                <h5 class="font-bold">{{ auth()->user()->name }}</h5>
                                <h6 class="text-muted mb-0">{{ auth()->user()->dosen->nidn ?? 'N/A' }}</h6>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pengajuan Baru --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Pengajuan Bimbingan Baru</h4>
                    </div>
                    <div class="card-content pb-4" style="max-height: 300px; overflow-y: auto;">
                        @forelse($pengajuanBaru ?? [] as $pengajuan)
                            <div class="recent-message d-flex px-4 py-3 border-bottom">
                                <div class="avatar avatar-lg">
                                    <img src="{{ $pengajuan->mahasiswa->user->image ?? asset('mazer/dist/assets/compiled/jpg/1.jpg') }}"
                                        alt="Avatar">
                                </div>
                                <div class="name ms-4">
                                    <h6 class="mb-1">{{ $pengajuan->mahasiswa->user->name }}</h6>
                                    <p class="text-muted mb-0 small">{{ Str::limit($pengajuan->judul, 30) }}</p>
                                    <small class="text-primary">{{ $pengajuan->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-3">
                                <p class="text-muted">Tidak ada pengajuan baru</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="card">
                    <div class="card-header">
                        <h4>Quick Actions</h4>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('bimbingan.index') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-list me-2"></i>Lihat Semua Bimbingan
                            </a>
                            <a href="{{ route('pengajuan-tugas-akhir.index') }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-file-earmark-text me-2"></i>Review Pengajuan
                            </a>
                            <a href="{{ route('jadwal-sidang.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-calendar me-2"></i>Atur Jadwal
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

    {{-- Chart Scripts --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.3/apexcharts.min.js"></script>
    <script>
        const bimbinganDosenData = @json($bimbinganBulanan ?? []);
        new ApexCharts(document.querySelector("#chart-bimbingan-dosen"), {
            series: [{
                name: 'Bimbingan',
                data: bimbinganDosenData.map(item => item.total)
            }],
            chart: {
                type: 'bar',
                height: 350
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    horizontal: false
                }
            },
            xaxis: {
                categories: bimbinganDosenData.map(item => item.bulan)
            },
            colors: ['#435ebe'],
            dataLabels: {
                enabled: false
            }
        }).render();
    </script>
@endsection
