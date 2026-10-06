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
    Route::get('/client', [ClientController::class, 'findAll']);
    Route::get('/client/{clientId}', [ClientController::class, 'findById']);
    Route::patch('/client/{clientId}', [ClientController::class, 'update']);
    Route::delete('/client/{clientId}', [ClientController::class, 'delete']);

    Route::post('/motorcycle/{clientId}', [MotorCycleController::class, 'create']);
    Route::get('/motorcycle/{clientId}/all', [MotorCycleController::class, 'findAll']);
    Route::get('/motorcycle/{clientId}/{motorcycleId}', [MotorCycleController::class, 'findById']);
    Route::patch('/motorcycle/{clientId}/{motorcycleId}', [MotorCycleController::class, 'update']);
    Route::delete('/motorcycle/{clientId}/{motorcycleId}', [MotorCycleController::class, 'delete']);
});
