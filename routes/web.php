<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'login')->name('login');
Route::view('/dashboard', 'dashboard')->name('dashboard');

Route::view('/data-admin', 'admin.fitur.kelola_user.data_admin.index')->name('admin.fitur.kelola_user.data_admin');

Route::view('/data-wilayah', 'admin.fitur.data_wilayah.index')->name('admin.fitur.data_wilayah');

