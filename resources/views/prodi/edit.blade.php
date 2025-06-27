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
                    <h3>Prodi</h3>
                    <p class="text-subtitle text-muted">
                        Edit Prodi
                    </p>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">Dashboard</a>
                            </li>

                            <li class="breadcrumb-item active" aria-current="page">
                                <a href="{{ route('prodis.index') }}">Prodi</a>
                            </li>

                            <li class="breadcrumb-item active" aria-current="page">
                                Edit Prodi
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <section class="section">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Data Prodi</h5>
                </div>
                <div class="card-body">

                    <form action="{{ route('prodis.update', $prodi->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                                value="{{ $prodi->name }}" required>
                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="jenjang" class="form-label">Jenjang</label>
                            <select name="jenjang" class="form-select @error('jenjang') is-invalid @enderror" required>
                                <option value="">Pilih Jenjang</option>
                                <option value="D3" {{ old('jenjang', $prodi->jenjang) == 'D3' ? 'selected' : '' }}>D3
                                </option>
                                <option value="S1" {{ old('jenjang', $prodi->jenjang) == 'S1' ? 'selected' : '' }}>S1
                                </option>
                                <option value="S2" {{ old('jenjang', $prodi->jenjang) == 'S2' ? 'selected' : '' }}>S2
                                </option>
                            </select>
                            @error('jenjang')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="" class="form-label">Kaprodi</label>
                            <input type="text" class="form-control @error('kaprodi') is-invalid @enderror" name="kaprodi"
                                value="{{ $prodi->kaprodi }}" required>
                            @error('kaprodi')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Buat Prodi</button>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
