<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('app_settings')) {
            DB::table('app_settings')->whereIn('key', [
                'app_features.host_goals.followers',
                'app_features.host_goals.weekly_live_minutes',
                'app_features.host_goals.weekly_gifted_coins',
            ])->delete();
        }

        if (Schema::hasTable('hosts')) {
            Schema::table('hosts', function (Blueprint $table) {
                $columns = [
                    'goal_followers',
                    'goal_weekly_live_minutes',
                    'goal_weekly_gifted_coins',
                ];

                $existing = array_values(array_filter(
                    $columns,
                    fn (string $column): bool => Schema::hasColumn('hosts', $column),
                ));

                if ($existing !== []) {
                    $table->dropColumn($existing);
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('hosts')) {
            Schema::table('hosts', function (Blueprint $table) {
                if (!Schema::hasColumn('hosts', 'goal_followers')) {
                    $table->string('goal_followers')->nullable()->after('video_call_rate_per_minute');
                }
                if (!Schema::hasColumn('hosts', 'goal_weekly_live_minutes')) {
                    $table->string('goal_weekly_live_minutes')->nullable()->after('goal_followers');
                }
                if (!Schema::hasColumn('hosts', 'goal_weekly_gifted_coins')) {
                    $table->string('goal_weekly_gifted_coins')->nullable()->after('goal_weekly_live_minutes');
                }
            });
        }
    }
};
