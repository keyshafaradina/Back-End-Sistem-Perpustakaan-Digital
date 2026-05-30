<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LaporanPeminjamanController;
use App\Http\Controllers\KunjunganController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

Route::post('/profile/admin/{id}', [AuthController::class, 'updateProfilAdmin']);
Route::put('/profile/admin/{id}', [AuthController::class, 'updateProfilAdmin']);

Route::post('/profile/anggota/{id}', [AuthController::class, 'updateProfilAnggota']);
Route::put('/profile/anggota/{id}', [AuthController::class, 'updateProfilAnggota']);

/*
|--------------------------------------------------------------------------
| DASHBOARD ADMIN STATISTIK
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index']);
Route::get('/dashboard/buku-populer', [DashboardController::class, 'bukuPopuler']);
Route::get('/dashboard/terlambat', [DashboardController::class, 'terlambat']);
Route::get('/dashboard/perpanjangan', [DashboardController::class, 'permohonanPerpanjangan']);
Route::get('/dashboard/jatuh-tempo', [DashboardController::class, 'jatuhTempo']);

/*
|--------------------------------------------------------------------------
| DASHBOARD SETTING UNTUK ANGGOTA
|--------------------------------------------------------------------------
*/

Route::get('/dashboard-setting', [DashboardController::class, 'editDashboard']);
Route::post('/dashboard-setting', [DashboardController::class, 'updateDashboard']);
Route::put('/dashboard-setting', [DashboardController::class, 'updateDashboard']);

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

Route::get('/buku-arsip', [BukuController::class, 'diarsipkan']);
Route::get('/buku-dihapus', [BukuController::class, 'dihapus']);

Route::get('/buku', [BukuController::class, 'index']);
Route::post('/buku', [BukuController::class, 'store']);

Route::put('/buku/{id}/arsipkan', [BukuController::class, 'arsipkan']);
Route::put('/buku/{id}/buka-arsip', [BukuController::class, 'bukaArsip']);
Route::put('/buku/{id}/hapuskan', [BukuController::class, 'hapuskan']);
Route::put('/buku/{id}/pulihkan', [BukuController::class, 'pulihkan']);

Route::get('/buku/{id}', [BukuController::class, 'show']);
Route::put('/buku/{id}', [BukuController::class, 'update']);
Route::delete('/buku/{id}', [BukuController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| PEMINJAMAN & PENGEMBALIAN
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
Route::post('/perpanjangan/{id}/ajukan', [LaporanPeminjamanController::class, 'ajukanPerpanjangan']);
Route::put('/perpanjangan/{id}/setujui', [LaporanPeminjamanController::class, 'setujuiPerpanjangan']);
Route::put('/perpanjangan/{id}/tolak', [LaporanPeminjamanController::class, 'tolakPerpanjangan']);

/*
|--------------------------------------------------------------------------
| KUNJUNGAN
|--------------------------------------------------------------------------
*/

Route::post('/kunjungan/simpan', [KunjunganController::class, 'simpan']);

/*
|--------------------------------------------------------------------------
| LAPORAN
|--------------------------------------------------------------------------
*/

Route::get('/laporan/kunjungan', [LaporanController::class, 'laporanKunjungan']);
Route::get('/laporan/peminjaman', [LaporanController::class, 'laporanPeminjaman']);
Route::get('/laporan/pengembalian', [LaporanController::class, 'laporanPengembalian']);
Route::post('/laporan/kunjungan/simpan', [LaporanController::class, 'simpanKunjungan']);