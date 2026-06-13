@extends('layouts.agency-tailadmin')
@section('title', 'Host Roster')
@section('page_intro', 'Agency-scoped host roster with current status, call performance, and live earnings.')

@php
  $hostCollection = $hosts->getCollection();
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ $overviewRoute ?? route('agency.dashboard') }}">Back to Dashboard</x-ui.button>
  <x-ui.button size="sm" href="{{ $callsRoute ?? route('agency.calls.index') }}">Open Call Reports</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-admin.stat-card label="Total Hosts" :value="number_format($summary['host_count'] ?? 0)" meta="Attached to this agency" />
    <x-admin.stat-card label="Active Hosts" :value="number_format($summary['active_host_count'] ?? 0)" meta="Currently enabled" tone="success" />
    <x-admin.stat-card label="Live Now" :value="number_format($summary['live_host_count'] ?? 0)" meta="Currently broadcasting" tone="warning" />
    <x-admin.stat-card label="Blocked Hosts" :value="number_format($summary['blocked_host_count'] ?? 0)" meta="Require review or action" tone="dark" />
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Host Directory</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $agency->name }}</p>
        </div>
      </div>
    </x-slot:header>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Host</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Video Room Min</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Video Gifts</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">PK Gross / Events</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Video Call</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Gross</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Host Payout</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Agency Payout</th>
            <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Actions</th>
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
                <div class="font-medium text-gray-900 dark:text-white">{{ $host->user?->name ?? '—' }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $host->stage_name ?: '—' }} · {{ $host->user?->email ?? '—' }}</div>
              </td>
              <td class="px-4 py-3">
                <x-ui.badge :color="$isOnline ? 'success' : 'dark'">{{ $isOnline ? 'Online' : 'Offline' }}</x-ui.badge>
              </td>
              <td class="px-4 py-3">{{ number_format((int) $host->dashboard_video_room_minutes) }}</td>
              <td class="px-4 py-3">{{ number_format((int) $host->dashboard_video_gift_gross) }}</td>
              <td class="px-4 py-3">{{ number_format((int) $host->dashboard_pk_gross) }} / {{ number_format((int) $host->dashboard_pk_event_count) }}</td>
              <td class="px-4 py-3">{{ number_format((int) $host->dashboard_video_call_minutes) }} / {{ number_format((int) $host->dashboard_video_call_gross) }}</td>
              <td class="px-4 py-3">{{ number_format((int) $host->dashboard_total_gross) }}</td>
              <td class="px-4 py-3">{{ number_format((int) $host->dashboard_host_payout) }}</td>
              <td class="px-4 py-3">{{ number_format((int) $host->dashboard_agency_payout) }}</td>
              <td class="px-4 py-3">
                <div class="flex justify-end">
                  <x-ui.button variant="outline" size="sm" href="{{ request()->routeIs('admin.*') ? route('admin.agencies.hosts.show', ['agency' => $agency->id, 'host' => $host->id]) : route('agency.hosts.show', $host) }}">View</x-ui.button>
                </div>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No hosts attached to this agency.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <x-slot:footer>
      <div class="flex justify-end">
        {{ $hosts->links() }}
      </div>
    </x-slot:footer>
  </x-common.component-card>
</div>
@endsection
