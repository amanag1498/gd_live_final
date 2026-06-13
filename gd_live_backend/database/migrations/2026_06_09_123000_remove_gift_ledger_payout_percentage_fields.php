<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_room_gift_earning_ledgers', function (Blueprint $table) {
            if (Schema::hasColumn('live_room_gift_earning_ledgers', 'host_payout_percentage')) {
                $table->dropColumn('host_payout_percentage');
            }

            if (Schema::hasColumn('live_room_gift_earning_ledgers', 'agency_payout_percentage')) {
                $table->dropColumn('agency_payout_percentage');
            }
        });
    }

    public function down(): void
    {
        Schema::table('live_room_gift_earning_ledgers', function (Blueprint $table) {
            if (!Schema::hasColumn('live_room_gift_earning_ledgers', 'host_payout_percentage')) {
                $table->decimal('host_payout_percentage', 5, 2)->default(0.00)->after('total_coins');
            }

            if (!Schema::hasColumn('live_room_gift_earning_ledgers', 'agency_payout_percentage')) {
                $table->decimal('agency_payout_percentage', 5, 2)->default(0.00)->after('host_payout_percentage');
            }
        });
    }
};
