@extends('layouts.agency-tailadmin')
@section('title', ($host->user?->name ?? $host->stage_name ?? 'Host Detail'))
@section('page_intro', 'Detailed host performance across calls, live rooms, payout items, and earnings inside your agency.')

@php
  $surfaceClass = 'rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950/60';
@endphp

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ $hostsIndexRoute ?? route('agency.hosts.index') }}">Back to Hosts</x-ui.button>
  <x-ui.button size="sm" href="{{ $callsRoute ?? route('agency.calls.index', ['host_id' => $host->id]) }}">Filter Call Reports</x-ui.button>
@endsection

@section('content')
  @php
    $summary = $detail['summary'];
    $availability = $host->user?->hostAvailability;
    $isOnline = in_array($availability?->socket_status, ['online'], true) || in_array($availability?->manual_status, ['online'], true);
    $avatar = $host->user?->avatar_url;
    $hostPhotos = $host->photos ?? collect();
  @endphp

  <div class="space-y-6">
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <x-admin.stat-card label="Calls" :value="number_format($summary['call_count'])" meta="Total call sessions" />
      <x-admin.stat-card label="Minutes" :value="number_format($summary['total_minutes'])" meta="Total video call time" tone="dark" />
      <x-admin.stat-card label="Gross Total" :value="number_format($summary['gross_total'])" meta="Combined earnings before payout" tone="brand" />
      <x-admin.stat-card label="Followers" :value="number_format($summary['followers'])" meta="Current follower base" tone="warning" />
    </section>

    <section class="grid gap-6 xl:grid-cols-[minmax(0,0.85fr)_minmax(0,1.15fr)]">
      <x-common.component-card title="Host Summary" desc="Profile, rates, availability, and basic location details.">
        <div class="space-y-4">
          <div class="flex items-start gap-4">
            <div class="h-18 w-18 overflow-hidden rounded-2xl border border-gray-200 bg-gray-100 dark:border-gray-800 dark:bg-gray-900">
              @if($avatar)
                <img src="{{ $avatar }}" alt="{{ $host->user?->name ?? 'Host avatar' }}" class="h-full w-full object-cover">
              @else
                <div class="flex h-full w-full items-center justify-center text-lg font-semibold text-gray-500 dark:text-gray-400">
                  {{ strtoupper(substr($host->user?->name ?? $host->stage_name ?? 'H', 0, 1)) }}
                </div>
              @endif
            </div>
            <div>
              <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $host->user?->name ?? '—' }}</div>
              <div class="text-sm text-gray-500 dark:text-gray-400">{{ $host->stage_name ?: '—' }} · {{ $host->user?->email ?? '—' }}</div>
              <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">Phone: {{ $host->contact_phone ?: '—' }}</div>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <span class="inline-flex h-2.5 w-2.5 rounded-full {{ $isOnline ? 'bg-success-500' : 'bg-gray-400' }}"></span>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $isOnline ? 'Online' : 'Offline' }}</span>
          </div>
          <div class="grid gap-3 sm:grid-cols-2">
            <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Country</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $host->country ?: '—' }}</div></div>
            <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">City</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $host->city ?: '—' }}</div></div>
            <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Video Rate</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ number_format((int) $host->video_call_rate_per_minute) }}</div></div>
          </div>
          @if($hostPhotos->isNotEmpty())
            <div class="space-y-3">
              <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Profile Photos</div>
              <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                @foreach($hostPhotos as $photo)
                  <div class="aspect-square overflow-hidden rounded-2xl border border-gray-200 bg-gray-100 dark:border-gray-800 dark:bg-gray-900">
                    <img src="{{ $photo->path }}" alt="Host photo {{ $loop->iteration }}" class="h-full w-full object-cover">
                  </div>
                @endforeach
              </div>
            </div>
          @endif
          @if($host->bio)
            <div class="rounded-2xl border border-gray-200 bg-white p-4 text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
              <div class="mb-2 text-xs uppercase tracking-[0.18em] text-gray-400">Bio</div>
              <div>{{ $host->bio }}</div>
            </div>
          @endif
        </div>
      </x-common.component-card>

      <x-common.component-card title="Earnings Summary" desc="Gross activity and payout breakdown across live rooms, PK, and calls.">
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              <tr class="bg-white dark:bg-gray-900"><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Room Minutes</th><td class="px-4 py-3">{{ number_format($summary['video_room_minutes']) }}</td><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Gift Gross</th><td class="px-4 py-3">{{ number_format($summary['video_gift_gross']) }}</td></tr>
              <tr class="bg-gray-50 dark:bg-gray-950/60"><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">PK Gross / Events</th><td class="px-4 py-3">{{ number_format($summary['pk_gross']) }} / {{ number_format($summary['pk_event_count']) }}</td><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">PK Host / Agency</th><td class="px-4 py-3">{{ number_format($summary['pk_host_earnings']) }} / {{ number_format($summary['pk_agency_earnings']) }}</td></tr>
              <tr class="bg-white dark:bg-gray-900"><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Call Min / Gross</th><td class="px-4 py-3">{{ number_format($summary['video_call_minutes']) }} / {{ number_format($summary['video_call_gross']) }}</td><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Live Rooms</th><td class="px-4 py-3">{{ number_format($summary['live_rooms']) }} / {{ number_format($summary['live_rooms_active']) }} live</td></tr>
              <tr class="bg-gray-50 dark:bg-gray-950/60"><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Host Payout</th><td class="px-4 py-3">{{ number_format($summary['host_payout']) }}</td><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Agency Payout</th><td class="px-4 py-3">{{ number_format($summary['agency_payout']) }}</td></tr>
              <tr class="bg-white dark:bg-gray-900"><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Total Payout</th><td class="px-4 py-3">{{ number_format($summary['total_payout']) }}</td><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Completed Calls</th><td class="px-4 py-3">{{ number_format($summary['completed_calls']) }}</td></tr>
            </tbody>
          </table>
        </div>
      </x-common.component-card>
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
      <x-common.component-card title="Recent Calls" desc="Latest paid calling activity for this host.">
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">ID</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Caller</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Type</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Coins</th></tr></thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              @forelse($detail['recentCalls'] as $call)
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3">#{{ $call->id }}</td>
                  <td class="px-4 py-3">{{ $call->caller?->name ?? '—' }}</td>
                  <td class="px-4 py-3">{{ ucfirst($call->type) }}</td>
                  <td class="px-4 py-3">{{ ucfirst($call->status) }}</td>
                  <td class="px-4 py-3">{{ number_format((int) $call->total_coins_charged) }}</td>
                </tr>
              @empty
                <tr class="bg-white dark:bg-gray-900"><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No call records yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </x-common.component-card>

      <x-common.component-card title="Weekly Payout Line Items" desc="Most recent settlement windows for this host.">
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Week</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Room</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Gifts</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">PK Gifts</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Call</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Total Coins</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Total INR</th></tr></thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              @forelse($detail['recentPayoutItems'] as $item)
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3">{{ optional($item->report?->period_start)->format('d M Y') ?: '—' }}</td>
                  <td class="px-4 py-3">{{ number_format($item->video_room_minutes) }} min</td>
                  <td class="px-4 py-3">{{ number_format($item->video_gift_coins) }}</td>
                  <td class="px-4 py-3">{{ number_format($item->pk_gift_coins) }}</td>
                  <td class="px-4 py-3">{{ number_format($item->video_call_coins) }} / {{ number_format($item->video_call_minutes) }} min</td>
                  <td class="px-4 py-3">{{ number_format($item->total_coins) }}</td>
                  <td class="px-4 py-3">{{ number_format($item->total_inr, 2) }}</td>
                </tr>
              @empty
                <tr class="bg-white dark:bg-gray-900"><td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No payout items yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </x-common.component-card>
    </section>

    <x-common.component-card title="Recent Live Rooms" desc="Latest room sessions hosted by this account.">
      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Room</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Started</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Ended</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse($detail['recentLiveRooms'] as $room)
              <tr class="bg-white dark:bg-gray-900">
                <td class="px-4 py-3">{{ $room->title ?: $room->room_id }}</td>
                <td class="px-4 py-3">{{ ucfirst($room->status) }}</td>
                <td class="px-4 py-3">{{ optional($room->started_at)->format('d M Y H:i') ?: '—' }}</td>
                <td class="px-4 py-3">{{ optional($room->ended_at)->format('d M Y H:i') ?: '—' }}</td>
              </tr>
            @empty
              <tr class="bg-white dark:bg-gray-900"><td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No live room activity yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </x-common.component-card>
  </div>
@endsection
