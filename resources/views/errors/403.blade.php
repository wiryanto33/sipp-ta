@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="text-center">
            <div class="mb-4">
                <svg class="mb-3" width="150" height="150" viewBox="0 0 150 150" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <circle cx="75" cy="75" r="70" fill="#FEE" stroke="#F44" stroke-width="3" />
                    <path d="M60 60 L90 90 M90 60 L60 90" stroke="#F44" stroke-width="4" stroke-linecap="round" />
                </svg>
            </div>

            <h1 class="display-4 fw-bold text-danger mb-2">403</h1>
            <h2 class="h3 mb-3 text-dark">Akses Dilarang</h2>

            <p class="lead text-muted mb-4">
                Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.<br>
                Hubungi administrator jika Anda merasa ini adalah kesalahan.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>

                @if (auth()->check())
                    @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('kaprodi'))
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-home me-2"></i>Dashboard
                        </a>
                    @elseif(auth()->user()->hasRole('dosen'))
                        <a href="{{ route('dosen.dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-home me-2"></i>Dashboard Dosen
                        </a>
                    @elseif(auth()->user()->hasRole('mahasiswa'))
                        <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-home me-2"></i>Dashboard Mahasiswa
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                @endif
            </div>

            <div class="mt-5 pt-5 border-top">
                <small class="text-muted d-block">
                    <i class="fas fa-info-circle me-2"></i>
                    Error Code: 403 Forbidden
                </small>
            </div>
        </div>
    </div>

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
@endsection
