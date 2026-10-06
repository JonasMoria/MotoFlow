<?php

namespace App\Repositories\MotorCycle;

use App\DTOs\MotorCycle\CreateClientMotorCycleDTO;
use App\DTOs\MotorCycle\FindAllClientMotorCycleDTO;
use App\Models\MotorCycle\MotorCycleModel;
use Illuminate\Pagination\LengthAwarePaginator;

class MotorCycleRepository {
    public function create(int $clientId, CreateClientMotorCycleDTO $motorcycleDTO): MotorCycleModel {
        return MotorCycleModel::create([
            'client_id' => $clientId,
            'plate' => $motorcycleDTO->plate,
            'renavam' => $motorcycleDTO->renavam,
            'chassis' => $motorcycleDTO->chassis,
            'brand' => $motorcycleDTO->brand,
            'model' => $motorcycleDTO->model,
            'year' => $motorcycleDTO->year,
            'color' => $motorcycleDTO->color,
            'engine_number' => $motorcycleDTO->engineNumber,
        ]);
    }

    public function findAll(
        int $userId,
        int $clientId,
        FindAllClientMotorCycleDTO $motorCycleDTO,
    ): LengthAwarePaginator {
        return MotorCycleModel::query()
            ->where('client_id', $clientId)
            ->whereHas(
                'client',
                fn ($query) => $query->where('user_id', $userId),
            )
            ->orderBy('brand')
            ->orderBy('model')
            ->paginate(
                perPage: $motorCycleDTO->perPage,
                page: $motorCycleDTO->page,
            );
    }

    public function findById(
        int $userId,
        int $clientId,
        int $motorcycleId,
    ): ?MotorCycleModel {
        return MotorCycleModel::query()
            ->whereKey($motorcycleId)
            ->where('client_id', $clientId)
            ->whereHas(
                'client',
                fn ($query) => $query->where('user_id', $userId),
            )
            ->first();
    }

    public function update(MotorCycleModel $motorCycle, array $data): MotorCycleModel {
        $motorCycle->update($data);

        return $motorCycle->refresh();
    }

    public function delete(MotorCycleModel $motorCycle): ?bool {
        return $motorCycle->delete();
    }
}
