<?php

namespace Database\Seeders;

use App\Models\EntryPack;
use App\Models\FortuneWheelSegment;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class FortuneWheelSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCoinRewards();
        $this->seedEntryPackRewards();
        $this->seedSubscriptionRewards();
    }

    private function seedCoinRewards(): void
    {
        $segments = [
            [
                'label' => '0 Coins',
                'reward_value_coins' => 0,
                'weight' => 42,
                'color' => '#334155',
                'sort_order' => 10,
            ],
            [
                'label' => '5 Coins',
                'reward_value_coins' => 5,
                'weight' => 24,
                'color' => '#14B8A6',
                'sort_order' => 20,
            ],
            [
                'label' => '10 Coins',
                'reward_value_coins' => 10,
                'weight' => 28,
                'color' => '#22C55E',
                'sort_order' => 30,
            ],
            [
                'label' => '25 Coins',
                'reward_value_coins' => 25,
                'weight' => 16,
                'color' => '#38BDF8',
                'sort_order' => 40,
            ],
            [
                'label' => '50 Coins',
                'reward_value_coins' => 50,
                'weight' => 8,
                'color' => '#A855F7',
                'sort_order' => 50,
            ],
            [
                'label' => '75 Coins',
                'reward_value_coins' => 75,
                'weight' => 3,
                'color' => '#EC4899',
                'sort_order' => 60,
            ],
            [
                'label' => '100 Coins',
                'reward_value_coins' => 100,
                'weight' => 3,
                'color' => '#F97316',
                'sort_order' => 70,
            ],
            [
                'label' => '200 Coins',
                'reward_value_coins' => 200,
                'weight' => 1,
                'color' => '#EAB308',
                'sort_order' => 80,
            ],
        ];

        foreach ($segments as $segment) {
            FortuneWheelSegment::query()->updateOrCreate(
                ['label' => $segment['label']],
                array_merge($segment, [
                    'reward_type' => FortuneWheelSegment::REWARD_COINS,
                    'entry_pack_id' => null,
                    'subscription_plan_id' => null,
                    'reward_duration_hours' => null,
                    'is_active' => true,
                    'meta' => [
                        'seeded' => true,
                        'profile' => 'default_coin_reward',
                    ],
                ]),
            );
        }
    }

    private function seedEntryPackRewards(): void
    {
        $packs = [
            ['name' => 'Basic Entry', 'label' => 'Basic Entry 1 Day', 'weight' => 2, 'color' => '#FACC15', 'sort_order' => 90],
            ['name' => 'VIP Entry', 'label' => 'VIP Entry 1 Day', 'weight' => 1, 'color' => '#EC4899', 'sort_order' => 100],
        ];

        foreach ($packs as $config) {
            $pack = EntryPack::query()
                ->where('name', $config['name'])
                ->where('is_active', true)
                ->first();

            if (! $pack) {
                continue;
            }

            FortuneWheelSegment::query()->updateOrCreate(
                ['label' => $config['label']],
                [
                    'reward_type' => FortuneWheelSegment::REWARD_ENTRY_PACK,
                    'reward_value_coins' => 0,
                    'entry_pack_id' => $pack->id,
                    'subscription_plan_id' => null,
                    'reward_duration_hours' => 24,
                    'weight' => $config['weight'],
                    'color' => $config['color'],
                    'icon_url' => null,
                    'is_active' => true,
                    'sort_order' => $config['sort_order'],
                    'meta' => [
                        'seeded' => true,
                        'profile' => 'default_entry_pack_reward',
                        'entry_pack_name' => $pack->name,
                    ],
                ],
            );
        }
    }

    private function seedSubscriptionRewards(): void
    {
        $plans = [
            ['name' => 'Bronze', 'label' => 'Bronze 1 Day', 'weight' => 1, 'color' => '#CD7F32', 'sort_order' => 110],
        ];

        foreach ($plans as $config) {
            $plan = SubscriptionPlan::query()
                ->where('name', $config['name'])
                ->where('is_active', true)
                ->first();

            if (! $plan) {
                continue;
            }

            FortuneWheelSegment::query()->updateOrCreate(
                ['label' => $config['label']],
                [
                    'reward_type' => FortuneWheelSegment::REWARD_SUBSCRIPTION,
                    'reward_value_coins' => 0,
                    'entry_pack_id' => null,
                    'subscription_plan_id' => $plan->id,
                    'reward_duration_hours' => 24,
                    'weight' => $config['weight'],
                    'color' => $config['color'],
                    'icon_url' => null,
                    'is_active' => true,
                    'sort_order' => $config['sort_order'],
                    'meta' => [
                        'seeded' => true,
                        'profile' => 'default_subscription_reward',
                        'subscription_plan_name' => $plan->name,
                    ],
                ],
            );
        }
    }
}
