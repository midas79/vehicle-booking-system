<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VehicleUsage;
use App\Models\Vehicle;
use App\Models\Booking;

class VehicleUsageSeeder extends Seeder
{
    public function run()
    {
        $vehicles = Vehicle::all();

        foreach ($vehicles as $vehicle) {
            // Add some trip records
            for ($i = 1; $i <= 5; $i++) {
                $startKm = ($i - 1) * 500 + 10000;
                $endKm = $startKm + rand(100, 500);
                $distance = $endKm - $startKm;

                VehicleUsage::create([
                    'vehicle_id' => $vehicle->id,
                    'usage_type' => 'trip',
                    'start_km' => $startKm,
                    'end_km' => $endKm,
                    'fuel_used' => round($distance / $vehicle->fuel_consumption, 2),
                    'created_at' => now()->subDays(rand(1, 30))
                ]);
            }

            // Add service record
            VehicleUsage::create([
                'vehicle_id' => $vehicle->id,
                'usage_type' => 'service',
                'service_type' => 'routine',
                'service_date' => now()->subDays(rand(30, 60)),
                'start_km' => 12000,
                'end_km' => 12000,
                'service_cost' => rand(500000, 2000000),
                'service_vendor' => 'Auto Service Center',
                'next_service_date' => now()->addDays(rand(30, 90)),
                'next_service_km' => 17000,
                'notes' => 'Routine maintenance'
            ]);
        }
    }
}