<?php

namespace App\Mappers\MotorCycle;

use App\DTOs\MotorCycle\UpdateClientMotorCycleDTO;
use App\Models\MotorCycle\MotorCycleModel;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

final class MotorCycleMapper {
    public static function toArray(MotorCycleModel $motorcycle): array {
        return [
            'id' => $motorcycle->id,
            'brand' => $motorcycle->brand,
            'model' => $motorcycle->model,
            'plate' => $motorcycle->plate,
            'renavam' => $motorcycle->renavam,
            'chassis' => $motorcycle->chassis,
            'year' => $motorcycle->year,
            'color' => $motorcycle->color,
            'engine_number' => $motorcycle->engine_number,
            'created_at' => $motorcycle->created_at?->toISOString(),
            'updated_at' => $motorcycle->updated_at?->toISOString(),
        ];
    }

    public static function toPaginatedArray(LengthAwarePaginator $paginator): array {
        return [
            'data' => collect($paginator->items())
                ->map(
                    fn (MotorCycleModel $motorcycle) =>
                        self::toArray($motorcycle),
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

    public static function toUpdateArray(UpdateClientMotorCycleDTO $motorcycleDTO): array {
        $fields = [
            'brand' => 'brand',
            'model' => 'model',
            'plate' => 'plate',
            'renavam' => 'renavam',
            'chassis' => 'chassis',
            'year' => 'year',
            'color' => 'color',
            'engineNumber' => 'engine_number',
        ];

        $data = [];

        foreach ($fields as $dtoField => $databaseField) {
            $value = $motorcycleDTO->{$dtoField};

            if ($value !== null) {
                $data[$databaseField] = $value;
            }
        }

        return $data;
    }
}
