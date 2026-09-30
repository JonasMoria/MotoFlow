<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use Illuminate\Http\JsonResponse;

abstract class Controller {
    public function makeResponse(
        string $messageFlag,
        HttpStatusCode $statusCode,
        mixed $data = null,
    ): JsonResponse {
        return response()->json([
            'message' => $messageFlag,
            'data' => $data,
        ], $statusCode->value);
    }
}
