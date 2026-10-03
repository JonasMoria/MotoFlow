<?php

use App\Enums\HttpStatusCode;
use App\Exceptions\AppException;
use App\Exceptions\FormRequestException;
use App\Traits\HttpResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn () => null);
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
            AuthenticationException $exception
        ) {
            return HttpResponse::makeHttpResponse(
                messageFlag: 'AUTH.UNAUTHENTICATED',
                statusCode: HttpStatusCode::UNAUTHORIZED,
            );
        });

        $exceptions->render(function (
            Throwable $exception
        ) {
            return HttpResponse::makeHttpResponse(
                messageFlag: 'SERVER.ERROR',
                statusCode: HttpStatusCode::INTERNAL_SERVER_ERROR,
            );
        });
    })->create();
