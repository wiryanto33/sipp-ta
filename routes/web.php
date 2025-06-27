<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BimbinganController;
use App\Http\Controllers\CategoryPenilaianController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DosenDashboardController;
use App\Http\Controllers\FormPenilaianController;
use App\Http\Controllers\JadwalSidangController;
use App\Http\Controllers\LogBimbinganController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\MahasiswaDashboardController;
use App\Http\Controllers\PengajuanTugasAkhirController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\PenilaianSidangController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProdiController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\RedirectIfAuthenticatedWithRole;
use Illuminate\Support\Facades\Route;

// Route::middleware('guest')->group(function () {
//     Route::get('/', [AuthenticatedSessionController::class, 'create'])
//         ->middleware(RedirectIfAuthenticatedWithRole::class)
//         ->name('login');
// });

Route::get('/', function () {
    return redirect(route('login'));
});

Route::middleware(['auth', 'role:admin|kaprodi'])->group(function () {
    Route::get('/dashboard-admin', [DashboardController::class, 'index'])
        ->middleware(['verified'])
        ->name('dashboard');
});

Route::middleware(['auth', 'check.profile', 'role:mahasiswa'])->group(function () {
    Route::get('/mahasiswa-dashboard', [MahasiswaDashboardController::class, 'index'])
        ->name('mahasiswa.dashboard');
});

Route::middleware(['auth', 'check.profile', 'role:dosen'])->group(function () {
    Route::get('/dosen-dashboard', [DosenDashboardController::class, 'index'])
        ->name('dosen.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/permission', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permission/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permission/store', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permission/{id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/permission/{id}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permission/{id}/delete', [PermissionController::class, 'destroy'])->name('permissions.destroy');

    Route::get('/role', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/role/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/role/store', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/role/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/role/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/role/{id}/delete', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::resource('prodis', ProdiController::class);

    // Route resource untuk CRUD user
    Route::resource('users', UserController::class);

    // Route khusus untuk mahasiswa
    Route::get('mahasiswas', [UserController::class, 'mahasiswaIndex'])->name('mahasiswas.index');

    // Route khusus untuk dosen
    Route::get('dosens', [UserController::class, 'dosenIndex'])->name('dosens.index');

    Route::get('kaprodis', [UserController::class, 'kaprodiIndex'])->name('kaprodis.index');

    // Route untuk bulk operations
    Route::delete('users/bulk-destroy', [UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');

    // Route untuk restore dan force delete (jika menggunakan soft delete)
    Route::patch('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::delete('users/{id}/force-destroy', [UserController::class, 'forceDestroy'])->name('users.force-destroy');

    //Route Pengajuan Ta
    Route::resource('pengajuan-tugas-akhir', PengajuanTugasAkhirController::class);

    // Update status pengajuan (admin/koordinator only)
    Route::put('/pengajuan/{pengajuan}/update-status', [PengajuanTugasAkhirController::class, 'updateStatus'])
        ->name('pengajuan.update-status');

    // Upload file skripsi (mahasiswa only - when status is sedang bimbingan)
    Route::post('/pengajuan/{pengajuan}/upload-skripsi', [PengajuanTugasAkhirController::class, 'uploadSkripsi'])
        ->name('pengajuan.upload-skripsi');

    // Download files (proposal or skripsi)
    Route::get('/pengajuan/{pengajuan}/download/{type}', [PengajuanTugasAkhirController::class, 'downloadFile'])
        ->name('pengajuan.download-file')
        ->where('type', 'proposal|skripsi');

    // Route Bimbingan
    Route::resource('bimbingan', BimbinganController::class);
    Route::put('bimbingan-approve/{bimbingan}', [BimbinganController::class, 'approve'])->name('bimbingan.approve');
    Route::put('bimbingan-reject/{bimbingan}', [BimbinganController::class, 'reject'])->name('bimbingan.reject');
    Route::put('bimbingan-complete/{bimbingan}', [BimbinganController::class, 'complete'])->name('bimbingan.complete');


    // Group routing log bimbingan di dalam prefix bimbingan
    Route::prefix('bimbingan/{bimbingan}/log-bimbingan')->name('log-bimbingan.')->group(function () {

        Route::get('/', [LogBimbinganController::class, 'index'])->name('index');
        Route::get('/create', [LogBimbinganController::class, 'create'])->name('create');
        Route::post('/', [LogBimbinganController::class, 'store'])->name('store');

        Route::get('/{logBimbingan}', [LogBimbinganController::class, 'show'])->name('show');
        Route::get('/{logBimbingan}/edit', [LogBimbinganController::class, 'edit'])->name('edit');
        Route::put('/{logBimbingan}', [LogBimbinganController::class, 'update'])->name('update');

        Route::delete('/{logBimbingan}', [LogBimbinganController::class, 'destroy'])->name('destroy');

        Route::post('/{logBimbingan}/saran', [LogBimbinganController::class, 'addSaran'])->name('saran');
        Route::get('/{logBimbingan}/download', [LogBimbinganController::class, 'downloadFile'])->name('download');
    });

    //route jadwal sidang
    Route::resource('jadwal-sidang', JadwalSidangController::class);
    Route::post('jadwal-sidang/{jadwalSidang}/status', [JadwalSidangController::class, 'updateStatus'])
        ->name('jadwal-sidang.update-status');
    Route::post('jadwal-sidang/{jadwalSidang}/kehadiran', [JadwalSidangController::class, 'updateKehadiran'])
        ->name('jadwal-sidang.update-kehadiran');
    Route::get('jadwal-sidang-calendar', [JadwalSidangController::class, 'calendar'])
        ->name('jadwal-sidang.calendar');
    Route::get('jadwal-sidang/{jadwalSidang}/download', [JadwalSidangController::class, 'downloadFile'])
        ->name('jadwal-sidang.download');


    Route::resource('penilaian-sidang', PenilaianSidangController::class);

    // Route khusus untuk print PDF
    Route::get('penilaian-sidang/{penilaianSidang}/print', [PenilaianSidangController::class, 'printPDF'])
        ->name('penilaian-sidang.print');

    // Route khusus untuk preview PDF
    Route::get('penilaian-sidang/{penilaianSidang}/preview', [PenilaianSidangController::class, 'previewPDF'])
        ->name('penilaian-sidang.preview');
});

require __DIR__ . '/auth.php';
