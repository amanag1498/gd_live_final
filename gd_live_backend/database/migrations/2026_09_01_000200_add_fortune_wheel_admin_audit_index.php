<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fortune_wheel_spins', function (Blueprint $table) {
            $table->index(
                ['spun_for_date', 'spin_type', 'reward_type', 'user_id'],
                'fw_spins_audit_idx',
            );
        });
    }

    public function down(): void
    {
        Schema::table('fortune_wheel_spins', function (Blueprint $table) {
            $table->dropIndex('fw_spins_audit_idx');
        });
    }
};
