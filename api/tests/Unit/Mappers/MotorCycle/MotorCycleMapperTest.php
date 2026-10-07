<?php

namespace Tests\Unit\Mappers\MotorCycle;

use App\DTOs\MotorCycle\UpdateClientMotorCycleDTO;
use App\Mappers\MotorCycle\MotorCycleMapper;
use App\Models\MotorCycle\MotorCycleModel;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class MotorCycleMapperTest extends TestCase {
    public function testToArray(): void {
        $motorcycle = new MotorCycleModel([
            'client_id' => 1,
            'brand' => 'Honda',
            'model' => 'CB 500F',
            'plate' => 'ABC1D23',
            'renavam' => '12345678901',
            'chassis' => '9C2PC4230HR000001',
            'year' => 2025,
            'color' => 'Preta',
            'engine_number' => 'CB500E000001',
        ]);

        $motorcycle->id = 1;
        $motorcycle->created_at = '2026-10-06 20:00:00';
        $motorcycle->updated_at = '2026-10-06 20:30:00';

        $actual = MotorCycleMapper::toArray($motorcycle);

        $expected = [
            'id' => 1,
            'brand' => 'Honda',
            'model' => 'CB 500F',
            'plate' => 'ABC1D23',
            'renavam' => '12345678901',
            'chassis' => '9C2PC4230HR000001',
            'year' => 2025,
            'color' => 'Preta',
            'engine_number' => 'CB500E000001',
            'created_at' => $motorcycle->created_at?->toISOString(),
            'updated_at' => $motorcycle->updated_at?->toISOString(),
        ];

        $this->assertSame(
            $expected,
            $actual,
            'Os dados da motocicleta devem ser convertidos corretamente para um array.',
        );
    }

    public function testToPaginatedArray(): void {
        $motorcycles = collect([
            new MotorCycleModel([
                'id' => 1,
                'client_id' => 1,
                'brand' => 'Honda',
                'model' => 'CB 500F',
                'plate' => 'ABC1D23',
                'renavam' => '12345678901',
                'chassis' => '9C2PC4230HR000001',
                'year' => 2025,
                'color' => 'Preta',
                'engine_number' => 'CB500E000001',
                'created_at' => '2026-10-06 20:00:00',
                'updated_at' => '2026-10-06 20:30:00',
            ]),
            new MotorCycleModel([
                'id' => 2,
                'client_id' => 1,
                'brand' => 'Yamaha',
                'model' => 'MT-07',
                'plate' => 'XYZ4E56',
                'renavam' => '98765432100',
                'chassis' => '9C6RM1230HR000002',
                'year' => 2024,
                'color' => 'Azul',
                'engine_number' => 'MT07E000002',
                'created_at' => '2026-10-06 19:00:00',
                'updated_at' => '2026-10-06 19:30:00',
            ]),
        ]);

        $motorcycles[0]->id = 1;
        $motorcycles[1]->id = 2;

        $paginator = new LengthAwarePaginator(
            $motorcycles,
            2,
            15,
            1,
        );

        $actual = MotorCycleMapper::toPaginatedArray($paginator);

        $this->assertCount(
            2,
            $actual['data'],
            'A paginação deve retornar a quantidade correta de motocicletas.',
        );

        $this->assertSame(
            1,
            $actual['data'][0]['id'],
            'A primeira motocicleta deve possuir o ID esperado.',
        );

        $this->assertSame(
            'Honda',
            $actual['data'][0]['brand'],
            'A primeira motocicleta deve possuir a marca esperada.',
        );

        $this->assertSame(
            'CB 500F',
            $actual['data'][0]['model'],
            'A primeira motocicleta deve possuir o modelo esperado.',
        );

        $this->assertSame(
            2,
            $actual['data'][1]['id'],
            'A segunda motocicleta deve possuir o ID esperado.',
        );

        $this->assertSame(
            'Yamaha',
            $actual['data'][1]['brand'],
            'A segunda motocicleta deve possuir a marca esperada.',
        );

        $this->assertSame(
            'MT-07',
            $actual['data'][1]['model'],
            'A segunda motocicleta deve possuir o modelo esperado.',
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

        $actual = MotorCycleMapper::toUpdateArray($motorcycleDTO);

        $expected = [
            'brand' => 'Honda',
            'model' => 'CB 500F',
            'plate' => 'ABC1D23',
            'renavam' => '12345678901',
            'chassis' => '9C2PC4230HR000001',
            'year' => 2025,
            'color' => 'Preta',
            'engine_number' => 'CB500E000001',
        ];

        $this->assertSame(
            $expected,
            $actual,
            'Todos os campos preenchidos do DTO devem ser convertidos corretamente para atualização.',
        );
    }

    public function testToUpdateArrayShouldIgnoreNullFields(): void {
        $motorcycleDTO = new UpdateClientMotorCycleDTO(
            'Honda',
            null,
            null,
            null,
            null,
            2025,
            'Preta',
            null,
        );

        $actual = MotorCycleMapper::toUpdateArray($motorcycleDTO);

        $expected = [
            'brand' => 'Honda',
            'year' => 2025,
            'color' => 'Preta',
        ];

        $this->assertSame(
            $expected,
            $actual,
            'Os campos com valor nulo não devem ser incluídos nos dados de atualização.',
        );
    }

    public function testToUpdateArrayShouldReturnEmptyArrayWhenAllFieldsAreNull(): void {
        $motorcycleDTO = new UpdateClientMotorCycleDTO(
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
        );

        $actual = MotorCycleMapper::toUpdateArray($motorcycleDTO);

        $expected = [];

        $this->assertSame(
            $expected,
            $actual,
            'Um DTO sem campos preenchidos deve resultar em um array vazio.',
        );
    }
}
