@extends('layouts.dashboard')

@section('title', 'Edit Jadwal Sidang')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Edit Jadwal Sidang</h4>
                        <a href="{{ route('jadwal-sidang.show', $jadwalSidang) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>

                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('jadwal-sidang.update', $jadwalSidang) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Informasi Tugas Akhir -->
                                <div class="col-md-6">
                                    <div class="card border-primary mb-3">
                                        <div class="card-header bg-primary text-white mb-3">
                                            <h6 class="mb-0"><i class="fas fa-graduation-cap"></i> Informasi Tugas Akhir
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label for="pengajuan_tugas_akhir_id" class="form-label">Pilih Tugas Akhir
                                                    <span class="text-danger">*</span></label>
                                                <select name="pengajuan_tugas_akhir_id" id="pengajuan_tugas_akhir_id"
                                                    class="form-control @error('pengajuan_tugas_akhir_id') is-invalid @enderror"
                                                    required>
                                                    <option value="">-- Pilih Tugas Akhir --</option>
                                                    @foreach ($tugasAkhirs as $tugasAkhir)
                                                        <option value="{{ $tugasAkhir->id }}"
                                                            {{ old('pengajuan_tugas_akhir_id', $jadwalSidang->pengajuan_tugas_akhir_id) == $tugasAkhir->id ? 'selected' : '' }}>
                                                            {{ $tugasAkhir->mahasiswa->user->nrp ?? 'No NRP' }} -
                                                            {{ $tugasAkhir->mahasiswa->user->name ?? 'No Name' }}
                                                            @if ($tugasAkhir->judul)
                                                                <br>{{ Str::limit($tugasAkhir->judul, 50) }}
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('pengajuan_tugas_akhir_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="jenis_sidang" class="form-label">Jenis Sidang <span
                                                        class="text-danger">*</span></label>
                                                <select name="jenis_sidang" id="jenis_sidang"
                                                    class="form-control @error('jenis_sidang') is-invalid @enderror"
                                                    required>
                                                    <option value="">-- Pilih Jenis Sidang --</option>
                                                    <option value="proposal"
                                                        {{ old('jenis_sidang', $jadwalSidang->jenis_sidang) === 'proposal' ? 'selected' : '' }}>
                                                        Proposal</option>
                                                    <option value="skripsi"
                                                        {{ old('jenis_sidang', $jadwalSidang->jenis_sidang) === 'skripsi' ? 'selected' : '' }}>
                                                        Skripsi</option>
                                                    <option value="tugas_akhir"
                                                        {{ old('jenis_sidang', $jadwalSidang->jenis_sidang) === 'tugas_akhir' ? 'selected' : '' }}>
                                                        Tugas Akhir</option>
                                                </select>
                                                @error('jenis_sidang')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Jadwal dan Tempat -->
                                <div class="col-md-6">
                                    <div class="card border-info mb-3">
                                        <div class="card-header bg-info text-white mb-3">
                                            <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Jadwal dan Tempat</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label for="tanggal_sidang" class="form-label">Tanggal dan Waktu Sidang
                                                    <span class="text-danger">*</span></label>
                                                <input type="datetime-local" name="tanggal_sidang" id="tanggal_sidang"
                                                    class="form-control @error('tanggal_sidang') is-invalid @enderror"
                                                    value="{{ old('tanggal_sidang', $jadwalSidang->tanggal_sidang ? \Carbon\Carbon::parse($jadwalSidang->tanggal_sidang)->format('Y-m-d\TH:i') : '') }}"
                                                    required>
                                                @error('tanggal_sidang')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="tempat_sidang" class="form-label">Tempat Sidang <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="tempat_sidang" id="tempat_sidang"
                                                    class="form-control @error('tempat_sidang') is-invalid @enderror"
                                                    value="{{ old('tempat_sidang', $jadwalSidang->tempat_sidang) }}"
                                                    placeholder="Contoh: Ruang Sidang Lantai 2" required>
                                                @error('tempat_sidang')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="form-group mb-3">
                                                <label for="ruang_sidang" class="form-label">Ruang Sidang</label>
                                                <input type="text" name="ruang_sidang" id="ruang_sidang"
                                                    class="form-control @error('ruang_sidang') is-invalid @enderror"
                                                    value="{{ old('ruang_sidang', $jadwalSidang->ruang_sidang) }}"
                                                    placeholder="Contoh: R.201">
                                                @error('ruang_sidang')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Daftar Penguji -->
                            <div class="card border-success mb-3">
                                <div class="card-header bg-success text-white mb-3">
                                    <h6 class="mb-0"><i class="fas fa-users"></i> Daftar Penguji <span
                                            class="text-warning">*</span></h6>
                                </div>
                                <div class="card-body">
                                    <div id="penguji-container">
                                        @foreach ($jadwalSidang->pengujiSidangs as $index => $penguji)
                                            <div class="penguji-item border rounded p-3 mb-3">
                                                <div class="row">
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <label class="form-label">Dosen</label>
                                                            <select name="penguji[{{ $index }}][dosen_id]"
                                                                class="form-control" required>
                                                                <option value="">-- Pilih Dosen --</option>
                                                                @foreach ($dosens as $dosen)
                                                                    <option value="{{ $dosen->id }}"
                                                                        {{ $penguji->dosen_id == $dosen->id ? 'selected' : '' }}>
                                                                        {{ $dosen->user->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <label class="form-label">Peran</label>
                                                            <select name="penguji[{{ $index }}][peran]"
                                                                class="form-control" required>
                                                                <option value="">-- Pilih Peran --</option>
                                                                <option value="ketua"
                                                                    {{ $penguji->peran === 'ketua' ? 'selected' : '' }}>
                                                                    Ketua</option>
                                                                <option value="sekretaris"
                                                                    {{ $penguji->peran === 'sekretaris' ? 'selected' : '' }}>
                                                                    Sekretaris</option>
                                                                <option value="penguji_1"
                                                                    {{ $penguji->peran === 'penguji_1' ? 'selected' : '' }}>
                                                                    Penguji 1</option>
                                                                <option value="penguji_2"
                                                                    {{ $penguji->peran === 'penguji_2' ? 'selected' : '' }}>
                                                                    Penguji 2</option>
                                                                <option value="pembimbing_1"
                                                                    {{ $penguji->peran === 'pembimbing_1' ? 'selected' : '' }}>
                                                                    Pembimbing 1</option>
                                                                <option value="pembimbing_2"
                                                                    {{ $penguji->peran === 'pembimbing_2' ? 'selected' : '' }}>
                                                                    Pembimbing 2</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label class="form-label">&nbsp;</label>
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm d-block remove-penguji">
                                                                <i class="fas fa-trash"></i> Hapus
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <button type="button" id="add-penguji" class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus"></i> Tambah Penguji
                                    </button>
                                    @error('penguji')
                                        <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- File dan Catatan -->
                            <div class="card border-warning mb-3">
                                <div class="card-header bg-warning text-dark mb-3">
                                    <h6 class="mb-0"><i class="fas fa-file-alt"></i> File dan Catatan</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="file_sidang" class="form-label">File Sidang</label>
                                                @if ($jadwalSidang->file_sidang)
                                                    <div class="mb-2">
                                                        <small class="text-muted">File saat ini:
                                                            <a href="{{ route('jadwal-sidang.download', $jadwalSidang) }}"
                                                                target="_blank">
                                                                {{ basename($jadwalSidang->file_sidang) }}
                                                            </a>
                                                        </small>
                                                    </div>
                                                @endif
                                                <input type="file" name="file_sidang" id="file_sidang"
                                                    class="form-control @error('file_sidang') is-invalid @enderror"
                                                    accept=".pdf,.doc,.docx">
                                                <small class="text-muted">Format: PDF, DOC, DOCX. Maksimal 2MB.</small>
                                                @error('file_sidang')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="catatan" class="form-label">Catatan</label>
                                                <textarea name="catatan" id="catatan" class="form-control @error('catatan') is-invalid @enderror" rows="3"
                                                    placeholder="Catatan tambahan untuk sidang">{{ old('catatan', $jadwalSidang->catatan) }}</textarea>
                                                @error('catatan')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Jadwal Sidang
                                </button>
                                <a href="{{ route('jadwal-sidang.show', $jadwalSidang) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let pengujiIndex = {{ count($jadwalSidang->pengujiSidangs) }};

                document.getElementById('add-penguji').addEventListener('click', function() {
                    const container = document.getElementById('penguji-container');

                    const newPenguji = document.createElement('div');
                    newPenguji.className = 'penguji-item border rounded p-3 mb-3';
                    newPenguji.innerHTML = `
            <div class="row">
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="form-label">Dosen</label>
                        <select name="penguji[${pengujiIndex}][dosen_id]" class="form-control" required>
                            <option value="">-- Pilih Dosen --</option>
                            @foreach ($dosens as $dosen)
                                <option value="{{ $dosen->id }}">{{ $dosen->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label class="form-label">Peran</label>
                        <select name="penguji[${pengujiIndex}][peran]" class="form-control" required>
                            <option value="">-- Pilih Peran --</option>
                            <option value="ketua">Ketua</option>
                            <option value="sekretaris">Sekretaris</option>
                            <option value="penguji_1">Penguji 1</option>
                            <option value="penguji_2">Penguji 2</option>
                            <option value="pembimbing_1">Pembimbing 1</option>
                            <option value="pembimbing_2">Pembimbing 2</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm remove-penguji">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                </div>
            </div>
        `;
                    container.appendChild(newPenguji);
                    pengujiIndex++;
                });

                document.addEventListener('click', function(e) {
                    if (e.target.classList.contains('remove-penguji') || e.target.closest('.remove-penguji')) {
                        const item = e.target.closest('.penguji-item');
                        const container = document.getElementById('penguji-container');
                        if (container.children.length > 1) {
                            item.remove();
                            updatePengujiIndices();
                        } else {
                            alert('Minimal harus ada satu penguji.');
                        }
                    }
                });

                function updatePengujiIndices() {
                    const items = document.querySelectorAll('.penguji-item');
                    items.forEach((item, index) => {
                        const selects = item.querySelectorAll('select');
                        selects.forEach(select => {
                            if (select.name.includes('[dosen_id]')) {
                                select.name = `penguji[${index}][dosen_id]`;
                            } else if (select.name.includes('[peran]')) {
                                select.name = `penguji[${index}][peran]`;
                            }
                        });
                    });
                    pengujiIndex = items.length;
                }
            });
        </script>
    @endpush

@endsection
