<?php

namespace App\Repositories\RepairOrder;

use App\DTOs\RepairOrder\CreateRepairOrderPartDTO;
use Illuminate\Support\Facades\DB;

class RepairOrderPartRepository {
    /**
     * @param CreateRepairOrderPartDTO[] $repairOrderPartsList
     */
    public function insertMulti(
        int $repairOrderId,
        array $repairOrderPartsList,
    ): void {
        if ($repairOrderPartsList === []) {
            return;
        }

        $data = [];
        foreach ($repairOrderPartsList as $part) {
            $data[] = [
                'repair_order_id' => $repairOrderId,
                'name' => $part->name,
                'description' => $part->description,
                'quantity' => $part->quantity,
                'unit_value' => $part->unitValue,
            ];
        }

        DB::table('repair_order_parts')->insert($data);
    }
}
