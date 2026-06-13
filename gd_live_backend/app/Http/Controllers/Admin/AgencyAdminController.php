<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Host;
use App\Models\LiveRoom;
use App\Models\LiveRoomPkBattle;
use App\Services\NotifyUser;
use App\Services\AgencyDashboardService;
use App\Services\AgencyWalletService;
use App\Services\CallReportService;
use Illuminate\Http\Request;

class AgencyAdminController extends Controller
{
    public function __construct(
        private AgencyDashboardService $dashboardService,
        private AgencyWalletService $agencyWalletService,
        private CallReportService $callReportService,
    ) {
    }

    public function index(Request $request)
    {
        $q = Agency::query()->with('owner');
        if ($s = $request->get('s')) {
            $q->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%$s%")
                    ->orWhereHas('owner', fn ($u) => $u
                        ->where('name', 'like', "%$s%")
                        ->orWhere('email', 'like', "%$s%"));
            });
        }
        $agencies = $q->latest()->paginate(20);
        return view('admin.agencies.index', compact('agencies'));
    }

    public function edit(Agency $agency)
    {
        $agency->load('owner');
        return view('admin.agencies.edit', compact('agency'));
    }

    public function dashboard(Agency $agency)
    {
        $agency->load('owner');
        $dashboard = $this->dashboardService->build($agency);
        $walletSummary = $this->agencyWalletService->summary($agency);
        $this->previewRoutes($agency, $overviewRoute, $hostsIndexRoute, $callsRoute, $payoutReportsRoute, $profileRoute, $videoRoomsRoute, $pkBattlesRoute);

        return view('agency.dashboard', compact(
            'agency',
            'dashboard',
            'walletSummary',
            'overviewRoute',
            'hostsIndexRoute',
            'callsRoute',
            'payoutReportsRoute',
            'profileRoute',
            'videoRoomsRoute',
            'pkBattlesRoute',
        ));
    }

    public function hosts(Agency $agency)
    {
        $dashboard = $this->dashboardService->build($agency, 25);
        $this->previewRoutes($agency, $overviewRoute, $hostsIndexRoute, $callsRoute, $payoutReportsRoute, $profileRoute, $videoRoomsRoute, $pkBattlesRoute);

        return view('agency.hosts.index', [
            'agency' => $agency,
            'hosts' => $dashboard['hosts'],
            'summary' => $dashboard['summary'],
            'overviewRoute' => $overviewRoute,
            'hostsIndexRoute' => $hostsIndexRoute,
            'callsRoute' => $callsRoute,
            'payoutReportsRoute' => $payoutReportsRoute,
            'profileRoute' => $profileRoute,
            'videoRoomsRoute' => $videoRoomsRoute,
            'pkBattlesRoute' => $pkBattlesRoute,
        ]);
    }

    public function hostShow(Agency $agency, Host $host)
    {
        abort_unless((int) $host->agency_id === (int) $agency->id, 404);

        $host->load(['user', 'user.hostAvailability', 'photos']);
        $detail = $this->dashboardService->hostDetail($agency, $host);
        $this->previewRoutes($agency, $overviewRoute, $hostsIndexRoute, $callsRoute, $payoutReportsRoute, $profileRoute, $videoRoomsRoute, $pkBattlesRoute);

        return view('agency.hosts.show', [
            'agency' => $agency,
            'host' => $host,
            'detail' => $detail,
            'overviewRoute' => $overviewRoute,
            'hostsIndexRoute' => $hostsIndexRoute,
            'callsRoute' => $callsRoute,
            'payoutReportsRoute' => $payoutReportsRoute,
            'profileRoute' => $profileRoute,
            'videoRoomsRoute' => $videoRoomsRoute,
            'pkBattlesRoute' => $pkBattlesRoute,
        ]);
    }

    public function calls(Request $request, Agency $agency)
    {
        return $this->dashboardOnlyRedirect($agency);
    }

    public function exportCalls(Request $request, Agency $agency)
    {
        return $this->dashboardOnlyRedirect($agency);
    }

    public function profile(Agency $agency)
    {
        return $this->dashboardOnlyRedirect($agency);
    }

    public function videoRooms(Request $request, Agency $agency)
    {
        return $this->dashboardOnlyRedirect($agency);
    }

    public function videoRoomShow(Agency $agency, LiveRoom $live_room)
    {
        return $this->dashboardOnlyRedirect($agency);
    }

    public function pkBattles(Request $request, Agency $agency)
    {
        return $this->dashboardOnlyRedirect($agency);
    }

    public function pkBattleShow(Agency $agency, LiveRoomPkBattle $pk_battle)
    {
        return $this->dashboardOnlyRedirect($agency);
    }

    private function roomIndex(Request $request, Agency $agency, string $roomType)
    {
        $rooms = LiveRoom::query()
            ->with(['host.user'])
            ->withCount([
                'participants as open_participant_count' => fn ($query) => $query->whereNull('left_at'),
                'participants as open_host_count' => fn ($query) => $query->whereNull('left_at')->where('role', 'host'),
                'participants as open_speaker_count' => fn ($query) => $query->whereNull('left_at')->where('role', 'speaker'),
            ])
            ->where('room_type', $roomType)
            ->whereHas('host', fn ($query) => $query->where('agency_id', $agency->id))
            ->when($request->filled('s'), function ($query) use ($request) {
                $s = $request->string('s')->trim()->toString();
                $query->where(function ($inner) use ($s) {
                    $inner->where('room_id', 'like', "%{$s}%")
                        ->orWhere('title', 'like', "%{$s}%")
                        ->orWhereHas('host.user', fn ($q) => $q->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')->toString()))
            ->latest('started_at')
            ->paginate(20)
            ->withQueryString();

        $this->previewRoutes($agency, $overviewRoute, $hostsIndexRoute, $callsRoute, $payoutReportsRoute, $profileRoute, $videoRoomsRoute, $pkBattlesRoute);

        return view('agency.live-rooms.index', compact(
            'agency',
            'rooms',
            'roomType',
            'overviewRoute',
            'hostsIndexRoute',
            'callsRoute',
            'payoutReportsRoute',
            'profileRoute',
            'videoRoomsRoute',
            'pkBattlesRoute',
        ));
    }

    private function roomShow(Agency $agency, LiveRoom $live_room, string $roomType)
    {
        abort_unless((int) $live_room->host?->agency_id === (int) $agency->id, 404);
        abort_unless((string) ($live_room->room_type ?? 'video') === $roomType, 404);

        $live_room->load(['host.user', 'participants.user', 'gifts.sender', 'gifts.gift', 'giftEarningLedgers']);
        $stats = [
            'participants_open' => $live_room->participants->whereNull('left_at')->count(),
            'host_open' => $live_room->participants->whereNull('left_at')->where('role', 'host')->count(),
            'speaker_open' => $live_room->participants->whereNull('left_at')->where('role', 'speaker')->count(),
            'gift_coins' => (int) $live_room->gifts->sum('total_coins'),
            'gift_events' => (int) $live_room->gifts->sum('quantity'),
            'gift_host_earnings' => (int) $live_room->giftEarningLedgers->sum('host_payout_coins'),
            'gift_agency_earnings' => (int) $live_room->giftEarningLedgers->sum('agency_payout_coins'),
            'gift_platform_earnings' => (int) $live_room->giftEarningLedgers->sum('platform_revenue_coins'),
            'duration_min' => $live_room->duration_minutes,
        ];

        $this->previewRoutes($agency, $overviewRoute, $hostsIndexRoute, $callsRoute, $payoutReportsRoute, $profileRoute, $videoRoomsRoute, $pkBattlesRoute);

        return view('agency.live-rooms.show', compact(
            'agency',
            'live_room',
            'roomType',
            'stats',
            'overviewRoute',
            'hostsIndexRoute',
            'callsRoute',
            'payoutReportsRoute',
            'profileRoute',
            'videoRoomsRoute',
            'pkBattlesRoute',
        ));
    }

    private function previewRoutes(
        Agency $agency,
        ?string &$overviewRoute,
        ?string &$hostsIndexRoute,
        ?string &$callsRoute,
        ?string &$payoutReportsRoute,
        ?string &$profileRoute,
        ?string &$videoRoomsRoute,
        ?string &$pkBattlesRoute,
    ): void {
        $overviewRoute = route('admin.agencies.dashboard', $agency);
        $hostsIndexRoute = route('admin.agencies.hosts.index', $agency);
        $callsRoute = route('admin.agencies.calls.index', $agency);
        $payoutReportsRoute = route('admin.agency-payout-reports.index', ['agency_id' => $agency->id]);
        $profileRoute = route('admin.agencies.profile.show', $agency);
        $videoRoomsRoute = route('admin.agencies.video-rooms.index', $agency);
        $pkBattlesRoute = route('admin.agencies.pk-battles.index', $agency);
    }

    private function dashboardOnlyRedirect(Agency $agency)
    {
        return redirect()
            ->route('admin.agencies.dashboard', $agency)
            ->with('error', 'Admin access is limited to the agency dashboard.');
    }

    public function update(Request $request, Agency $agency)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:2000',
        ]);

        $agency->update([
            'name' => $data['name'],
            'contact_email' => $data['contact_email'] ?? $agency->contact_email,
            'contact_phone' => $data['contact_phone'] ?? $agency->contact_phone,
            'notes' => $data['notes'] ?? $agency->notes,
        ]);

        return redirect()->route('admin.agencies.index')->with('ok', 'Agency updated.');
    }

    public function block(Agency $agency)
    {
        $agency->update(['is_blocked' => true]);
        try {
            NotifyUser::send((int) $agency->owner_user_id, [
                'type' => 'agency_blocked',
                'title' => 'Account status changed',
                'body' => 'Your agency account has been blocked by admin.',
                'meta' => ['agency_id' => $agency->id],
                'screen' => 'notifications',
            ], ['push' => true, 'persist' => true]);
        } catch (\Throwable $e) {
        }

        return back()->with('ok', 'Agency blocked.');
    }

    public function unblock(Agency $agency)
    {
        $agency->update(['is_blocked' => false]);
        try {
            NotifyUser::send((int) $agency->owner_user_id, [
                'type' => 'agency_unblocked',
                'title' => 'Account status changed',
                'body' => 'Your agency account has been unblocked.',
                'meta' => ['agency_id' => $agency->id],
                'screen' => 'notifications',
            ], ['push' => true, 'persist' => true]);
        } catch (\Throwable $e) {
        }

        return back()->with('ok', 'Agency unblocked.');
    }
}
