@extends('layouts.dashboard')

@section('title', 'Detail Jadwal Sidang')

@section('content')
    <div class="container-fluid">
        <div class="row">
            @if (session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Detail Jadwal Sidang</h4>
                        <div class="btn-group">
                            @can('edit jadwal-sidang')
                                <a href="{{ route('jadwal-sidang.edit', $jadwalSidang) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endcan

                            {{-- <a href="{{ route('penilaian-sidang.create', $jadwalSidang->id) }}"><i class="fas fa-plus"></i>
                                Beri Penilaian</a> --}}

                            <a href="{{ route('penilaian-sidang.create', $jadwalSidang->id) }}"
                                class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Beri Penilaian
                            </a>

                            <a href="{{ route('jadwal-sidang.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="row">
                            <!-- Informasi Mahasiswa -->
                            <div class="col-md-6">
                                <div class="card border-primary mb-3">
                                    <div class="card-header bg-primary text-white mb-3">
                                        <h6 class="mb-0"><i class="fas fa-user-graduate"></i> Informasi Mahasiswa</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="40%"><strong>Nama</strong></td>
                                                <td>: {{ $jadwalSidang->tugasAkhir->mahasiswa->user->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>NRP</strong></td>
                                                <td>: {{ $jadwalSidang->tugasAkhir->mahasiswa->user->nrp ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Judul Tugas Akhir</strong></td>
                                                <td>: {{ $jadwalSidang->tugasAkhir->judul ?? '-' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Informasi Sidang -->
                            <div class="col-md-6">
                                <div class="card border-info mb-3">
                                    <div class="card-header bg-info text-white mb-3">
                                        <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Informasi Sidang</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-borderless">
                                            <tr>
                                                <td width="40%"><strong>Jenis Sidang</strong></td>
                                                <td>:
                                                    <span class="badge badge-secondary">
                                                        {{ ucfirst($jadwalSidang->jenis_sidang) }}
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><strong>Status</strong></td>
                                                <td>:
                                                    <span
                                                        class="badge
                                                    @switch($jadwalSidang->status)
                                                        @case('dijadwalkan') badge-primary @break
                                                        @case('berlangsung') badge-warning @break
                                                        @case('selesai') badge-success @break
                                                        @case('ditunda') badge-secondary @break
                                                        @case('dibatalkan') badge-danger @break
                                                        @default badge-light
                                                    @endswitch">
                                                        {{ ucfirst($jadwalSidang->status) }}
                                                    </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><strong>Tanggal & Waktu</strong></td>
                                                <td>:
                                                    {{ $jadwalSidang->tanggal_sidang->translatedFormat('l, d F Y H:i') }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><strong>Tempat</strong></td>
                                                <td>: {{ $jadwalSidang->tempat_sidang }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Ruang</strong></td>
                                                <td>: {{ $jadwalSidang->ruang_sidang ?: '-' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Penguji -->
                        <div class="card border-success mb-3">
                            <div
                                class="card-header bg-success text-white d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="fas fa-users"></i> Daftar Penguji</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Nama Dosen</th>
                                                <th>Peran</th>
                                                <th>Status Kehadiran</th>
                                                <th>Status Penilaian</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($jadwalSidang->pengujiSidangs as $index => $penguji)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $penguji->dosen->user->name ?? '-' }}</td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            {{ ucwords(str_replace('_', ' ', $penguji->peran)) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge {{ $penguji->hadir ? 'bg-success' : 'bg-secondary' }}">
                                                            {{ $penguji->hadir ? 'Hadir' : 'Belum Hadir' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $penilaian = \App\Models\PenilaianSidang::where(
                                                                'jadwal_sidang_id',
                                                                $jadwalSidang->id,
                                                            )
                                                                ->where('penguji_sidang_id', $penguji->id)
                                                                ->first();
                                                        @endphp
                                                        <span class="badge {{ $penilaian ? 'bg-success' : 'bg-warning' }}">
                                                            {{ $penilaian ? 'Sudah Dinilai' : 'Belum Dinilai' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">Tidak ada penguji</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- File dan Catatan -->
                        @if ($jadwalSidang->file_sidang || $jadwalSidang->catatan)
                            <div class="card border-warning mb-3">
                                <div class="card-header bg-warning text-dark mb-3">
                                    <h6 class="mb-0"><i class="fas fa-file-alt"></i> File dan Catatan</h6>
                                </div>
                                <div class="card-body">

                                    @if ($jadwalSidang->file_sidang)
                                        <div class="mb-3">
                                            <strong>File Sidang:</strong>
                                            <div class="mt-2">
                                                <a href="{{ asset('storage/' . $jadwalSidang->file_sidang) }}"
                                                    class="btn btn-outline-success btn-sm mb-1" target="_blank">
                                                    <i class="fas fa-download"></i> Download File Sidang
                                                </a>
                                            </div>
                                        </div>
                                    @endif


                                    @if ($jadwalSidang->catatan)
                                        <div>
                                            <strong>Catatan:</strong>
                                            <div class="mt-2 p-3 bg-light rounded">
                                                {{ $jadwalSidang->catatan }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Update Status Sidang</h6>
                                    </div>
                                    <div class="card-body">

                                        @can('edit update-status')
                                            <form action="{{ route('jadwal-sidang.update-status', $jadwalSidang) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('POST')
                                                <div class="form-group">
                                                    <select name="status" class="form-control" onchange="this.form.submit()">
                                                        <option value="dijadwalkan"
                                                            {{ $jadwalSidang->status === 'dijadwalkan' ? 'selected' : '' }}>
                                                            Dijadwalkan</option>
                                                        <option value="berlangsung"
                                                            {{ $jadwalSidang->status === 'berlangsung' ? 'selected' : '' }}>
                                                            Berlangsung</option>
                                                        <option value="selesai"
                                                            {{ $jadwalSidang->status === 'selesai' ? 'selected' : '' }}>Selesai
                                                        </option>
                                                        <option value="ditunda"
                                                            {{ $jadwalSidang->status === 'ditunda' ? 'selected' : '' }}>Ditunda
                                                        </option>
                                                        <option value="dibatalkan"
                                                            {{ $jadwalSidang->status === 'dibatalkan' ? 'selected' : '' }}>
                                                            Dibatalkan</option>
                                                    </select>
                                                </div>
                                            </form>
                                        @endcan

                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Update Kehadiran Penguji</h6>
                                    </div>
                                    <div class="card-body">
                                        @can('edit update-kehadiran')
                                            @php
                                                // Filter penguji yang merupakan dosen yang sedang login
                                                $pengujiLogin = $jadwalSidang->pengujiSidangs->where(
                                                    'dosen.user_id',
                                                    auth()->id(),
                                                );
                                            @endphp

                                            @if ($pengujiLogin->count() > 0)
                                                <form action="{{ route('jadwal-sidang.update-kehadiran', $jadwalSidang) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('POST')

                                                    @foreach ($pengujiLogin as $penguji)
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox"
                                                                name="kehadiran[{{ $penguji->id }}]" value="1"
                                                                {{ $penguji->hadir ? 'checked' : '' }}
                                                                id="kehadiran_{{ $penguji->id }}">
                                                            <label class="form-check-label"
                                                                for="kehadiran_{{ $penguji->id }}">
                                                                {{ $penguji->dosen->user->name ?? 'Unknown' }}
                                                                ({{ ucwords(str_replace('_', ' ', $penguji->peran)) }})
                                                            </label>
                                                        </div>
                                                    @endforeach

                                                    <button type="submit" class="btn btn-primary btn-sm mt-2">
                                                        <i class="fas fa-save"></i> Update Kehadiran
                                                    </button>
                                                </form>
                                            @else
                                                <div class="alert alert-info">
                                                    <i class="fas fa-info-circle"></i>
                                                    Anda tidak terdaftar sebagai penguji pada sidang ini.
                                                </div>
                                            @endif
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
