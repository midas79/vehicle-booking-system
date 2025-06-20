<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Approval;
use App\Models\VehicleUsage;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample bookings
        $booking1 = Booking::create([
            'booking_number' => 'BK' . date('Ymd') . '0001',
            'user_id' => 1,
            'vehicle_id' => 1,
            'driver_id' => 1,
            'purpose' => 'Meeting dengan client di Bandung',
            'destination' => 'Bandung',
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(2)->addHours(8),
            'status' => 'approved'
        ]);

        // Create approvals
        Approval::create([
            'booking_id' => $booking1->id,
            'approver_id' => 3,
            'level' => 1,
            'status' => 'approved',
            'approved_at' => now()->subHours(2)
        ]);

        Approval::create([
            'booking_id' => $booking1->id,
            'approver_id' => 5,
            'level' => 2,
            'status' => 'approved',
            'approved_at' => now()->subHour()
        ]);

        // Create another pending booking
        $booking2 = Booking::create([
            'booking_number' => 'BK' . date('Ymd') . '0002',
            'user_id' => 2,
            'vehicle_id' => 3,
            'driver_id' => 3,
            'purpose' => 'Pengiriman material ke site tambang',
            'destination' => 'Tambang Sulawesi Utara',
            'start_date' => now()->addDays(3),
            'end_date' => now()->addDays(5),
            'status' => 'pending'
        ]);

        Approval::create([
            'booking_id' => $booking2->id,
            'approver_id' => 3,
            'level' => 1,
            'status' => 'pending'
        ]);

        Approval::create([
            'booking_id' => $booking2->id,
            'approver_id' => 5,
            'level' => 2,
            'status' => 'pending'
        ]);

        // Create completed booking with usage data
        $booking3 = Booking::create([
            'booking_number' => 'BK' . date('Ymd', strtotime('-1 day')) . '0001',
            'user_id' => 1,
            'vehicle_id' => 2,
            'driver_id' => 2,
            'purpose' => 'Survey lokasi tambang baru',
            'destination' => 'Kalimantan Timur',
            'start_date' => now()->subDays(3),
            'end_date' => now()->subDay(),
            'status' => 'completed'
        ]);

        VehicleUsage::create([
            'booking_id' => $booking3->id,
            'vehicle_id' => 2,
            'start_km' => 15000,
            'end_km' => 15850,
            'fuel_used' => 78.5,
            'notes' => 'Perjalanan lancar tanpa kendala'
        ]);
    }
}