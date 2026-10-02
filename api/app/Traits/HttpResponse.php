<?php

namespace App\Traits;

use App\Enums\HttpStatusCode;
use Illuminate\Http\JsonResponse;

trait HttpResponse {
    public static function makeHttpResponse(
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
