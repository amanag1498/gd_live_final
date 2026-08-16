<?php

namespace App\Services;

use App\Models\LiveRoom;
use Carbon\CarbonInterface;

class LiveRoomTimingService
{
    public function overlapMinutes(
        LiveRoom $room,
        CarbonInterface $periodStart,
        CarbonInterface $periodEnd,
    ): int {
        $roomStart = $room->started_at?->copy();
        $roomEnd = ($room->ended_at ?? $room->last_activity_at ?? $room->started_at)?->copy();

        if (!$roomStart || !$roomEnd) {
            return 0;
        }

        $effectiveStart = $roomStart->greaterThan($periodStart) ? $roomStart : $periodStart->copy();
        $effectiveEnd = $roomEnd->lessThan($periodEnd) ? $roomEnd : $periodEnd->copy();

        if ($effectiveEnd->lessThanOrEqualTo($effectiveStart)) {
            return 0;
        }

        return (int) floor($effectiveStart->diffInSeconds($effectiveEnd) / 60);
    }
}
