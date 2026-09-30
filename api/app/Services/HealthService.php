<?php

namespace App\Services;

class HealthService {
    public function getAppStatus(): array {
        return [
            'application' => 'MotoFlow API',
            'status' => 'Online',
            'message' => 'Welcome!',
        ];
    }
}
