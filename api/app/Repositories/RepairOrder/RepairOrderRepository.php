<?php

namespace App\Repositories\RepairOrder;

use App\DTOs\RepairOrder\CreateRepairOrderDTO;
use App\Enums\RepairStatusCode;
use App\Models\RepairOrder\RepairOrderModel;
use Illuminate\Support\Facades\DB;

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

    public function listActives(int $userId): array {
        return DB::table('repair_order')
            ->join(
                'clients_motorcycles',
                'clients_motorcycles.id',
                '=',
                'repair_order.motorcycle_id',
            )
            ->join(
                'users_clients',
                'users_clients.id',
                '=',
                'clients_motorcycles.client_id',
            )
            ->where('users_clients.user_id', $userId)
            ->whereNotIn(
                'repair_order.status',
                RepairStatusCode::getNotActiveStatusValues(),
            )
            ->whereNull('repair_order.deleted_at')
            ->whereNull('users_clients.deleted_at')
            ->select([
                'repair_order.id',
                'repair_order.motorcycle_id',
                'repair_order.status',
                'repair_order.urgency_level',
                'repair_order.title',
                'repair_order.description',
                'repair_order.diagnosis',
                'repair_order.observations',
                'repair_order.started_at',
                'repair_order.finished_at',
                'repair_order.delivered_at',
                'repair_order.created_at',

                'clients_motorcycles.brand AS motorcycle_brand',
                'clients_motorcycles.model AS motorcycle_model',
                'clients_motorcycles.color AS motorcycle_color',
                'clients_motorcycles.plate AS motorcycle_plate',

                'users_clients.name AS client_name',
                'users_clients.phone AS client_phone',
                'users_clients.avatar_path AS profile_photo',
            ])
            ->orderByDesc('repair_order.created_at')
            ->get()
            ->toArray();
    }
}
