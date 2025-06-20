<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->integer('estimated_km')->nullable()->after('end_date');
            $table->decimal('estimated_fuel', 10, 2)->nullable()->after('estimated_km');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['estimated_km', 'estimated_fuel']);
        });
    }
};