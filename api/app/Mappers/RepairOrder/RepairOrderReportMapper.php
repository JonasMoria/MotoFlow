<?php

namespace App\Mappers\RepairOrder;

class RepairOrderReportMapper {
    public static function toArray(
        array $repairOrder,
        array $parts,
        array $services,
    ): array {
        return [
            'id' => $repairOrder['id'],
            'status' => $repairOrder['status'],
            'urgency_level' => $repairOrder['urgency_level'],
            'title' => $repairOrder['title'],
            'description' => $repairOrder['description'],
            'diagnosis' => $repairOrder['diagnosis'],
            'observations' => $repairOrder['observations'],
            'started_at' => $repairOrder['started_at'],
            'finished_at' => $repairOrder['finished_at'],
            'delivered_at' => $repairOrder['delivered_at'],
            'created_at' => $repairOrder['created_at'],
            'updated_at' => $repairOrder['updated_at'],

            'client' => [
                'id' => $repairOrder['client_id'],
                'name' => $repairOrder['client_name'],
                'phone' => $repairOrder['client_phone'],
                'email' => $repairOrder['client_email'],
                'document' => $repairOrder['client_document'],
                'address' => $repairOrder['client_address'],
                'profile_photo' => $repairOrder['profile_photo'],
            ],

            'motorcycle' => [
                'id' => $repairOrder['motorcycle_id'],
                'brand' => $repairOrder['motorcycle_brand'],
                'model' => $repairOrder['motorcycle_model'],
                'plate' => $repairOrder['motorcycle_plate'],
                'renavam' => $repairOrder['motorcycle_renavam'],
                'chassis' => $repairOrder['motorcycle_chassis'],
                'year' => $repairOrder['motorcycle_year'],
                'color' => $repairOrder['motorcycle_color'],
                'engine_number' => $repairOrder['motorcycle_engine_number'],
            ],

            'parts' => array_map(
                static fn (object $part): array => (array) $part,
                $parts,
            ),

            'services' => array_map(
                static fn (object $service): array => (array) $service,
                $services,
            ),
        ];
    }
}
