<?php

namespace App\Services;

use App\Models\Agency;
use App\Models\CallEarningLedger;
use App\Models\CallSession;
use App\Models\Host;
use App\Models\LiveRoom;
use App\Models\LiveRoomGiftEarningLedger;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgencyReportService
{
    public function overview(Request $request): array
    {
        [$from, $to] = $this->resolveRange($request);

        $base = $this->callEarningsRangeQuery($from, $to);
        $liveBase = $this->liveRoomRangeQuery($from, $to)
            ->whereHas('host', fn ($query) => $query->whereNotNull('agency_id'));
        $giftBase = LiveRoomGiftEarningLedger::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('agency_id');
        $pkBase = $this->pkGiftBase($from, $to);
        $overviewRoomIds = (clone $liveBase)->pluck('id');
        $overviewParticipants = $this->participantStatsForRoomIds($overviewRoomIds);

        $agencies = Agency::query()
            ->withCount('hosts')
            ->with('owner')
            ->orderBy('name')
            ->get();

        return [
            'from' => $from,
            'to' => $to,
            'agencies' => $agencies,
            'kpis' => [
                'total_agencies' => Agency::query()->count(),
                'active_agencies' => Agency::query()->where('is_blocked', false)->count(),
                'total_hosts' => Host::query()->count(),
                'live_rooms' => (int) (clone $liveBase)->count(),
                'participants_total' => $overviewParticipants['participants_total'],
                'participants_unique' => $overviewParticipants['participants_unique'],
                'video_call_minutes' => (int) (clone $base)->where('call_sessions.type', 'video')->sum('call_earning_ledgers.billable_minutes'),
                'video_call_coins' => (int) (clone $base)->where('call_sessions.type', 'video')->sum('call_earning_ledgers.total_coins'),
                'live_minutes' => $this->sumLiveRoomMinutes((clone $liveBase)->get(), $from, $to),
                'live_gift_coins' => (int) (clone $giftBase)->sum('total_coins'),
                'room_gift_coins' => max(0, (int) (clone $giftBase)->sum('total_coins') - (int) (clone $pkBase)->sum('live_room_gift_earning_ledgers.total_coins')),
                'pk_gift_coins' => (int) (clone $pkBase)->sum('live_room_gift_earning_ledgers.total_coins'),
                'pk_event_count' => (int) (clone $pkBase)->count(),
                'gross_coins' => $this->grossCoins(
                    (int) (clone $base)->where('call_sessions.type', 'video')->sum('call_earning_ledgers.total_coins'),
                    max(0, (int) (clone $giftBase)->sum('total_coins') - (int) (clone $pkBase)->sum('live_room_gift_earning_ledgers.total_coins')),
                    (int) (clone $pkBase)->sum('live_room_gift_earning_ledgers.total_coins'),
                ),
            ],
            'charts' => [
                'calls_over_time' => $this->callsOverTime($from, $to),
                'earnings_over_time' => $this->earningsOverTime($from, $to),
                'top_agencies' => $this->topAgencies($from, $to),
                'top_hosts' => $this->topHosts($from, $to),
                'call_type' => $this->callTypeBreakdown($from, $to),
                'call_status' => $this->callStatusBreakdown($from, $to),
                'live_rooms_over_time' => $this->liveRoomsOverTime($from, $to),
                'live_gifts_over_time' => $this->liveGiftsOverTime($from, $to),
            ],
            'weekly_rows' => $this->weeklyRows($from, $to),
        ];
    }

    public function detail(Agency $agency, Request $request): array
    {
        [$from, $to] = $this->resolveRange($request);

        $recentCallsBase = CallSession::query()
            ->with(['caller', 'receiver', 'host.user'])
            ->where('agency_id', $agency->id)
            ->whereBetween('created_at', [$from, $to]);
        $base = $this->callEarningsRangeQuery($from, $to, $agency->id);
        $liveBase = $this->liveRoomRangeQuery($from, $to)
            ->whereHas('host', fn ($query) => $query->where('agency_id', $agency->id));
        $giftBase = LiveRoomGiftEarningLedger::query()
            ->where('agency_id', $agency->id)
            ->whereBetween('created_at', [$from, $to]);
        $pkBase = $this->pkGiftBase($from, $to, $agency->id);
        $summaryRoomIds = (clone $liveBase)->pluck('id');
        $summaryParticipants = $this->participantStatsForRoomIds($summaryRoomIds);

        $hosts = Host::query()
            ->with('user')
            ->where('agency_id', $agency->id)
            ->withCount('followers')
            ->get()
            ->map(function (Host $host) use ($agency, $from, $to) {
                $calls = $this->callEarningsRangeQuery($from, $to, $agency->id, $host->id);
                $liveRooms = $this->liveRoomRangeQuery($from, $to)
                    ->where('host_id', $host->id);
                $liveGifts = LiveRoomGiftEarningLedger::query()
                    ->where('host_id', $host->id)
                    ->where('agency_id', $agency->id)
                    ->whereBetween('created_at', [$from, $to]);
                $pkGifts = $this->pkGiftBase($from, $to, $host->agency_id, $host->id);
                $roomIds = (clone $liveRooms)->pluck('id');
                $participants = $this->participantStatsForRoomIds($roomIds);
                $videoCallCoins = (int) (clone $calls)->where('call_sessions.type', 'video')->sum('call_earning_ledgers.total_coins');
                $roomGiftCoins = max(0, (int) (clone $liveGifts)->sum('total_coins') - (int) (clone $pkGifts)->sum('live_room_gift_earning_ledgers.total_coins'));
                $pkGiftCoins = (int) (clone $pkGifts)->sum('live_room_gift_earning_ledgers.total_coins');

                return [
                    'host' => $host,
                    'video_call_minutes' => (int) (clone $calls)->where('call_sessions.type', 'video')->sum('call_earning_ledgers.billable_minutes'),
                    'video_call_coins' => $videoCallCoins,
                    'live_rooms' => (int) (clone $liveRooms)->count(),
                    'live_minutes' => $this->sumLiveRoomMinutes((clone $liveRooms)->get(), $from, $to),
                    'participants_total' => $participants['participants_total'],
                    'participants_unique' => $participants['participants_unique'],
                    'live_gift_coins' => (int) (clone $liveGifts)->sum('total_coins'),
                    'pk_gift_coins' => $pkGiftCoins,
                    'room_gift_coins' => $roomGiftCoins,
                    'pk_event_count' => (int) (clone $pkGifts)->count(),
                    'gross_coins' => $this->grossCoins($videoCallCoins, $roomGiftCoins, $pkGiftCoins),
                ];
            })
            ->sortByDesc('gross_coins')
            ->values();

        return [
            'agency' => $agency->load(['owner', 'hosts.user']),
            'from' => $from,
            'to' => $to,
            'summary' => [
                'hosts' => $agency->hosts()->count(),
                'live_rooms' => (int) (clone $liveBase)->count(),
                'live_minutes' => $this->sumLiveRoomMinutes((clone $liveBase)->get(), $from, $to),
                'participants_total' => $summaryParticipants['participants_total'],
                'participants_unique' => $summaryParticipants['participants_unique'],
                'video_call_minutes' => (int) (clone $base)->where('call_sessions.type', 'video')->sum('call_earning_ledgers.billable_minutes'),
                'video_call_coins' => (int) (clone $base)->where('call_sessions.type', 'video')->sum('call_earning_ledgers.total_coins'),
                'live_gift_coins' => (int) (clone $giftBase)->sum('total_coins'),
                'room_gift_coins' => max(0, (int) (clone $giftBase)->sum('total_coins') - (int) (clone $pkBase)->sum('live_room_gift_earning_ledgers.total_coins')),
                'pk_gift_coins' => (int) (clone $pkBase)->sum('live_room_gift_earning_ledgers.total_coins'),
                'pk_event_count' => (int) (clone $pkBase)->count(),
                'gross_coins' => $this->grossCoins(
                    (int) (clone $base)->where('call_sessions.type', 'video')->sum('call_earning_ledgers.total_coins'),
                    max(0, (int) (clone $giftBase)->sum('total_coins') - (int) (clone $pkBase)->sum('live_room_gift_earning_ledgers.total_coins')),
                    (int) (clone $pkBase)->sum('live_room_gift_earning_ledgers.total_coins'),
                ),
            ],
            'hosts_table' => $hosts,
            'weekly_breakdown' => $this->agencyWeeklyBreakdown($agency, $from, $to),
            'recent_calls' => (clone $recentCallsBase)->latest('id')->limit(20)->get(),
            'recent_live_rooms' => (clone $liveBase)->with('host.user')->latest('started_at')->limit(20)->get(),
        ];
    }

    private function resolveRange(Request $request): array
    {
        $timezone = (string) config('app.timezone', 'Asia/Kolkata');
        $from = $request->date('from', null, $timezone) ?: now($timezone)->subDays(6)->startOfDay();
        $to = $request->date('to', null, $timezone) ?: now($timezone)->endOfDay();

        return [$from->copy()->startOfDay(), $to->copy()->endOfDay()];
    }

    private function callsOverTime(Carbon $from, Carbon $to): array
    {
        $rows = $this->callEarningsRangeQuery($from, $to)
            ->selectRaw('DATE(call_earning_ledgers.created_at) as label, COUNT(*) as total')
            ->groupBy('label')
            ->orderBy('label')
            ->get()
            ->pluck('total', 'label');

        return $this->seriesFromDays($from, $to, fn (string $day) => (int) ($rows[$day] ?? 0));
    }

    private function earningsOverTime(Carbon $from, Carbon $to): array
    {
        $rows = $this->callEarningsRangeQuery($from, $to)
            ->selectRaw('DATE(call_earning_ledgers.created_at) as label, SUM(call_earning_ledgers.total_coins) as coins, SUM(call_earning_ledgers.host_earning) as host_earning, SUM(call_earning_ledgers.agency_earning) as agency_earning')
            ->groupBy('label')
            ->orderBy('label')
            ->get()
            ->keyBy('label');

        $days = $this->dayLabels($from, $to);

        return [
            'labels' => $days,
            'coins' => collect($days)->map(fn ($day) => (int) ($rows[$day]->coins ?? 0))->values()->all(),
            'host_earnings' => collect($days)->map(fn ($day) => (int) ($rows[$day]->host_earning ?? 0))->values()->all(),
            'agency_earnings' => collect($days)->map(fn ($day) => (int) ($rows[$day]->agency_earning ?? 0))->values()->all(),
        ];
    }

    private function topAgencies(Carbon $from, Carbon $to): array
    {
        $rows = $this->callEarningsRangeQuery($from, $to)
            ->with('agency')
            ->whereRaw('COALESCE(call_earning_ledgers.agency_id, call_sessions.agency_id, hosts.agency_id) IS NOT NULL')
            ->selectRaw('COALESCE(call_earning_ledgers.agency_id, call_sessions.agency_id, hosts.agency_id) as agency_id, SUM(call_earning_ledgers.total_coins) as coins, SUM(call_earning_ledgers.agency_earning) as earnings')
            ->groupByRaw('COALESCE(call_earning_ledgers.agency_id, call_sessions.agency_id, hosts.agency_id)')
            ->orderByDesc('coins')
            ->limit(8)
            ->get();

        return [
            'labels' => $rows->map(fn ($row) => $row->agency?->name ?? ('Agency #'.$row->agency_id))->all(),
            'coins' => $rows->map(fn ($row) => (int) $row->coins)->all(),
            'earnings' => $rows->map(fn ($row) => (int) $row->earnings)->all(),
        ];
    }

    private function topHosts(Carbon $from, Carbon $to): array
    {
        $rows = $this->callEarningsRangeQuery($from, $to)
            ->with('host.user')
            ->whereNotNull('call_earning_ledgers.host_id')
            ->selectRaw('call_earning_ledgers.host_id as host_id, SUM(call_earning_ledgers.total_coins) as coins, COUNT(*) as calls')
            ->groupBy('call_earning_ledgers.host_id')
            ->orderByDesc('coins')
            ->limit(8)
            ->get();

        return [
            'labels' => $rows->map(fn ($row) => $row->host?->user?->name ?? ('Host #'.$row->host_id))->all(),
            'coins' => $rows->map(fn ($row) => (int) $row->coins)->all(),
            'calls' => $rows->map(fn ($row) => (int) $row->calls)->all(),
        ];
    }

    private function callTypeBreakdown(Carbon $from, Carbon $to): array
    {
        $video = $this->callEarningsRangeQuery($from, $to)
            ->where('call_sessions.type', 'video')
            ->count();

        return [
            'labels' => ['Video'],
            'values' => [(int) $video],
        ];
    }

    private function callStatusBreakdown(Carbon $from, Carbon $to): array
    {
        $statuses = ['requested', 'ringing', 'accepted', 'ended', 'rejected', 'missed', 'failed'];
        $counts = CallSession::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'labels' => collect($statuses)->map(fn ($status) => ucfirst($status))->all(),
            'values' => collect($statuses)->map(fn ($status) => (int) ($counts[$status] ?? 0))->all(),
        ];
    }

    private function liveRoomsOverTime(Carbon $from, Carbon $to): array
    {
        $rows = $this->liveRoomRangeQuery($from, $to)
            ->whereHas('host', fn ($query) => $query->whereNotNull('agency_id'))
            ->selectRaw('DATE(started_at) as label, COUNT(*) as total')
            ->groupBy('label')
            ->orderBy('label')
            ->get()
            ->pluck('total', 'label');

        return $this->seriesFromDays($from, $to, fn (string $day) => (int) ($rows[$day] ?? 0));
    }

    private function liveGiftsOverTime(Carbon $from, Carbon $to): array
    {
        $rows = LiveRoomGiftEarningLedger::query()
            ->whereBetween('created_at', [$from, $to])
            ->whereNotNull('agency_id')
            ->selectRaw('DATE(created_at) as label, SUM(total_coins) as gift_coins, SUM(host_payout_coins) as host_earnings, SUM(agency_payout_coins) as agency_earnings')
            ->groupBy('label')
            ->orderBy('label')
            ->get()
            ->keyBy('label');

        $days = $this->dayLabels($from, $to);

        return [
            'labels' => $days,
            'gift_coins' => collect($days)->map(fn ($day) => (int) ($rows[$day]->gift_coins ?? 0))->values()->all(),
            'host_earnings' => collect($days)->map(fn ($day) => (int) ($rows[$day]->host_earnings ?? 0))->values()->all(),
            'agency_earnings' => collect($days)->map(fn ($day) => (int) ($rows[$day]->agency_earnings ?? 0))->values()->all(),
        ];
    }

    private function weeklyRows(Carbon $from, Carbon $to): array
    {
        $rows = Agency::query()
            ->with(['hosts.user'])
            ->withCount('hosts')
            ->get()
            ->map(function (Agency $agency) use ($from, $to) {
                $calls = $this->callEarningsRangeQuery($from, $to, $agency->id);
                $liveRooms = $this->liveRoomRangeQuery($from, $to)
                    ->whereHas('host', fn ($query) => $query->where('agency_id', $agency->id));
                $liveGifts = LiveRoomGiftEarningLedger::query()
                    ->where('agency_id', $agency->id)
                    ->whereBetween('created_at', [$from, $to]);
                $pkGifts = $this->pkGiftBase($from, $to, $agency->id);

                $roomIds = (clone $liveRooms)->pluck('id');
                $participants = $this->participantStatsForRoomIds($roomIds);
                $videoCallCoins = (int) (clone $calls)->where('call_sessions.type', 'video')->sum('call_earning_ledgers.total_coins');
                $roomGiftCoins = max(0, (int) (clone $liveGifts)->sum('total_coins') - (int) (clone $pkGifts)->sum('live_room_gift_earning_ledgers.total_coins'));
                $pkGiftCoins = (int) (clone $pkGifts)->sum('live_room_gift_earning_ledgers.total_coins');
                $topHost = $this->topHostForAgency($agency->id, $from, $to);

                return [
                    'agency' => $agency,
                    'host_count' => (int) $agency->hosts_count,
                    'video_call_minutes' => (int) (clone $calls)->where('call_sessions.type', 'video')->sum('call_earning_ledgers.billable_minutes'),
                    'video_call_coins' => $videoCallCoins,
                    'live_rooms' => (int) (clone $liveRooms)->count(),
                    'live_minutes' => $this->sumLiveRoomMinutes((clone $liveRooms)->get(), $from, $to),
                    'participants_total' => $participants['participants_total'],
                    'participants_unique' => $participants['participants_unique'],
                    'live_gift_coins' => (int) (clone $liveGifts)->sum('total_coins'),
                    'room_gift_coins' => $roomGiftCoins,
                    'pk_gift_coins' => $pkGiftCoins,
                    'pk_event_count' => (int) (clone $pkGifts)->count(),
                    'gross_coins' => $this->grossCoins($videoCallCoins, $roomGiftCoins, $pkGiftCoins),
                    'top_host' => $topHost,
                ];
            })
            ->sortByDesc('gross_coins')
            ->values();

        return $rows->all();
    }

    private function agencyWeeklyBreakdown(Agency $agency, Carbon $from, Carbon $to): array
    {
        $period = CarbonPeriod::create($from->copy()->startOfWeek(), '1 week', $to->copy()->endOfWeek());

        return collect($period)->map(function (Carbon $weekStart) use ($agency, $to) {
            $weekEnd = $weekStart->copy()->endOfWeek();
            if ($weekEnd->greaterThan($to)) {
                $weekEnd = $to->copy();
            }
            $calls = $this->callEarningsRangeQuery($weekStart, $weekEnd, $agency->id);
            $liveRooms = $this->liveRoomRangeQuery($weekStart, $weekEnd)
                ->whereHas('host', fn ($query) => $query->where('agency_id', $agency->id));
            $liveGifts = LiveRoomGiftEarningLedger::query()
                ->where('agency_id', $agency->id)
                ->whereBetween('created_at', [$weekStart, $weekEnd]);
            $pkGifts = $this->pkGiftBase($weekStart, $weekEnd, $agency->id);
            $roomIds = (clone $liveRooms)->pluck('id');
            $participants = $this->participantStatsForRoomIds($roomIds);
            $videoCallCoins = (int) (clone $calls)->where('call_sessions.type', 'video')->sum('call_earning_ledgers.total_coins');
            $roomGiftCoins = max(0, (int) (clone $liveGifts)->sum('total_coins') - (int) (clone $pkGifts)->sum('live_room_gift_earning_ledgers.total_coins'));
            $pkGiftCoins = (int) (clone $pkGifts)->sum('live_room_gift_earning_ledgers.total_coins');

            return [
                'week_start' => $weekStart->format('Y-m-d'),
                'video_call_minutes' => (int) (clone $calls)->where('call_sessions.type', 'video')->sum('call_earning_ledgers.billable_minutes'),
                'video_call_coins' => $videoCallCoins,
                'live_rooms' => (int) (clone $liveRooms)->count(),
                'live_minutes' => $this->sumLiveRoomMinutes((clone $liveRooms)->get(), $weekStart, $weekEnd),
                'participants_total' => $participants['participants_total'],
                'participants_unique' => $participants['participants_unique'],
                'live_gift_coins' => (int) (clone $liveGifts)->sum('total_coins'),
                'room_gift_coins' => $roomGiftCoins,
                'pk_gift_coins' => $pkGiftCoins,
                'pk_event_count' => (int) (clone $pkGifts)->count(),
                'gross_coins' => $this->grossCoins($videoCallCoins, $roomGiftCoins, $pkGiftCoins),
            ];
        })->values()->all();
    }

    private function pkGiftBase(Carbon $from, Carbon $to, ?int $agencyId = null, ?int $hostId = null)
    {
        return LiveRoomGiftEarningLedger::query()
            ->join('live_room_gifts', 'live_room_gifts.id', '=', 'live_room_gift_earning_ledgers.live_room_gift_id')
            ->join('live_room_pk_events', function ($join) {
                $join->on('live_room_pk_events.wallet_transaction_id', '=', 'live_room_gifts.transaction_id')
                    ->where('live_room_pk_events.event_type', '=', 'gift');
            })
            ->whereBetween('live_room_gift_earning_ledgers.created_at', [$from, $to])
            ->when($agencyId !== null, fn ($query) => $query->where('live_room_gift_earning_ledgers.agency_id', $agencyId))
            ->when($hostId !== null, fn ($query) => $query->where('live_room_gift_earning_ledgers.host_id', $hostId));
    }

    private function callEarningsRangeQuery(
        Carbon $from,
        Carbon $to,
        ?int $agencyId = null,
        ?int $hostId = null,
    ) {
        return CallEarningLedger::query()
            ->join('call_sessions', 'call_sessions.id', '=', 'call_earning_ledgers.call_session_id')
            ->join('hosts', 'hosts.id', '=', 'call_earning_ledgers.host_id')
            ->where('call_sessions.status', 'ended')
            ->where('call_earning_ledgers.total_coins', '>', 0)
            ->whereBetween('call_earning_ledgers.created_at', [$from, $to])
            ->when($agencyId !== null, function ($query) use ($agencyId) {
                $query->where(function ($agency) use ($agencyId) {
                    $agency->where('call_earning_ledgers.agency_id', $agencyId)
                        ->orWhere(function ($sessionFallback) use ($agencyId) {
                            $sessionFallback->whereNull('call_earning_ledgers.agency_id')
                                ->where('call_sessions.agency_id', $agencyId);
                        })
                        ->orWhere(function ($hostFallback) use ($agencyId) {
                            $hostFallback->whereNull('call_earning_ledgers.agency_id')
                                ->whereNull('call_sessions.agency_id')
                                ->where('hosts.agency_id', $agencyId);
                        });
                });
            })
            ->when($hostId !== null, fn ($query) => $query->where('call_earning_ledgers.host_id', $hostId));
    }

    private function liveRoomRangeQuery(Carbon $from, Carbon $to)
    {
        return LiveRoom::query()
            ->whereNotNull('started_at')
            ->where('started_at', '<=', $to)
            ->whereRaw('COALESCE(ended_at, last_activity_at, started_at) >= ?', [$from->toDateTimeString()]);
    }

    private function sumLiveRoomMinutes($rooms, Carbon $from, Carbon $to): int
    {
        return (int) $rooms->sum(function (LiveRoom $room) use ($from, $to) {
            $roomStart = $room->started_at?->copy();
            $roomEnd = ($room->ended_at ?? $room->last_activity_at ?? $room->started_at)?->copy();

            if (! $roomStart || ! $roomEnd) {
                return 0;
            }

            $effectiveStart = $roomStart->greaterThan($from) ? $roomStart : $from->copy();
            $effectiveEnd = $roomEnd->lessThan($to) ? $roomEnd : $to->copy();

            if ($effectiveEnd->lessThanOrEqualTo($effectiveStart)) {
                return 0;
            }

            return (int) floor($effectiveStart->diffInSeconds($effectiveEnd) / 60);
        });
    }

    private function participantStatsForRoomIds($roomIds): array
    {
        if ($roomIds->isEmpty()) {
            return [
                'participants_total' => 0,
                'participants_unique' => 0,
            ];
        }

        $rows = DB::table('live_room_participants')
            ->whereIn('live_room_id', $roomIds)
            ->get(['user_id', 'session_id']);

        $unique = $rows
            ->map(fn ($row) => $row->user_id ? 'user:'.$row->user_id : 'sess:'.$row->session_id)
            ->filter()
            ->unique()
            ->count();

        return [
            'participants_total' => (int) $rows->count(),
            'participants_unique' => (int) $unique,
        ];
    }

    private function grossCoins(int $videoCallCoins, int $roomGiftCoins, int $pkGiftCoins): int
    {
        return max(0, $videoCallCoins + $roomGiftCoins + $pkGiftCoins);
    }

    private function topHostForAgency(int $agencyId, Carbon $from, Carbon $to): ?string
    {
        $topHost = Host::query()
            ->with('user')
            ->where('agency_id', $agencyId)
            ->get()
            ->map(function (Host $host) use ($agencyId, $from, $to) {
                $videoCallCoins = (int) $this->callEarningsRangeQuery($from, $to, $agencyId, $host->id)
                    ->where('call_sessions.type', 'video')
                    ->sum('call_earning_ledgers.total_coins');
                $liveGifts = LiveRoomGiftEarningLedger::query()
                    ->where('host_id', $host->id)
                    ->where('agency_id', $agencyId)
                    ->whereBetween('created_at', [$from, $to]);
                $pkGifts = $this->pkGiftBase($from, $to, $agencyId, $host->id);
                $pkGiftCoins = (int) (clone $pkGifts)->sum('live_room_gift_earning_ledgers.total_coins');
                $roomGiftCoins = max(0, (int) (clone $liveGifts)->sum('total_coins') - $pkGiftCoins);

                return [
                    'name' => $host->user?->name,
                    'user_id' => $host->user_id,
                    'gross_coins' => $this->grossCoins($videoCallCoins, $roomGiftCoins, $pkGiftCoins),
                ];
            })
            ->sortByDesc('gross_coins')
            ->first();

        if (! $topHost) {
            return null;
        }

        return ($topHost['name'] ?: 'Unknown host').' · User #'.$topHost['user_id'];
    }

    private function seriesFromDays(Carbon $from, Carbon $to, callable $resolver): array
    {
        $days = $this->dayLabels($from, $to);

        return [
            'labels' => $days,
            'values' => collect($days)->map(fn ($day) => $resolver($day))->values()->all(),
        ];
    }

    private function dayLabels(Carbon $from, Carbon $to): array
    {
        return collect(CarbonPeriod::create($from, '1 day', $to))
            ->map(fn (Carbon $day) => $day->format('Y-m-d'))
            ->values()
            ->all();
    }
}
