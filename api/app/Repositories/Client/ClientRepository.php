<?php

namespace App\Repositories\Client;

use App\DTOs\Client\CreateClientDTO;
use App\DTOs\Client\FindAllClientDTO;
use App\Models\Client\ClientModel;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientRepository {
    public function create(int $userId, CreateClientDTO $data): ClientModel {
        return ClientModel::create([
            'user_id' => $userId,
            'name' => $data->name,
            'phone' => $data->phone,
            'email' => $data->email,
            'document' => $data->document,
            'address' => $data->address,
        ]);
    }

    public function update(ClientModel $client, array $data): ClientModel {
        $client->update($data);

        return $client->refresh();
    }

    public function existsByIdAndUserId(int $clientId, int $userId): bool {
        return ClientModel::query()
            ->where('id', $clientId)
            ->where('user_id', $userId)
            ->exists();
    }

    public function findAll(
        int $userId,
        FindAllClientDTO $clientDTO,
    ): LengthAwarePaginator {
        return ClientModel::query()
            ->where('user_id', $userId)
            ->orderBy('name')
            ->paginate(
                perPage: $clientDTO->perPage,
                page: $clientDTO->page,
            );
    }

    public function findById(
        int $userId,
        int $clientId,
    ): ?ClientModel {
        return ClientModel::query()
            ->where('user_id', $userId)
            ->whereKey($clientId)
            ->first();
    }

    public function delete(ClientModel $client): ?bool {
        return $client->delete();
    }
}
