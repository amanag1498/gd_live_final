<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_rooms', function (Blueprint $table) {
            $table->index(['host_id', 'started_at'], 'live_rooms_host_started_idx');
        });

        Schema::table('wallets', function (Blueprint $table) {
            $table->index(['balance', 'user_id'], 'wallets_balance_user_idx');
        });

        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->index(['type', 'wallet_id'], 'wallet_transactions_type_wallet_idx');
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex('wallet_transactions_type_wallet_idx');
        });

        Schema::table('wallets', function (Blueprint $table) {
            $table->dropIndex('wallets_balance_user_idx');
        });

        Schema::table('live_rooms', function (Blueprint $table) {
            $table->dropIndex('live_rooms_host_started_idx');
        });
    }
};
