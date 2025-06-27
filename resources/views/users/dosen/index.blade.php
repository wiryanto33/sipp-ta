@extends('layouts.dashboard')

@section('content')
    <header class="mb-3">
        <a href="#" class="burger-btn d-block d-xl-none">
            <i class="bi bi-justify fs-3"></i>
        </a>
    </header>

    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>{{ Auth::user()->hasRole('dosen') ? 'Profil Dosen' : 'Dosen' }}</h3>
                    <p class="text-subtitle text-muted">
                        {{ Auth::user()->hasRole('dosen') ? 'Data Profil Anda' : 'Data Dosen' }}
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ Auth::user()->hasRole('dosen') ? 'Profil Dosen' : 'Dosen' }}
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{ Auth::user()->hasRole('dosen') ? 'Data Profil Anda' : 'Data Dosen' }}</h5>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (!Auth::user()->hasRole('dosen'))
                        <div class="d-flex">
                            <a href="{{ route('users.create') }}" class="btn btn-primary mb-3 ms-auto">Tambah Dosen</a>
                        </div>
                    @endif

                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Image</th>
                                <th>Email</th>
                                <th>Prodi</th>
                                <th>NIDN</th>
                                <th>Bidang</th>
                                <th>Status</th>
                                <th>Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($dosens as $user)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $user->name }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $user->pangkat }} {{ $user->korps }} NRP {{ $user->nrp }}
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($user->image)
                                            <x-image-preview src="{{ asset($user->image) }}" />
                                        @else
                                            <x-image-preview
                                                src="{{ asset('mazer/dist/assets/compiled/png/avatar.png') }}" />
                                        @endif
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->dosen->prodi->name ?? '-' }}</td>
                                    <td>{{ $user->dosen->nidn ?? '-' }}</td>
                                    <td>{{ $user->dosen->bidang_studi ?? '-' }}</td>
                                    <td>
                                        @if ($user->status === 'aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @elseif($user->status === 'nonaktif')
                                            <span class="badge bg-danger">Non-Aktif</span>
                                        @elseif($user->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($user->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @can('show users')
                                                <a href="{{ route('users.show', $user->id) }}"
                                                    class="btn btn-sm btn-outline-info" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                            @endcan

                                            @can('edit users')
                                                <a href="{{ route('users.edit', $user->id) }}"
                                                    class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                            @endcan

                                            @can('delete users')
                                                @if (!Auth::user()->hasRole('dosen'))
                                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                        style="display:inline-block"
                                                        onsubmit="return confirm('Yakin ingin menghapus data Dosen {{ $user->name }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endcan

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="py-4">
                                            <i class="bi bi-inbox fs-1 text-muted"></i>
                                            <p class="text-muted mt-2">
                                                {{ Auth::user()->hasRole('dosen') ? 'Data profil tidak ditemukan' : 'Belum ada data Dosen' }}
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if (Auth::user()->hasRole('dosen'))
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle"></i>
                            <strong>Informasi:</strong> Anda hanya dapat melihat dan mengedit data profil Anda sendiri.
                            Untuk mengubah role atau status, silakan hubungi administrator.
                        </div>
                    @endif

                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#table1').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                },
                "pageLength": 10,
                "ordering": true,
                "searching": true,
                "columnDefs": [{
                    "orderable": false,
                    "targets": [1, 7] // Disable ordering for Image and Action columns
                }]
            });
        });
    </script>
@endpush
