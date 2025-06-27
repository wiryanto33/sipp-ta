@extends('layouts.dashboard')

@section('content')
    <div class="container">
        <h1 class="mb-4">Tambah User</h1>

        {{-- Tampilkan pesan sukses atau error --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        {{-- Tampilkan error validasi --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Terjadi kesalahan saat menyimpan data:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" id="userForm">
            @csrf

            {{-- Info Umum --}}
            <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
            </div>

            <div class="mb-3">
                <label for="pangkat" class="form-label">Pangkat</label>
                <input type="text" name="pangkat" class="form-control"  value="{{ old('pangkat') }}">
            </div>

            <div class="mb-3">
                <label for="korps" class="form-label">Korps</label>
                <input type="text" name="korps" class="form-control"  value="{{ old('korps') }}">
            </div>

            <div class="mb-3">
                <label for="nrp" class="form-label">NRP</label>
                <input type="text" name="nrp" class="form-control"  maxlength="8"
                    value="{{ old('nrp') }}">
            </div>

            <div class="mb-3">
                <label for="" class="form-label">Image</label>
                <input type="file" class="form-control @error('image') is-invalid
                @enderror"
                    value="{{ old('image') }}" name="image">
                @error('image')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="role_id" class="form-label">Role</label>
                <select name="role_id" id="roleSelect" class="form-control" required>
                    <option value="">-- Pilih Role --</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Detail Mahasiswa --}}
            <div id="mahasiswaFields" style="display: none;">
                <h4>Data Mahasiswa</h4>

                <div class="mb-3">
                    <label for="prodi_id_mahasiswa" class="form-label">Program Studi</label>
                    <select name="prodi_id_mahasiswa" class="form-control">
                        <option value="">-- Pilih Prodi --</option>
                        @foreach ($prodis as $prodi)
                            <option value="{{ $prodi->id }}">{{ $prodi->name }} - {{ $prodi->jenjang }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="angkatan" class="form-label">Angkatan</label>
                    <input type="number" name="angkatan" class="form-control" value="{{ old('angkatan') }}">
                </div>

                <div class="mb-3">
                    <label for="semester" class="form-label">Semester</label>
                    <input type="number" name="semester" class="form-control" value="{{ old('semester') }}">
                </div>

                <div class="mb-3">
                    <label for="ipk" class="form-label">IPK</label>
                    <input type="text" name="ipk" class="form-control" value="{{ old('ipk') }}">
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Nomor HP</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control">{{ old('alamat') }}</textarea>
                </div>
            </div>


            {{-- Detail Dosen --}}
            <div id="dosenFields" style="display: none;">
                <h4>Data Dosen</h4>

                <div class="mb-3">
                    <label for="prodi_id_dosen" class="form-label">Program Studi</label>
                    <select name="prodi_id_dosen" class="form-control">
                        <option value="">-- Pilih Prodi --</option>
                        @foreach ($prodis as $prodi)
                            <option value="{{ $prodi->id }}">{{ $prodi->name }} - {{ $prodi->jenjang }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="nidn" class="form-label">NIDN</label>
                    <input type="text" name="nidn" class="form-control" value="{{ old('nidn') }}">
                </div>

                <div class="mb-3">
                    <label for="jabatan_akademik" class="form-label">Jabatan Akademik</label>
                    <input type="text" name="jabatan_akademik" class="form-control"
                        value="{{ old('jabatan_akademik') }}">
                </div>

                <div class="mb-3">
                    <label for="bidang_studi" class="form-label">Bidang Studi</label>
                    <input type="text" name="bidang_studi" class="form-control" value="{{ old('bidang_studi') }}">
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Nomor HP</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control">{{ old('alamat') }}</textarea>
                </div>
            </div>

            {{-- detail Kaprodi --}}
            <div id="kaprodiFields" style="display: none;">
                <h4>Data Kaprodi</h4>

                <div class="mb-3">
                    <label for="prodi_id_kaprodi" class="form-label">Program Studi</label>
                    <select name="prodi_id_kaprodi" class="form-control">
                        <option value="">-- Pilih Prodi --</option>
                        @foreach ($prodis as $prodi)
                            <option value="{{ $prodi->id }}">{{ $prodi->name }} - {{ $prodi->jenjang }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Nomor HP</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                </div>

                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control">{{ old('alamat') }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
@endsection

@php
    $roleMap = $roles->pluck('name', 'id')->mapWithKeys(fn($v, $k) => [(string) $k => $v]);
@endphp

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('roleSelect');
            const mahasiswaFields = document.getElementById('mahasiswaFields');
            const dosenFields = document.getElementById('dosenFields');
            const kaprodiFields = document.getElementById('kaprodiFields');
            const roleMap = @json($roleMap);

            function toggleRoleFields(roleName) {
                const role = roleName.toLowerCase();
                mahasiswaFields.style.display = (role === 'mahasiswa') ? 'block' : 'none';
                dosenFields.style.display = (role === 'dosen') ? 'block' : 'none';
                kaprodiFields.style.display = (role === 'kaprodi') ? 'block' : 'none';
            }

            roleSelect.addEventListener('change', function() {
                const selectedRoleId = this.value;
                const roleName = roleMap[selectedRoleId];

                console.log('Selected Role ID:', selectedRoleId);
                console.log('Mapped Role Name:', roleName);

                toggleRoleFields(roleName);
            });

            // Initial state (on load)
            const currentRole = roleSelect.value;
            if (currentRole && roleMap[currentRole]) {
                toggleRoleFields(roleMap[currentRole]);
            }
        });
    </script>
@endpush
