<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $palette = [
            '#8A63E8',
            '#4BE3C2',
            '#5EA1FF',
            '#FF8A65',
            '#FFC857',
            '#FF6B9A',
            '#9A7EF0',
            '#2DD4BF',
            '#60A5FA',
            '#F472B6',
        ];

        $threshold = 1000000;
        $rows = [];
        $now = now();

        for ($level = 11; $level <= 100; $level++) {
            $threshold += 500000 + (($level - 11) * 50000);
            $rows[] = [
                'level' => $level,
                'title' => 'Level '.$level,
                'min_spend_coins' => $threshold,
                'badge_icon' => 'stars',
                'badge_color' => $palette[($level - 1) % count($palette)],
                'benefits' => json_encode([]),
                'is_active' => true,
                'sort_order' => $level * 10,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('user_levels')->upsert(
            $rows,
            ['level'],
            ['title', 'min_spend_coins', 'badge_icon', 'badge_color', 'benefits', 'is_active', 'sort_order', 'updated_at']
        );

    }

    public function down(): void
    {
        DB::table('user_levels')
            ->whereBetween('level', [11, 100])
            ->delete();
    }
};
