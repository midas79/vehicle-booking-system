<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            ['name' => 'Kantor Pusat Jakarta', 'type' => 'head_office'],
            ['name' => 'Kantor Cabang Surabaya', 'type' => 'branch'],
            ['name' => 'Tambang Sulawesi Utara', 'type' => 'mine'],
            ['name' => 'Tambang Sulawesi Tengah', 'type' => 'mine'],
            ['name' => 'Tambang Kalimantan Timur', 'type' => 'mine'],
            ['name' => 'Tambang Kalimantan Selatan', 'type' => 'mine'],
            ['name' => 'Tambang Maluku Utara', 'type' => 'mine'],
            ['name' => 'Tambang Papua Barat', 'type' => 'mine'],
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }
    }
}