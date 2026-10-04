<?php

namespace App\Http\Controllers\MotorCycle;

use App\DTOs\MotorCycle\CreateClientMotorCycleDTO;
use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\MotorCycle\CreateClientMotorCycleRequest;
use App\Services\MotorCycle\MotorCycleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class MotorCycleController extends Controller {
    private MotorCycleService $motorCycleService;
    public function __construct(
        ?MotorCycleService $motorCycleService = null,
    ) {
        $this->motorCycleService = $motorCycleService ?? new MotorCycleService();
    }

    public function create(int $clientId, CreateClientMotorCycleRequest $request): JsonResponse {
        $createResponse = function () use ($clientId, $request) {
            $user = Auth::user();
            $motorcycleDTO = CreateClientMotorCycleDTO::fromArray(
                $request->validated(),
            );

            $response = $this->motorCycleService->createClientMotorCycle($user, $clientId, $motorcycleDTO);

            return self::makeHttpResponse(
                'MOTORCYCLE.CREATE.DONE',
                HttpStatusCode::CREATED,
                $response,
            );
        };

        return $this->makeRequest('MOTORCYCLE.CREATE', $createResponse);
    }
}
