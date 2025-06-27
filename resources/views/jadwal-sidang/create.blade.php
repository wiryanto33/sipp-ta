{{-- resources/views/jadwal-sidang/create.blade.php --}}
@extends('layouts.dashboard')

@section('title', 'Tambah Jadwal Sidang')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tambah Jadwal Sidang</h3>
                        <div class="card-tools">
                            <a href="{{ route('jadwal-sidang.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif


                    <form action="{{ route('jadwal-sidang.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-6">

                                    <div class="form-group mb-3">
                                        <label for="pengajuan_tugas_akhir_id" class="form-label">Tugas Akhir <span
                                                class="text-danger">*</span></label>
                                        <select name="pengajuan_tugas_akhir_id" id="tugas_akhir_id"
                                            class="form-select @error('pengajuan_tugas_akhir_id') is-invalid @enderror"
                                            required>
                                            <option value="">Pilih Tugas Akhir</option>
                                            @foreach ($tugasAkhirs as $ta)
                                                @php
                                                    // Prioritas: old() value, kemudian default dari controller
                                                    $isSelected = old('pengajuan_tugas_akhir_id')
                                                        ? old('pengajuan_tugas_akhir_id') == $ta->id
                                                        : $defaultTugasAkhirId == $ta->id;
                                                @endphp
                                                <option value="{{ $ta->id }}" {{ $isSelected ? 'selected' : '' }}>
                                                    {{ $ta->mahasiswa->user->name ?? 'N/A' }} -
                                                    {{ $ta->mahasiswa->user->nrp ?? 'N/A' }}
                                                    ({{ $ta->judul }})
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
                                            class="form-select @error('jenis_sidang') is-invalid @enderror" required>
                                            <option value="">Pilih Jenis Sidang</option>
                                            <option value="proposal"
                                                {{ old('jenis_sidang') == 'proposal' ? 'selected' : '' }}>Proposal</option>
                                            <option value="skripsi"
                                                {{ old('jenis_sidang') == 'skripsi' ? 'selected' : '' }}>Skripsi</option>
                                            <option value="tugas_akhir"
                                                {{ old('jenis_sidang') == 'tugas_akhir' ? 'selected' : '' }}>Tugas Akhir
                                            </option>
                                        </select>
                                        @error('jenis_sidang')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="tanggal_sidang" class="form-label">Tanggal & Waktu Sidang <span
                                                class="text-danger">*</span></label>
                                        <input type="datetime-local" name="tanggal_sidang" id="tanggal_sidang"
                                            class="form-control @error('tanggal_sidang') is-invalid @enderror"
                                            value="{{ old('tanggal_sidang') }}" required>
                                        @error('tanggal_sidang')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Jadwal sidang: Senin-Jumat, 08:00-17:00 WIB
                                        </small>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="tempat_sidang" class="form-label">Tempat Sidang <span
                                                class="text-danger">*</span></label>
                                        <input type="text" name="tempat_sidang" id="tempat_sidang"
                                            class="form-control @error('tempat_sidang') is-invalid @enderror"
                                            value="{{ old('tempat_sidang') }}" placeholder="Contoh: Gedung A Lantai 2"
                                            required>
                                        @error('tempat_sidang')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="ruang_sidang" class="form-label">Ruang Sidang</label>
                                        <input type="text" name="ruang_sidang" id="ruang_sidang"
                                            class="form-control @error('ruang_sidang') is-invalid @enderror"
                                            value="{{ old('ruang_sidang') }}" placeholder="Contoh: Ruang 201">
                                        @error('ruang_sidang')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="file_sidang" class="form-label">File Sidang</label>
                                        <input type="file" name="file_sidang" id="file_sidang"
                                            class="form-control @error('file_sidang') is-invalid @enderror"
                                            accept=".pdf,.doc,.docx">
                                        <small class="form-text text-muted">Format: PDF, DOC, DOCX (Max: 2MB)</small>
                                        @error('file_sidang')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="catatan" class="form-label">Catatan</label>
                                        <textarea name="catatan" id="catatan" rows="3" class="form-control @error('catatan') is-invalid @enderror"
                                            placeholder="Catatan tambahan untuk sidang">{{ old('catatan') }}</textarea>
                                        @error('catatan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Right Column - Penguji -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Daftar Penguji <span class="text-danger">*</span>
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div id="penguji-container">
                                                <div class="penguji-item border rounded p-3 mb-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Dosen</label>
                                                            <select name="penguji[0][dosen_id]" class="form-select"
                                                                required>
                                                                <option value="">Pilih Dosen</option>
                                                                @foreach ($dosens as $dosen)
                                                                    <option value="{{ $dosen->id }}">
                                                                        {{ $dosen->user->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label">Peran</label>
                                                            <div class="d-flex">
                                                                <select name="penguji[0][peran]" class="form-select"
                                                                    required>
                                                                    <option value="">Pilih Peran</option>
                                                                    <option value="ketua">Ketua Penguji</option>
                                                                    <option value="sekretaris">Sekretaris</option>
                                                                    <option value="penguji_1">Penguji 1</option>
                                                                    <option value="penguji_2">Penguji 2</option>
                                                                    <option value="pembimbing_1">Pembimbing 1</option>
                                                                    <option value="pembimbing_2">Pembimbing 2</option>
                                                                </select>
                                                                <button type="button"
                                                                    class="btn btn-outline-danger ms-2 remove-penguji"
                                                                    style="display: none;">
                                                                    <i class="fas fa-minus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="button" id="add-penguji" class="btn btn-outline-primary">
                                                <i class="fas fa-plus"></i> Tambah Penguji
                                            </button>

                                            @error('penguji')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Jadwal
                            </button>
                            <a href="{{ route('jadwal-sidang.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let pengujiCount = 1;

            document.getElementById('add-penguji').addEventListener('click', function() {
                const container = document.getElementById('penguji-container');
                const newPenguji = document.querySelector('.penguji-item').cloneNode(true);

                // Update name attributes
                newPenguji.querySelector('select[name*="dosen_id"]').name =
                    `penguji[${pengujiCount}][dosen_id]`;
                newPenguji.querySelector('select[name*="peran"]').name = `penguji[${pengujiCount}][peran]`;

                // Reset values
                newPenguji.querySelectorAll('select').forEach(select => select.value = '');

                // Show remove button
                newPenguji.querySelector('.remove-penguji').style.display = 'block';

                container.appendChild(newPenguji);
                pengujiCount++;

                updateRemoveButtons();
            });

            document.addEventListener('click', function(e) {
                if (e.target.closest('.remove-penguji')) {
                    e.target.closest('.penguji-item').remove();
                    updateRemoveButtons();
                }
            });

            function updateRemoveButtons() {
                const items = document.querySelectorAll('.penguji-item');
                items.forEach((item, index) => {
                    const removeBtn = item.querySelector('.remove-penguji');
                    if (items.length > 1) {
                        removeBtn.style.display = 'block';
                    } else {
                        removeBtn.style.display = 'none';
                    }
                });
            }
        });
    </script>
@endsection
