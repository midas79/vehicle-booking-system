<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VehicleUsageController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ActivityLogController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard - accessible by all authenticated users
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/monthly-trend', [DashboardController::class, 'monthlyTrend']);

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin only routes (using middleware)
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('bookings', BookingController::class)->except(['show']);
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::post('/reports/export', [ReportController::class, 'export'])->name('reports.export');

        // Vehicle usage routes
        Route::get('/vehicle-usage', [VehicleUsageController::class, 'index'])
            ->name('vehicle-usage.index');

        Route::get('/bookings/{booking}/usage/create', [VehicleUsageController::class, 'create'])
            ->name('vehicle-usage.create');
        Route::post('/bookings/{booking}/usage', [VehicleUsageController::class, 'store'])
            ->name('vehicle-usage.store');

        // Service management routes
        Route::get('/vehicle-services', [VehicleUsageController::class, 'serviceIndex'])
            ->name('vehicle-usage.service-index');
        Route::get('/vehicle-services/create', [VehicleUsageController::class, 'createService'])
            ->name('vehicle-usage.create-service');
        Route::post('/vehicle-services', [VehicleUsageController::class, 'storeService'])
            ->name('vehicle-usage.store-service');

        // Vehicle monitoring
        Route::get('/vehicle-monitoring', [VehicleUsageController::class, 'monitoring'])
            ->name('vehicle-usage.monitoring');

        // Activity Logs
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    // Approver and Admin routes
    Route::middleware(['role:approver,admin'])->group(function () {
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings/{booking}/approve', [BookingController::class, 'approve'])
            ->name('bookings.approve');
        Route::post('/bookings/{booking}/reject', [BookingController::class, 'reject'])
            ->name('bookings.reject');
    });
});

require __DIR__ . '/auth.php';