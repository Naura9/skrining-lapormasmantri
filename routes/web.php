<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'login')->name('login');
Route::view('/dashboard', 'dashboard')->name('dashboard');

Route::view('/data-admin', 'admin.fitur.kelola_user.data_admin.index')->name('admin.fitur.kelola_user.data_admin');
Route::view('/data-kader', 'admin.fitur.kelola_user.data_kader.index')->name('admin.fitur.kelola_user.data_kader');
Route::view('/data-nakes', 'admin.fitur.kelola_user.data_nakes.index')->name('admin.fitur.kelola_user.data_nakes');
Route::post('/admin/fitur/kelola_user/data_kader/import', [UserController::class, 'import_kader'])->name('data-kader.import_kader');
Route::post('/admin/fitur/kelola_user/data_nakes/import', [UserController::class, 'import_nakes'])->name('data-nakes.import_nakes');

Route::view('/kategori-skrining', 'admin.fitur.skrining.kategori.index')->name('admin.fitur.skrining.kategori');

Route::view('/data-wilayah', 'admin.fitur.data_wilayah.index')->name('admin.fitur.data_wilayah');

