<?php

namespace App\Exceptions;

use App\Enums\HttpStatusCode;
use RuntimeException;

class FormRequestException extends RuntimeException {
    public function __construct(
        string $message,
        ?HttpStatusCode $code = null,
    ) {
        return parent::__construct(
            $message,
            $code != null
                ? $code->value
                : HttpStatusCode::UNPROCESSABLE_ENTITY->value,
        );
    }
}
