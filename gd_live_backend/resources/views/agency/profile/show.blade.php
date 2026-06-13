@extends('layouts.agency-tailadmin')
@section('title', 'Agency Profile')
@section('page_intro', 'Agency account and ownership details used across host and payout operations.')

@php
  $surfaceClass = 'rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950/60';
@endphp

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ $overviewRoute ?? route('agency.dashboard') }}">Back to Dashboard</x-ui.button>
  <x-ui.button size="sm" href="{{ $payoutReportsRoute ?? route('agency.payout-reports.index') }}">Weekly Payout Reports</x-ui.button>
@endsection

@section('content')
  @php
    $summary = $profile['summary'];
    $owner = $profile['owner'];
  @endphp

  <div class="space-y-6">
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <x-admin.stat-card label="Hosts" :value="number_format($summary['host_count'])" meta="Total roster size" />
      <x-admin.stat-card label="Active Hosts" :value="number_format($summary['active_hosts'])" meta="Currently available or working" tone="brand" />
      <x-admin.stat-card label="Payout Reports" :value="number_format($summary['payout_reports'])" meta="Recorded reporting windows" tone="warning" />
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
      <x-common.component-card title="Agency Details" desc="Core commercial and contact data used across reporting and host operations.">
        <div class="grid gap-3 sm:grid-cols-2">
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Name</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $agency->name }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Legal Name</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $agency->legal_name ?: '—' }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Contact Email</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $agency->contact_email ?: '—' }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Contact Phone</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $agency->contact_phone ?: '—' }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Blocked</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $summary['blocked'] ? 'Yes' : 'No' }}</div></div>
        </div>
        @if($agency->notes)
          <div class="mt-4 rounded-2xl border border-gray-200 bg-white p-4 text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
            <div class="mb-2 text-xs uppercase tracking-[0.18em] text-gray-400">Notes</div>
            <div>{{ $agency->notes }}</div>
          </div>
        @endif
      </x-common.component-card>

      <x-common.component-card title="Owner Account" desc="Primary account attached to agency administration.">
        <div class="grid gap-3 sm:grid-cols-2">
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Owner</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $owner?->name ?? '—' }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Email</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $owner?->email ?? '—' }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Registered At</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ optional($owner?->created_at)->format('d M Y') ?: '—' }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Role</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">Agency</div></div>
        </div>
      </x-common.component-card>
    </section>
  </div>
@endsection
