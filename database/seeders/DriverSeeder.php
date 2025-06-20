<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Driver;

class DriverSeeder extends Seeder
{
    public function run(): void
    {
        $drivers = [
            // Jakarta drivers
            [
                'name' => 'Budi Santoso',
                'phone' => '081234567890',
                'license_number' => 'SIM-JKT-001',
                'region_id' => 1
            ],
            [
                'name' => 'Ahmad Yani',
                'phone' => '081234567891',
                'license_number' => 'SIM-JKT-002',
                'region_id' => 1
            ],
            // Surabaya drivers
            [
                'name' => 'Joko Widodo',
                'phone' => '081234567892',
                'license_number' => 'SIM-SBY-001',
                'region_id' => 2
            ],
            // Mine site drivers
            [
                'name' => 'Siti Nurhaliza',
                'phone' => '081234567893',
                'license_number' => 'SIM-SUL-001',
                'region_id' => 3
            ],
            [
                'name' => 'Andi Wijaya',
                'phone' => '081234567894',
                'license_number' => 'SIM-SUL-002',
                'region_id' => 3
            ],
            [
                'name' => 'Rudi Hartono',
                'phone' => '081234567895',
                'license_number' => 'SIM-KAL-001',
                'region_id' => 5
            ],
            [
                'name' => 'Dewi Sartika',
                'phone' => '081234567896',
                'license_number' => 'SIM-KAL-002',
                'region_id' => 6
            ],
            [
                'name' => 'Bambang Pamungkas',
                'phone' => '081234567897',
                'license_number' => 'SIM-MLK-001',
                'region_id' => 7
            ],
        ];

        foreach ($drivers as $driver) {
            Driver::create($driver);
        }
    }
}