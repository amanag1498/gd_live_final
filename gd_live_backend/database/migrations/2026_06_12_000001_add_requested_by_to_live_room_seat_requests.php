<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('live_room_seat_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('live_room_seat_requests', 'requested_by')) {
                $table->foreignId('requested_by')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('live_room_seat_requests', function (Blueprint $table) {
            if (Schema::hasColumn('live_room_seat_requests', 'requested_by')) {
                $table->dropConstrainedForeignId('requested_by');
            }
        });
    }
};
