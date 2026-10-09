<?php

namespace App\Repositories\RepairOrder;

use App\DTOs\RepairOrder\CreateRepairOrderServiceDTO;
use Illuminate\Support\Facades\DB;

class RepairOrderServiceRepository {
    /**
     * @param CreateRepairOrderServiceDTO[] $repairOrderServicesList
     */
    public function insertMulti(
        int $repairOrderId,
        array $repairOrderServicesList,
    ): void {
        if ($repairOrderServicesList === []) {
            return;
        }

        $data = [];

        foreach ($repairOrderServicesList as $service) {
            $data[] = [
                'repair_order_id' => $repairOrderId,
                'name' => $service->name,
                'description' => $service->description,
                'quantity' => $service->quantity,
                'unit_value' => $service->unitValue,
            ];
        }

        DB::table('repair_order_services')->insert($data);
    }
}
