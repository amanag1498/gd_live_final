@extends('layouts.admin-tailadmin')
@section('title','GD Live Overview')
@section('page_actions')
  <x-ui.button href="{{ route('admin.calls.index') }}" size="sm">Call Reports</x-ui.button>
@endsection

@section('content')
@php
  $pendingWork = $stats['pendingAgency'] + $stats['pendingHost'];
@endphp

<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="grid gap-6 px-6 py-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(280px,0.7fr)] lg:px-8">
      <div>
        <div class="mb-4 flex flex-wrap gap-2">
          <x-ui.badge color="dark">Operations</x-ui.badge>
          <x-ui.badge color="primary">Realtime</x-ui.badge>
          <x-ui.badge color="warning">Monetization</x-ui.badge>
        </div>
        <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Platform status and moderation overview</h2>
        <p class="mt-3 max-w-3xl text-sm text-gray-600 dark:text-gray-300">This workspace tracks onboarding queues, operational health, wallet supply, and recent transaction flow from one TailAdmin control surface.</p>
      </div>
      <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
          <div class="text-sm text-gray-500 dark:text-gray-400">Pending Work</div>
          <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">{{ number_format($pendingWork) }}</div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
          <div class="text-sm text-gray-500 dark:text-gray-400">Health Endpoints</div>
          <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">3</div>
        </div>
      </div>
    </div>
  </section>

  <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-admin.stat-card label="Pending Agency" :value="number_format($stats['pendingAgency'])" meta="Awaiting approval action" tone="dark">
      @slot('icon')
        <i class="ti ti-building text-lg"></i>
      @endslot
    </x-admin.stat-card>
    <x-admin.stat-card label="Pending Host" :value="number_format($stats['pendingHost'])" meta="Host onboarding queue" tone="brand">
      @slot('icon')
        <i class="ti ti-user-star text-lg"></i>
      @endslot
    </x-admin.stat-card>
    <x-admin.stat-card label="Total Users" :value="number_format($stats['totalUsers'])" meta="Registered user accounts">
      @slot('icon')
        <i class="ti ti-users text-lg"></i>
      @endslot
    </x-admin.stat-card>
    <x-admin.stat-card label="Total Agencies" :value="number_format($stats['totalAgencies'])" meta="Agency entities onboarded">
      @slot('icon')
        <i class="ti ti-building-community text-lg"></i>
      @endslot
    </x-admin.stat-card>
    <x-admin.stat-card label="Total Hosts" :value="number_format($stats['totalHosts'])" meta="Hosts available to platform">
      @slot('icon')
        <i class="ti ti-microphone-2 text-lg"></i>
      @endslot
    </x-admin.stat-card>
    <x-admin.stat-card label="Coin Supply" :value="number_format($stats['coinSupply'])" :meta="'Users '.number_format($stats['userCoinSupply']).' · Agencies '.number_format($stats['agencyCoinSupply'])" tone="dark">
      @slot('icon')
        <i class="ti ti-coins text-lg"></i>
      @endslot
    </x-admin.stat-card>
    <x-admin.stat-card label="Blocked Users" :value="number_format($stats['blockedUsers'])" meta="Accounts under restriction" tone="danger">
      @slot('icon')
        <i class="ti ti-ban text-lg"></i>
      @endslot
    </x-admin.stat-card>
  </section>

  <section class="grid gap-4 xl:grid-cols-3">
    <x-admin.stat-card label="Auth Failures" :value="number_format((int) ($opsMetrics['auth_failures'] ?? 0))" meta="Recent authentication breakdowns" tone="danger">
      @slot('icon')
        <i class="ti ti-shield-x text-lg"></i>
      @endslot
    </x-admin.stat-card>
    <x-admin.stat-card label="Room Joins" :value="number_format((int) ($opsMetrics['room_joins'] ?? 0))" meta="Live-room entry events" tone="brand">
      @slot('icon')
        <i class="ti ti-users-group text-lg"></i>
      @endslot
    </x-admin.stat-card>
    <x-admin.stat-card label="Queue Failures" :value="number_format((int) ($opsMetrics['queue_failures'] ?? 0))" meta="Background job pipeline failures" tone="warning">
      @slot('icon')
        <i class="ti ti-alert-triangle text-lg"></i>
      @endslot
    </x-admin.stat-card>
  </section>

  <x-common.component-card title="Health Checks" desc="Current readiness and metrics exposure configuration.">
    <div class="overflow-hidden rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          <tr class="bg-white dark:bg-gray-900">
            <th class="w-64 px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Live Endpoint</th>
            <td class="px-4 py-3"><code>{{ $healthConfig['liveEndpoint'] }}</code></td>
          </tr>
          <tr class="bg-gray-50 dark:bg-gray-950/60">
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Ready Endpoint</th>
            <td class="px-4 py-3"><code>{{ $healthConfig['readyEndpoint'] }}</code></td>
          </tr>
          <tr class="bg-white dark:bg-gray-900">
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Metrics Endpoint</th>
            <td class="px-4 py-3"><code>{{ $healthConfig['metricsEndpoint'] }}</code></td>
          </tr>
          <tr class="bg-gray-50 dark:bg-gray-950/60">
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Metrics Auth Header</th>
            <td class="px-4 py-3"><code>{{ $healthConfig['metricsHeader'] }}</code></td>
          </tr>
          <tr class="bg-white dark:bg-gray-900">
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Metrics Key Configured</th>
            <td class="px-4 py-3">
              <x-ui.badge :color="$healthConfig['metricsKeyConfigured'] ? 'success' : 'warning'">{{ $healthConfig['metricsKeyConfigured'] ? 'Yes' : 'No' }}</x-ui.badge>
            </td>
          </tr>
          <tr class="bg-gray-50 dark:bg-gray-950/60">
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Expose Dependency Errors</th>
            <td class="px-4 py-3">
              <x-ui.badge :color="$healthConfig['exposeErrors'] ? 'error' : 'dark'">{{ $healthConfig['exposeErrors'] ? 'Enabled' : 'Disabled' }}</x-ui.badge>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </x-common.component-card>

  <section class="grid gap-6 xl:grid-cols-2">
    <x-common.component-card>
      <x-slot:header>
        <div class="flex items-center justify-between gap-3">
          <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Latest Agency Requests</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Newest agency applications waiting in the review queue.</p>
          </div>
          <x-ui.button href="{{ route('admin.agency-requests.index') }}" variant="outline" size="sm">View all</x-ui.button>
        </div>
      </x-slot:header>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60">
            <tr>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">User</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Agency</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Applied</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse($latestAgency as $r)
              <tr class="bg-white dark:bg-gray-900">
                <td class="px-4 py-3">{{ $r->id }}</td>
                <td class="px-4 py-3">
                  <div class="font-medium text-gray-900 dark:text-white">{{ $r->user?->name ?? '—' }}</div>
                  <div class="text-gray-500 dark:text-gray-400">{{ $r->user?->email ?? '' }}</div>
                </td>
                <td class="px-4 py-3">{{ $r->agency_name }}</td>
                <td class="px-4 py-3"><x-ui.badge :color="$r->status === 'pending' ? 'warning' : ($r->status === 'approved' ? 'success' : 'error')">{{ ucfirst($r->status) }}</x-ui.badge></td>
                <td class="px-4 py-3">{{ $r->created_at?->format('d M Y') }}</td>
              </tr>
            @empty
              <tr class="bg-white dark:bg-gray-900"><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No recent items.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </x-common.component-card>

    <x-common.component-card>
      <x-slot:header>
        <div class="flex items-center justify-between gap-3">
          <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Latest Host Requests</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">New host onboarding applications and current review stage.</p>
          </div>
          <x-ui.button href="{{ route('admin.host-requests.index') }}" variant="outline" size="sm">View all</x-ui.button>
        </div>
      </x-slot:header>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60">
            <tr>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">User</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Stage</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Applied</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse($latestHost as $r)
              <tr class="bg-white dark:bg-gray-900">
                <td class="px-4 py-3">{{ $r->id }}</td>
                <td class="px-4 py-3">
                  <div class="font-medium text-gray-900 dark:text-white">{{ $r->user?->name ?? '—' }}</div>
                  <div class="text-gray-500 dark:text-gray-400">{{ $r->user?->email ?? '' }}</div>
                </td>
                <td class="px-4 py-3">{{ $r->stage_name ?: '—' }}</td>
                <td class="px-4 py-3"><x-ui.badge :color="$r->status === 'pending' ? 'warning' : ($r->status === 'approved' ? 'success' : 'error')">{{ ucfirst($r->status) }}</x-ui.badge></td>
                <td class="px-4 py-3">{{ $r->created_at?->format('d M Y') }}</td>
              </tr>
            @empty
              <tr class="bg-white dark:bg-gray-900"><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No recent items.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </x-common.component-card>
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex items-center justify-between gap-3">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Latest Coin Transactions</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Recent credits and debits across user wallets.</p>
        </div>
        <x-ui.button href="{{ route('admin.wallets.index') }}" variant="outline" size="sm">View wallets</x-ui.button>
      </div>
    </x-slot:header>
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">User</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Type</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Amount</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Reference</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">When</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($latestTx as $tx)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3">{{ $tx->id }}</td>
              <td class="px-4 py-3">
                <div class="font-medium text-gray-900 dark:text-white">{{ $tx->wallet?->user?->name ?? '—' }}</div>
                <div class="text-gray-500 dark:text-gray-400">{{ $tx->wallet?->user?->email ?? '' }}</div>
              </td>
              <td class="px-4 py-3"><x-ui.badge :color="$tx->type === 'credit' ? 'success' : 'error'">{{ ucfirst($tx->type) }}</x-ui.badge></td>
              <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ number_format($tx->amount) }}</td>
              <td class="px-4 py-3"><code>{{ $tx->reference ?? '-' }}</code></td>
              <td class="px-4 py-3">{{ $tx->created_at?->diffForHumans() }}</td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900"><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No transactions yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </x-common.component-card>
</div>
@endsection
