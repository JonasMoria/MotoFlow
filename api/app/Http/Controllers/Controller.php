<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use App\Exceptions\AppException;
use App\Exceptions\FormRequestException;
use App\Services\ApiLogger;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Throwable;

abstract class Controller {
    use HttpResponse;

    public function makeRequest(
        string $flag,
        callable $callback,
    ): JsonResponse {
        try {
            return $callback();
        } catch (FormRequestException $formRequestException) {
            return self::makeHttpResponse(
                $formRequestException->getMessage(),
                HttpStatusCode::from(
                    $formRequestException->getCode(),
                ),
            );
        } catch (AppException $customException) {
            return self::makeHttpResponse(
                $customException->getMessage(),
                HttpStatusCode::from(
                    $customException->getCode(),
                ),
            );
        } catch (Throwable $fatalError) {
            ApiLogger::error($flag, $fatalError);

            return self::makeHttpResponse(
                'SYSTEM.FATAL.ERROR',
                HttpStatusCode::INTERNAL_SERVER_ERROR,
            );
        }
    }
}
