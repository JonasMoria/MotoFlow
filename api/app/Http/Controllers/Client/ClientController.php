<?php

namespace App\Http\Controllers\Client;

use App\DTOs\Client\CreateClientDTO;
use App\DTOs\Client\FindAllClientDTO;
use App\DTOs\Client\UpdateClientDTO;
use App\Enums\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CreateClientRequest;
use App\Http\Requests\Client\FindAllClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
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

    public function findAll(FindAllClientRequest $request): JsonResponse {
        $findAllResponse = function () use ($request) {
            $user = Auth::user();
            $findAllClientsDTO = FindAllClientDTO::fromArray(
                $request->validated(),
            );

            $clients = $this->clientService->findAll(
                $user,
                $findAllClientsDTO,
            );

            return self::makeHttpResponse(
                'CLIENT.FIND_ALL.SUCCESS',
                HttpStatusCode::OK,
                $clients,
            );
        };

        return $this->makeRequest('CLIENT.FIND_ALL', $findAllResponse);
    }

    public function findById(int $clientId): JsonResponse {
        $findByIdResponse = function () use ($clientId) {
            $user = Auth::user();

            $client = $this->clientService->findById(
                $user,
                $clientId,
            );

            return self::makeHttpResponse(
                'CLIENT.FIND_BY_ID.SUCCESS',
                HttpStatusCode::OK,
                $client,
            );
        };

        return $this->makeRequest('CLIENT.FIND_BY_ID', $findByIdResponse);
    }

    public function update(UpdateClientRequest $request, int $clientId): JsonResponse {
        $updateResponse = function () use ($request, $clientId) {
            $user = Auth::user();
            $clientDTO = UpdateClientDTO::fromArray(
                $request->validated(),
            );

            $client = $this->clientService->update(
                $user,
                $clientId,
                $clientDTO,
            );

            return self::makeHttpResponse(
                'CLIENT.UPDATED',
                HttpStatusCode::OK,
                $client,
            );
        };

        return $this->makeRequest('CLIENT.UPDATE', $updateResponse);
    }

    public function delete(int $clientId): JsonResponse {
        $deleteResponse = function () use ($clientId) {
            $user = Auth::user();

            $clientRemoved = $this->clientService->delete(
                $user,
                $clientId,
            );

            return self::makeHttpResponse(
                'CLIENT.DELETED',
                HttpStatusCode::OK,
                $clientRemoved,
            );
        };

        return $this->makeRequest('CLIENT.DELETE', $deleteResponse);
    }
}
