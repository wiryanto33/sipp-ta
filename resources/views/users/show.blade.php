@extends('layouts.dashboard')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-4">Detail Pengguna</h2>

        {{-- Informasi Umum --}}
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Informasi Umum</h5>
                <div class="row align-items-center">
                    <div class="col-md-3 text-center mb-3 mb-md-0">
                        <img src="{{ $user->image ? asset($user->image) : asset('mazer/dist/assets/compiled/png/avatar.png') }}"
                            alt="User Image" class="img-fluid rounded-circle"
                            style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Nama:</strong> {{ $user->name }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Email:</strong> {{ $user->email }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Pangkat:</strong> {{ $user->pangkat }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Korps:</strong> {{ $user->korps }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>NRP:</strong> {{ $user->nrp }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Status:</strong>
                                @if ($user->status === 'aktif')
                                    <span class="badge bg-success">Aktif</span>
                                @elseif ($user->status === 'nonaktif')
                                    <span class="badge bg-danger">Non-Aktif</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($user->status) }}</span>
                                @endif
                            </div>
                            <div class="col-md-12 mt-2">
                                <strong>Role:</strong>
                                @foreach ($user->roles as $role)
                                    <span class="badge bg-primary">{{ $role->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Mahasiswa --}}
        @if ($user->mahasiswa)
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Detail Mahasiswa</h5>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>IPK:</strong> {{ $user->mahasiswa->ipk }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Semester:</strong> {{ $user->mahasiswa->semester }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Program Studi:</strong> {{ $user->mahasiswa->prodi->name ?? '-' }} -
                            {{ $user->mahasiswa->prodi->jenjang ?? '-' }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Angkatan:</strong> {{ $user->mahasiswa->angkatan }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Alamat:</strong> {{ $user->mahasiswa->alamat }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Telephone:</strong> {{ $user->mahasiswa->phone }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Detail Dosen --}}
        @if ($user->dosen)
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Detail Dosen</h5>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>NIDN:</strong> {{ $user->dosen->nidn }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Program Studi:</strong> {{ $user->dosen->prodi->name ?? '-' }} -
                            {{ $user->dosen->prodi->jenjang ?? '-' }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Jabatan Akademik:</strong> {{ $user->dosen->jabatan_akademik }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Alamat:</strong> {{ $user->dosen->alamat }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Telephone:</strong> {{ $user->dosen->phone }}
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Detail Kaprodi --}}
        @if ($user->kaprodi)
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-3">Detail Kaprodi</h5>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Alamat:</strong> {{ $user->kaprodi->alamat }}
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Telephone:</strong> {{ $user->kaprodi->phone }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
