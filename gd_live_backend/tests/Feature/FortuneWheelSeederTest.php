<?php

namespace Tests\Feature;

use App\Models\FortuneWheelSegment;
use Database\Seeders\CommonSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FortuneWheelSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_common_seeder_creates_idempotent_fortune_wheel_segments(): void
    {
        $this->seed(CommonSeeder::class);
        $firstCount = FortuneWheelSegment::query()->count();

        $this->seed(CommonSeeder::class);

        $this->assertSame($firstCount, FortuneWheelSegment::query()->count());
        $this->assertDatabaseHas('fortune_wheel_segments', [
            'label' => '0 Coins',
            'reward_type' => FortuneWheelSegment::REWARD_COINS,
            'reward_value_coins' => 0,
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('fortune_wheel_segments', [
            'label' => '10 Coins',
            'reward_type' => FortuneWheelSegment::REWARD_COINS,
            'reward_value_coins' => 10,
            'is_active' => true,
        ]);
        $this->assertTrue(
            FortuneWheelSegment::query()
                ->whereIn('reward_type', [
                    FortuneWheelSegment::REWARD_ENTRY_PACK,
                    FortuneWheelSegment::REWARD_SUBSCRIPTION,
                ])
                ->exists(),
        );
    }
}
