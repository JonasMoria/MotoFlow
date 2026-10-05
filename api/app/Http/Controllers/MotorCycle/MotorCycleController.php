<?php

namespace App\Http\Controllers\MotorCycle;


use App\DTOs\MotorCycle\CreateClientMotorCycleDTO;
use App\DTOs\MotorCycle\FindAllClientMotorCycleDTO;
use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\MotorCycle\CreateClientMotorCycleRequest;
use App\Http\Requests\MotorCycle\FindAllClientMotorCycleRequest;
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

    public function findAll(
        FindAllClientMotorCycleRequest $request,
        int $clientId,
    ): JsonResponse {
        $findAllResponse = function () use ($request, $clientId) {
            $user = Auth::user();
            $findAllMotorCycleDTO = FindAllClientMotorCycleDTO::fromArray(
                $request->validated(),
            );

            $motorcycles = $this->motorCycleService->findAll(
                $user,
                $clientId,
                $findAllMotorCycleDTO,
            );

            return self::makeHttpResponse(
                'MOTORCYCLE.FIND_ALL.SUCCESS',
                HttpStatusCode::OK,
                $motorcycles,
            );
        };

        return $this->makeRequest('MOTORCYCLE.FIND_ALL', $findAllResponse);
    }

    public function findById(
        int $clientId,
        int $motorcycleId,
    ): JsonResponse {
        $findByIdResponse = function () use ($clientId, $motorcycleId) {
            $user = Auth::user();

            $motorcycle = $this->motorCycleService->findById(
                $user,
                $clientId,
                $motorcycleId,
            );

            return self::makeHttpResponse(
                'MOTORCYCLE.FIND_BY_ID.SUCCESS',
                HttpStatusCode::OK,
                $motorcycle,
            );
        };

        return $this->makeRequest('MOTORCYCLE.FIND_BY_ID', $findByIdResponse);
    }
}
