<?php

use App\Http\Controllers\Api\AnggotaKeluargaController;
use App\Http\Controllers\Api\IdentitasAnggotaController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\IdentitasKeluargaController;
use App\Http\Controllers\Api\KeluargaController;
use App\Http\Controllers\Api\KelurahanController;
use App\Http\Controllers\Api\PertanyaanController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\SkriningController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MonitoringController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api;" middleware group. Enjoy building your API!
|
*/


Route::get('/kelurahan', [KelurahanController::class, 'index']);
Route::get('/kelurahan/{id}', [KelurahanController::class, 'show']);
Route::post('/kelurahan', [KelurahanController::class, 'store']);
Route::put('/kelurahan', [KelurahanController::class, 'update']);
Route::delete('/kelurahan/{id}', [KelurahanController::class, 'destroy']);

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

Route::get('/kategori', [KategoriController::class, 'index']);
Route::get('/kategori/{id}', [KategoriController::class, 'show']);
Route::post('/kategori', [KategoriController::class, 'store']);
Route::put('/kategori', [KategoriController::class, 'update']);
Route::delete('/kategori/{id}', [KategoriController::class, 'destroy']);

Route::get('/section', [SectionController::class, 'index']);
Route::get('/section/{id}', [SectionController::class, 'show']);
Route::post('/section', [SectionController::class, 'store']);
Route::put('/section', [SectionController::class, 'update']);
Route::delete('/section/{id}', [SectionController::class, 'destroy']);

Route::get('/pertanyaan', [PertanyaanController::class, 'index']);
Route::get('/pertanyaan/{id}', [PertanyaanController::class, 'show']);
Route::post('/pertanyaan', [PertanyaanController::class, 'store']);
Route::put('/pertanyaan', [PertanyaanController::class, 'update']);
Route::delete('/pertanyaan/{id}', [PertanyaanController::class, 'destroy']);

Route::put('/section/{id}/move', [SectionController::class, 'move']);
Route::put('pertanyaan/{id}/move', [PertanyaanController::class, 'move']);

Route::get('/unit_rumah', [UnitController::class, 'index']);
Route::get('/unit_rumah/{id}', [UnitController::class, 'show']);
Route::post('/unit_rumah', [UnitController::class, 'store']);
Route::put('/unit_rumah', [UnitController::class, 'update']);
Route::delete('/unit_rumah/{id}', [UnitController::class, 'destroy']);

Route::get('/data_keluarga', [KeluargaController::class, 'index']);
Route::get('/data_keluarga/{id}', [KeluargaController::class, 'show']);
Route::post('/data_keluarga', [KeluargaController::class, 'store']);
Route::put('/data_keluarga', [KeluargaController::class, 'update']);
Route::delete('/data_keluarga/{id}', [KeluargaController::class, 'destroy']);

Route::get('/data_anggota', [AnggotaKeluargaController::class, 'index']);
Route::get('/data_anggota/{id}', [AnggotaKeluargaController::class, 'show']);
Route::post('/data_anggota', [AnggotaKeluargaController::class, 'store']);
Route::put('/data_anggota', [AnggotaKeluargaController::class, 'update']);
Route::delete('/data_anggota/{id}', [AnggotaKeluargaController::class, 'destroy']);

Route::get('/identitas_keluarga', [IdentitasKeluargaController::class, 'index']);
Route::get('/identitas_keluarga/{id}', [IdentitasKeluargaController::class, 'show']);
Route::post('/identitas_keluarga', [IdentitasKeluargaController::class, 'store']);
Route::put('/identitas_keluarga', [IdentitasKeluargaController::class, 'update']);
Route::delete('/identitas_keluarga/{id}', [IdentitasKeluargaController::class, 'destroy']);

Route::get('/identitas_anggota', [IdentitasAnggotaController::class, 'index']);
Route::get('/identitas_anggota/{id}', [IdentitasAnggotaController::class, 'show']);
Route::post('/identitas_anggota', [IdentitasAnggotaController::class, 'store']);
Route::put('/identitas_anggota', [IdentitasAnggotaController::class, 'update']);
Route::delete('/identitas_anggota/{id}', [IdentitasAnggotaController::class, 'destroy']);

Route::get('/skrining', [SkriningController::class, 'index']);
Route::get('/skrining/{id}', [SkriningController::class, 'show']);
Route::post('/skrining', [SkriningController::class, 'store']);
Route::put('/skrining', [SkriningController::class, 'update']);
Route::delete('/skrining/{id}', [SkriningController::class, 'destroy']);

Route::get('/monitoring_kader', [MonitoringController::class, 'monitoringKader']);
Route::get('/monitoring/nik-per-kk', [MonitoringController::class, 'monitoringNikPerKk']);
Route::get('/monitoring/nik-per-siklus', [MonitoringController::class, 'monitoringNikPerSiklus']);
Route::get('/monitoring/hasil-skrining', [MonitoringController::class, 'monitoringHasilSkrining']);

Route::get('/', function () {
    return response()->failed(['Endpoint yang anda minta tidak tersedia']);
});

/**
 * Jika Frontend meminta request endpoint API yang tidak terdaftar
 * maka akan menampilkan HTTP 404
 */
Route::fallback(function () {
    return response()->failed(['Endpoint yang anda minta tidak tersedia']);
});
