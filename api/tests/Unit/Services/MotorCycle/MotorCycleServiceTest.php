<?php

namespace Tests\Unit\Services\Client;

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
use App\Services\MotorCycle\MotorCycleService;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class MotorCycleServiceTest extends TestCase {
    public function testCreateClientMotorCycleWithInvalidUser(): void {
        $clientId = 1;

        $motorcycleDTO = new CreateClientMotorCycleDTO(
            'Honda',
            'CB 500F',
            'ABC1D23',
            '12345678901',
            '9C2PC4230HR000001',
            2025,
            'Preta',
            'CB500E000001',
        );

        $motorcycleRepository = $this->createMock(MotorCycleRepository::class);
        $clientRepository = $this->createMock(ClientRepository::class);

        $motorcycleRepository
            ->expects($this->never())
            ->method('create');
        $clientRepository
            ->expects($this->never())
            ->method('existsByIdAndUserId');

        $service = new MotorCycleService(
            $motorcycleRepository,
            $clientRepository,
        );

        $this->expectException(AppException::class);
        $this->expectExceptionCode(HttpStatusCode::UNAUTHORIZED->value);

        $service->createClientMotorCycle(
            null,
            $clientId,
            $motorcycleDTO,
        );
    }

    public function testCreateClientMotorCycleWithInvalidClient(): void {
        $user = new User();
        $user->id = 1;

        $clientId = 10;

        $motorcycleDTO = new CreateClientMotorCycleDTO(
            'Honda',
            'CB 500F',
            'ABC1D23',
            '12345678901',
            '9C2PC4230HR000001',
            2025,
            'Preta',
            'CB500E000001',
        );

        $motorcycleRepository = $this->createMock(MotorCycleRepository::class);
        $clientRepository = $this->createMock(ClientRepository::class);

        $clientRepository
            ->expects($this->once())
            ->method('existsByIdAndUserId')
            ->with($clientId, $user->id)
            ->willReturn(false);

        $motorcycleRepository
            ->expects($this->never())
            ->method('create');

        $service = new MotorCycleService(
            $motorcycleRepository,
            $clientRepository,
        );

        $this->expectException(AppException::class);
        $this->expectExceptionCode(HttpStatusCode::NOT_FOUND->value);

        $service->createClientMotorCycle(
            $user,
            $clientId,
            $motorcycleDTO,
        );
    }

    public function testCreateClientMotorCycle(): void {
        $user = new User();
        $user->id = 1;

        $clientId = 10;

        $motorcycleDTO = new CreateClientMotorCycleDTO(
            'Honda',
            'CB 500F',
            'ABC-1D23',
            '123.456.789-01',
            '9C2PC4230HR000001',
            2025,
            'Preta',
            'CB500E000001',
        );

        $normalizedMotorcycleDTO = new CreateClientMotorCycleDTO(
            'Honda',
            'CB 500F',
            'ABC1D23',
            '12345678901',
            '9C2PC4230HR000001',
            2025,
            'Preta',
            'CB500E000001',
        );

        $motorcycle = new MotorCycleModel();
        $motorcycle->id = 123;

        $motorcycleRepository = $this->createMock(MotorCycleRepository::class);
        $clientRepository = $this->createMock(ClientRepository::class);

        $clientRepository
            ->expects($this->once())
            ->method('existsByIdAndUserId')
            ->with($clientId, $user->id)
            ->willReturn(true);
        $motorcycleRepository
            ->expects($this->once())
            ->method('create')
            ->with(
                $clientId,
                $normalizedMotorcycleDTO,
            )
            ->willReturn($motorcycle);

        $service = new MotorCycleService(
            $motorcycleRepository,
            $clientRepository,
        );

        $actual = $service->createClientMotorCycle(
            $user,
            $clientId,
            $motorcycleDTO,
        );

        $expected = [
            'id' => $motorcycle->id,
        ];

        $this->assertSame(
            $expected,
            $actual,
            'A motocicleta deve ser criada corretamente e retornar o ID gerado.',
        );
    }

    public function testFindAllWithInvalidUser(): void {
        $clientId = 10;
        $motorcycleDTO = new FindAllClientMotorCycleDTO(10, 1);

        $this->expectException(AppException::class);
        $this->expectExceptionCode(HttpStatusCode::UNAUTHORIZED->value);

        (new MotorCycleService())->findAll(null, $clientId, $motorcycleDTO);
    }

    public function testFindAll(): void {
        $user = new User();
        $user->id = 1;

        $clientId = 10;

        $motorcycleDTO = new FindAllClientMotorCycleDTO(10, 1);

        $response = new LengthAwarePaginator(
            collect([
                new MotorCycleModel([
                    'brand' => 'Honda',
                    'model' => 'CB 500F',
                    'plate' => 'ABC1D23',
                    'renavam' => '12345678901',
                    'chassis' => '9C2PC4230HR000001',
                    'year' => 2025,
                    'color' => 'Preta',
                    'engine_number' => 'CB500E000001',
                ]),
                new MotorCycleModel([
                    'brand' => 'Yamaha',
                    'model' => 'MT-07',
                    'plate' => 'XYZ9A87',
                    'renavam' => '98765432109',
                    'chassis' => '9C6RM1740K0000002',
                    'year' => 2024,
                    'color' => 'Azul',
                    'engine_number' => 'MT07E000002',
                ]),
            ]),
            2,
            10,
            1,
        );

        $motorcycleRepository = $this->createMock(MotorCycleRepository::class);
        $motorcycleRepository
            ->expects($this->once())
            ->method('findAll')
            ->with($user->id, $clientId, $motorcycleDTO)
            ->willReturn($response);

        $service = new MotorCycleService($motorcycleRepository);

        $actual = $service->findAll($user, $clientId, $motorcycleDTO);
        $expected = MotorCycleMapper::toPaginatedArray($response);

        $this->assertSame(
            $expected,
            $actual,
            'A listagem de motocicletas deve retornar os dados e a paginação corretamente.',
        );
    }

    public function testFindByIdWithInvalidUser(): void {
        $clientId = 10;
        $motorcycleId = 20;

        $this->expectException(AppException::class);
        $this->expectExceptionCode(HttpStatusCode::UNAUTHORIZED->value);

        (new MotorCycleService())->findById(null, $clientId, $motorcycleId);
    }

    public function testFindById(): void {
        $user = new User();
        $user->id = 1;

        $clientId = 10;
        $motorcycleId = 20;

        $motorcycle = new MotorCycleModel([
            'brand' => 'Honda',
            'model' => 'CB 500F',
            'plate' => 'ABC1D23',
            'renavam' => '12345678901',
            'chassis' => '9C2PC4230HR000001',
            'year' => 2025,
            'color' => 'Preta',
            'engine_number' => 'CB500E000001',
        ]);

        $motorcycle->id = $motorcycleId;

        $motorcycleRepository = $this->createMock(MotorCycleRepository::class);
        $motorcycleRepository
            ->expects($this->once())
            ->method('findById')
            ->with(
                $user->id,
                $clientId,
                $motorcycleId,
            )
            ->willReturn($motorcycle);

        $service = new MotorCycleService($motorcycleRepository);

        $actual = $service->findById(
            $user,
            $clientId,
            $motorcycleId,
        );

        $expected = [
            'id' => $motorcycleId,
            'brand' => 'Honda',
            'model' => 'CB 500F',
            'plate' => 'ABC1D23',
            'renavam' => '12345678901',
            'chassis' => '9C2PC4230HR000001',
            'year' => 2025,
            'color' => 'Preta',
            'engine_number' => 'CB500E000001',
            'created_at' => null,
            'updated_at' => null,
        ];

        $this->assertSame(
            $expected,
            $actual,
            'A motocicleta deve ser encontrada e seus dados devem ser retornados corretamente.',
        );
    }

    public function testFindByIdWithInvalidMotorcycle(): void {
        $user = new User();
        $user->id = 1;

        $clientId = 10;
        $motorcycleId = 20;

        $motorcycleRepository = $this->createMock(MotorCycleRepository::class);
        $motorcycleRepository
            ->expects($this->once())
            ->method('findById')
            ->with(
                $user->id,
                $clientId,
                $motorcycleId,
            )
            ->willReturn(null);

        $service = new MotorCycleService($motorcycleRepository);

        $this->expectException(AppException::class);
        $this->expectExceptionCode(HttpStatusCode::NOT_FOUND->value);

        $service->findById($user, $clientId, $motorcycleId);
    }

    public function testUpdateWithInvalidUser(): void {
        $motorcycleDTO = new UpdateClientMotorCycleDTO(
            'Honda',
            'CB 500F',
            'ABC1D23',
            '12345678901',
            '9C2PC4230HR000001',
            2025,
            'Preta',
            'CB500E000001',
        );

        $clientId = 10;
        $motorcycleId = 20;

        $this->expectException(AppException::class);
        $this->expectExceptionCode(HttpStatusCode::UNAUTHORIZED->value);

        (new MotorCycleService())->update(
            null,
            $motorcycleDTO,
            $clientId,
            $motorcycleId,
        );
    }

    public function testUpdateWithInvalidMotorcycle(): void {
        $user = new User();
        $user->id = 1;

        $motorcycleDTO = new UpdateClientMotorCycleDTO(
            'Honda',
            'CB 500F',
            'ABC1D23',
            '12345678901',
            '9C2PC4230HR000001',
            2025,
            'Preta',
            'CB500E000001',
        );

        $clientId = 10;
        $motorcycleId = 20;

        $motorcycleRepository = $this->createMock(MotorCycleRepository::class);
        $motorcycleRepository
            ->expects($this->once())
            ->method('findById')
            ->with(
                $user->id,
                $clientId,
                $motorcycleId,
            )
            ->willReturn(null);

        $service = new MotorCycleService($motorcycleRepository);

        $this->expectException(AppException::class);
        $this->expectExceptionCode(HttpStatusCode::NOT_FOUND->value);

        $service->update(
            $user,
            $motorcycleDTO,
            $clientId,
            $motorcycleId,
        );
    }

    public function testUpdate(): void {
        $user = new User();
        $user->id = 1;

        $clientId = 10;
        $motorcycleId = 20;

        $motorcycleDTO = new UpdateClientMotorCycleDTO(
            'Honda',
            'CB 500F',
            'ABC1D23',
            '12345678901',
            '9C2PC4230HR000001',
            2025,
            'Preta',
            'CB500E000001',
        );

        $motorcycle = new MotorCycleModel([
            'brand' => 'Yamaha',
            'model' => 'MT-07',
            'plate' => 'XYZ9A87',
            'renavam' => '98765432109',
            'chassis' => '9C6RM1740K0000002',
            'year' => 2024,
            'color' => 'Azul',
            'engine_number' => 'MT07E000002',
        ]);

        $motorcycle->id = $motorcycleId;

        $motorcycleUpdated = new MotorCycleModel([
            'brand' => 'Honda',
            'model' => 'CB 500F',
            'plate' => 'ABC1D23',
            'renavam' => '12345678901',
            'chassis' => '9C2PC4230HR000001',
            'year' => 2025,
            'color' => 'Preta',
            'engine_number' => 'CB500E000001',
        ]);

        $motorcycleUpdated->id = $motorcycleId;

        $data = [
            'brand' => 'Honda',
            'model' => 'CB 500F',
            'plate' => 'ABC1D23',
            'renavam' => '12345678901',
            'chassis' => '9C2PC4230HR000001',
            'year' => 2025,
            'color' => 'Preta',
            'engine_number' => 'CB500E000001',
        ];

        $motorcycleRepository = $this->createMock(MotorCycleRepository::class);

        $motorcycleRepository
            ->expects($this->once())
            ->method('findById')
            ->with(
                $user->id,
                $clientId,
                $motorcycleId,
            )
            ->willReturn($motorcycle);
        $motorcycleRepository
            ->expects($this->once())
            ->method('update')
            ->with(
                $motorcycle,
                $data,
            )
            ->willReturn($motorcycleUpdated);

        $service = new MotorCycleService($motorcycleRepository);

        $actual = $service->update(
            $user,
            $motorcycleDTO,
            $clientId,
            $motorcycleId,
        );

        $expected = [
            'id' => $motorcycleId,
        ];

        $this->assertSame(
            $expected,
            $actual,
            'A motocicleta deve ser atualizada corretamente e retornar o ID da motocicleta.',
        );
    }

    public function testDeleteWithInvalidUser(): void {
        $clientId = 10;
        $motorcycleId = 20;

        $this->expectException(AppException::class);
        $this->expectExceptionCode(HttpStatusCode::UNAUTHORIZED->value);

        (new MotorCycleService())->delete(
            null,
            $clientId,
            $motorcycleId,
        );
    }

    public function testDeleteWithInvalidMotorcycle(): void {
        $user = new User();
        $user->id = 1;

        $clientId = 10;
        $motorcycleId = 20;

        $motorcycleRepository = $this->createMock(MotorCycleRepository::class);
        $motorcycleRepository
            ->expects($this->once())
            ->method('findById')
            ->with(
                $user->id,
                $clientId,
                $motorcycleId,
            )
            ->willReturn(null);

        $service = new MotorCycleService($motorcycleRepository);

        $this->expectException(AppException::class);
        $this->expectExceptionCode(HttpStatusCode::NOT_FOUND->value);

        $service->delete(
            $user,
            $clientId,
            $motorcycleId,
        );
    }

    public function testDelete(): void {
        $user = new User();
        $user->id = 1;

        $clientId = 10;
        $motorcycleId = 20;

        $motorcycle = new MotorCycleModel([
            'brand' => 'Honda',
            'model' => 'CB 500F',
            'plate' => 'ABC1D23',
            'renavam' => '12345678901',
            'chassis' => '9C2PC4230HR000001',
            'year' => 2025,
            'color' => 'Preta',
            'engine_number' => 'CB500E000001',
        ]);

        $motorcycle->id = $motorcycleId;

        $motorcycleRepository = $this->createMock(MotorCycleRepository::class);
        $motorcycleRepository
            ->expects($this->once())
            ->method('findById')
            ->with(
                $user->id,
                $clientId,
                $motorcycleId,
            )
            ->willReturn($motorcycle);
        $motorcycleRepository
            ->expects($this->once())
            ->method('delete')
            ->with($motorcycle)
            ->willReturn(true);

        $service = new MotorCycleService($motorcycleRepository);

        $actual = $service->delete(
            $user,
            $clientId,
            $motorcycleId,
        );

        $expected = [
            'removed' => true,
        ];

        $this->assertSame(
            $expected,
            $actual,
            'A motocicleta deve ser removida corretamente e retornar o resultado da exclusão.',
        );
    }
}
