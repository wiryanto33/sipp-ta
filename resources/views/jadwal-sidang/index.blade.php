@extends('layouts.dashboard')

@section('title', 'Jadwal Sidang')

@section('content')
    <div class="container-fluid">
        <div class="row">
            
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Daftar Jadwal Sidang</h3>
                        <div>
                            <a href="{{ route('jadwal-sidang.calendar') }}" class="btn btn-outline-primary me-2">
                                <i class="fas fa-calendar"></i> Kalender
                            </a>

                            @can('create jadwal-sidang')
                                <a href="{{ route('jadwal-sidang.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tambah Jadwal
                                </a>
                            @endcan

                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Filter Form -->
                        <form method="GET" class="mb-4">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="dijadwalkan"
                                            {{ request('status') == 'dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                                        <option value="berlangsung"
                                            {{ request('status') == 'berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>
                                            Selesai</option>
                                        <option value="ditunda" {{ request('status') == 'ditunda' ? 'selected' : '' }}>
                                            Ditunda</option>
                                        <option value="dibatalkan"
                                            {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="jenis" class="form-select">
                                        <option value="">Semua Jenis</option>
                                        <option value="proposal" {{ request('jenis') == 'proposal' ? 'selected' : '' }}>
                                            Proposal</option>
                                        <option value="skripsi" {{ request('jenis') == 'skripsi' ? 'selected' : '' }}>
                                            Skripsi</option>
                                        <option value="tugas_akhir"
                                            {{ request('jenis') == 'tugas_akhir' ? 'selected' : '' }}>Tugas Akhir</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="tanggal_dari" class="form-control"
                                        value="{{ request('tanggal_dari') }}" placeholder="Dari Tanggal">
                                </div>
                                <div class="col-md-2">
                                    <input type="date" name="tanggal_sampai" class="form-control"
                                        value="{{ request('tanggal_sampai') }}" placeholder="Sampai Tanggal">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
                                </div>
                            </div>
                        </form>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Mahasiswa</th>
                                        <th>Jenis Sidang</th>
                                        <th>Tanggal</th>
                                        <th>Tempat</th>
                                        <th>Status</th>
                                        <th>Penguji</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($jadwalSidangs as $jadwal)
                                        <tr>
                                            <td>
                                                <strong>{{ $jadwal->tugasAkhir->mahasiswa->user->name ?? 'N/A' }}</strong><br>
                                                <small
                                                    class="text-muted">{{ $jadwal->tugasAkhir->mahasiswa->user->nrp ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ ucfirst($jadwal->jenis_sidang) }}</span>
                                            </td>
                                            <td>
                                                {{ $jadwal->formatted_tanggal }}<br>
                                                <small class="text-muted">{{ $jadwal->ruang_sidang }}</small>
                                            </td>
                                            <td>{{ $jadwal->tempat_sidang }}</td>
                                            <td>
                                                <span class="badge {{ $jadwal->status_badge }}">
                                                    {{ ucfirst($jadwal->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $jadwal->pengujiSidangs->count() }} orang
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group">

                                                    @can('show jadwal-sidang')
                                                        <a href="{{ route('jadwal-sidang.show', $jadwal) }}"
                                                            class="btn btn-sm btn-outline-info">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endcan


                                                    @can('edit jadwal-sidang')
                                                        <a href="{{ route('jadwal-sidang.edit', $jadwal) }}"
                                                            class="btn btn-sm btn-outline-warning">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endcan

                                                    @can('delete jadwal-sidang')
                                                        <form action="{{ route('jadwal-sidang.destroy', $jadwal) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Yakin ingin menghapus?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <p class="text-muted">Tidak ada jadwal sidang ditemukan</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        {{ $jadwalSidangs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
