<?php

namespace App\Services;

use App\Models\CallEarningLedger;
use App\Models\Host;
use App\Models\LiveRoom;
use App\Models\LiveRoomGiftEarningLedger;
use App\Models\LiveRoomPkBattle;
use Carbon\Carbon;

class HostEarningsReportService
{
    public function __construct(private LiveRoomTimingService $roomTiming)
    {
    }

    public function payloadForHost(Host $host): array
    {
        $now = now($this->businessTimezone());

        return [
            'today' => $this->buildPeriodPayload(
                $host,
                $now->copy()->startOfDay(),
                $now->copy()->endOfDay(),
                'Today'
            ),
            'current_week' => $this->buildPeriodPayload(
                $host,
                $now->copy()->startOfWeek(Carbon::MONDAY),
                $now->copy()->endOfWeek(Carbon::SUNDAY),
                'This Week'
            ),
            'last_week' => $this->buildPeriodPayload(
                $host,
                $now->copy()->subWeek()->startOfWeek(Carbon::MONDAY),
                $now->copy()->subWeek()->endOfWeek(Carbon::SUNDAY),
                'Last Week'
            ),
        ];
    }

    private function buildPeriodPayload(Host $host, Carbon $from, Carbon $to, string $label): array
    {
        $callSummary = CallEarningLedger::query()
            ->join('call_sessions', 'call_sessions.id', '=', 'call_earning_ledgers.call_session_id')
            ->where('call_earning_ledgers.host_id', $host->id)
            ->where('call_sessions.status', 'ended')
            ->where('call_earning_ledgers.total_coins', '>', 0)
            ->whereBetween('call_earning_ledgers.created_at', [$from, $to])
            ->selectRaw("
                SUM(CASE
                    WHEN call_sessions.type = 'video'
                    THEN call_earning_ledgers.billable_minutes
                    ELSE 0
                END) as video_minutes,
                SUM(CASE
                    WHEN call_sessions.type = 'video'
                    THEN call_earning_ledgers.total_coins
                    ELSE 0
                END) as video_earnings
            ")
            ->first();

        $giftSummary = LiveRoomGiftEarningLedger::query()
            ->join('live_room_gifts', 'live_room_gifts.id', '=', 'live_room_gift_earning_ledgers.live_room_gift_id')
            ->join('live_rooms', 'live_rooms.id', '=', 'live_room_gift_earning_ledgers.live_room_id')
            ->leftJoin('live_room_pk_events', function ($join) {
                $join->on('live_room_pk_events.wallet_transaction_id', '=', 'live_room_gifts.transaction_id')
                    ->where('live_room_pk_events.event_type', '=', 'gift');
            })
            ->where('live_room_gift_earning_ledgers.host_id', $host->id)
            ->whereBetween('live_room_gift_earning_ledgers.created_at', [$from, $to])
            ->selectRaw("
                SUM(CASE
                    WHEN live_room_pk_events.id IS NULL AND live_rooms.room_type = 'video'
                    THEN live_room_gift_earning_ledgers.total_coins
                    ELSE 0
                END) as video_room_gift_coins,
                SUM(CASE
                    WHEN live_room_pk_events.id IS NOT NULL
                    THEN live_room_gift_earning_ledgers.total_coins
                    ELSE 0
                END) as pk_gift_coins
            ")
            ->first();

        $rooms = LiveRoom::query()
            ->where('host_id', $host->id)
            ->whereNotNull('started_at')
            ->where('started_at', '<=', $to)
            ->whereRaw(
                'COALESCE(ended_at, last_activity_at, started_at) >= ?',
                [$from->toDateTimeString()]
            )
            ->get(['id', 'room_type', 'started_at', 'ended_at', 'last_activity_at', 'status']);

        $pkBattles = LiveRoomPkBattle::query()
            ->where(function ($query) use ($host) {
                $query->where('host_a_id', $host->id)->orWhere('host_b_id', $host->id);
            })
            ->where(function ($query) use ($from, $to) {
                $query
                    ->whereBetween('started_at', [$from, $to])
                    ->orWhereBetween('ended_at', [$from, $to])
                    ->orWhereBetween('created_at', [$from, $to]);
            })
            ->get(['id', 'host_a_id', 'host_b_id', 'status', 'started_at', 'ended_at', 'created_at']);

        $videoGiftCoins = (int) ($giftSummary?->video_room_gift_coins ?? 0);
        $pkCoins = (int) ($giftSummary?->pk_gift_coins ?? 0);

        $videoRoomMinutes = 0;
        foreach ($rooms as $room) {
            if (($room->room_type ?? 'video') !== 'video') {
                continue;
            }

            $minutes = $this->roomTiming->overlapMinutes(
                $room,
                $from,
                $to
            );
            if ($minutes <= 0) {
                continue;
            }
            $videoRoomMinutes += $minutes;
        }

        return [
            'label' => $label,
            'from' => $from->toIso8601String(),
            'to' => $to->toIso8601String(),
            'summary' => [
                'total_video_room_minutes' => $videoRoomMinutes,
                'total_gifted_coins' => $videoGiftCoins + $pkCoins,
                'total_room_gifts_coins' => $videoGiftCoins,
                'video_room_gifts_coins' => $videoGiftCoins,
                'video_room_gift_earnings' => $videoGiftCoins,
                'video_call_minutes' => (int) ($callSummary?->video_minutes ?? 0),
                'video_call_earnings' => (int) ($callSummary?->video_earnings ?? 0),
                'pk_room_count' => $pkBattles->count(),
                'pk_gift_coins' => $pkCoins,
                'pk_earnings' => $pkCoins,
            ],
        ];
    }

    private function businessTimezone(): string
    {
        return (string) config('app.timezone', 'Asia/Kolkata');
    }
}
