<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Services\ActivityLogService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register ActivityLogService
        $this->app->singleton(ActivityLogService::class, function ($app) {
            return new ActivityLogService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Define gates for roles
        Gate::define('admin', function (User $user) {
            return $user->role === 'admin';
        });

        Gate::define('approver', function (User $user) {
            return $user->role === 'approver';
        });

        Gate::define('admin-or-approver', function (User $user) {
            return in_array($user->role, ['admin', 'approver']);
        });
    }
}