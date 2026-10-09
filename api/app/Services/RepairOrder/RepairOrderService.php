<?php

namespace App\Services\RepairOrder;

use App\DTOs\RepairOrder\CreateRepairOrderDTO;
use App\DTOs\RepairOrder\CreateRepairOrderPartDTO;
use App\DTOs\RepairOrder\CreateRepairOrderServiceDTO;
use App\Enums\HttpStatusCode;
use App\Exceptions\AppException;
use App\Models\RepairOrder\RepairOrderModel;
use App\Models\User\User;
use App\Repositories\MotorCycle\MotorCycleRepository;
use App\Repositories\RepairOrder\RepairOrderPartRepository;
use App\Repositories\RepairOrder\RepairOrderRepository;
use App\Repositories\RepairOrder\RepairOrderServiceRepository;
use Illuminate\Support\Facades\DB;

class RepairOrderService {
    private RepairOrderRepository $repairOrderRepository;
    private RepairOrderPartRepository $repairOrderPartRepository;
    private RepairOrderServiceRepository $repairOrderServiceRepository;
    private MotorCycleRepository $motorCycleRepository;

    public function __construct(
        ?RepairOrderRepository $repairOrderRepository = null,
        ?RepairOrderPartRepository $repairOrderPartRepository = null,
        ?RepairOrderServiceRepository $repairOrderServiceRepository = null,
        ?MotorCycleRepository $motorCycleRepository = null,
    ) {
        $this->repairOrderRepository =
            $repairOrderRepository ?? new RepairOrderRepository();

        $this->repairOrderPartRepository =
            $repairOrderPartRepository ?? new RepairOrderPartRepository();

        $this->repairOrderServiceRepository =
            $repairOrderServiceRepository ?? new RepairOrderServiceRepository();

        $this->motorCycleRepository =
            $motorCycleRepository ?? new MotorCycleRepository();
    }

    /**
     * @param CreateRepairOrderPartDTO[] $repairOrderPartsList
     * @param CreateRepairOrderServiceDTO[] $repairOrderServicesList
     */
    public function createRepairOrder(
        ?User $user,
        int $motorcycleId,
        CreateRepairOrderDTO $repairOrder,
        array $repairOrderPartsList,
        array $repairOrderServicesList,
    ): array {
        $this->validateUser($user);

        $this->validateMotorCycle(
            $user->id,
            $motorcycleId,
        );

        return DB::transaction(function () use (
            $motorcycleId,
            $repairOrder,
            $repairOrderPartsList,
            $repairOrderServicesList,
        ): array {
            $repairOrderModel = $this->insertRepairOrder(
                $motorcycleId,
                $repairOrder,
            );

            $this->repairOrderPartRepository->insertMulti(
                $repairOrderModel->id,
                $repairOrderPartsList,
            );

            $this->repairOrderServiceRepository->insertMulti(
                $repairOrderModel->id,
                $repairOrderServicesList,
            );

            return [
                'id' => $repairOrderModel->id,
            ];
        });
    }

    private function validateUser(?User $user): void {
        if (!$user) {
            throw new AppException(
                'USER.UNAUTHENTICATED',
                HttpStatusCode::UNAUTHORIZED,
            );
        }
    }

    private function validateMotorCycle(
        int $userId,
        int $motorcycleId,
    ): void {
        $exists = $this->motorCycleRepository->existsById(
            $userId,
            $motorcycleId,
        );

        if (!$exists) {
            throw new AppException(
                'REPAIR_ORDER.MOTORCYCLE.NOT_FOUND',
                HttpStatusCode::NOT_FOUND,
            );
        }
    }

    private function insertRepairOrder(
        int $motorcycleId,
        CreateRepairOrderDTO $repairOrder,
    ): RepairOrderModel {
        return $this->repairOrderRepository->insert(
            $motorcycleId,
            $repairOrder,
        );
    }
}
