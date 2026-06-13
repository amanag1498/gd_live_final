<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hosts', function (Blueprint $table) {
            if (Schema::hasColumn('hosts', 'payout_percentage')) {
                $table->dropColumn('payout_percentage');
            }
            if (Schema::hasColumn('hosts', 'weekly_bonus')) {
                $table->dropColumn('weekly_bonus');
            }
        });

        Schema::table('agencies', function (Blueprint $table) {
            if (Schema::hasColumn('agencies', 'payout_percentage')) {
                $table->dropColumn('payout_percentage');
            }
            if (Schema::hasColumn('agencies', 'weekly_bonus')) {
                $table->dropColumn('weekly_bonus');
            }
        });
    }

    public function down(): void
    {
        Schema::table('hosts', function (Blueprint $table) {
            if (!Schema::hasColumn('hosts', 'payout_percentage')) {
                $table->decimal('payout_percentage', 5, 2)->default(0.00)->after('bio');
            }
            if (!Schema::hasColumn('hosts', 'weekly_bonus')) {
                $table->unsignedBigInteger('weekly_bonus')->default(0)->after('payout_percentage');
            }
        });

        Schema::table('agencies', function (Blueprint $table) {
            if (!Schema::hasColumn('agencies', 'payout_percentage')) {
                $table->decimal('payout_percentage', 5, 2)->default(0.00)->after('is_blocked');
            }
            if (!Schema::hasColumn('agencies', 'weekly_bonus')) {
                $table->unsignedBigInteger('weekly_bonus')->default(0)->after('payout_percentage');
            }
        });
    }
};
