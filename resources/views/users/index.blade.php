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
                    <h3>Mahasiswa</h3>
                    <p class="text-subtitle text-muted">
                        Data Mahasiswa
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Mahasiswa
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data Mahasiswa</h5>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="d-flex">
                        <a href="{{ route('mahasiswas.create') }}" class="btn btn-primary mb-3 ms-auto">Tambah Mahasiswa</a>
                    </div>

                    <table class="table table-striped" id="table1">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Image</th>
                                <th>Prodi</th>
                                <th>Angkatan</th>
                                <th>Semester</th>
                                <th>Status</th>
                                <th>Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($mahasiswas as $mhs)
                                <tr>
                                    <td>
                                        {{ $mhs->user->name }}
                                        <p class="mb-0">
                                            {{ $mhs->user->pangkat }} {{ $mhs->user->korps }} NRP {{ $mhs->user->nrp }}
                                        </p>
                                    </td>
                                    <td>
                                        @if ($mhs->user->image)
                                            <x-image-preview src="{{ asset($mhs->user->image) }}" />
                                        @else
                                            <x-image-preview src="{{ asset('mazer/dist/assets/compiled/png/avatar.png') }}" />
                                        @endif
                                    </td>
                                    <td>{{ $mhs->prodi->name ?? '-' }}</td>
                                    <td>{{ $mhs->angkatan }}</td>
                                    <td>{{ $mhs->semester }}</td>
                                    <td>
                                        @if ($mhs->user->status === 'aktif')
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">{{ ucfirst($mhs->user->status) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('mahasiswas.edit', $mhs->id) }}" class="btn btn-sm btn-info">Edit</a>

                                        <form action="{{ route('mahasiswas.destroy', $mhs->id) }}" method="POST"
                                            style="display:inline-block" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </section>
    </div>
@endsection
