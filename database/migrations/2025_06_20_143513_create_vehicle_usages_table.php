// database/migrations/2025_06_20_143513_create_vehicle_usages_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vehicle_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->nullable()->constrained();
            $table->foreignId('vehicle_id')->constrained();
            $table->enum('usage_type', ['trip', 'service', 'maintenance'])->default('trip');
            $table->string('service_type')->nullable();
            $table->date('service_date')->nullable();
            $table->date('next_service_date')->nullable();
            $table->integer('next_service_km')->nullable();
            $table->integer('start_km');
            $table->integer('end_km');
            $table->decimal('fuel_used', 10, 2)->nullable();
            $table->decimal('service_cost', 10, 2)->nullable();
            $table->string('service_vendor')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_usages');
    }
};