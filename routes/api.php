<?php

use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\FamilyController;
use App\Http\Controllers\Api\FamilyMemberController;
use App\Http\Controllers\Api\KelurahanController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\ScreeningController;
use App\Http\Controllers\Api\SiteController;
use App\Http\Controllers\Api\UserController;
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


    Route::get('/questions', [QuestionController::class, 'index']);
    Route::get('/questions/{id}', [QuestionController::class, 'show']);
    Route::post('/questions', [QuestionController::class, 'store']);
    Route::put('/questions', [QuestionController::class, 'update']);
    Route::delete('/questions/{id}', [QuestionController::class, 'destroy']);

    Route::get('/families', [FamilyController::class, 'index']);
    Route::get('/families/{id}', [FamilyController::class, 'show']);
    Route::post('/families', [FamilyController::class, 'store']);
    Route::put('/families', [FamilyController::class, 'update']);
    Route::delete('/families/{id}', [FamilyController::class, 'destroy']);

    Route::get('/family-members', [FamilyMemberController::class, 'index']);
    Route::get('/family-members/{id}', [FamilyMemberController::class, 'show']);
    Route::post('/family-members', [FamilyMemberController::class, 'store']);
    Route::put('/family-members', [FamilyMemberController::class, 'update']);
    Route::delete('/family-members/{id}', [FamilyMemberController::class, 'destroy']);

    Route::get('/screenings', [ScreeningController::class, 'index']);
    Route::get('/screenings/{id}', [ScreeningController::class, 'show']);
    Route::post('/screenings', [ScreeningController::class, 'store']);
    Route::put('/screenings', [ScreeningController::class, 'update']);
    Route::delete('/screenings/{id}', [ScreeningController::class, 'destroy']);

    Route::get('/answers', [AnswerController::class, 'index']);
    Route::get('/answers/{id}', [AnswerController::class, 'show']);
    Route::post('/answers', [AnswerController::class, 'store']);
    Route::put('/answers', [AnswerController::class, 'update']);
    Route::delete('/answers/{id}', [AnswerController::class, 'destroy']);

    Route::get('/report/screening-activity', [ScreeningController::class, 'getScreeningActivity']);


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
