@extends('layouts.agency-tailadmin')
@section('title', 'Agency Dashboard')
@section('page_intro', 'Agency-side operations view for host roster, live performance, call earnings, and weekly payout readiness.')

@php
  $isAgencyPanel = request()->routeIs('agency.*');
  $agencyHostsRoute = $hostsIndexRoute ?? route('agency.hosts.index');
  $agencyCallsRoute = $callsRoute ?? route('agency.calls.index');
  $agencyWalletRoute = $walletRoute ?? ($isAgencyPanel ? route('agency.wallet.show') : route('admin.agencies.wallet.show', $agency));
  $agencyPayoutRoute = $payoutReportsRoute ?? route('agency.payout-reports.index');
  $agencyProfileRoute = $profileRoute ?? ($isAgencyPanel ? route('agency.profile.show') : route('admin.agencies.profile.show', $agency));
  $agencyRoomsRoute = $videoRoomsRoute ?? ($isAgencyPanel ? route('agency.video-rooms.index') : route('admin.agencies.video-rooms.index', $agency));
  $agencyPkRoute = $pkBattlesRoute ?? ($isAgencyPanel ? route('agency.pk-battles.index') : route('admin.agencies.pk-battles.index', $agency));
@endphp

@section('page_actions')
  <a href="{{ $agencyHostsRoute }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Hosts</a>
  <a href="{{ $agencyCallsRoute }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Call Reports</a>
  <a href="{{ $agencyRoomsRoute }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Video Rooms</a>
  <a href="{{ $agencyPkRoute }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">PK Battles</a>
  <a href="{{ $agencyProfileRoute }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Profile</a>
  <a href="{{ $agencyWalletRoute }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Wallet</a>
  <a href="{{ $agencyPayoutRoute }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-3 py-2 text-xs font-medium text-white transition hover:bg-brand-600">Weekly Payout Reports</a>
@endsection

