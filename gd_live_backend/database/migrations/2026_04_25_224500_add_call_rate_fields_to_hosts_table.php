<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosts', function (Blueprint $table) {
            if (!Schema::hasColumn('hosts', 'video_call_rate_per_minute')) {
                $table->unsignedInteger('video_call_rate_per_minute')->nullable()->after('weekly_bonus');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hosts', function (Blueprint $table) {
            if (Schema::hasColumn('hosts', 'video_call_rate_per_minute')) {
                $table->dropColumn('video_call_rate_per_minute');
            }
        });
    }
};
