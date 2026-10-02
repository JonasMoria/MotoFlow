<?php

use App\Enums\HttpStatusCode;
use App\Exceptions\AppException;
use App\Exceptions\FormRequestException;
use App\Services\ApiLogger;
use App\Traits\HttpResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (
            FormRequestException|AppException $exception
        ) {
            return HttpResponse::makeHttpResponse(
                messageFlag: $exception->getMessage(),
                statusCode: HttpStatusCode::from($exception->getCode()),
            );
        });

        $exceptions->render(function (
            Throwable $exception
        ) {
            return HttpResponse::makeHttpResponse(
                messageFlag: $exception->getMessage(),
                statusCode: HttpStatusCode::from($exception->getCode()),
            );
        });
    })->create();
