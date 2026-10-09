<?php

namespace App\Http\Controllers\RepairOrder;

use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\RepairOrder\CreateRepairOrderRequest;
use App\Mappers\RepairOrder\RepairOrderRequestMapper;
use App\Services\RepairOrder\RepairOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RepairOrderController extends Controller {
    private RepairOrderService $repairOrderService;

    public function __construct(
        ?RepairOrderService $repairOrderService = null,
    ) {
        $this->repairOrderService = $repairOrderService ?? new RepairOrderService();
    }

    public function create(int $motorcycleId, CreateRepairOrderRequest $request): JsonResponse {
        $createResponse = function () use ($motorcycleId, $request) {
            $user = Auth::user();
            $data = $request->validated();

            $repairOrder = RepairOrderRequestMapper::toCreateDTO($data);
            $parts = RepairOrderRequestMapper::toPartsDTOList($data['parts'] ?? []);
            $services = RepairOrderRequestMapper::toServicesDTOList($data['services'] ?? []);

            $response = $this->repairOrderService->createRepairOrder(
                $user,
                $motorcycleId,
                $repairOrder,
                $parts,
                $services,
            );

            return self::makeHttpResponse(
                'REPAIR_ORDER.CREATE.DONE',
                HttpStatusCode::CREATED,
                $response,
            );
        };

        return $this->makeRequest('REPAIR_ORDER.CREATE', $createResponse);
    }
}
