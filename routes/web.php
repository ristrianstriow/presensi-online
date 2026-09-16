<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\JabatanController as AdminJabatanController;
use App\Http\Controllers\Admin\KetidakhadiranController as AdminKetidakhadiranController;
use App\Http\Controllers\Admin\LokasiPresensiController as AdminLokasiController;
use App\Http\Controllers\Admin\PegawaiController as AdminPegawaiController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\RekapPresensiController as AdminRekapController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\KetidakhadiranController as StaffKetidakhadiranController;
use App\Http\Controllers\Staff\PresensiController as StaffPresensiController;
use App\Http\Controllers\Staff\ProfileController as StaffProfileController;
use App\Http\Controllers\Staff\RiwayatPresensiController as StaffRiwayatController;
use Illuminate\Support\Facades\Route;

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Pegawai CRUD
    Route::resource('pegawai', AdminPegawaiController::class);

    // Jabatan CRUD
    Route::get('/jabatan', [AdminJabatanController::class, 'index'])->name('jabatan.index');
    Route::post('/jabatan', [AdminJabatanController::class, 'store'])->name('jabatan.store');
    Route::put('/jabatan/{jabatan}', [AdminJabatanController::class, 'update'])->name('jabatan.update');
    Route::delete('/jabatan/{jabatan}', [AdminJabatanController::class, 'destroy'])->name('jabatan.destroy');

    // Lokasi Presensi CRUD
    Route::resource('lokasi', AdminLokasiController::class);

    // Rekap Presensi (Harian & Bulanan)
    Route::get('/rekap', [AdminRekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/print', [AdminRekapController::class, 'print'])->name('rekap.print');
    Route::get('/rekap/bulanan', [AdminRekapController::class, 'bulanan'])->name('rekap.bulanan');
    Route::get('/rekap/bulanan/print', [AdminRekapController::class, 'printBulanan'])->name('rekap.bulanan.print');
    Route::get('/rekap/bulanan/{pegawai}/detail', [AdminRekapController::class, 'detailBulanan'])->name('rekap.bulanan.detail');

    // Ketidakhadiran / Pengajuan Izin
    Route::get('/ketidakhadiran', [AdminKetidakhadiranController::class, 'index'])->name('ketidakhadiran.index');
    Route::patch('/ketidakhadiran/{ketidakhadiran}/status', [AdminKetidakhadiranController::class, 'updateStatus'])->name('ketidakhadiran.status');

    // Akun User
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');

    // Profile Admin
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
});

// Staff Routes
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');

    // Presensi Online (GPS + Kamera)
    Route::get('/presensi', [StaffPresensiController::class, 'index'])->name('presensi.index');
    Route::post('/presensi/validate-location', [StaffPresensiController::class, 'validateLocation'])->name('presensi.validate-location');
    Route::post('/presensi/submit', [StaffPresensiController::class, 'submit'])->name('presensi.submit');

    // Riwayat Presensi
    Route::get('/riwayat', [StaffRiwayatController::class, 'index'])->name('riwayat.index');

    // Ketidakhadiran / Izin
    Route::get('/ketidakhadiran', [StaffKetidakhadiranController::class, 'index'])->name('ketidakhadiran.index');
    Route::get('/ketidakhadiran/create', [StaffKetidakhadiranController::class, 'create'])->name('ketidakhadiran.create');
    Route::post('/ketidakhadiran', [StaffKetidakhadiranController::class, 'store'])->name('ketidakhadiran.store');

    // Profile Staff
    Route::get('/profile', [StaffProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [StaffProfileController::class, 'update'])->name('profile.update');
});
