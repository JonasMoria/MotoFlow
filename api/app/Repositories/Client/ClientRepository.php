<?php

namespace App\Repositories\Client;

use App\DTOs\Client\CreateClientDTO;
use App\Models\Client\ClientModel;

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
}
