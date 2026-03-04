<?php

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


//kader
Route::view('/skrining-kk', 'kader.fitur.skrining.skrining_kk')->name('kader.fitur.skrining_kk');
Route::view('/skrining-nik', 'kader.fitur.skrining.skrining_nik')->name('kader.fitur.skrining_nik');

