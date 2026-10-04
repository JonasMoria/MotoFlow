<?php

use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\MotorCycle\MotorCycleController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

// Public API's
Route::get('/health', [HealthController::class, 'check']);

Route::post('/login', [UserController::class, 'login']);
Route::post('/login/verify', [UserController::class, 'verifyLogin']);

// Logged API's
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/logout-all', [UserController::class, 'logoutAll']);

    Route::post('/client', [ClientController::class, 'create']);

    Route::post('/motorcycle/{clientId}', [MotorCycleController::class, 'create']);
});
