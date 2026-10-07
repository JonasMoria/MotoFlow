<?php

namespace Tests\Unit\Services\Client;

use App\DTOs\Client\CreateClientDTO;
use App\DTOs\Client\FindAllClientDTO;
use App\DTOs\Client\UpdateClientDTO;
use App\Enums\HttpStatusCode;
use App\Exceptions\AppException;
use App\Mappers\Client\ClientMapper;
use App\Models\Client\ClientModel;
use App\Models\User\User;
use App\Repositories\Client\ClientRepository;
use App\Services\Client\ClientService;
use Illuminate\Pagination\LengthAwarePaginator;
use RuntimeException;
use Tests\TestCase;

class ClientServiceTest extends TestCase {
    public function testCreateClientThrowsExceptionWithInvalidUser(): void {
        $this->expectException(AppException::class);
        $this->expectExceptionCode(HttpStatusCode::UNAUTHORIZED->value);
        $this->expectExceptionMessage('USER.UNAUTHENTICATED');

        $service = new ClientService();
        $service->createClient(
            null,
            new CreateClientDTO('name', '99999999999', null, null, null, null),
        );
    }

    public function testCreateClientWhenInsertThrowsException(): void {
        $service = $this->getMockBuilder(ClientService::class)
            ->onlyMethods(['insertClient'])
            ->getMock();

        $user = new User();
        $clientDTO =  new CreateClientDTO('name', '99999999999', null, null, null, null);

        $service
            ->expects($this->once())
            ->method('insertClient')
            ->with($user, $clientDTO)
            ->willThrowException(new RuntimeException('TEST'));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('TEST');

        $service->createClient($user, $clientDTO);
    }

    public function testCreateClient(): void {
        $user = new User();
        $clientDTO =  new CreateClientDTO(
            'name',
            '99999999999',
            'email@email.com',
            '11122233344455',
            'Address Test',
            null,
        );

        $service = $this->getMockBuilder(ClientService::class)
            ->onlyMethods(['insertClient'])
            ->getMock();
        $service
            ->expects($this->once())
            ->method('insertClient')
            ->with($user, $clientDTO)
            ->willReturn([
                1, '/path/image',
            ]);

        $actual = $service->createClient($user, $clientDTO);
        $expected = [
            'id' => 1,
            'avatar_path' => '/path/image',
        ];

        $this->assertEquals($expected, $actual, 'Fluxo de criação do cliente funciona como esperado');
    }

    public function testFindAll(): void {
        $user = new User();
        $user->id = 123;

        $clientDTO =  new FindAllClientDTO(1, 10);

        $response = new LengthAwarePaginator(
            collect([
                new ClientModel([
                    'id' => 1,
                    'user_id' => 1,
                    'name' => 'João da Silva',
                    'phone' => '5519987654321',
                    'email' => 'joao@email.com',
                    'document' => '12345678900',
                    'address' => 'Rua das Flores, 100',
                    'avatar_path' => null,
                ]),
                new ClientModel([
                    'id' => 3,
                    'user_id' => 1,
                    'name' => 'Paulo Sergio',
                    'phone' => '5519987654321',
                    'email' => 'paulo@email.com',
                    'document' => '12345678900',
                    'address' => 'Rua das sementes, 100',
                    'avatar_path' => null,
                ]),
            ]),
            2,
            15,
            1,
        );

        $clientRepository = $this->createMock(ClientRepository::class);
        $clientRepository
            ->expects($this->once())
            ->method('findAll')
            ->with($user->id, $clientDTO)
            ->willReturn($response);

        $service = new ClientService($clientRepository);

        $actual = $service->findAll($user, $clientDTO);
        $expected = ClientMapper::toPaginatedArray($response);

        $this->assertEquals($expected, $actual, 'Lista de clientes é retornado na estrutra esperada');
    }

    public function testFindById(): void {
        $user = new User();
        $user->id = 123;

        $clientId = 1;

        $client = new ClientModel([
            'id' => 1,
            'user_id' => 1,
            'name' => 'João da Silva',
            'phone' => '5511998765432',
            'email' => 'joao.silva@email.com',
            'document' => '12345678900',
            'address' => 'Rua das Flores, 100',
            'avatar_path' => 'avatars/clients/joao-da-silva.jpg',
            'created_at' => '2026-10-06 20:00:00',
            'updated_at' => '2026-10-06 20:30:00',
        ]);

        $clientRepository = $this->createMock(ClientRepository::class);
        $clientRepository
            ->expects($this->once())
            ->method('findById')
            ->with($user->id, $clientId)
            ->willReturn($client);

        $actual = (new ClientService($clientRepository))->findById($user, $clientId);
        $expected = ClientMapper::toArray($client);

        $this->assertEquals($expected, $actual, 'Busca de cliente por id funciona como esperado');
    }

