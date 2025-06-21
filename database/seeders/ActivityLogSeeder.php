<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ActivityLog;
use App\Models\User;

class ActivityLogSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        $actions = ['login', 'logout', 'create', 'update', 'delete', 'view', 'approve', 'reject'];
        $models = ['Booking', 'Vehicle', 'Driver', 'User', null];

        // Create 50 sample logs
        for ($i = 0; $i < 50; $i++) {
            $user = $users->random();
            $action = $actions[array_rand($actions)];
            $model = $models[array_rand($models)];
            
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'model' => $model,
                'model_id' => $model ? rand(1, 10) : null,
                'description' => $this->generateDescription($action, $model, $user->name),
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
            ]);
        }

        $this->command->info('Created 50 sample activity logs.');
    }

    private function generateDescription($action, $model, $userName)
    {
        if (!$model) {
            return match($action) {
                'login' => "$userName logged into the system",
                'logout' => "$userName logged out from the system",
                default => "$userName performed $action action"
            };
        }

        return match($action) {
            'create' => "$userName created a new $model",
            'update' => "$userName updated $model information",
            'delete' => "$userName deleted a $model",
            'view' => "$userName viewed $model details",
            'approve' => "$userName approved a $model request",
            'reject' => "$userName rejected a $model request",
            default => "$userName performed $action on $model"
        };
    }
}