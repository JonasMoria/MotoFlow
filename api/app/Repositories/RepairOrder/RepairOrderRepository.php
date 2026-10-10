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

    public function getById(int $userId, int $repairOrderId): ?array {
        $repair = DB::table('repair_order AS RO')
            ->join(
                'clients_motorcycles AS CM',
                'CM.id',
                '=',
                'RO.motorcycle_id',
            )
            ->join(
                'users_clients AS UC',
                'UC.id',
                '=',
                'CM.client_id',
            )
            ->where(
                'RO.id',
                $repairOrderId,
            )
            ->where(
                'UC.user_id',
                $userId,
            )
            ->select([
                // Ordem de serviço
                'RO.id',
                'RO.motorcycle_id',
                'RO.status',
                'RO.urgency_level',
                'RO.title',
                'RO.description',
                'RO.diagnosis',
                'RO.observations',
                'RO.started_at',
                'RO.finished_at',
                'RO.delivered_at',
                'RO.created_at',
                'RO.updated_at',

                // Motocicleta
                'CM.brand as motorcycle_brand',
                'CM.model as motorcycle_model',
                'CM.plate as motorcycle_plate',
                'CM.renavam as motorcycle_renavam',
                'CM.chassis as motorcycle_chassis',
                'CM.year as motorcycle_year',
                'CM.color as motorcycle_color',
                'CM.engine_number as motorcycle_engine_number',

                // Cliente
                'UC.id as client_id',
                'UC.name as client_name',
                'UC.phone as client_phone',
                'UC.email as client_email',
                'UC.document as client_document',
                'UC.address as client_address',
                'UC.avatar_path as profile_photo',
            ])
            ->first();

        return $repair !== null
            ? (array) $repair
            : null;
    }

    public function findById(int $userId, int $repairOrderId): ?RepairOrderModel {
        return RepairOrderModel::query()
            ->whereKey($repairOrderId)
            ->whereHas(
                'motorcycle',
                fn ($query) => $query->whereHas(
                    'client',
                    fn ($query) => $query->where('user_id', $userId),
                ),
            )
            ->first();
    }

    public function delete(RepairOrderModel $repairOrder): ?bool {
        return $repairOrder->delete();
    }
}
