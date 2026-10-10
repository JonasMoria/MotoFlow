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

    public function getAllByRepairId(int $repairOrderId): array {
        return DB::table('repair_order_parts')
            ->where(
                'repair_order_id',
                $repairOrderId,
            )
            ->select([
                'id AS repair_part_id',
                'name AS repair_part_name',
                'description AS repair_part_description',
                'quantity AS repair_part_quantity',
                'unit_value AS repair_part_unit_value',
            ])
            ->get()
            ->all();
    }
}
