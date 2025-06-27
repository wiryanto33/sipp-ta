@extends('layouts.dashboard')

@section('content')

    <div class="col-md-12">
        <div class="p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-graduation-cap"></i> Pengajuan Tugas Akhir</h2>
                @if (auth()->user()->isMahasiswa())
                    <a href="{{ route('pengajuan-tugas-akhir.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Ajukan Tugas Akhir
                    </a>
                @endif
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    @if ($pengajuans->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th>No</th>
                                        @if (!auth()->user()->isMahasiswa())
                                            <th>Mahasiswa</th>
                                            <th>NRP</th>
                                            <th>Prodi</th>
                                        @endif
                                        <th>Judul</th>
                                        <th>Bidang Penelitian</th>
                                        <th>Status</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pengajuans as $index => $pengajuan)
                                        <tr>
                                            <td>{{ $pengajuans->firstItem() + $index }}</td>
                                            @if (!auth()->user()->isMahasiswa())
                                                <td>{{ $pengajuan->mahasiswa->user->name }}</td>
                                                <td>{{ $pengajuan->mahasiswa->user->nrp }}</td>
                                                <td>{{ $pengajuan->mahasiswa->prodi->name ?? '-' }}</td>
                                            @endif
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;"
                                                    title="{{ $pengajuan->judul }}">
                                                    {{ $pengajuan->judul }}
                                                </div>
                                            </td>
                                            <td>{{ $pengajuan->bidang_penelitian }}</td>
                                            <td>
                                                <span class="badge bg-{{ $pengajuan->status_color }}">
                                                    {{ $pengajuan->status_label }}
                                                </span>
                                            </td>
                                            <td>{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('pengajuan-tugas-akhir.show', $pengajuan) }}"
                                                        class="btn btn-sm btn-info" title="Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>

                                                    @if (auth()->user()->isMahasiswa() && in_array($pengajuan->status, ['draft', 'ditolak']))
                                                        <a href="{{ route('pengajuan-tugas-akhir.edit', $pengajuan) }}"
                                                            class="btn btn-sm btn-warning" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $pengajuans->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-5x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada pengajuan tugas akhir</h5>
                            @if (auth()->user()->isMahasiswa())
                                <p class="text-muted">Klik tombol "Ajukan Tugas Akhir" untuk memulai</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
@endsection
