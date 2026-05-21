<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanPeminjamanController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/admin/login', [AdminController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

/*
|--------------------------------------------------------------------------
| DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/dashboard/edit', [DashboardController::class, 'editDashboard']);
Route::put('/dashboard/edit', [DashboardController::class, 'updateDashboard']);

Route::get('/dashboard/buku-populer', [DashboardController::class, 'bukuPopuler']);
Route::get('/dashboard/terlambat', [DashboardController::class, 'terlambat']);
Route::get('/dashboard/perpanjangan', [DashboardController::class, 'permohonanPerpanjangan']);
Route::get('/dashboard/jatuh-tempo', [DashboardController::class, 'jatuhTempo']);

/*
|--------------------------------------------------------------------------
| ANGGOTA
|--------------------------------------------------------------------------
*/

Route::get('/anggota', [AnggotaController::class, 'index']);
Route::get('/anggota/{id}', [AnggotaController::class, 'show']);
Route::post('/anggota/register', [AnggotaController::class, 'register']);
Route::delete('/anggota/{id}', [AnggotaController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| BUKU
|--------------------------------------------------------------------------
*/

Route::get('/buku', [BukuController::class, 'index']);
Route::post('/buku', [BukuController::class, 'store']);
Route::get('/buku/{id}', [BukuController::class, 'show']);
Route::put('/buku/{id}', [BukuController::class, 'update']);

Route::put('/buku/{id}/arsipkan', [BukuController::class, 'arsipkan']);
Route::get('/buku-arsip', [BukuController::class, 'diarsipkan']);
Route::put('/buku/{id}/buka-arsip', [BukuController::class, 'bukaArsip']);

Route::put('/buku/{id}/hapuskan', [BukuController::class, 'hapuskan']);
Route::get('/buku-dihapus', [BukuController::class, 'dihapus']);
Route::put('/buku/{id}/pulihkan', [BukuController::class, 'pulihkan']);
Route::delete('/buku/{id}', [BukuController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| PEMINJAMAN
|--------------------------------------------------------------------------
*/

Route::get('/peminjaman', [PeminjamanController::class, 'index']);
Route::post('/peminjaman/scan-anggota', [PeminjamanController::class, 'scanAnggota']);
Route::post('/peminjaman/cari-buku', [PeminjamanController::class, 'cariBuku']);
Route::post('/peminjaman/simpan', [PeminjamanController::class, 'simpan']);
Route::put('/peminjaman/{id}/kembalikan', [PeminjamanController::class, 'kembalikan']);
Route::post('/peminjaman/batal', [PeminjamanController::class, 'batal']);

/*
|--------------------------------------------------------------------------
| PERPANJANGAN BUKU
|--------------------------------------------------------------------------
*/

Route::get('/perpanjangan', [LaporanPeminjamanController::class, 'index']);
Route::get('/perpanjangan/{id}', [LaporanPeminjamanController::class, 'formPerpanjangan']);

// Anggota mengajukan perpanjangan
Route::post('/perpanjangan/{id}/ajukan', [LaporanPeminjamanController::class, 'ajukanPerpanjangan']);

// Admin menyetujui perpanjangan
Route::put('/perpanjangan/{id}/setujui', [LaporanPeminjamanController::class, 'setujuiPerpanjangan']);

// Admin menolak perpanjangan
Route::put('/perpanjangan/{id}/tolak', [LaporanPeminjamanController::class, 'tolakPerpanjangan']);

/*
|--------------------------------------------------------------------------
| LAPORAN
|--------------------------------------------------------------------------
*/

Route::get('/laporan/peminjaman', [LaporanController::class, 'index']);