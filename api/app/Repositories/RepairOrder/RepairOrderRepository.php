<?php

namespace App\Repositories\RepairOrder;

use App\DTOs\RepairOrder\CreateRepairOrderDTO;
use App\Models\RepairOrder\RepairOrderModel;

class RepairOrderRepository {
    public function insert(
        int $motorcycleId,
        CreateRepairOrderDTO $repairOrder,
    ): RepairOrderModel {
        $data = [
            'motorcycle_id' => $motorcycleId,
            'title' => $repairOrder->title,
            'description' => $repairOrder->description,
            'diagnosis' => $repairOrder->diagnosis,
            'observations' => $repairOrder->observations,
        ];

        if ($repairOrder->status !== null) {
            $data['status'] = $repairOrder->status;
        }

        if ($repairOrder->urgencyLevel !== null) {
            $data['urgency_level'] = $repairOrder->urgencyLevel;
        }

        return RepairOrderModel::create($data);
    }
}
