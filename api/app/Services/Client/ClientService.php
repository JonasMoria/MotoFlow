<?php

namespace App\Services\Client;

use App\DTOs\Client\CreateClientDTO;
use App\DTOs\Client\FindAllClientDTO;
use App\DTOs\Client\UpdateClientDTO;
use App\Enums\HttpStatusCode;
use App\Exceptions\AppException;
use App\Mappers\Client\ClientMapper;
use App\Models\Client\ClientModel;
use App\Models\User\User;
use App\Repositories\Client\ClientRepository;
use App\Support\PhoneNormalizer;
use App\Support\StringNormalizer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ClientService {
    private ClientRepository $clientRepository;

    public function __construct(
        ?ClientRepository $clientRepository = null,
    ) {
        $this->clientRepository = $clientRepository ?? new ClientRepository();
    }

    public function createClient(?User $user, CreateClientDTO $clientDTO): array {
        $this->validateUser($user);

        $avatarPath = '';

        try {
            [$clientId, $avatarPath] = $this->insertClient(
                $user,
                $clientDTO,
            );

            return [
                'id' => $clientId,
                'avatar_path' => $avatarPath ?: null,
            ];
        } catch (Throwable $exception) {
            if ($avatarPath !== '') {
                Storage::disk('public')->delete($avatarPath);
            }

            throw $exception;
        }
    }

    protected function insertClient(
        User $user,
        CreateClientDTO $clientDTO,
    ): array {
        $avatarPath = '';

        $result = DB::transaction(function () use ($user, $clientDTO, &$avatarPath): array {
            $clientDTONormalized = $this->normalizeCreateClientData($clientDTO);

            $client = $this->clientRepository->create(
                $user->id,
                $clientDTONormalized,
            );

            if ($clientDTONormalized->avatar !== null) {
                $avatarPath = $this->putClientAvatarPath(
                    $client,
                    $clientDTONormalized,
                );

                if ($avatarPath !== '') {
                    $this->clientRepository->update($client, [
                        'avatar_path' => $avatarPath,
                    ]);
                }
            }

            return [
                $client->id,
                $avatarPath,
            ];
        });

        return $result;
    }

    protected function normalizeCreateClientData(CreateClientDTO $clientDTO): CreateClientDTO {
        return CreateClientDTO::fromArray([
            'name' => trim($clientDTO->name),
            'phone' => PhoneNormalizer::brazilian($clientDTO->phone),
            'email' => $clientDTO->email
                ? strtolower(trim($clientDTO->email))
                : null,
            'document' => $clientDTO->document
                ? StringNormalizer::numbersOnly($clientDTO->document)
                : null,
            'address' => $clientDTO->address
                ? trim($clientDTO->address)
                : null,
            'avatar' => $clientDTO->avatar,
        ]);
    }

    protected function putClientAvatarPath(
        ClientModel $client,
        CreateClientDTO $clientDTO,
    ): string {
        $path = $clientDTO->avatar->store(
            "clients/{$client->id}/avatar",
            'public',
        );

        return $path ?: '';
    }

    public function findAll(?User $user, FindAllClientDTO $clientDTO): array {
        $this->validateUser($user);

        $clients = $this->clientRepository->findAll(
            $user->id,
            $clientDTO,
        );

        $clientsMapped = ClientMapper::toPaginatedArray($clients);
        return $clientsMapped;
    }

    public function findById(?User $user, int $clientId): array {
        $this->validateUser($user);

        $client = $this->getClient($user->id, $clientId);

        $clientMapped = ClientMapper::toArray($client);
        return $clientMapped;
    }

    public function update(
        ?User $user,
        int $clientId,
        UpdateClientDTO $clientDTO,
    ): array {
        $this->validateUser($user);

        $client = $this->getClient($user->id, $clientId);

        $data = ClientMapper::toUpdateArray($clientDTO);

        $oldAvatarPath = $client->avatar_path;
        $newAvatarPath = null;

        try {
            if ($clientDTO->avatar !== null) {
                $newAvatarPath = $clientDTO->avatar->store(
                    "clients/{$client->id}/avatar",
                    'public',
                );

                $data['avatar_path'] = $newAvatarPath;
            }

            $clientUpdated = $this->clientRepository->update(
                $client,
                $data,
            );

            if ($newAvatarPath !== null && $oldAvatarPath !== null) {
                Storage::disk('public')->delete($oldAvatarPath);
            }

            return [
                'id' => $clientUpdated->id,
            ];
        } catch (Throwable $exception) {
            if ($newAvatarPath !== null) {
                Storage::disk('public')->delete($newAvatarPath);
            }

            throw $exception;
        }
    }

    private function validateUser(?User $user): void {
        if (!$user) {
            throw new AppException('USER.UNAUTHENTICATED', HttpStatusCode::UNAUTHORIZED);
        }
    }

    private function getClient(int $userId, int $clientId): ClientModel {
        $client = $this->clientRepository->findById($userId, $clientId);

        if (!$client) {
            throw new AppException('CLIENT.NOT_FOUND', HttpStatusCode::NOT_FOUND);
        }

        return $client;
    }
}
