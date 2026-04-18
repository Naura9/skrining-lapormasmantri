<?php

use App\Http\Controllers\Api\IdentitasAnggotaController;
use App\Http\Controllers\Api\IdentitasKeluargaController;
use App\Http\Controllers\Api\MonitoringController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'login')->name('login');
Route::view('/dashboard', 'dashboard')->name('dashboard');

//admin
Route::view('/data-admin', 'admin.fitur.kelola_user.data_admin.index')->name('admin.fitur.kelola_user.data_admin');
Route::view('/data-kader', 'admin.fitur.kelola_user.data_kader.index')->name('admin.fitur.kelola_user.data_kader');
Route::view('/data-nakes', 'admin.fitur.kelola_user.data_nakes.index')->name('admin.fitur.kelola_user.data_nakes');
Route::post('/admin/fitur/kelola_user/data_kader/import', [UserController::class, 'import_kader'])->name('data-kader.import_kader');
Route::post('/admin/fitur/kelola_user/data_nakes/import', [UserController::class, 'import_nakes'])->name('data-nakes.import_nakes');

Route::view('/kategori-skrining', 'admin.fitur.skrining.kategori.index')->name('admin.fitur.skrining.kategori');
Route::view('/pertanyaan-kk', 'admin.fitur.skrining.pertanyaan_kk.index')->name('admin.fitur.skrining.pertanyaan_kk');
Route::view('/pertanyaan-nik', 'admin.fitur.skrining.pertanyaan_nik.index')->name('admin.fitur.skrining.pertanyaan_nik');

Route::view('/data-wilayah', 'admin.fitur.data_wilayah.index')->name('admin.fitur.data_wilayah');

Route::view('/monitoring-kader', 'admin.fitur.monitoring.kader')->name('admin.fitur.monitoring.kader');
Route::view('/monitoring-nik-per-siklus', 'admin.fitur.monitoring.nik_per_siklus')->name('admin.fitur.monitoring.nik_per_siklus');
Route::view('/monitoring-nik-per-kk', 'admin.fitur.monitoring.nik_per_kk')->name('admin.fitur.monitoring.nik_per_kk');

Route::view('/hasil-skrining', 'admin.fitur.monitoring.hasil_skrining')->name('admin.fitur.monitoring.hasil_skrining');
Route::get('/download/hasil-skrining', [MonitoringController::class, 'exportHasilskrining'])->name('hasil_skrining.download');
Route::get('/hasil-skrining/edit/{unit_id}', [MonitoringController::class, 'edit'])->name('hasil-skrining.edit');

Route::view('/data-warga-kk', 'admin.fitur.data_warga.kk.index')->name('admin.fitur.data_warga.kk');
Route::view('/data-warga-nik', 'admin.fitur.data_warga.nik.index')->name('admin.fitur.data_warga.nik');
Route::post('/admin/fitur/data_warga/kk/import', [IdentitasKeluargaController::class, 'import_data_keluarga'])->name('data-warga.kk.import');
Route::post('/admin/fitur/data_warga/nik/import', [IdentitasAnggotaController::class, 'import_anggota'])->name('data-warga.nik.import');

//kader
Route::view('/dashboard-kader', 'kader.dashboard_kader')->name('kader.dashboard_kader');

Route::view('/skrining-kk', 'kader.fitur.skrining.skrining_kk')->name('kader.fitur.skrining_kk');
Route::view('/skrining-nik', 'kader.fitur.skrining.skrining_nik')->name('kader.fitur.skrining_nik');

