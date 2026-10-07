<?php

namespace Tests\Unit\Mappers\Client;

use App\DTOs\Client\UpdateClientDTO;
use App\Mappers\Client\ClientMapper;
use App\Models\Client\ClientModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class ClientMapperTest extends TestCase {
    public function testToArray(): void {
        $client = new ClientModel([
            'user_id' => 1,
            'name' => 'João da Silva',
            'phone' => '5511998765432',
            'email' => 'joao.silva@email.com',
            'document' => '12345678900',
            'address' => 'Rua das Flores, 100',
            'avatar_path' => 'avatars/clients/joao-da-silva.jpg',
        ]);

        $client->id = 1;
        $client->created_at = '2026-10-06 20:00:00';
        $client->updated_at = '2026-10-06 20:30:00';

        $actual = ClientMapper::toArray($client);

        $expected = [
            'id' => 1,
            'name' => 'João da Silva',
            'phone' => '5511998765432',
            'email' => 'joao.silva@email.com',
            'document' => '12345678900',
            'address' => 'Rua das Flores, 100',
            'avatar_path' => 'avatars/clients/joao-da-silva.jpg',
            'created_at' => $client->created_at?->toISOString(),
            'updated_at' => $client->updated_at?->toISOString(),
        ];

        $this->assertSame(
            $expected,
            $actual,
            'Os dados do cliente devem ser convertidos corretamente para um array.',
        );
    }

    public function testToPaginatedArray(): void {
        $clients = collect([
            new ClientModel([
                'user_id' => 1,
                'name' => 'João da Silva',
                'phone' => '5511998765432',
                'email' => 'joao.silva@email.com',
                'document' => '12345678900',
                'address' => 'Rua das Flores, 100',
                'avatar_path' => null,
                'created_at' => '2026-10-06 20:00:00',
                'updated_at' => '2026-10-06 20:30:00',
            ]),
            new ClientModel([
                'user_id' => 1,
                'name' => 'Maria Oliveira',
                'phone' => '5511987654321',
                'email' => 'maria.oliveira@email.com',
                'document' => '98765432100',
                'address' => 'Av. Brasil, 500',
                'avatar_path' => null,
                'created_at' => '2026-10-06 19:00:00',
                'updated_at' => '2026-10-06 19:30:00',
            ]),
        ]);

        $clients[0]->id = 1;
        $clients[1]->id = 2;

        $paginator = new LengthAwarePaginator(
            $clients,
            2,
            15,
            1,
        );

        $actual = ClientMapper::toPaginatedArray($paginator);

        $this->assertCount(
            2,
            $actual['data'],
            'A paginação deve retornar a quantidade correta de clientes.',
        );

        $this->assertSame(
            1,
            $actual['data'][0]['id'],
            'O primeiro cliente deve possuir o ID esperado.',
        );

        $this->assertSame(
            'João da Silva',
            $actual['data'][0]['name'],
            'O primeiro cliente deve possuir o nome esperado.',
        );

        $this->assertSame(
            2,
            $actual['data'][1]['id'],
            'O segundo cliente deve possuir o ID esperado.',
        );

        $this->assertSame(
            'Maria Oliveira',
            $actual['data'][1]['name'],
            'O segundo cliente deve possuir o nome esperado.',
        );

        $this->assertSame(
            [
                'current_page' => 1,
                'per_page' => 15,
                'total' => 2,
                'last_page' => 1,
                'from' => 1,
                'to' => 2,
            ],
            $actual['pagination'],
            'Os dados de paginação devem ser retornados corretamente.',
        );
    }

    public function testToUpdateArrayShouldConvertAllDtoFields(): void {
        $clientDTO = new UpdateClientDTO(
            'João da Silva Atualizado',
            '5511987654321',
            'joao.atualizado@email.com',
            '12345678900',
            'Rua Nova, 200',
            null,
        );

        $actual = ClientMapper::toUpdateArray($clientDTO);

        $expected = [
            'name' => 'João da Silva Atualizado',
            'phone' => '5511987654321',
            'email' => 'joao.atualizado@email.com',
            'document' => '12345678900',
            'address' => 'Rua Nova, 200',
        ];

        $this->assertSame(
            $expected,
            $actual,
            'Todos os campos preenchidos do DTO devem ser convertidos corretamente para atualização.',
        );
    }

    public function testToUpdateArrayShouldIgnoreNullFields(): void {
        $clientDTO = new UpdateClientDTO(
            'João da Silva Atualizado',
            null,
            null,
            null,
            'Rua Nova, 200',
            null,
        );

        $actual = ClientMapper::toUpdateArray($clientDTO);

        $expected = [
            'name' => 'João da Silva Atualizado',
            'address' => 'Rua Nova, 200',
        ];

        $this->assertSame(
            $expected,
            $actual,
            'Os campos com valor nulo não devem ser incluídos nos dados de atualização.',
        );
    }

    public function testToUpdateArrayShouldReturnEmptyArrayWhenAllFieldsAreNull(): void {
        $clientDTO = new UpdateClientDTO(
            null,
            null,
            null,
            null,
            null,
            null,
        );

        $actual = ClientMapper::toUpdateArray($clientDTO);

        $expected = [];

        $this->assertSame(
            $expected,
            $actual,
            'Um DTO sem campos preenchidos deve resultar em um array vazio.',
        );
    }
}
