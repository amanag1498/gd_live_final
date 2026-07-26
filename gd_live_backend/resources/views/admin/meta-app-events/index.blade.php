@extends('layouts.admin-tailadmin')
@section('title', 'Meta App Events')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
  $tabClass = fn (string $tab) => $activeTab === $tab
    ? 'border-brand-500 bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-300'
    : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white';
@endphp

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Meta App Events</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Campaign measurement, verified conversions, consent visibility, and release setup in one place.</p>
      </div>
    </x-slot:header>

    <nav class="flex flex-wrap gap-2 border-b border-gray-200 pb-3 dark:border-gray-800" aria-label="Meta App Events sections">
      @foreach(['overview' => 'Overview', 'events' => 'Event Audit', 'setup' => 'Setup & Health'] as $tab => $label)
        <a href="{{ route('admin.meta-app-events.index', ['tab' => $tab]) }}" class="rounded-xl border px-4 py-2 text-sm font-semibold transition {{ $tabClass($tab) }}" aria-current="{{ $activeTab === $tab ? 'page' : 'false' }}">{{ $label }}</a>
      @endforeach
    </nav>
  </x-common.component-card>

  @if(!$setup['database_ready'])
    <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-800 dark:border-red-900/40 dark:bg-red-500/10 dark:text-red-200">
      <p class="font-semibold">Meta event database migration is missing.</p>
      <p class="mt-1">Run <code class="rounded bg-white/70 px-1.5 py-0.5 text-xs dark:bg-black/20">php artisan migrate --force</code> on the production backend. App login and verified recharge remain available while auditing is paused.</p>
    </div>
  @endif

  <div class="rounded-2xl border border-blue-200 bg-blue-50 px-5 py-4 text-sm text-blue-800 dark:border-blue-900/40 dark:bg-blue-500/10 dark:text-blue-200">
    <p class="font-semibold">Event audit is not user acquisition attribution.</p>
    <p class="mt-1">This page confirms that GD Live recorded an app event. Meta decides whether an install came from a Facebook or Instagram ad inside Ads Manager. Per-user campaign attribution requires an MMP attribution callback to be stored in GD Live.</p>
  </div>

  @if($activeTab === 'overview')
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <x-admin.stat-card label="Events" :value="number_format($summary['events'])" tone="brand" />
      <x-admin.stat-card label="Registrations" :value="number_format($summary['registrations'])" tone="success" />
      <x-admin.stat-card label="Verified Purchases" :value="number_format($summary['purchases'])" tone="warning" />
      <x-admin.stat-card label="Purchase Revenue" :value="'₹'.number_format($summary['revenue'], 2)" tone="dark" />
    </div>

    <div class="grid gap-6 xl:grid-cols-2">
      <x-common.component-card title="Conversion Funnel" desc="Server and app events recorded for the selected data set.">
        <div class="space-y-3">
          @forelse($eventBreakdown as $row)
            <div class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3 dark:border-gray-800">
              <span class="text-sm font-medium capitalize text-gray-700 dark:text-gray-300">{{ str_replace('_', ' ', $row->event_name) }}</span>
              <x-ui.badge color="dark">{{ number_format($row->event_count) }}</x-ui.badge>
            </div>
          @empty
            <p class="py-6 text-center text-sm text-gray-500">No events have been received yet.</p>
          @endforelse
        </div>
      </x-common.component-card>

      <x-common.component-card title="Platform & Consent" desc="ATT choices are reported without exposing device identifiers.">
        <div class="grid gap-3 sm:grid-cols-2">
          @foreach($platformBreakdown as $row)
            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">
              <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ $row->platform_name }}</p>
              <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($row->event_count) }}</p>
            </div>
          @endforeach
          <div class="rounded-xl border border-green-200 bg-green-50 p-4 dark:border-green-900/40 dark:bg-green-500/10">
            <p class="text-xs font-medium uppercase tracking-wide text-green-700 dark:text-green-300">Tracking allowed</p>
            <p class="mt-2 text-2xl font-semibold text-green-800 dark:text-green-200">{{ number_format($consent['allowed']) }}</p>
          </div>
          <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/40 dark:bg-amber-500/10">
            <p class="text-xs font-medium uppercase tracking-wide text-amber-700 dark:text-amber-300">Tracking declined</p>
            <p class="mt-2 text-2xl font-semibold text-amber-800 dark:text-amber-200">{{ number_format($consent['declined']) }}</p>
          </div>
        </div>
      </x-common.component-card>
    </div>
  @elseif($activeTab === 'events')
    <x-common.component-card>
      <x-slot:header>
        <form method="get" class="grid gap-3 md:grid-cols-[190px_150px_160px_160px_auto]">
          <input type="hidden" name="tab" value="events">
          <select name="event_name" class="{{ $inputClass }}">
            <option value="">All events</option>
            @foreach($eventNames as $eventName)
              <option value="{{ $eventName }}" @selected(request('event_name') === $eventName)>{{ ucwords(str_replace('_', ' ', $eventName)) }}</option>
            @endforeach
          </select>
          <select name="platform" class="{{ $inputClass }}">
            <option value="">All platforms</option>
            <option value="android" @selected(request('platform') === 'android')>Android</option>
            <option value="ios" @selected(request('platform') === 'ios')>iOS</option>
          </select>
          <input type="date" name="from" value="{{ request('from') }}" class="{{ $inputClass }}" aria-label="From date">
          <input type="date" name="to" value="{{ request('to') }}" class="{{ $inputClass }}" aria-label="To date">
          <div class="flex gap-2">
            <x-ui.button type="submit" size="sm">Apply</x-ui.button>
            <x-ui.button href="{{ route('admin.meta-app-events.index', ['tab' => 'events']) }}" variant="outline" size="sm">Reset</x-ui.button>
          </div>
        </form>
      </x-slot:header>

      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60">
            <tr><th class="px-4 py-3 text-left">When</th><th class="px-4 py-3 text-left">Event</th><th class="px-4 py-3 text-left">User</th><th class="px-4 py-3 text-left">Platform</th><th class="px-4 py-3 text-left">Consent</th><th class="px-4 py-3 text-right">Value</th><th class="px-4 py-3 text-left">Audit source</th></tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse($events as $event)
              <tr class="bg-white dark:bg-gray-900">
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $event->occurred_at?->format('d M Y H:i') }}</td>
                <td class="px-4 py-3 font-medium capitalize text-gray-900 dark:text-white">{{ str_replace('_', ' ', $event->event_name) }}</td>
                <td class="px-4 py-3">{{ $event->user?->name ?? '—' }}<div class="text-xs text-gray-500">{{ $event->user?->email ?? '' }}</div></td>
                <td class="px-4 py-3">{{ $event->platform ?? 'Server' }}<div class="text-xs text-gray-500">{{ $event->app_version ?? '' }}</div></td>
                <td class="px-4 py-3">{{ $event->advertiser_tracking_enabled === null ? 'Not reported' : ($event->advertiser_tracking_enabled ? 'Allowed' : 'Declined') }}</td>
                <td class="px-4 py-3 text-right">{{ $event->value !== null ? ($event->currency.' '.number_format($event->value, 2)) : '—' }}</td>
                <td class="px-4 py-3">{{ ucfirst($event->source) }}@if($event->paymentOrder)<div class="text-xs text-gray-500">{{ $event->paymentOrder->order_id }}</div>@endif</td>
              </tr>
            @empty
              <tr><td colspan="7" class="px-4 py-10 text-center text-gray-500">No Meta app events found for these filters.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <x-slot:footer><div class="flex justify-end">{{ $events->links() }}</div></x-slot:footer>
    </x-common.component-card>
  @else
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
      <x-common.component-card title="Integration Health" desc="Secrets are never displayed in the admin panel.">
        <div class="space-y-3">
          @foreach([
            ['Database migration', $setup['database_ready'], $setup['database_ready'] ? 'meta_app_events is ready' : 'Run php artisan migrate --force'],
            ['Meta App ID', filled($setup['app_id']), $setup['app_id'] ?: 'Missing META_APP_ID'],
            ['Client Token', $setup['client_token_configured'], $setup['client_token_configured'] ? 'Configured securely' : 'Missing META_CLIENT_TOKEN'],
            ['Ad Account', filled($setup['ad_account_id']), $setup['ad_account_id'] ?: 'Missing META_AD_ACCOUNT_ID'],
            ['Business Portfolio', filled($setup['business_id']), $setup['business_id'] ?: 'Missing META_BUSINESS_ID'],
            ['Event Pipeline', $setup['server_events'] + $setup['app_events'] > 0, number_format($setup['server_events']).' server / '.number_format($setup['app_events']).' app events'],
          ] as [$label, $healthy, $detail])
            <div class="flex items-center justify-between gap-4 rounded-xl border border-gray-200 px-4 py-3 dark:border-gray-800">
              <div><p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $label }}</p><p class="mt-1 text-xs text-gray-500">{{ $detail }}</p></div>
              <x-ui.badge :color="$healthy ? 'success' : 'warning'">{{ $healthy ? 'Ready' : 'Action needed' }}</x-ui.badge>
            </div>
          @endforeach
        </div>
      </x-common.component-card>

      <x-common.component-card title="Release Checklist" desc="Complete these steps for every production build.">
        <ol class="space-y-3 text-sm text-gray-600 dark:text-gray-300">
          <li>1. Link the Meta app, ad account, and Business Portfolio.</li>
          <li>2. Add native App ID and Client Token files to Android and iOS.</li>
          <li>3. Build normally; Meta app events are enabled by default.</li>
          <li>4. Run Laravel migrations before releasing the app.</li>
          <li>5. Verify install, registration, consent, login, and purchase in App Ads Helper.</li>
        </ol>
        <p class="mt-5 rounded-xl bg-gray-50 px-4 py-3 text-xs text-gray-500 dark:bg-gray-950/60">Last received: {{ $setup['last_event_at'] ? \Carbon\Carbon::parse($setup['last_event_at'])->format('d M Y H:i') : 'No events yet' }}</p>
      </x-common.component-card>
    </div>
  @endif
</div>
@endsection
