@extends('layouts.admin-tailadmin')
@section('title', ($report['host']->user?->name ?? $report['host']->stage_name ?? ('Host #'.$report['host']->id)) . ' Report')

@php
  $host = $report['host'];
  $summary = $report['summary'];
  $from = $report['from'];
  $to = $report['to'];
@endphp

@section('page_actions')
  <div class="flex gap-3">
    <x-ui.button variant="outline" size="sm" href="{{ route('admin.reports.hosts', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}">Back to Host Reports</x-ui.button>
    <x-ui.button size="sm" href="{{ route('admin.hosts.edit', $host) }}">Edit Host</x-ui.button>
  </div>
@endsection

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $host->user?->name ?? $host->stage_name ?? ('Host #'.$host->id) }}</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Agency: {{ $host->agency?->name ?? 'Independent' }} · Stage name: {{ $host->stage_name ?: '—' }} · Followers: {{ number_format($summary['followers']) }}</p>
      </div>
    </x-slot:header>
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <x-admin.stat-card label="Calls" :value="number_format($summary['calls'])" tone="brand" />
      <x-admin.stat-card label="Minutes" :value="number_format($summary['minutes'])" tone="dark" />
      <x-admin.stat-card label="Call Coins" :value="number_format($summary['call_coins'])" tone="success" />
      <x-admin.stat-card label="Host Earnings" :value="number_format($summary['host_earnings'])" tone="warning" />
      <x-admin.stat-card label="Live Rooms" :value="number_format($summary['live_rooms'])" />
      <x-admin.stat-card label="Live Gift Coins" :value="number_format($summary['live_gift_coins'])" />
      <x-admin.stat-card label="PK Coins" :value="number_format($summary['pk_gift_coins'])" :meta="number_format($summary['pk_event_count']).' events'" />
      <x-admin.stat-card label="Agency Earnings" :value="number_format($summary['agency_earnings'])" tone="danger" />
    </section>
  </x-common.component-card>

  <div class="grid gap-6 xl:grid-cols-2">
    <x-common.component-card title="Weekly Breakdown" padding="compact">
      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left text-gray-500">Week</th><th class="px-4 py-3 text-left text-gray-500">Calls</th><th class="px-4 py-3 text-left text-gray-500">Minutes</th><th class="px-4 py-3 text-left text-gray-500">Call Coins</th><th class="px-4 py-3 text-left text-gray-500">Live Gifts</th><th class="px-4 py-3 text-left text-gray-500">PK</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">@forelse($report['weekly_breakdown'] as $week)<tr class="bg-white dark:bg-gray-900"><td class="px-4 py-3">{{ \Carbon\Carbon::parse($week['week_start'])->format('d M Y') }}</td><td class="px-4 py-3">{{ number_format($week['calls']) }}</td><td class="px-4 py-3">{{ number_format($week['minutes']) }}</td><td class="px-4 py-3">{{ number_format($week['call_coins']) }}</td><td class="px-4 py-3">{{ number_format($week['live_gift_coins']) }}</td><td class="px-4 py-3">{{ number_format($week['pk_gift_coins']) }} / {{ number_format($week['pk_event_count']) }}</td></tr>@empty<tr class="bg-white dark:bg-gray-900"><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No weekly data.</td></tr>@endforelse</tbody>
        </table>
      </div>
    </x-common.component-card>

    <x-common.component-card title="Recent Followers" padding="compact">
      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left text-gray-500">User</th><th class="px-4 py-3 text-left text-gray-500">Online Alerts</th><th class="px-4 py-3 text-left text-gray-500">Followed</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">@forelse($report['followers'] as $follow)<tr class="bg-white dark:bg-gray-900"><td class="px-4 py-3">{{ $follow->user?->name ?? ('User #'.$follow->user_id) }}</td><td class="px-4 py-3">{{ $follow->notify_when_online ? 'Yes' : 'No' }}</td><td class="px-4 py-3">{{ optional($follow->created_at)?->format('d M Y H:i') ?: '—' }}</td></tr>@empty<tr class="bg-white dark:bg-gray-900"><td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No followers yet.</td></tr>@endforelse</tbody>
        </table>
      </div>
    </x-common.component-card>
  </div>

  <div class="grid gap-6 xl:grid-cols-2">
    <x-common.component-card title="Recent Calls" padding="compact">
      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left text-gray-500">ID</th><th class="px-4 py-3 text-left text-gray-500">Caller</th><th class="px-4 py-3 text-left text-gray-500">Type</th><th class="px-4 py-3 text-left text-gray-500">Status</th><th class="px-4 py-3 text-left text-gray-500">Minutes</th><th class="px-4 py-3 text-left text-gray-500">Coins</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">@forelse($report['recent_calls'] as $call)<tr class="bg-white dark:bg-gray-900"><td class="px-4 py-3">#{{ $call->id }}</td><td class="px-4 py-3">{{ $call->caller?->name ?? ('User #'.$call->caller_id) }}</td><td class="px-4 py-3">{{ ucfirst($call->type) }}</td><td class="px-4 py-3">{{ ucfirst($call->status) }}</td><td class="px-4 py-3">{{ number_format((int) $call->billable_minutes) }}</td><td class="px-4 py-3">{{ number_format((int) $call->total_coins_charged) }}</td></tr>@empty<tr class="bg-white dark:bg-gray-900"><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No recent calls.</td></tr>@endforelse</tbody>
        </table>
      </div>
    </x-common.component-card>

    <x-common.component-card title="Recent Live Rooms" padding="compact">
      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left text-gray-500">Room</th><th class="px-4 py-3 text-left text-gray-500">Status</th><th class="px-4 py-3 text-left text-gray-500">Started</th><th class="px-4 py-3 text-left text-gray-500">Ended</th><th class="px-4 py-3 text-left text-gray-500">Duration</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">@forelse($report['recent_live_rooms'] as $room)<tr class="bg-white dark:bg-gray-900"><td class="px-4 py-3">{{ $room->title ?: $room->room_id }}</td><td class="px-4 py-3">{{ ucfirst($room->status) }}</td><td class="px-4 py-3">{{ optional($room->started_at)?->format('d M Y H:i') ?: '—' }}</td><td class="px-4 py-3">{{ optional($room->ended_at)?->format('d M Y H:i') ?: '—' }}</td><td class="px-4 py-3">{{ $room->duration_minutes !== null ? number_format($room->duration_minutes) . ' min' : '—' }}</td></tr>@empty<tr class="bg-white dark:bg-gray-900"><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No live rooms in this range.</td></tr>@endforelse</tbody>
        </table>
      </div>
    </x-common.component-card>
  </div>
</div>
@endsection
