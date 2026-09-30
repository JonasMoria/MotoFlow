<?php

use App\Http\Controllers\HealthController;
use Illuminate\Support\Facades\Route;

// Public API's
Route::get('/health', [HealthController::class, 'check']);
