<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Throwable;

class ApiLogger {
    public static function error(
        string $flag,
        Throwable $exception,
    ): void {
        Log::channel('daily')->error(
            $flag,
            [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ],
        );
    }
}