@section('content')
  @if(!$agency)
    <x-ui.alert variant="warning" title="Agency not ready">
      Your agency is not created yet. If you recently applied, wait for admin approval.
    </x-ui.alert>
  @else
    @php
      $summary = $dashboard['summary'] ?? [];
      $hosts = $dashboard['hosts'] ?? collect();
      $recentPayoutReports = $dashboard['recentPayoutReports'] ?? collect();
      $recentLiveRooms = $dashboard['recentLiveRooms'] ?? collect();
      $topHosts = $dashboard['topHosts'] ?? collect();
    @endphp

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
      <x-admin.stat-card label="Total Hosts" :value="number_format($summary['host_count'] ?? 0)" :meta="'Blocked: '.number_format($summary['blocked_host_count'] ?? 0)">
        @slot('icon')<i class="ti ti-users text-lg"></i>@endslot
      </x-admin.stat-card>
      <x-admin.stat-card label="Active Hosts" :value="number_format($summary['active_host_count'] ?? 0)" :meta="'Live now: '.number_format($summary['live_host_count'] ?? 0)" tone="brand">
        @slot('icon')<i class="ti ti-broadcast text-lg"></i>@endslot
      </x-admin.stat-card>
      <x-admin.stat-card label="Gross Total" :value="number_format($summary['gross_total'] ?? 0)" :meta="'Host payout '.number_format($summary['host_payout_total'] ?? 0).' · Agency payout '.number_format($summary['agency_payout_total'] ?? 0)">
        @slot('icon')<i class="ti ti-chart-bar text-lg"></i>@endslot
      </x-admin.stat-card>
      <x-admin.stat-card label="Combined Payout" :value="number_format($summary['combined_payout_total'] ?? 0)" meta="Host plus agency earned totals" tone="dark">
        @slot('icon')<i class="ti ti-cash text-lg"></i>@endslot
      </x-admin.stat-card>
      <x-admin.stat-card label="Video Activity" :value="number_format($summary['video_room_minutes'] ?? 0).' min'" :meta="'Calls '.number_format($summary['video_call_minutes'] ?? 0).' min · Gifts '.number_format($summary['video_gift_gross'] ?? 0)">
        @slot('icon')<i class="ti ti-video text-lg"></i>@endslot
      </x-admin.stat-card>
      <x-admin.stat-card label="PK Earnings" :value="number_format($summary['pk_gross'] ?? 0)" :meta="'Events '.number_format($summary['pk_event_count'] ?? 0).' · Agency '.number_format($summary['pk_agency_earnings'] ?? 0)" tone="warning">
        @slot('icon')<i class="ti ti-swords text-lg"></i>@endslot
      </x-admin.stat-card>
      <x-admin.stat-card label="Payout Reports" :value="number_format($summary['payout_reports'] ?? 0)" :meta="'Approved unpaid: '.number_format($summary['approved_unpaid_reports'] ?? 0)">
        @slot('icon')<i class="ti ti-file-invoice text-lg"></i>@endslot
      </x-admin.stat-card>
      <x-admin.stat-card label="Approved Unpaid Amount" :value="number_format($summary['approved_unpaid_amount'] ?? 0)" meta="Offline payout pending review/payment" tone="danger">
        @slot('icon')<i class="ti ti-hourglass text-lg"></i>@endslot
      </x-admin.stat-card>
      <x-admin.stat-card label="Agency Wallet" :value="number_format($walletSummary['balance'] ?? 0)" :meta="'Loaded '.number_format($walletSummary['total_loaded'] ?? 0).' · Sent '.number_format($walletSummary['total_distributed'] ?? 0)">
        @slot('icon')<i class="ti ti-wallet text-lg"></i>@endslot
      </x-admin.stat-card>
    </section>

    <section class="grid gap-6 xl:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)]">
      <x-common.component-card title="Agency Summary">
        <div class="space-y-4">
            <div class="mb-3">
              <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $agency->name }}</div>
              <div class="text-sm text-gray-500 dark:text-gray-400">{{ $agency->legal_name ?: 'No legal name on file' }}</div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Owner</div>
                <div class="font-semibold text-gray-900 dark:text-white">{{ $agency->owner?->name ?? '—' }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $agency->owner?->email ?? '—' }}</div>
              </div>
              <div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Contact</div>
                <div class="font-semibold text-gray-900 dark:text-white">{{ $agency->contact_phone ?: '—' }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $agency->contact_email ?: '—' }}</div>
              </div>
            </div>
            @if($agency->notes)
              <div class="border-t border-gray-200 pt-4 text-sm text-gray-600 dark:border-gray-800 dark:text-gray-300">
                <div class="mb-1 text-sm text-gray-500 dark:text-gray-400">Notes</div>
                <div>{{ $agency->notes }}</div>
              </div>
            @endif
        </div>
      </x-common.component-card>
      <x-common.component-card title="Top Hosts By Gross Activity">
        <div class="space-y-4">
              @forelse($topHosts as $row)
                <div class="flex items-start justify-between gap-4 border-b border-gray-200 pb-4 last:border-0 last:pb-0 dark:border-gray-800">
                  <div>
                    <div class="font-semibold text-gray-900 dark:text-white">
                      @php
                        $hostProfileRoute = $isAgencyPanel
                          ? route('agency.hosts.show', $row['host'])
                          : route('admin.agencies.hosts.show', ['agency' => $agency->id, 'host' => $row['host']->id]);
                      @endphp
                      <a href="{{ $hostProfileRoute }}" class="hover:text-brand-500">
                        {{ $row['host']->user?->name ?? $row['host']->stage_name }}
                      </a>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $row['host']->stage_name ?: '—' }} · Calls: {{ number_format($row['call_count']) }} · PK {{ number_format($row['pk_event_count']) }}</div>
                  </div>
                  <div class="text-right">
                    <div class="font-semibold text-gray-900 dark:text-white">{{ number_format($row['gross']) }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Agency payout {{ number_format($row['agency_earnings']) }} · PK {{ number_format($row['pk_gross']) }}</div>
                  </div>
                </div>
              @empty
                <div class="text-sm text-gray-500 dark:text-gray-400">No host activity yet.</div>
              @endforelse
        </div>
      </x-common.component-card>
    </section>

    <x-common.component-card>
      <x-slot:header>
        <div class="flex items-center justify-between gap-3">
          <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Host Roster</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Agency-scoped host performance and contribution breakdown.</p>
          </div>
        </div>
      </x-slot:header>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60">
            <tr>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Host</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Room Min</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Gifts</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">PK Gross / Events</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Call Min / Earn</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Gross</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Host Payout</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Agency Payout</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Total Payout</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Joined</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse($hosts as $host)
              @php
                $availability = $host->user?->hostAvailability;
                $isOnline = in_array($availability?->socket_status, ['online'], true) || in_array($availability?->manual_status, ['online'], true);
              @endphp
              <tr class="bg-white dark:bg-gray-900">
                <td class="px-4 py-3">
                  <div class="font-semibold text-gray-900 dark:text-white">
                    <a href="{{ $isAgencyPanel ? route('agency.hosts.show', $host) : route('admin.agencies.hosts.show', ['agency' => $agency->id, 'host' => $host->id]) }}" class="hover:text-brand-500">
                      {{ $host->user?->name ?? '—' }}
                    </a>
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">{{ $host->stage_name ?: '—' }} · {{ $host->user?->email ?? '' }}</div>
                </td>
                <td class="px-4 py-3">
                  <x-ui.badge :color="$isOnline ? 'success' : 'dark'">{{ $isOnline ? 'Online' : 'Offline' }}</x-ui.badge>
                </td>
                <td class="px-4 py-3">{{ number_format((int) $host->dashboard_video_room_minutes) }}</td>
                <td class="px-4 py-3">{{ number_format((int) $host->dashboard_video_gift_gross) }}</td>
                <td class="px-4 py-3">{{ number_format((int) $host->dashboard_pk_gross) }} / {{ number_format((int) $host->dashboard_pk_event_count) }}</td>
                <td class="px-4 py-3">{{ number_format((int) $host->dashboard_video_call_minutes) }} / {{ number_format((int) $host->dashboard_video_call_gross) }}</td>
                <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ number_format((int) $host->dashboard_total_gross) }}</td>
                <td class="px-4 py-3">{{ number_format((int) $host->dashboard_host_payout) }}</td>
                <td class="px-4 py-3">{{ number_format((int) $host->dashboard_agency_payout) }}</td>
                <td class="px-4 py-3">{{ number_format((int) $host->dashboard_total_payout) }}</td>
                <td class="px-4 py-3">{{ optional($host->created_at)->format('d M Y') ?: '—' }}</td>
              </tr>
            @empty
              <tr class="bg-white dark:bg-gray-900"><td colspan="12" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No hosts attached to this agency.</td></tr>
            @endforelse
          </tbody>
        </table>
        @if(method_exists($hosts, 'links'))
          <div class="mt-4 flex justify-end">{{ $hosts->links() }}</div>
        @endif
      </div>
    </x-common.component-card>

    <section class="grid gap-6 xl:grid-cols-2">
      <x-common.component-card>
        <x-slot:header>
          <div class="flex items-center justify-between gap-3">
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Recent Weekly Payout Reports</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Latest agency payout reporting windows and review state.</p>
            </div>
            <a href="{{ $agencyPayoutRoute }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">View All</a>
          </div>
        </x-slot:header>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
              <thead class="bg-gray-50 dark:bg-gray-950/60">
                <tr>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Week</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Total Coins</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Final Payable</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Total INR</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($recentPayoutReports as $report)
                  <tr class="bg-white dark:bg-gray-900">
                    <td class="px-4 py-3">{{ optional($report->period_start)->format('d M Y') }} - {{ optional($report->period_end)->format('d M Y') }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ number_format($report->total_coins) }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ number_format($report->final_payable) }}</td>
                    <td class="px-4 py-3">{{ number_format($report->total_inr, 2) }}</td>
                    <td class="px-4 py-3"><x-ui.badge color="dark">{{ ucwords(str_replace('_', ' ', $report->status)) }}</x-ui.badge></td>
                  </tr>
                @empty
                  <tr class="bg-white dark:bg-gray-900"><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No payout reports yet.</td></tr>
                @endforelse
              </tbody>
            </table>
        </div>
      </x-common.component-card>
      <x-common.component-card title="Recent Live Rooms" desc="Latest room sessions and current room-state history.">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
              <thead class="bg-gray-50 dark:bg-gray-950/60">
                <tr>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Room</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Host</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Started</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($recentLiveRooms as $room)
                  <tr class="bg-white dark:bg-gray-900">
                    <td class="px-4 py-3">{{ $room->title ?: $room->room_id }}</td>
                    <td class="px-4 py-3">{{ $room->host?->user?->name ?? '—' }}</td>
                    <td class="px-4 py-3"><x-ui.badge color="dark">{{ ucfirst($room->status) }}</x-ui.badge></td>
                    <td class="px-4 py-3">{{ optional($room->started_at)->format('d M Y H:i') ?: '—' }}</td>
                  </tr>
                @empty
                  <tr class="bg-white dark:bg-gray-900"><td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No live room activity yet.</td></tr>
                @endforelse
              </tbody>
            </table>
        </div>
      </x-common.component-card>
    </section>
  @endif
@endsection
