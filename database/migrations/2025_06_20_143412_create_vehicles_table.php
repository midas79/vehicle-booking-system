<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('license_plate')->unique();
            $table->enum('type', ['passenger', 'cargo']);
            $table->enum('ownership', ['owned', 'rented']);
            $table->decimal('fuel_consumption', 5, 2)->nullable();
            $table->integer('service_schedule_days')->default(30);
            $table->date('last_service_date')->nullable();
            $table->enum('status', ['available', 'in_use', 'maintenance'])->default('available');
            $table->foreignId('region_id')->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};