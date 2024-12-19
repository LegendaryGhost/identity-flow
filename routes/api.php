<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(AuthController::class)->group(function () {
    Route::post('/inscription', 'inscription');
    Route::post('/connexion', 'connexion');
    Route::get('/verification-email/{tokenVerification}', 'verificationEmail');
});

Route::post('/auth/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/auth/verification-pin',[\App\Http\Controllers\AuthController::class,'verificationPin']);
Route::get('/auth/reinitialisation-tentative',[\App\Http\Controllers\AuthController::class,'reinitialisationTentative']);
