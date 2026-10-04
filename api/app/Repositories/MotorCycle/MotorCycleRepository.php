<?php

namespace App\Repositories\MotorCycle;

use App\DTOs\MotorCycle\CreateClientMotorCycleDTO;
use App\Models\MotorCycle\MotorCycleModel;

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
}
