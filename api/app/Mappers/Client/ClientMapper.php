<?php

namespace App\Mappers\Client;

use App\Models\Client\ClientModel;
use Illuminate\Pagination\LengthAwarePaginator;

final class ClientMapper {
    public static function toArray(ClientModel $client): array {
        return [
            'id' => $client->id,
            'name' => $client->name,
            'phone' => $client->phone,
            'email' => $client->email,
            'document' => $client->document,
            'address' => $client->address,
            'avatar_path' => $client->avatar_path,
            'created_at' => $client->created_at?->toISOString(),
            'updated_at' => $client->updated_at?->toISOString(),
        ];
    }

    public static function toPaginatedArray(LengthAwarePaginator $paginator): array {
        return [
            'data' => collect($paginator->items())
                ->map(
                    fn (ClientModel $client) => self::toArray($client),
                )
                ->values()
                ->all(),

            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];
    }
}
