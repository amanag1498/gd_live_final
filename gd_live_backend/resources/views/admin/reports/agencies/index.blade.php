@extends('layouts.admin-tailadmin')
@section('title','Agency Reports')

@php
  $kpis = $report['kpis'];
  $rows = $report['weekly_rows'];
  $from = $report['from'];
  $to = $report['to'];
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
@endphp

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ route('admin.agency-payout-reports.index', ['date_from' => $from->format('Y-m-d'), 'date_to' => $to->format('Y-m-d')]) }}">Open Payout Drafts</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div class="max-w-2xl">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Agency Reports</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Agency-level room, participant, video call, room gift, PK gift, and gross visibility across the selected reporting window.</p>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row">
          <form class="flex gap-3" method="get">
            <input type="date" name="from" class="{{ $inputClass }}" value="{{ $from->format('Y-m-d') }}">
            <input type="date" name="to" class="{{ $inputClass }}" value="{{ $to->format('Y-m-d') }}">
            <x-ui.button type="submit" size="sm">Apply Range</x-ui.button>
          </form>
          <form method="post" action="{{ route('admin.agency-payout-reports.generate') }}">
            @csrf
            <input type="hidden" name="start" value="{{ $from->format('Y-m-d') }}">
            <input type="hidden" name="end" value="{{ $to->format('Y-m-d') }}">
            <x-ui.button variant="outline" size="sm" type="submit">Generate Payout Drafts</x-ui.button>
          </form>
        </div>
      </div>
    </x-slot:header>
      <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
    <x-admin.stat-card label="Agencies" :value="number_format($kpis['total_agencies'])" tone="brand" />
    <x-admin.stat-card label="Active Agencies" :value="number_format($kpis['active_agencies'])" tone="success" />
    <x-admin.stat-card label="Hosts" :value="number_format($kpis['total_hosts'])" />
    <x-admin.stat-card label="Rooms" :value="number_format($kpis['live_rooms'])" :meta="number_format($kpis['live_minutes']).' min'" tone="dark" />
    <x-admin.stat-card label="Participants" :value="number_format($kpis['participants_total'])" :meta="number_format($kpis['participants_unique']).' unique'" tone="warning" />
    <x-admin.stat-card label="Video Call" :value="number_format($kpis['video_call_minutes']).' min'" :meta="number_format($kpis['video_call_coins']).' coins'" />
    <x-admin.stat-card label="Room Gifts" :value="number_format($kpis['room_gift_coins'])" :meta="number_format($kpis['live_gift_coins']).' total live gifts'" />
    <x-admin.stat-card label="PK Gifts" :value="number_format($kpis['pk_gift_coins'])" :meta="number_format($kpis['pk_event_count']).' events'" />
    <x-admin.stat-card label="Gross" :value="number_format($kpis['gross_coins'])" tone="success" />
    </section>
  </x-common.component-card>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Agency Breakdown</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Performance, ownership, top host contribution, and payout draft coverage for each agency.</p>
        </div>
        <div class="rounded-full border border-gray-200 bg-gray-50 px-3 py-1 text-xs font-medium text-gray-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
          {{ $from->format('d M Y') }} to {{ $to->format('d M Y') }}
        </div>
      </div>
    </x-slot:header>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left text-gray-500">Agency</th>
            <th class="px-4 py-3 text-left text-gray-500">Hosts</th>
            <th class="px-4 py-3 text-left text-gray-500">Rooms</th>
            <th class="px-4 py-3 text-left text-gray-500">Participants</th>
            <th class="px-4 py-3 text-left text-gray-500">Video Call</th>
            <th class="px-4 py-3 text-left text-gray-500">Room Gifts / Coins</th>
            <th class="px-4 py-3 text-left text-gray-500">Gift / PK</th>
            <th class="px-4 py-3 text-left text-gray-500">Gross</th>
            <th class="px-4 py-3 text-left text-gray-500">Top Host</th>
            <th class="px-4 py-3 text-right text-gray-500">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($rows as $row)
            @php($payoutReport = $row['payout_report'] ?? null)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-4">
                <div class="font-semibold text-gray-900 dark:text-white">{{ $row['agency']->name }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $row['agency']->owner?->name ?? 'No owner assigned' }}</div>
              </td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ number_format($row['host_count']) }}</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
                {{ number_format($row['live_rooms']) }}
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($row['live_minutes']) }} min</div>
              </td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
                {{ number_format($row['participants_total']) }}
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($row['participants_unique']) }} unique</div>
              </td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
                {{ number_format($row['video_call_minutes']) }} min
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($row['video_call_coins']) }} coins</div>
              </td>
              <td class="px-4 py-4 font-semibold text-gray-900 dark:text-white">
                {{ number_format($row['room_gift_coins']) }}
              </td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
                {{ number_format($row['live_gift_coins']) }}
                <div class="text-xs text-gray-500 dark:text-gray-400">PK {{ number_format($row['pk_gift_coins']) }} · {{ number_format($row['pk_event_count']) }} events</div>
              </td>
              <td class="px-4 py-4 font-semibold text-gray-900 dark:text-white">{{ number_format($row['gross_coins']) }}</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
                {{ $row['top_host'] ?: 'No ranked host yet' }}
              </td>
              <td class="px-4 py-4 text-right">
                <div class="flex flex-wrap justify-end gap-2">
                  @if($payoutReport)
                    <x-ui.button variant="outline" size="sm" href="{{ route('admin.agency-payout-reports.show', $payoutReport) }}">View Draft</x-ui.button>
                  @endif
                  <x-ui.button variant="outline" size="sm" href="{{ route('admin.reports.agencies.show', ['agency' => $row['agency']->id, 'from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}">View Detail</x-ui.button>
                </div>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900"><td colspan="10" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No agency data in this range.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </x-common.component-card>
</div>
@endsection
