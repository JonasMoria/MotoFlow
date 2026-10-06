<?php

namespace App\Services\MotorCycle;

use App\DTOs\MotorCycle\CreateClientMotorCycleDTO;
use App\DTOs\MotorCycle\FindAllClientMotorCycleDTO;
use App\DTOs\MotorCycle\UpdateClientMotorCycleDTO;
use App\Enums\HttpStatusCode;
use App\Exceptions\AppException;
use App\Mappers\MotorCycle\MotorCycleMapper;
use App\Models\MotorCycle\MotorCycleModel;
use App\Models\User\User;
use App\Repositories\Client\ClientRepository;
use App\Repositories\MotorCycle\MotorCycleRepository;
use App\Support\PlateNormalizer;
use App\Support\StringNormalizer;

class MotorCycleService {
    private MotorCycleRepository $motorCycleRepository;
    private ClientRepository $clientRepository;

    public function __construct(
        ?MotorCycleRepository $motorCycleRepository = null,
        ?ClientRepository $clientRepository = null,
    ) {
        $this->motorCycleRepository = $motorCycleRepository ?? new MotorCycleRepository();
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
        $motorcycle = $this->motorCycleRepository->create($clientId, $motorcycleDTO);

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

    public function findAll(
        ?User $user,
        int $clientId,
        FindAllClientMotorCycleDTO $motorCycleDTO,
    ): array {
        $this->validateUser($user);

        $motorcycles = $this->motorCycleRepository->findAll(
            $user->id,
            $clientId,
            $motorCycleDTO,
        );

        $motorcyclesMapped = MotorCycleMapper::toPaginatedArray($motorcycles);
        return $motorcyclesMapped;
    }

    public function findById(
        ?User $user,
        int $clientId,
        int $motorcycleId,
    ) {
        $this->validateUser($user);

        $motorcycle = $this->getMotorcycle(
            $user->id,
            $clientId,
            $motorcycleId,
        );

        $motorcycleMapped = MotorCycleMapper::toArray($motorcycle);
        return $motorcycleMapped;
    }

    public function update(
        ?User $user,
        UpdateClientMotorCycleDTO $motorCycleDTO,
        int $clientId,
        int $motorcycleId,
    ): array {
        $this->validateUser($user);

        $motorcycle = $this->getMotorcycle(
            $user->id,
            $clientId,
            $motorcycleId,
        );

        $data = MotorCycleMapper::toUpdateArray($motorCycleDTO);

        $motorcycleUpdated = $this->motorCycleRepository->update($motorcycle, $data);

        return [
            'id' => $motorcycleUpdated->id,
        ];
    }

    public function delete(?User $user, int $clientId, int $motorcycleId): array {
        $this->validateUser($user);

        $motorcycle = $this->getMotorcycle(
            $user->id,
            $clientId,
            $motorcycleId,
        );

        $removed = $this->motorCycleRepository->delete($motorcycle);

        return [
            'removed' => $removed,
        ];
    }

    private function validateUser(?User $user): void {
        if (!$user) {
            throw new AppException('USER.UNAUTHENTICATED', HttpStatusCode::UNAUTHORIZED);
        }
    }

    private function getMotorcycle(int $userId, int $clientId, int $motorcycleId): MotorCycleModel {
        $motorcycle = $this->motorCycleRepository->findById(
            $userId,
            $clientId,
            $motorcycleId,
        );

        if (!$motorcycle) {
            throw new AppException('MOTORCYCLE.NOT_FOUND', HttpStatusCode::NOT_FOUND);
        }

        return $motorcycle;
    }
}
