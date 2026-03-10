@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header mb-5">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title mb-2">Tambah User Pengguna</h1>
                    <p class="text-muted">Formulir untuk menambahkan user baru ke sistem</p>
                </div>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </div>

        {{-- Tampilkan pesan sukses atau error --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Sukses!</strong>
                </div>
                <p class="mb-0 mt-2">{{ session('success') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Terjadi Kesalahan!</strong>
                </div>
                <p class="mb-0 mt-2">{{ session('error') }}</p>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Tampilkan error validasi --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-times-circle me-2"></i>
                    <strong>Validasi Gagal</strong>
                </div>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li class="mb-2"><small>{{ $error }}</small></li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" id="userForm">
            @csrf

            <div class="row">
                <div class="col-lg-8">
                    <!-- Info Umum Card -->
                    <div class="card shadow-sm mb-4 border-0">
                        <div class="card-header bg-gradient"
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-user-circle me-2"></i>Informasi Umum
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="name" class="form-label fw-bold">Nama <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control form-control-lg" required
                                        value="{{ old('name') }}" placeholder="Masukkan nama lengkap">
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold">Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" required
                                        value="{{ old('email') }}" placeholder="user@example.com">
                                </div>

                                <div class="col-md-6">
                                    <label for="role_id" class="form-label fw-bold">Role <span
                                            class="text-danger">*</span></label>
                                    <select name="role_id" id="roleSelect" class="form-select" required>
                                        <option value="">-- Pilih Role --</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}"
                                                {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="pangkat" class="form-label fw-bold">Pangkat</label>
                                    <input type="text" name="pangkat" class="form-control" value="{{ old('pangkat') }}"
                                        placeholder="Masukkan pangkat">
                                </div>

                                <div class="col-md-4">
                                    <label for="korps" class="form-label fw-bold">Korps</label>
                                    <input type="text" name="korps" class="form-control" value="{{ old('korps') }}"
                                        placeholder="Masukkan korps">
                                </div>

                                <div class="col-md-4">
                                    <label for="nrp" class="form-label fw-bold">NRP</label>
                                    <input type="text" name="nrp" class="form-control" maxlength="8"
                                        value="{{ old('nrp') }}" placeholder="Masukkan NRP">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Security Card -->
                    <div class="card shadow-sm mb-4 border-0">
                        <div class="card-header bg-gradient"
                            style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-lock me-2"></i>Keamanan Akun
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="password" class="form-label fw-bold">Password <span
                                            class="text-danger">*</span></label>
                                    <input type="password" name="password" class="form-control" required
                                        placeholder="Masukkan password">
                                    <small class="text-muted d-block mt-1">Minimal 8 karakter</small>
                                </div>

                                <div class="col-md-6">
                                    <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Password
                                        <span class="text-danger">*</span></label>
                                    <input type="password" name="password_confirmation" class="form-control" required
                                        placeholder="Konfirmasi password">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Role-Specific Details -->
                    <!-- Detail Mahasiswa -->
                    <div id="mahasiswaFields" class="card shadow-sm mb-4 border-0" style="display: none;">
                        <div class="card-header bg-gradient"
                            style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-graduation-cap me-2"></i>Data Mahasiswa
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="prodi_id_mahasiswa" class="form-label fw-bold">Program Studi</label>
                                    <select name="prodi_id_mahasiswa" class="form-select">
                                        <option value="">-- Pilih Prodi --</option>
                                        @foreach ($prodis as $prodi)
                                            <option value="{{ $prodi->id }}">{{ $prodi->name }} -
                                                {{ $prodi->jenjang }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="angkatan" class="form-label fw-bold">Angkatan</label>
                                    <input type="number" name="angkatan" class="form-control"
                                        value="{{ old('angkatan') }}" placeholder="2020">
                                </div>

                                <div class="col-md-6">
                                    <label for="semester" class="form-label fw-bold">Semester</label>
                                    <input type="number" name="semester" class="form-control"
                                        value="{{ old('semester') }}" placeholder="1">
                                </div>

                                <div class="col-md-6">
                                    <label for="ipk" class="form-label fw-bold">IPK</label>
                                    <input type="text" name="ipk" class="form-control"
                                        value="{{ old('ipk') }}" placeholder="3.50">
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-bold">Nomor HP</label>
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ old('phone') }}" placeholder="081234567890">
                                </div>

                                <div class="col-12">
                                    <label for="alamat" class="form-label fw-bold">Alamat</label>
                                    <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Dosen -->
                    <div id="dosenFields" class="card shadow-sm mb-4 border-0" style="display: none;">
                        <div class="card-header bg-gradient"
                            style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-chalkboard-user me-2"></i>Data Dosen
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="prodi_id_dosen" class="form-label fw-bold">Program Studi</label>
                                    <select name="prodi_id_dosen" class="form-select">
                                        <option value="">-- Pilih Prodi --</option>
                                        @foreach ($prodis as $prodi)
                                            <option value="{{ $prodi->id }}">{{ $prodi->name }} -
                                                {{ $prodi->jenjang }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="nidn" class="form-label fw-bold">NIDN</label>
                                    <input type="text" name="nidn" class="form-control"
                                        value="{{ old('nidn') }}" placeholder="Masukkan NIDN">
                                </div>

                                <div class="col-md-6">
                                    <label for="jabatan_akademik" class="form-label fw-bold">Jabatan Akademik</label>
                                    <input type="text" name="jabatan_akademik" class="form-control"
                                        value="{{ old('jabatan_akademik') }}" placeholder="Dosen Biasa">
                                </div>

                                <div class="col-12">
                                    <label for="bidang_studi" class="form-label fw-bold">Bidang Studi</label>
                                    <input type="text" name="bidang_studi" class="form-control"
                                        value="{{ old('bidang_studi') }}" placeholder="Masukkan bidang studi">
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-bold">Nomor HP</label>
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ old('phone') }}" placeholder="081234567890">
                                </div>

                                <div class="col-md-6">
                                    <label>&nbsp;</label>
                                </div>

                                <div class="col-12">
                                    <label for="alamat" class="form-label fw-bold">Alamat</label>
                                    <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Kaprodi -->
                    <div id="kaprodiFields" class="card shadow-sm mb-4 border-0" style="display: none;">
                        <div class="card-header bg-gradient"
                            style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <h5 class="card-title mb-0 text-white">
                                <i class="fas fa-award me-2"></i>Data Kaprodi
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="prodi_id_kaprodi" class="form-label fw-bold">Program Studi</label>
                                    <select name="prodi_id_kaprodi" class="form-select">
                                        <option value="">-- Pilih Prodi --</option>
                                        @foreach ($prodis as $prodi)
                                            <option value="{{ $prodi->id }}">{{ $prodi->name }} -
                                                {{ $prodi->jenjang }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-bold">Nomor HP</label>
                                    <input type="text" name="phone" class="form-control"
                                        value="{{ old('phone') }}" placeholder="081234567890">
                                </div>

                                <div class="col-md-6">
                                    <label>&nbsp;</label>
                                </div>

                                <div class="col-12">
                                    <label for="alamat" class="form-label fw-bold">Alamat</label>
                                    <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 mb-5">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Photo Card -->
                    <div class="card shadow-sm mb-4 border-0 sticky-top" style="top: 20px;">
                        <div class="card-header bg-gradient"
                            style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-image me-2"></i>Foto Profil
                            </h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <div id="imagePreview" class="mb-3" style="display:none;">
                                    <img id="previewImg" src="" alt="Preview"
                                        style="max-width: 200px; height: 200px; object-fit: cover; border-radius: 12px; border: 3px solid #667eea;">
                                </div>
                                <div id="noImagePlaceholder" class="text-center">
                                    <div
                                        style="width: 200px; height: 200px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); border-radius: 12px; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                        <div>
                                            <i class="fas fa-user fa-4x text-muted mb-2"></i>
                                            <p class="text-muted small">Belum ada foto</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <label for="image" class="form-label fw-bold">Pilih Foto</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror"
                                name="image" id="imageInput" accept="image/*">

                            @error('image')
                                <div class="invalid-feedback d-block mt-2">
                                    {{ $message }}
                                </div>
                            @enderror

                            <small class="text-muted d-block mt-2">
                                <i class="fas fa-info-circle me-1"></i>Format: JPG, PNG, GIF<br>
                                Ukuran maksimal: 2MB
                            </small>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="card shadow-sm border-0 bg-light">
                        <div class="card-body">
                            <h6 class="card-title mb-3">
                                <i class="fas fa-lightbulb me-2 text-warning"></i>Informasi
                            </h6>
                            <ul class="list-unstyled small">
                                <li class="mb-2">
                                    <strong>Nama & Email:</strong> Wajib diisi
                                </li>
                                <li class="mb-2">
                                    <strong>Password:</strong> Minimal 8 karakter
                                </li>
                                <li class="mb-2">
                                    <strong>Role:</strong> Tentukan role pengguna
                                </li>
                                <li>
                                    <strong>Data Tambahan:</strong> Akan muncul sesuai role dipilih
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
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
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const noImagePlaceholder = document.getElementById('noImagePlaceholder');
            const roleMap = @json($roleMap);

            // Handle image preview
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        previewImg.src = event.target.result;
                        imagePreview.style.display = 'block';
                        noImagePlaceholder.style.display = 'none';
                    }
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.style.display = 'none';
                    noImagePlaceholder.style.display = 'block';
                }
            });

            // Handle role selection
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

    <style>
        .card-header.bg-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .sticky-top {
            z-index: 1020;
        }

        @media (max-width: 991.98px) {
            .sticky-top {
                position: static;
            }
        }
    </style>
@endpush
