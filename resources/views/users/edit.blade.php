@extends('layouts.dashboard')

@section('content')

    {{-- layout profil --}}
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Account Profile</h3>
                    <p class="text-subtitle text-muted">A page where users can change profile information</p>
                </div>
            </div>
        </div>

        {{-- Error Alert --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <div>
                        <strong>Terjadi kesalahan!</strong> Silakan periksa kembali data yang Anda masukkan.
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Success Alert --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong>Berhasil! {{ session('success') }} </strong>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Warning Alert --}}
        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian!</strong> {{ session('warning') }}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <section class="section">
            <div class="row">
                <!-- Profile Card -->
                <div class="col-12 col-lg-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body text-center py-4">
                            <div class="position-relative d-inline-block mb-1">
                                <div class="avatar avatar-2xl">
                                    <x-image-preview
                                        src="{{ $user->image ? asset($user->image) : asset('mazer/dist/assets/compiled/png/avatar.png') }}"
                                        class="rounded-circle border border-3 border-white shadow-sm" />
                                </div>
                            </div>
                            <h4 class="fw-bold text-gray mb-1">{{ $user->name }}</h4>
                            <p class="text-muted mb-2">{{ $user->email }}</p>
                            <div class="d-flex justify-content-center gap-2 flex-wrap">
                                @if ($user->hasRole('mahasiswa'))
                                    <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                        <i class="fas fa-graduation-cap me-1"></i>Mahasiswa
                                    </span>
                                @elseif($user->hasRole('dosen'))
                                    <span class="badge bg-success-subtle text-success px-3 py-2">
                                        <i class="fas fa-chalkboard-teacher me-1"></i>Dosen
                                    </span>
                                @endif
                                <span
                                    class="badge {{ $user->status == 'aktif' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} px-3 py-2">
                                    <i class="fas fa-circle me-1"
                                        style="font-size: 0.6rem;"></i>{{ ucfirst($user->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="col-12 col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-gradient-primary text-white py-3">
                            <h5 class="mb-0 fw-semibold">
                                <i class="fas fa-user-edit me-2"></i>Edit Profil Pengguna
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('users.update', $user->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <!-- Informasi Dasar -->
                                <div class="mb-4">
                                    <h6 class="text-primary fw-semibold mb-3 border-bottom border-primary pb-2">
                                        <i class="fas fa-info-circle me-2"></i>Informasi Dasar
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="image" class="form-label fw-medium">
                                                <i class="fas fa-user me-1 text-muted"></i>Foto
                                            </label>
                                            <input type="file" class="form-control form-control-lg" name="image"
                                                value="" placeholder="upload foto anda">
                                            @error('image')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                            <small class="text-muted">Format: JPG, PNG, GIF. Maksimal 2MB</small>
                                        </div>


                                        <div class="col-md-6">
                                            <label for="name" class="form-label fw-medium">
                                                <i class="fas fa-user me-1 text-muted"></i>Nama Lengkap
                                            </label>
                                            <input type="text" class="form-control form-control-lg" name="name"
                                                value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap">
                                            @error('name')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="email" class="form-label fw-medium">
                                                <i class="fas fa-envelope me-1 text-muted"></i>Email
                                            </label>
                                            <input type="email" name="email" id="email"
                                                class="form-control form-control-lg" placeholder="example@email.com"
                                                value="{{ old('email', $user->email) }}">
                                            @error('email')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi Kepangkatan -->
                                <div class="mb-4">
                                    <h6 class="text-primary fw-semibold mb-3 border-bottom border-primary pb-2">
                                        <i class="fas fa-medal me-2"></i>Informasi Kepangkatan
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="pangkat" class="form-label fw-medium">
                                                <i class="fas fa-star me-1 text-muted"></i>Pangkat
                                            </label>
                                            <input type="text" class="form-control" name="pangkat"
                                                value="{{ old('pangkat', $user->pangkat) }}" placeholder="Pangkat">

                                            @error('pangkat')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror

                                        </div>

                                        <div class="col-md-4">
                                            <label for="korps" class="form-label fw-medium">
                                                <i class="fas fa-shield-alt me-1 text-muted"></i>Korps
                                            </label>
                                            <input type="text" class="form-control" name="korps"
                                                value="{{ old('korps', $user->korps) }}" placeholder="Korps">

                                            @error('korps')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <div class="col-md-4">
                                            <label for="nrp" class="form-label fw-medium">
                                                <i class="fas fa-id-card me-1 text-muted"></i>NRP
                                            </label>
                                            <input type="text" class="form-control" name="nrp"
                                                value="{{ old('nrp', $user->nrp) }}"
                                                placeholder="Nomor Registrasi Prajurit">
                                            @error('nrp')
                                                <div class="invalid-feedback">
                                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Pengaturan Akun -->
                                <div class="mb-4">
                                    <h6 class="text-primary fw-semibold mb-3 border-bottom border-primary pb-2">
                                        <i class="fas fa-cog me-2"></i>Pengaturan Akun
                                    </h6>
                                    <div class="row g-3">
                                        <!-- Role Section -->
                                        <div class="col-md-6">
                                            @if (auth()->user()->hasRole('admin'))
                                                <label for="role_id" class="form-label fw-medium ">
                                                    <i class="fas fa-user-tag me-1 text-muted"></i>Role
                                                </label>
                                                <select name="role_id" class="form-select mb-3">
                                                    @foreach ($roles as $role)
                                                        <option value="{{ $role->id }}"
                                                            {{ $user->roles->first()->id == $role->id ? 'selected' : '' }}>
                                                            {{ ucfirst($role->name) }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <input type="hidden" name="role_id"
                                                    value="{{ $user->roles->first()->id }}">
                                                <label for="role_display" class="form-label fw-medium">
                                                    <i class="fas fa-user-tag me-1 text-muted"></i>Role
                                                </label>
                                                <input type="text" class="form-control bg-light"
                                                    value="{{ ucfirst($user->roles->first()->name) }}" readonly>
                                            @endif
                                        </div>

                                        <!-- Status Section -->
                                        <div class="col-md-6">
                                            @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))
                                                <label for="status" class="form-label fw-medium">
                                                    <i class="fas fa-toggle-on me-1 text-muted"></i>Status
                                                </label>
                                                <select name="status" class="form-select">
                                                    <option value="aktif"
                                                        @if ($user->status == 'aktif') selected @endif>
                                                        <i class="fas fa-check-circle"></i> Aktif
                                                    </option>
                                                    <option value="nonaktif"
                                                        @if ($user->status == 'nonaktif') selected @endif>
                                                        <i class="fas fa-times-circle"></i> Tidak Aktif
                                                    </option>
                                                </select>
                                            @else
                                                <input type="hidden" name="status" value="{{ $user->status }}">
                                                <label for="status_display" class="form-label fw-medium">
                                                    <i class="fas fa-toggle-on me-1 text-muted"></i>Status
                                                </label>
                                                <input type="text" class="form-control bg-light"
                                                    value="{{ ucfirst($user->status) }}" readonly>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Data Mahasiswa -->
                                @if ($user->hasRole('mahasiswa'))
                                    <div class="mb-4">
                                        <div class="card border-0 bg-primary-subtle">
                                            <div class="card-header bg-primary text-white py-3">
                                                <h6 class="mb-0 fw-semibold">
                                                    <i class="fas fa-graduation-cap me-2"></i>Data Mahasiswa
                                                </h6>
                                            </div>
                                            <div class="card-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <label for="prodi_id_mahasiswa" class="form-label fw-medium">
                                                            <i class="fas fa-university me-1 text-muted"></i>Program Studi
                                                        </label>
                                                        <select name="prodi_id_mahasiswa" class="form-select">
                                                            <option value="">-- Pilih Program Studi --</option>
                                                            @foreach ($prodis as $prodi)
                                                                <option value="{{ $prodi->id }}"
                                                                    {{ $user->mahasiswa->prodi_id == $prodi->id ? 'selected' : '' }}>
                                                                    {{ $prodi->name }} - {{ $prodi->jenjang }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label for="angkatan" class="form-label fw-medium">
                                                            <i class="fas fa-calendar-alt me-1 text-muted"></i>Angkatan
                                                        </label>
                                                        <input type="number" name="angkatan" class="form-control"
                                                            min="2000" max="2030"
                                                            value="{{ old('angkatan', $user->mahasiswa->angkatan ?? '') }}"
                                                            placeholder="2024">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label for="semester" class="form-label fw-medium">
                                                            <i class="fas fa-layer-group me-1 text-muted"></i>Semester
                                                        </label>
                                                        <input type="number" name="semester" class="form-control"
                                                            min="1" max="14"
                                                            value="{{ old('semester', $user->mahasiswa->semester ?? '') }}"
                                                            placeholder="1-14">
                                                    </div>

                                                    <div class="col-md-4">
                                                        @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin') || auth()->user()->hasRole('dosen'))
                                                            <label for="ipk" class="form-label fw-medium">
                                                                <i class="fas fa-chart-line me-1 text-muted"></i>IPK
                                                            </label>
                                                            <input type="number" step="0.01" name="ipk"
                                                                class="form-control" min="0" max="4"
                                                                value="{{ old('ipk', $user->mahasiswa->ipk ?? '') }}"
                                                                placeholder="0.00 - 4.00">
                                                        @else
                                                            <input type="hidden" name="ipk"
                                                                value="{{ $user->mahasiswa->ipk ?? '' }}">
                                                            <label for="ipk_display" class="form-label fw-medium">
                                                                <i class="fas fa-chart-line me-1 text-muted"></i>IPK
                                                            </label>
                                                            <input type="text" class="form-control bg-light"
                                                                value="{{ $user->mahasiswa->ipk ?? '-' }}" readonly>
                                                        @endif
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="phone" class="form-label fw-medium">
                                                            <i class="fas fa-phone me-1 text-muted"></i>No. HP
                                                        </label>
                                                        <input type="tel" name="phone" class="form-control"
                                                            value="{{ old('phone', $user->mahasiswa->phone ?? '') }}"
                                                            placeholder="08xxxxxxxxxx">
                                                    </div>

                                                    <div class="col-12">
                                                        <label for="alamat" class="form-label fw-medium">
                                                            <i class="fas fa-map-marker-alt me-1 text-muted"></i>Alamat
                                                        </label>
                                                        <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat', $user->mahasiswa->alamat ?? '') }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Data Dosen -->
                                @if ($user->hasRole('dosen'))
                                    <div class="mb-4">
                                        <div class="card border-0 bg-success-subtle">
                                            <div class="card-header bg-success text-white py-3">
                                                <h6 class="mb-0 fw-semibold">
                                                    <i class="fas fa-chalkboard-teacher me-2"></i>Data Dosen
                                                </h6>
                                            </div>
                                            <div class="card-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="prodi_id_dosen" class="form-label fw-medium">
                                                            <i class="fas fa-university me-1 text-muted"></i>Program Studi
                                                        </label>
                                                        <select name="prodi_id_dosen" class="form-select">
                                                            <option value="">-- Pilih Program Studi --</option>
                                                            @foreach ($prodis as $prodi)
                                                                <option value="{{ $prodi->id }}"
                                                                    {{ $user->dosen->prodi_id == $prodi->id ? 'selected' : '' }}>
                                                                    {{ $prodi->name }} - {{ $prodi->jenjang }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="nidn" class="form-label fw-medium">
                                                            <i class="fas fa-id-badge me-1 text-muted"></i>NIDN
                                                        </label>
                                                        <input type="text" name="nidn" class="form-control"
                                                            value="{{ old('nidn', $user->dosen->nidn ?? '') }}"
                                                            placeholder="Nomor Induk Dosen Nasional">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="jabatan_akademik" class="form-label fw-medium">
                                                            <i class="fas fa-briefcase me-1 text-muted"></i>Jabatan
                                                            Akademik
                                                        </label>
                                                        <input type="text" name="jabatan_akademik"
                                                            class="form-control"
                                                            value="{{ old('jabatan_akademik', $user->dosen->jabatan_akademik ?? '') }}"
                                                            placeholder="Asisten Ahli, Lektor, dll">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="bidang_studi" class="form-label fw-medium">
                                                            <i class="fas fa-book me-1 text-muted"></i>Bidang Studi
                                                        </label>
                                                        <input type="text" name="bidang_studi" class="form-control"
                                                            value="{{ old('bidang_studi', $user->dosen->bidang_studi ?? '') }}"
                                                            placeholder="Bidang keahlian">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="phone" class="form-label fw-medium">
                                                            <i class="fas fa-phone me-1 text-muted"></i>No. HP
                                                        </label>
                                                        <input type="tel" name="phone" class="form-control"
                                                            value="{{ old('phone', $user->dosen->phone ?? '') }}"
                                                            placeholder="08xxxxxxxxxx">
                                                    </div>

                                                    <div class="col-12">
                                                        <label for="alamat" class="form-label fw-medium">
                                                            <i class="fas fa-map-marker-alt me-1 text-muted"></i>Alamat
                                                        </label>
                                                        <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat', $user->dosen->alamat ?? '') }}</textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Data Kaprodi --}}
                                @if ($user->hasRole('kaprodi'))
                                    <div class="mb-4">
                                        <div class="card border-0 bg-info-subtle">
                                            <div class="card-header bg-info text-white py-3">
                                                <h6 class="mb-0 fw-semibold">
                                                    <i class="fas fa-user-tie me-2"></i>Data Kaprodi
                                                </h6>
                                            </div>

                                            <div class="card-body p-4">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label for="phone" class="form-label fw-medium">
                                                            <i class="fas fa-phone me-1 text-muted"></i>No. HP
                                                        </label>
                                                        <input type="tel" name="phone" class="form-control"
                                                            value="{{ old('phone', $user->kaprodi->phone ?? '') }}"
                                                            placeholder="08xxxxxxxxxx">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label for="prodi_id_kaprodi" class="form-label fw-medium">
                                                            <i class="fas fa-university me-1 text-muted"></i>Program Studi
                                                        </label>
                                                        <select name="prodi_id_kaprodi" class="form-select">
                                                            <option value="">-- Pilih Program Studi --</option>
                                                            @foreach ($prodis as $prodi)
                                                                <option value="{{ $prodi->id }}"
                                                                    {{ $user->kaprodi->prodi_id == $prodi->id ? 'selected' : '' }}>
                                                                    {{ $prodi->name }} - {{ $prodi->jenjang }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                </div>

                                                <label for="alamat" class="form-label fw-medium">
                                                    <i class="fas fa-map-marker-alt me-1 text-muted"></i>Alamat
                                                </label>
                                                <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat', $user->kaprodi->alamat ?? '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="fas fa-save me-2"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <style>
            .bg-gradient-primary {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #667eea;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            }

            .card {
                transition: all 0.3s ease;
            }

            .card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1) !important;
            }

            .badge {
                font-weight: 500;
                font-size: 0.75rem;
            }

            .border-primary {
                border-color: #667eea !important;
            }

            .text-primary {
                color: #667eea !important;
            }

            .bg-primary {
                background-color: #667eea !important;
            }

            .btn-primary {
                background-color: #667eea;
                border-color: #667eea;
            }

            .btn-primary:hover {
                background-color: #5a6fd8;
                border-color: #5a6fd8;
            }

            .avatar img {
                object-fit: cover;
            }

            .form-label {
                margin-bottom: 0.5rem;
                font-size: 0.9rem;
            }

            .card-header h6 {
                font-size: 1rem;
            }
        </style>
    </div>
@endsection
