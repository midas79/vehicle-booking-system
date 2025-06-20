<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            // Jakarta vehicles
            [
                'name' => 'Toyota Innova Reborn',
                'license_plate' => 'B 1234 ABC',
                'type' => 'passenger',
                'ownership' => 'owned',
                'fuel_consumption' => 12.5,
                'region_id' => 1,
                'last_service_date' => now()->subDays(15)
            ],
            [
                'name' => 'Mitsubishi Pajero Sport',
                'license_plate' => 'B 5678 DEF',
                'type' => 'passenger',
                'ownership' => 'owned',
                'fuel_consumption' => 10.8,
                'region_id' => 1,
                'last_service_date' => now()->subDays(25)
            ],
            // Surabaya vehicles
            [
                'name' => 'Isuzu Elf NMR 71',
                'license_plate' => 'L 9012 GHI',
                'type' => 'cargo',
                'ownership' => 'owned',
                'fuel_consumption' => 8.5,
                'region_id' => 2,
                'last_service_date' => now()->subDays(10)
            ],
            // Mine site vehicles
            [
                'name' => 'Hino Ranger 500',
                'license_plate' => 'DT 3456 JKL',
                'type' => 'cargo',
                'ownership' => 'rented',
                'fuel_consumption' => 6.5,
                'region_id' => 3,
                'last_service_date' => now()->subDays(5)
            ],
            [
                'name' => 'Toyota Hilux D-Cab',
                'license_plate' => 'DT 7890 MNO',
                'type' => 'passenger',
                'ownership' => 'owned',
                'fuel_consumption' => 11.2,
                'region_id' => 3,
                'last_service_date' => now()->subDays(20)
            ],
            [
                'name' => 'Mitsubishi Fuso Fighter',
                'license_plate' => 'DA 2345 PQR',
                'type' => 'cargo',
                'ownership' => 'rented',
                'fuel_consumption' => 7.8,
                'region_id' => 4,
                'last_service_date' => now()->subDays(35)
            ],
            [
                'name' => 'Toyota Fortuner VRZ',
                'license_plate' => 'DD 6789 STU',
                'type' => 'passenger',
                'ownership' => 'owned',
                'fuel_consumption' => 10.5,
                'region_id' => 5,
                'last_service_date' => now()->subDays(8)
            ],
            [
                'name' => 'Isuzu Giga FVR 34',
                'license_plate' => 'DE 1234 VWX',
                'type' => 'cargo',
                'ownership' => 'owned',
                'fuel_consumption' => 6.2,
                'region_id' => 6,
                'last_service_date' => now()->subDays(12)
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }
    }
}