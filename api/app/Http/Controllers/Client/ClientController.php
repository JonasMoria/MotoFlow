<?php

namespace App\Http\Controllers\Client;

use App\DTOs\Client\CreateClientDTO;
use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CreateClientRequest;
use App\Services\Client\ClientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller {
    private ClientService $clientService;
    public function __construct(
        ?ClientService $clientService = null,
    ) {
        $this->clientService = $clientService ?? new ClientService();
    }

    public function create(CreateClientRequest $request): JsonResponse {
        $createResponse = function () use ($request) {
            $user = Auth::user();
            $clientDTO = CreateClientDTO::fromArray(
                $request->validated(),
            );

            $response = $this->clientService->createClient($user, $clientDTO);

            return self::makeHttpResponse(
                'CLIENT.CREATE.DONE',
                HttpStatusCode::CREATED,
                $response,
            );
        };

        return $this->makeRequest('CLIENT.CREATE', $createResponse);
    }
}
