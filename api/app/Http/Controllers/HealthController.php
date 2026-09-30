<?php

namespace App\Http\Controllers;

use App\Enums\HttpStatusCode;
use App\Services\ApiLogger;
use App\Services\HealthService;
use Illuminate\Http\JsonResponse;
use Throwable;

class HealthController extends Controller {
    private HealthService $healthService;
    private ApiLogger $logger;

    public function __construct(
        ?HealthService $healthService = null,
        ?ApiLogger $logger = null,
    ) {
        $this->healthService = $healthService ?? new HealthService();
        $this->logger = $logger ?? new ApiLogger();
    }

    public function check(): JsonResponse {
        try {
            $healthData = $this->healthService->getAppStatus();

            return $this->makeResponse(
                'APP.STATUS.OK',
                HttpStatusCode::OK,
                $healthData,
            );
        } catch (Throwable $th) {
            $this->logger->error('APP.HEALTH', $th);

            return $this->makeResponse(
                'APP.STATUS.HAS.PROBLEM',
                HttpStatusCode::INTERNAL_SERVER_ERROR,
            );
        }
    }
}
