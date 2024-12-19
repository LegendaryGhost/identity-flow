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


Route::controller(AuthController::class)
    ->prefix('/auth')->group(function () {
    Route::middleware('ensure_json_api_requests')->group(function () {
        Route::post('/inscription', 'inscription');

        Route::post('/connexion', 'connexion');
        Route::post('/verification-pin', 'verificationPin');
    });

    Route::get('/verification-email/{tokenVerification}', 'verificationEmail');
    Route::get('/reinitialisation-tentative', 'reinitialisationTentative');
});


