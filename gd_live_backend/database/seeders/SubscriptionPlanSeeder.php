<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            ['name' => 'Base', 'price_coins' => 300, 'duration_days' => 1],
            ['name' => 'Bronze', 'price_coins' => 750, 'duration_days' => 3],
            ['name' => 'Silver', 'price_coins' => 1400, 'duration_days' => 7],
            ['name' => 'Gold', 'price_coins' => 2250, 'duration_days' => 15],
            ['name' => 'Platinum', 'price_coins' => 3000, 'duration_days' => 30],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::query()->updateOrCreate(
                ['name' => $plan['name']],
                array_merge($plan, ['is_active' => true]),
            );
        }
    }
}
