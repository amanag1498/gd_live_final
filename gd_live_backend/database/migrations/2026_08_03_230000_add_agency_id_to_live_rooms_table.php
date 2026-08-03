<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('live_rooms', function (Blueprint $table) {
            $table->foreignId('agency_id')
                ->nullable()
                ->after('host_id')
                ->constrained()
                ->nullOnDelete();
            $table->index(['agency_id', 'started_at'], 'live_rooms_agency_started_idx');
        });

        DB::table('live_room_gift_earning_ledgers')
            ->whereNotNull('agency_id')
            ->selectRaw('live_room_id, MIN(agency_id) as agency_id')
            ->groupBy('live_room_id')
            ->havingRaw('COUNT(DISTINCT agency_id) = 1')
            ->orderBy('live_room_id')
            ->get()
            ->each(function ($row) {
                DB::table('live_rooms')
                    ->where('id', $row->live_room_id)
                    ->whereNull('agency_id')
                    ->update(['agency_id' => $row->agency_id]);
            });
    }

    public function down(): void
    {
        Schema::table('live_rooms', function (Blueprint $table) {
            $table->dropIndex('live_rooms_agency_started_idx');
            $table->dropConstrainedForeignId('agency_id');
        });
    }
};
