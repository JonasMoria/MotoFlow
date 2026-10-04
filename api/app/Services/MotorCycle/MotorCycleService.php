<?php

namespace App\Services\MotorCycle;

use App\DTOs\MotorCycle\CreateClientMotorCycleDTO;
use App\Enums\HttpStatusCode;
use App\Exceptions\AppException;
use App\Models\User\User;
use App\Repositories\Client\ClientRepository;
use App\Repositories\MotorCycle\MotorCycleRepository;
use App\Support\PlateNormalizer;
use App\Support\StringNormalizer;

class MotorCycleService {
    private MotorCycleRepository $motorcicleRepository;
    private ClientRepository $clientRepository;

    public function __construct(
        ?MotorCycleRepository $motorcicleRepository = null,
        ?ClientRepository $clientRepository = null,
    ) {
        $this->motorcicleRepository = $motorcicleRepository ?? new MotorCycleRepository();
        $this->clientRepository = $clientRepository ?? new ClientRepository();
    }

    public function createClientMotorCycle(
        ?User $user,
        int $clientId,
        CreateClientMotorCycleDTO $clientMotorCycleDTO,
    ): array {
        if (!$user) {
            throw new AppException('USER.UNAUTHENTICATED', HttpStatusCode::UNAUTHORIZED);
        }

        $isValidClient = $this->clientRepository->existsByIdAndUserId($clientId, $user->id);
        if (!$isValidClient) {
            throw new AppException('CLIENT.NOT.FOUND', HttpStatusCode::NOT_FOUND);
        }

        $motorcycleDTO = $this->normalizeCreateMotorCycle($clientMotorCycleDTO);
        $motorcycle = $this->motorcicleRepository->create($clientId, $motorcycleDTO);

        return [
            'id' => $motorcycle->id,
        ];
    }

    protected function normalizeCreateMotorCycle(
        CreateClientMotorCycleDTO $clientMotorCycleDTO,
    ): CreateClientMotorCycleDTO {
        return CreateClientMotorCycleDTO::fromArray([
            'brand' => $clientMotorCycleDTO->brand,
            'model' => $clientMotorCycleDTO->model,
            'plate' => PlateNormalizer::brazilian($clientMotorCycleDTO->plate),
            'renavam' => $clientMotorCycleDTO->renavam
                ? StringNormalizer::numbersOnly($clientMotorCycleDTO->renavam)
                : null,
            'chassis' => $clientMotorCycleDTO->chassis,
            'year' => $clientMotorCycleDTO->year,
            'color' => $clientMotorCycleDTO->color,
            'engine_number' => $clientMotorCycleDTO->engineNumber,
        ]);
    }
}
