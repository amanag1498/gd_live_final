@extends('layouts.admin-tailadmin')
@section('title', $report['agency']->name . ' Report')

@php
  $agency = $report['agency'];
  $summary = $report['summary'];
  $hosts = $report['hosts_table'];
  $weeks = $report['weekly_breakdown'];
  $recentCalls = $report['recent_calls'];
  $from = $report['from'];
  $to = $report['to'];
  $payoutReport = $report['payout_report'] ?? null;
@endphp

@section('page_actions')
  <div class="flex flex-wrap gap-3">
    <x-ui.button variant="outline" size="sm" href="{{ route('admin.reports.agencies', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}">Back to Agency Reports</x-ui.button>
    <x-ui.button variant="outline" size="sm" href="{{ route('admin.agencies.dashboard', $agency) }}">Agency Dashboard</x-ui.button>
    <x-ui.button size="sm" href="{{ route('admin.agencies.edit', $agency) }}">Edit Agency</x-ui.button>
  </div>
@endsection

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $agency->name }}</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Owner: {{ $agency->owner?->name ?? '—' }} · Hosts: {{ number_format($summary['hosts']) }} · Contact: {{ $agency->contact_email ?: '—' }}</p>
      </div>
    </x-slot:header>
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <x-admin.stat-card label="Rooms" :value="number_format($summary['live_rooms'])" :meta="number_format($summary['live_minutes']) . ' min'" tone="brand" />
      <x-admin.stat-card label="Participants" :value="number_format($summary['participants_total'])" :meta="number_format($summary['participants_unique']) . ' unique'" tone="dark" />
      <x-admin.stat-card label="Video Call" :value="number_format($summary['video_call_minutes']).' min'" :meta="number_format($summary['video_call_coins']).' coins'" />
      <x-admin.stat-card label="Room Gift Coins" :value="number_format($summary['room_gift_coins'])" :meta="number_format($summary['live_gift_coins']).' total live gifts'" />
      <x-admin.stat-card label="PK Coins" :value="number_format($summary['pk_gift_coins'])" :meta="number_format($summary['pk_event_count']).' events'" />
      <x-admin.stat-card label="Gross" :value="number_format($summary['gross_coins'])" tone="success" />
      <x-admin.stat-card label="Blocked" :value="$agency->is_blocked ? 'Yes' : 'No'" tone="danger" />
    </section>
  </x-common.component-card>

  @if($payoutReport)
    <x-common.component-card title="Linked Payout Draft" desc="Current payout draft for the selected report window." padding="compact">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="text-sm text-gray-600 dark:text-gray-300">Status: <span class="font-semibold text-gray-900 dark:text-white">{{ ucwords(str_replace('_', ' ', $payoutReport->status)) }}</span> · Final Payable: <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($payoutReport->final_payable) }}</span></div>
        <div class="flex gap-2"><x-ui.button variant="outline" size="sm" href="{{ route('admin.agency-payout-reports.show', $payoutReport) }}">View Draft</x-ui.button><x-ui.button variant="outline" size="sm" href="{{ route('admin.agency-payout-reports.export', $payoutReport) }}">PDF</x-ui.button></div>
      </div>
    </x-common.component-card>
  @endif

  <x-common.component-card title="Hosts" padding="compact">
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left text-gray-500">Host</th><th class="px-4 py-3 text-left text-gray-500">Followers</th><th class="px-4 py-3 text-left text-gray-500">Rooms</th><th class="px-4 py-3 text-left text-gray-500">Participants</th><th class="px-4 py-3 text-left text-gray-500">Video Call</th><th class="px-4 py-3 text-left text-gray-500">Room Gifts / Coins</th><th class="px-4 py-3 text-left text-gray-500">Gift / PK</th><th class="px-4 py-3 text-left text-gray-500">Gross</th></tr></thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">@forelse($hosts as $row)<tr class="bg-white dark:bg-gray-900"><td class="px-4 py-3"><a class="font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.reports.hosts.show', ['host' => $row['host']->id, 'from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}">{{ $row['host']->user?->name ?? $row['host']->stage_name }}</a><div class="text-xs text-gray-500 dark:text-gray-400">{{ $row['host']->stage_name }}</div></td><td class="px-4 py-3">{{ number_format($row['host']->followers_count ?? 0) }}</td><td class="px-4 py-3">{{ number_format($row['live_rooms']) }}<div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($row['live_minutes']) }} min</div></td><td class="px-4 py-3">{{ number_format($row['participants_total']) }}<div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($row['participants_unique']) }} unique</div></td><td class="px-4 py-3">{{ number_format($row['video_call_minutes']) }} min<div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($row['video_call_coins']) }} coins</div></td><td class="px-4 py-3">{{ number_format($row['room_gift_coins']) }}</td><td class="px-4 py-3">{{ number_format($row['live_gift_coins']) }}<div class="text-xs text-gray-500 dark:text-gray-400">PK {{ number_format($row['pk_gift_coins']) }} · {{ number_format($row['pk_event_count']) }} events</div></td><td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ number_format($row['gross_coins']) }}</td></tr>@empty<tr class="bg-white dark:bg-gray-900"><td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No hosts attached to this agency.</td></tr>@endforelse</tbody>
      </table>
    </div>
  </x-common.component-card>

  <div class="grid gap-6 xl:grid-cols-2">
    <x-common.component-card title="Weekly Breakdown" padding="compact">
      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left text-gray-500">Week</th><th class="px-4 py-3 text-left text-gray-500">Rooms</th><th class="px-4 py-3 text-left text-gray-500">Participants</th><th class="px-4 py-3 text-left text-gray-500">Video Call</th><th class="px-4 py-3 text-left text-gray-500">Room Gifts / Coins</th><th class="px-4 py-3 text-left text-gray-500">Gift / PK</th><th class="px-4 py-3 text-left text-gray-500">Gross</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">@forelse($weeks as $week)<tr class="bg-white dark:bg-gray-900"><td class="px-4 py-3">{{ \Carbon\Carbon::parse($week['week_start'])->format('d M Y') }}</td><td class="px-4 py-3">{{ number_format($week['live_rooms']) }}<div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($week['live_minutes']) }} min</div></td><td class="px-4 py-3">{{ number_format($week['participants_total']) }}<div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($week['participants_unique']) }} unique</div></td><td class="px-4 py-3">{{ number_format($week['video_call_minutes']) }} min<div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($week['video_call_coins']) }} coins</div></td><td class="px-4 py-3">{{ number_format($week['room_gift_coins']) }}</td><td class="px-4 py-3">{{ number_format($week['live_gift_coins']) }}<div class="text-xs text-gray-500 dark:text-gray-400">PK {{ number_format($week['pk_gift_coins']) }} · {{ number_format($week['pk_event_count']) }} events</div></td><td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ number_format($week['gross_coins']) }}</td></tr>@empty<tr class="bg-white dark:bg-gray-900"><td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No weekly data.</td></tr>@endforelse</tbody>
        </table>
      </div>
    </x-common.component-card>

    <x-common.component-card title="Recent Calls" padding="compact">
      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left text-gray-500">ID</th><th class="px-4 py-3 text-left text-gray-500">Caller</th><th class="px-4 py-3 text-left text-gray-500">Host</th><th class="px-4 py-3 text-left text-gray-500">Type</th><th class="px-4 py-3 text-left text-gray-500">Coins</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">@forelse($recentCalls as $call)<tr class="bg-white dark:bg-gray-900"><td class="px-4 py-3">#{{ $call->id }}</td><td class="px-4 py-3">{{ $call->caller?->name }}</td><td class="px-4 py-3">{{ $call->host?->user?->name }}</td><td class="px-4 py-3">{{ ucfirst($call->type) }}</td><td class="px-4 py-3">{{ number_format((int) $call->total_coins_charged) }}</td></tr>@empty<tr class="bg-white dark:bg-gray-900"><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No recent calls.</td></tr>@endforelse</tbody>
        </table>
      </div>
    </x-common.component-card>
  </div>
</div>
@endsection
