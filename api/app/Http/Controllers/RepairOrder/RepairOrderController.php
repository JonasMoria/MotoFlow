<?php

namespace App\Http\Controllers\RepairOrder;

use App\Enums\HttpStatusCode;
use App\Enums\PaperFormatCode;
use App\Exceptions\AppException;
use App\Http\Controllers\Controller;
use App\Http\Requests\RepairOrder\CreateRepairOrderRequest;
use App\Mappers\RepairOrder\RepairOrderRequestMapper;
use App\Services\RepairOrder\RepairOrderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
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

    public function listActive(): JsonResponse {
        $listResponse = function () {
            $user = Auth::user();

            $repairs = $this->repairOrderService->listActiveRepairs($user);

            return self::makeHttpResponse(
                'REPAIR.ACTIVE.LIST',
                HttpStatusCode::OK,
                $repairs,
            );
        };

        return $this->makeRequest('REPAIR.LIST.DONE', $listResponse);
    }

    public function get(int $repairOrderId): JsonResponse {
        $repairResponse = function () use ($repairOrderId) {
            $user = Auth::user();

            $repair = $this->repairOrderService->getRepairOrder($user, $repairOrderId);

            return self::makeHttpResponse(
                'REPAIR.GET.OK',
                HttpStatusCode::OK,
                $repair,
            );
        };

        return $this->makeRequest('REPAIR.GET', $repairResponse);
    }

    public function update(
        int $motorcycleId,
        int $repairOrderId,
        CreateRepairOrderRequest $request,
    ): JsonResponse {
        $updateResponse = function () use ($motorcycleId, $repairOrderId, $request) {
            $user = Auth::user();
            $data = $request->validated();

            $repairOrder = RepairOrderRequestMapper::toCreateDTO($data);
            $parts = RepairOrderRequestMapper::toPartsDTOList($data['parts'] ?? []);
            $services = RepairOrderRequestMapper::toServicesDTOList($data['services'] ?? []);

            $response = $this->repairOrderService->updateRepairOrder(
                $user,
                $motorcycleId,
                $repairOrderId,
                $repairOrder,
                $parts,
                $services,
            );

            return self::makeHttpResponse(
                'REPAIR_ORDER.UPDATE.DONE',
                HttpStatusCode::CREATED,
                $response,
            );
        };

        return $this->makeRequest('REPAIR_ORDER.UPDATE', $updateResponse);
    }

    public function delete(int $repairOrderId): JsonResponse {
        $deleteResponse = function () use ($repairOrderId) {
            $user = Auth::user();

            $response = $this->repairOrderService->removeRepair(
                $user,
                $repairOrderId,
            );

            return self::makeHttpResponse(
                'REPAIR_ORDER.DELETE.DONE',
                HttpStatusCode::CREATED,
                $response,
            );
        };

        return $this->makeRequest('REPAIR_ORDER.DELETE', $deleteResponse);
    }

    public function download(int $repairOrderId): Response|string {
        try {
            $user = Auth::user();
            $repairOrder = $this->repairOrderService->getRepairOrder($user, $repairOrderId);

            return $this->loadPDF(
                'pdf.repair-order',
                [
                    'repairOrder' => $repairOrder,
                ],
                PaperFormatCode::A4->label(),
                "OS-{$repairOrderId}.pdf",
            );

        } catch (AppException $appException) {
            return response()->json(
                [
                    'message' => $appException->getMessage(),
                    'data' => null,
                ],
                $appException->getCode(),
            );
        } catch (Exception $th) {
            return response()->json(
                [
                    'message' => 'SYSTEM.FATAL.ERROR',
                    'data' => null,
                ],
                HttpStatusCode::INTERNAL_SERVER_ERROR->value,
            );
        }
    }

    public function sentToClient(int $repairOrderId): JsonResponse {
        $sendResponse = function () use ($repairOrderId) {
            $user = Auth::user();

            $response = $this->repairOrderService->sendRepairOrderToClient(
                $user,
                $repairOrderId,
            );

            return self::makeHttpResponse(
                'REPAIR_ORDER.SENT.DONE',
                HttpStatusCode::ACCEPTED,
                $response,
            );
        };

        return $this->makeRequest('REPAIR_ORDER.SENT', $sendResponse);
    }
}
