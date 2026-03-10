@extends('layouts.dashboard')

@section('content')
    <div class="container-fluid d-flex align-items-center justify-content-center" style="min-height: 80vh;">
        <div class="text-center">
            <div class="mb-4">
                <svg class="mb-3" width="150" height="150" viewBox="0 0 150 150" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <circle cx="75" cy="75" r="70" fill="#FEE" stroke="#F44" stroke-width="3" />
                    <circle cx="60" cy="60" r="6" fill="#F44" />
                    <circle cx="90" cy="60" r="6" fill="#F44" />
                    <path d="M65 85 Q75 95 85 85" stroke="#F44" stroke-width="3" stroke-linecap="round" />
                </svg>
            </div>

            <h1 class="display-4 fw-bold text-danger mb-2">401</h1>
            <h2 class="h3 mb-3 text-dark">Tidak Terautentikasi</h2>

            <p class="lead text-muted mb-4">
                Silakan login terlebih dahulu untuk mengakses halaman ini.<br>
                Anda perlu memiliki akun yang valid untuk melanjutkan.
            </p>

            <div class="d-flex gap-2 justify-content-center">
                <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>

                <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </a>
            </div>

            <div class="mt-5 pt-5 border-top">
                <small class="text-muted d-block">
                    <i class="fas fa-info-circle me-2"></i>
                    Error Code: 401 Unauthorized
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
