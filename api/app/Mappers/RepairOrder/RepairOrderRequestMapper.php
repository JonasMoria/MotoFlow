<?php

namespace App\Mappers\RepairOrder;

use App\DTOs\RepairOrder\CreateRepairOrderDTO;
use App\DTOs\RepairOrder\CreateRepairOrderPartDTO;
use App\DTOs\RepairOrder\CreateRepairOrderServiceDTO;

class RepairOrderRequestMapper {
    public static function toCreateDTO(array $data): CreateRepairOrderDTO {
        return CreateRepairOrderDTO::fromArray($data);
    }

    /**
     * @return CreateRepairOrderPartDTO[]
     */
    public static function toPartsDTOList(array $parts): array {
        $result = [];

        foreach ($parts as $part) {
            $result[] = CreateRepairOrderPartDTO::fromArray($part);
        }

        return $result;
    }

    /**
     * @return CreateRepairOrderServiceDTO[]
     */
    public static function toServicesDTOList(array $services): array {
        $result = [];

        foreach ($services as $service) {
            $result[] = CreateRepairOrderServiceDTO::fromArray($service);
        }

        return $result;
    }
}
