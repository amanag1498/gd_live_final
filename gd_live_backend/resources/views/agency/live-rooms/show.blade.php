@extends('layouts.agency-tailadmin')
@section('title', ucfirst($roomType) . ' Room')
@section('page_intro', 'Read-only room detail for agency operations, participation, and gift earnings visibility.')

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ request()->routeIs('admin.*') ? route('admin.agencies.' . $roomType . '-rooms.index', $agency) : route('agency.' . $roomType . '-rooms.index') }}">Back to {{ ucfirst($roomType) }} Rooms</x-ui.button>
  <x-ui.button size="sm" href="{{ $pkBattlesRoute ?? route('agency.pk-battles.index') }}">PK Battles</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <x-admin.stat-card label="Participants" :value="number_format($stats['participants_open'])" meta="Currently connected participants" />
    <x-admin.stat-card label="On Stage" :value="number_format($stats['host_open'] + $stats['speaker_open'])" meta="Host plus speakers active now" tone="warning" />
    <x-admin.stat-card label="Gift Coins" :value="number_format($stats['gift_coins'])" meta="Coins spent in this room" tone="brand" />
    <x-admin.stat-card label="Agency Earnings" :value="number_format($stats['gift_agency_earnings'])" meta="Agency share from gifts" tone="success" />
  </section>

  <section class="grid gap-6 xl:grid-cols-[380px_minmax(0,1fr)]">
    <x-common.component-card title="Room Summary" desc="Read-only operational overview for this agency room.">
      <div class="space-y-4 text-sm">
        <div class="font-semibold text-gray-900 dark:text-white">{{ $live_room->title ?: $live_room->room_id }}</div>
        <div class="text-gray-500 dark:text-gray-400">{{ $live_room->room_id }} · {{ ucfirst($live_room->status) }}</div>
        <div class="grid gap-4 sm:grid-cols-2">
          <div><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Host</div><div class="mt-1 font-medium text-gray-900 dark:text-white">{{ $live_room->host?->user?->name ?? '—' }}</div></div>
          <div><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Peak Viewers</div><div class="mt-1 font-medium text-gray-900 dark:text-white">{{ number_format((int) $live_room->peak_viewers) }}</div></div>
          <div><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Started</div><div class="mt-1 font-medium text-gray-900 dark:text-white">{{ optional($live_room->started_at)->format('d M Y H:i') ?: '—' }}</div></div>
          <div><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Ended</div><div class="mt-1 font-medium text-gray-900 dark:text-white">{{ optional($live_room->ended_at)->format('d M Y H:i') ?: '—' }}</div></div>
          <div><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Duration</div><div class="mt-1 font-medium text-gray-900 dark:text-white">{{ $stats['duration_min'] !== null ? number_format($stats['duration_min']) . ' min' : '—' }}</div></div>
          <div><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Type</div><div class="mt-1 font-medium text-gray-900 dark:text-white">{{ ucfirst($roomType) }}</div></div>
        </div>
      </div>
    </x-common.component-card>

    <x-common.component-card title="Gift Earnings" desc="Gift activity and payout visibility for agency operations.">
      <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-admin.stat-card label="Gift Events" :value="number_format($stats['gift_events'])" meta="Total gift events recorded" />
        <x-admin.stat-card label="Gift Coins" :value="number_format($stats['gift_coins'])" meta="Total coins spent in room" tone="brand" />
        <x-admin.stat-card label="Host Earnings" :value="number_format($stats['gift_host_earnings'])" meta="Host share from gifts" tone="success" />
        <x-admin.stat-card label="Agency Earnings" :value="number_format($stats['gift_agency_earnings'])" meta="Agency share from gifts" tone="warning" />
        <x-admin.stat-card label="Platform Earnings" :value="number_format($stats['gift_platform_earnings'])" meta="Platform share from gifts" tone="dark" />
        <x-admin.stat-card label="Open Participants" :value="number_format($stats['participants_open'])" meta="Current connected audience" />
      </div>
    </x-common.component-card>
  </section>

  <x-common.component-card title="Recent Gifts" desc="Latest gift activity recorded for this room.">
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Sender</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Gift</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Qty</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Total Coins</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Created</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($live_room->gifts->take(20) as $gift)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3">{{ $gift->sender?->name ?? '—' }}</td>
              <td class="px-4 py-3">{{ $gift->gift?->name ?? '—' }}</td>
              <td class="px-4 py-3">{{ number_format((int) $gift->quantity) }}</td>
              <td class="px-4 py-3">{{ number_format((int) $gift->total_coins) }}</td>
              <td class="px-4 py-3">{{ optional($gift->created_at)->format('d M Y H:i') ?: '—' }}</td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No gifts recorded for this room.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </x-common.component-card>
</div>
@endsection
