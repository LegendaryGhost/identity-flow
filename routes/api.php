<?php

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

Route::get('/email', [\App\Http\Controllers\TestEmail::class, 'sendEmail']);
Route::post('/auth/login', [\App\Http\Controllers\AuthController::class, 'login']);
Route::post('/auth/verification-pin',[\App\Http\Controllers\AuthController::class,'verificationPin']);
Route::get('/auth/reinitialisation-tentative',[\App\Http\Controllers\AuthController::class,'reinitialisationTentative']);
