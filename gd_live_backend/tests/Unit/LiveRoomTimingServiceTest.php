<?php

namespace Tests\Unit;

use App\Models\LiveRoom;
use App\Services\LiveRoomTimingService;
use Carbon\Carbon;
use Tests\TestCase;

class LiveRoomTimingServiceTest extends TestCase
{
    public function test_it_clips_room_time_to_the_report_period_and_floors_partial_minutes(): void
    {
        $room = new LiveRoom();
        $room->started_at = Carbon::parse('2026-07-19 23:59:30');
        $room->ended_at = Carbon::parse('2026-07-20 00:02:29');

        $minutes = (new LiveRoomTimingService())->overlapMinutes(
            $room,
            Carbon::parse('2026-07-20 00:00:00'),
            Carbon::parse('2026-07-20 23:59:59'),
        );

        $this->assertSame(2, $minutes);
    }

    public function test_it_uses_last_activity_for_a_room_without_an_end_timestamp(): void
    {
        $room = new LiveRoom();
        $room->started_at = Carbon::parse('2026-07-20 10:00:00');
        $room->last_activity_at = Carbon::parse('2026-07-20 10:05:59');

        $minutes = (new LiveRoomTimingService())->overlapMinutes(
            $room,
            Carbon::parse('2026-07-20 00:00:00'),
            Carbon::parse('2026-07-20 23:59:59'),
        );

        $this->assertSame(5, $minutes);
    }
}