    public function testFindByIdWhenNotFoundUser(): void {
        $user = new User();
        $user->id = 123;

        $clientId = 1;

        $clientRepository = $this->createMock(ClientRepository::class);
        $clientRepository
            ->expects($this->once())
            ->method('findById')
            ->with($user->id, $clientId)
            ->willReturn(null);

        $this->expectException(AppException::class);
        $this->expectExceptionCode(HttpStatusCode::NOT_FOUND->value);

        (new ClientService($clientRepository))->findById($user, $clientId);
    }

    public function testUpdate(): void {
        $user = new User();
        $user->id = 1;

        $clientId = 1;

        $clientDTO = new UpdateClientDTO('name', '99999999999', null, null, null, null);
        $clientData = ClientMapper::toUpdateArray($clientDTO);

        $client = new ClientModel();
        $client->id = 123;

        $clientRepository = $this->createMock(ClientRepository::class);
        $clientRepository
            ->expects($this->once())
            ->method('findById')
            ->with($user->id, $clientId)
            ->willReturn($client);
        $clientRepository
            ->expects($this->once())
            ->method('update')
            ->with($client, $clientData)
            ->willReturn($client);

        $actual = (new ClientService($clientRepository))->update($user, $clientId, $clientDTO);
        $expected = [
            'id' => $client->id,
        ];

        $this->assertEquals($expected, $actual, 'Update de cliente funciona como esperado');
    }

    public function testUpdateWithInvalidUser(): void {
        $user = null;
        $clientId = 1;
        $clientDTO = new UpdateClientDTO('name', '99999999999', null, null, null, null);

        $this->expectException(AppException::class);
        $this->expectExceptionCode(HttpStatusCode::UNAUTHORIZED->value);

        (new ClientService())->update($user, $clientId, $clientDTO);
    }

    public function testUpdateWithInvalidClient(): void {
        $user = new User();
        $user->id = 1;

        $clientId = 1;
        $clientDTO = new UpdateClientDTO('name', '99999999999', null, null, null, null);

        $this->expectException(AppException::class);
        $this->expectExceptionCode(HttpStatusCode::NOT_FOUND->value);

        $clientRepository = $this->createMock(ClientRepository::class);
        $clientRepository
            ->expects($this->once())
            ->method('findById')
            ->with($user->id, $clientId)
            ->willReturn(null);

        (new ClientService($clientRepository))->update($user, $clientId, $clientDTO);
    }

    public function testDelete(): void {
        $user = new User();
        $user->id = 1;
        $clientId = 1;

        $client = new ClientModel();
        $client->id = 123;

        $clientRepository = $this->createMock(ClientRepository::class);
        $clientRepository
            ->expects($this->once())
            ->method('findById')
            ->with($user->id, $clientId)
            ->willReturn($client);
        $clientRepository
            ->expects($this->once())
            ->method('delete')
            ->with($client)
            ->willReturn(true);

        $actual = (new ClientService($clientRepository))->delete($user, $clientId);
        $expected = [
            'removed' => true,
        ];

        $this->assertEquals($expected, $actual, 'Remoção de cliente por id funciona como esperado');
    }

    public function testDeletWithInvalidUser(): void {
        $user = null;
        $clientId = 1;

        $this->expectException(AppException::class);
        $this->expectExceptionCode(HttpStatusCode::UNAUTHORIZED->value);

        (new ClientService())->delete($user, $clientId);
    }

    public function testDeleteWithInvalidClient(): void {
        $user = new User();
        $user->id = 1;
        $clientId = 1;

        $this->expectException(AppException::class);
        $this->expectExceptionCode(HttpStatusCode::NOT_FOUND->value);

        $clientRepository = $this->createMock(ClientRepository::class);
        $clientRepository
            ->expects($this->once())
            ->method('findById')
            ->with($user->id, $clientId)
            ->willReturn(null);

        (new ClientService($clientRepository))->delete($user, $clientId);
    }
}
