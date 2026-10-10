<?php

namespace App\Services\RepairOrder;

use App\DTOs\RepairOrder\CreateRepairOrderDTO;
use App\DTOs\RepairOrder\CreateRepairOrderPartDTO;
use App\DTOs\RepairOrder\CreateRepairOrderServiceDTO;
use App\Enums\HttpStatusCode;
use App\Exceptions\AppException;
use App\Mail\RepairOrder\RepairOrderToClientMail;
use App\Mappers\RepairOrder\RepairOrderReportMapper;
use App\Models\RepairOrder\RepairOrderModel;
use App\Models\User\User;
use App\Repositories\MotorCycle\MotorCycleRepository;
use App\Repositories\RepairOrder\RepairOrderPartRepository;
use App\Repositories\RepairOrder\RepairOrderRepository;
use App\Repositories\RepairOrder\RepairOrderServiceRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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

    public function listActiveRepairs(?User $user): array {
        $this->validateUser($user);

        $repairs = $this->repairOrderRepository->listActives($user->id);

        return $repairs;
    }

    public function getRepairOrder(?User $user, int $repairOrderId): array {
        $this->validateUser($user);

        $repair = $this->repairOrderRepository->getById($user->id, $repairOrderId);
        if ($repair === null) {
            throw new AppException('REPAIR.NOT.FOUND', HttpStatusCode::NOT_FOUND);
        }

        $repairId = (int) $repair['id'];

        $parts = $this->repairOrderPartRepository->getAllByRepairId($repairId);
        $services = $this->repairOrderServiceRepository->getAllByRepairId($repairId);

        $report = RepairOrderReportMapper::toArray(
            $repair,
            $parts,
            $services,
        );

        return $report;
    }

    /**
     * @param CreateRepairOrderPartDTO[] $repairOrderPartsList
     * @param CreateRepairOrderServiceDTO[] $repairOrderServicesList
     */
    public function updateRepairOrder(
        ?User $user,
        int $motorcycleId,
        int $repairOrderId,
        CreateRepairOrderDTO $repairOrder,
        array $repairOrderPartsList,
        array $repairOrderServicesList,
    ): array {
        $this->validateUser($user);

        $oldRepair = $this->findAndValidateRepairOrder(
            $user->id,
            $repairOrderId,
        );

        $repair = $this->createRepairOrder(
            $user,
            $motorcycleId,
            $repairOrder,
            $repairOrderPartsList,
            $repairOrderServicesList,
        );

        $this->repairOrderRepository->delete($oldRepair);

        return [
            'id' => $repair['id'],
        ];
    }

    public function removeRepair(
        ?User $user,
        int $repairOrderid,
    ): array {
        $this->validateUser($user);
        $repair = $this->findAndValidateRepairOrder(
            $user->id,
            $repairOrderid,
        );

        $removed = $this->repairOrderRepository->delete($repair);

        return [
            'removed' => $removed,
        ];
    }

    public function sendRepairOrderToClient(
        ?User $user,
        int $repairOrderId,
    ): array {
        $this->validateUser($user);

        $repairOrder = $this->getRepairOrder($user, $repairOrderId);

        $clientEmail = $repairOrder['client']['email'] ?? null;
        if (!is_string($clientEmail) || filter_var($clientEmail, FILTER_VALIDATE_EMAIL) === false) {
            throw new AppException(
                'REPAIR_ORDER.CLIENT.EMAIL.INVALID',
                HttpStatusCode::UNPROCESSABLE_ENTITY,
            );
        }

        Mail::to($clientEmail)->queue(
            new RepairOrderToClientMail(
                repairOrder: $repairOrder,
            ),
        );

        return [
            'queued' => true,
            'repair_order_id' => $repairOrderId,
            'recipient' => $clientEmail,
        ];
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

    private function findAndValidateRepairOrder(
        int $userId,
        int $repairOrderId,
    ): RepairOrderModel {
        $repair = $this->repairOrderRepository->findById(
            $userId,
            $repairOrderId,
        );

        if ($repair === null) {
            throw new AppException('REPAIR_ORDER.NOT_FOUND', HttpStatusCode::NOT_FOUND);
        }

        return $repair;
    }
}
