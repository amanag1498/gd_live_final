@extends('layouts.admin-tailadmin')

@section('title', 'Fortune Wheel')

@php
  $segments = collect($payload['segments'] ?? []);
  $summary = $payload['summary'] ?? [];
  $entitlements = $payload['entitlement_summary'] ?? [];
  $todayEntitlements = $entitlements['today'] ?? [];
  $weekEntitlements = $entitlements['week'] ?? [];
  $spinAudit = $payload['spin_audit'] ?? [];
  $auditSummary = $spinAudit['summary'] ?? [];
  $dailyAudit = collect($spinAudit['daily'] ?? []);
  $auditSpins = $spinAudit['spins'] ?? null;
  $settings = $payload['settings'] ?? [];
  $expected = $payload['expected_value'] ?? [];
  $entryPacks = collect($payload['entry_packs'] ?? []);
  $subscriptionPlans = collect($payload['subscription_plans'] ?? []);
  $eligibleSegmentIds = collect($payload['eligible_segment_ids'] ?? [])->map(fn ($id) => (int) $id);
  $healthWarnings = collect($payload['health_warnings'] ?? []);
  $eligibleWeight = max(0, (int) ($expected['total_weight'] ?? 0));
  $coinsCollected = (int) ($summary['coins_collected_today'] ?? 0);
  $coinsRewarded = (int) ($summary['coins_rewarded_today'] ?? 0);
  $netCoinFlow = $coinsCollected - $coinsRewarded;
  $auditNetCoinFlow = (int) ($auditSummary['coins_collected'] ?? 0) - (int) ($auditSummary['coins_rewarded'] ?? 0);
  $inputClass = 'h-10 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="dark">Game Operations</x-ui.badge>
            <x-ui.badge color="brand">Fortune Wheel</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Fortune Wheel Control Room</h2>
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Manage the exact rewards, selection odds, daily free spin, and paid spin economy from one place.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.entry-packs.index') }}">Entry Packs</x-ui.button>
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.subscription-plans.index') }}">Subscriptions</x-ui.button>
          <x-ui.button size="sm" href="{{ route('admin.settings.games.edit', ['game' => 'fortune_wheel']) }}">Game Settings</x-ui.button>
        </div>
      </div>
    </div>
  </section>

  @if($healthWarnings->isNotEmpty())
    <section class="rounded-2xl border border-warning-200 bg-warning-50 p-5 dark:border-warning-500/30 dark:bg-warning-500/10">
      <div class="flex items-start gap-3">
        <div class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-warning-100 text-warning-700 dark:bg-warning-500/20 dark:text-warning-300">!</div>
        <div>
          <h3 class="font-semibold text-warning-900 dark:text-warning-200">Action needed before players spin</h3>
          <ul class="mt-2 space-y-1 text-sm text-warning-800 dark:text-warning-300">
            @foreach($healthWarnings as $warning)
              <li>• {{ $warning }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    </section>
  @endif

  <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <x-admin.stat-card label="Status" :value="!empty($settings['enabled']) ? 'Enabled' : 'Disabled'" :meta="number_format((int) ($summary['eligible_segments'] ?? 0)) . ' selectable of ' . number_format((int) ($summary['configured_segments'] ?? 0)) . ' configured'" tone="success" />
    <x-admin.stat-card label="Spins Today" :value="number_format((int) ($summary['spins_today'] ?? 0))" :meta="number_format((int) ($summary['free_spins_today'] ?? 0)) . ' free, ' . number_format((int) ($summary['paid_spins_today'] ?? 0)) . ' paid'" tone="warning" />
    <x-admin.stat-card label="Net Coin Flow Today" :value="($netCoinFlow >= 0 ? '+' : '').number_format($netCoinFlow)" :meta="number_format($coinsCollected) . ' collected, ' . number_format($coinsRewarded) . ' rewarded'" :tone="$netCoinFlow >= 0 ? 'success' : 'danger'" />
    <x-admin.stat-card label="Paid Spin Coin Margin" :value="number_format((float) ($expected['estimated_coin_margin'] ?? 0), 2)" :meta="number_format((float) ($expected['estimated_coin_margin_percent'] ?? 0), 1) . '% · entitlement value excluded'" />
  </section>

  <section class="grid gap-4 lg:grid-cols-2">
    <x-admin.stat-card label="Entitlements Today" :value="number_format((int) ($todayEntitlements['total_entitlements'] ?? 0))" :meta="number_format((int) ($todayEntitlements['entry_pack_entitlements'] ?? 0)) . ' entry packs, ' . number_format((int) ($todayEntitlements['subscription_entitlements'] ?? 0)) . ' subscriptions · ' . number_format((int) ($todayEntitlements['entitlement_hours'] ?? 0)) . 'h granted'" tone="brand" />
    <x-admin.stat-card label="Entitlements This Week" :value="number_format((int) ($weekEntitlements['total_entitlements'] ?? 0))" :meta="number_format((int) ($weekEntitlements['entry_pack_entitlements'] ?? 0)) . ' entry packs, ' . number_format((int) ($weekEntitlements['subscription_entitlements'] ?? 0)) . ' subscriptions · ' . ($entitlements['week_start'] ?? '') . ' to ' . ($entitlements['week_end'] ?? '')" tone="success" />
  </section>

  @if(((int) ($todayEntitlements['entitlement_grant_issues'] ?? 0) + (int) ($weekEntitlements['entitlement_grant_issues'] ?? 0)) > 0)
    <section class="rounded-2xl border border-error-200 bg-error-50 p-4 text-sm text-error-700 dark:border-error-500/30 dark:bg-error-500/10 dark:text-error-300">
      Entitlement integrity warning: {{ number_format((int) ($weekEntitlements['entitlement_grant_issues'] ?? 0)) }} reward result(s) this week do not reference a persisted entry-pack or subscription grant. Review the matching audit rows.
    </section>
  @endif

  <section class="grid gap-4 lg:grid-cols-2">
    <x-common.component-card title="Reward Mix" desc="Calculated from runtime-eligible segments only, using their configured weights.">
      <div class="grid gap-3 sm:grid-cols-3">
        @foreach([
          ['label' => 'Zero coins', 'value' => $expected['zero_coin_probability'] ?? 0],
          ['label' => 'Entry pack', 'value' => $expected['entry_pack_probability'] ?? 0],
          ['label' => 'Subscription', 'value' => $expected['subscription_probability'] ?? 0],
        ] as $metric)
          <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $metric['label'] }}</div>
            <div class="mt-2 text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format((float) $metric['value'], 2) }}%</div>
          </div>
        @endforeach
      </div>
      <p class="mt-4 text-xs text-gray-500">Average coin reward: {{ number_format((float) ($expected['average_coin_reward'] ?? 0), 2) }}. Timed pack and subscription business value is intentionally not treated as coins.</p>
    </x-common.component-card>

    <x-common.component-card title="Catalog Readiness" desc="Only active catalog items can be attached to active wheel rewards.">
      <div class="grid gap-3 sm:grid-cols-2">
        <a href="{{ route('admin.entry-packs.index') }}" class="rounded-2xl border border-gray-200 p-4 transition hover:border-brand-300 hover:bg-brand-50 dark:border-gray-800 dark:hover:border-brand-500/50 dark:hover:bg-brand-500/10">
          <div class="text-sm text-gray-500">Active entry packs</div>
          <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($entryPacks->count()) }}</div>
          <div class="mt-2 text-xs font-semibold text-brand-600 dark:text-brand-400">Manage packs →</div>
        </a>
        <a href="{{ route('admin.subscription-plans.index') }}" class="rounded-2xl border border-gray-200 p-4 transition hover:border-brand-300 hover:bg-brand-50 dark:border-gray-800 dark:hover:border-brand-500/50 dark:hover:bg-brand-500/10">
          <div class="text-sm text-gray-500">Active subscription plans</div>
          <div class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ number_format($subscriptionPlans->count()) }}</div>
          <div class="mt-2 text-xs font-semibold text-brand-600 dark:text-brand-400">Manage plans →</div>
        </a>
      </div>
    </x-common.component-card>
  </section>

  <x-common.component-card title="Spin & Entitlement Audit" desc="Filter the immutable spin ledger by business date ({{ $settings['timezone'] ?? 'Asia/Kolkata' }}), player, spin type, or reward. Daily totals and detail rows use the same filters.">
    <form method="get" action="{{ route('admin.games.fortune-wheel.dashboard') }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-12">
      <div class="md:col-span-2 xl:col-span-3">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Player / Spin Search</label>
        <input name="q" value="{{ $filters['q'] ?? '' }}" class="{{ $inputClass }}" placeholder="Spin ID, user ID, name, email, key…">
      </div>
      <div class="xl:col-span-2">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Period</label>
        <select name="period" class="{{ $inputClass }}" data-fortune-audit-period>
          <option value="today" @selected(($filters['period'] ?? '') === 'today')>Today</option>
          <option value="week" @selected(($filters['period'] ?? 'week') === 'week')>This week</option>
          <option value="30_days" @selected(($filters['period'] ?? '') === '30_days')>Last 30 days</option>
          <option value="custom" @selected(($filters['period'] ?? '') === 'custom')>Custom dates</option>
        </select>
      </div>
      <div class="xl:col-span-2">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">From</label>
        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="{{ $inputClass }}" data-fortune-audit-date>
      </div>
      <div class="xl:col-span-2">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">To</label>
        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="{{ $inputClass }}" data-fortune-audit-date>
      </div>
      <div class="xl:col-span-1">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Spin</label>
        <select name="spin_type" class="{{ $inputClass }}">
          <option value="">All</option>
          <option value="free" @selected(($filters['spin_type'] ?? '') === 'free')>Free</option>
          <option value="paid" @selected(($filters['spin_type'] ?? '') === 'paid')>Paid</option>
        </select>
      </div>
      <div class="xl:col-span-2">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Reward</label>
        <select name="reward_type" class="{{ $inputClass }}">
          <option value="">All rewards</option>
          <option value="coins" @selected(($filters['reward_type'] ?? '') === 'coins')>Coins</option>
          <option value="entry_pack" @selected(($filters['reward_type'] ?? '') === 'entry_pack')>Entry pack</option>
          <option value="subscription" @selected(($filters['reward_type'] ?? '') === 'subscription')>Subscription</option>
        </select>
      </div>
      <div class="xl:col-span-2">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Rows</label>
        <select name="per_page" class="{{ $inputClass }}">
          @foreach([25, 50, 100] as $pageSize)
            <option value="{{ $pageSize }}" @selected(($filters['per_page'] ?? 25) === $pageSize)>{{ $pageSize }} per page</option>
          @endforeach
        </select>
      </div>
      <div class="flex flex-wrap items-end gap-2 md:col-span-2 xl:col-span-10 xl:justify-end">
        <x-ui.button type="submit" size="sm">Apply Filters</x-ui.button>
        <x-ui.button variant="outline" href="{{ route('admin.games.fortune-wheel.dashboard') }}" size="sm">Reset to This Week</x-ui.button>
      </div>
    </form>

    <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
      @foreach([
        ['label' => 'Matching Spins', 'value' => number_format((int) ($auditSummary['total_spins'] ?? 0)), 'meta' => number_format((int) ($auditSummary['unique_players'] ?? 0)).' players'],
        ['label' => 'Free / Paid', 'value' => number_format((int) ($auditSummary['free_spins'] ?? 0)).' / '.number_format((int) ($auditSummary['paid_spins'] ?? 0)), 'meta' => 'spin split'],
        ['label' => 'Entry Packs', 'value' => number_format((int) ($auditSummary['entry_pack_entitlements'] ?? 0)), 'meta' => 'entitlements granted'],
        ['label' => 'Subscriptions', 'value' => number_format((int) ($auditSummary['subscription_entitlements'] ?? 0)), 'meta' => number_format((int) ($auditSummary['entitlement_hours'] ?? 0)).' total entitlement hours'],
        ['label' => 'Net Coin Flow', 'value' => ($auditNetCoinFlow >= 0 ? '+' : '').number_format($auditNetCoinFlow), 'meta' => number_format((int) ($auditSummary['coins_collected'] ?? 0)).' in · '.number_format((int) ($auditSummary['coins_rewarded'] ?? 0)).' out'],
      ] as $metric)
        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-white/[0.03]">
          <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $metric['label'] }}</div>
          <div class="mt-2 text-xl font-semibold text-gray-900 dark:text-white">{{ $metric['value'] }}</div>
          <div class="mt-1 text-xs text-gray-500">{{ $metric['meta'] }}</div>
        </div>
      @endforeach
    </div>

    @if((int) ($auditSummary['entitlement_grant_issues'] ?? 0) > 0)
      <div class="mt-4 rounded-2xl border border-error-200 bg-error-50 p-4 text-sm font-medium text-error-700 dark:border-error-500/30 dark:bg-error-500/10 dark:text-error-300">
        {{ number_format((int) $auditSummary['entitlement_grant_issues']) }} matching entitlement reward(s) have no persisted grant reference.
      </div>
    @endif

    <div class="mt-6 overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-[900px] divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
            <th class="px-4 py-3">Business Date</th><th class="px-4 py-3">Spins</th><th class="px-4 py-3">Free / Paid</th><th class="px-4 py-3">Entry Packs</th><th class="px-4 py-3">Subscriptions</th><th class="px-4 py-3">Grant Issues</th><th class="px-4 py-3">Hours Granted</th><th class="px-4 py-3">Coin In / Out</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($dailyAudit as $day)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ \Carbon\CarbonImmutable::parse($day->spun_for_date)->format('D, d M Y') }}</td>
              <td class="px-4 py-3">{{ number_format((int) $day->total_spins) }}</td>
              <td class="px-4 py-3">{{ number_format((int) $day->free_spins) }} / {{ number_format((int) $day->paid_spins) }}</td>
              <td class="px-4 py-3">{{ number_format((int) $day->entry_pack_entitlements) }}</td>
              <td class="px-4 py-3">{{ number_format((int) $day->subscription_entitlements) }}</td>
              <td class="px-4 py-3"><x-ui.badge :color="(int) $day->entitlement_grant_issues > 0 ? 'error' : 'success'">{{ number_format((int) $day->entitlement_grant_issues) }}</x-ui.badge></td>
              <td class="px-4 py-3">{{ number_format((int) $day->entitlement_hours) }}h</td>
              <td class="px-4 py-3">{{ number_format((int) $day->coins_collected) }} / {{ number_format((int) $day->coins_rewarded) }}</td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900"><td colspan="8" class="px-4 py-8 text-center text-gray-500">No daily activity matches these filters.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-[1050px] divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
            <th class="px-4 py-3">Spin</th><th class="px-4 py-3">Player</th><th class="px-4 py-3">Type / Cost</th><th class="px-4 py-3">Reward</th><th class="px-4 py-3">Entitlement Grant</th><th class="px-4 py-3">Business Date</th><th class="px-4 py-3">Created</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($auditSpins ?? [] as $spin)
            @php
              $rewardName = match($spin->reward_type) {
                'coins' => number_format((int) $spin->reward_value_coins).' coins',
                'entry_pack' => $spin->entryPack?->name ?? data_get($spin->meta, 'segment_label') ?? 'Entry pack',
                'subscription' => $spin->subscriptionPlan?->name ?? data_get($spin->meta, 'segment_label') ?? 'Subscription',
                default => ucfirst(str_replace('_', ' ', $spin->reward_type)),
              };
              $grantId = $spin->reward_type === 'entry_pack' ? $spin->user_entry_pack_id : ($spin->reward_type === 'subscription' ? $spin->user_subscription_id : null);
            @endphp
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-4"><div class="font-semibold text-gray-900 dark:text-white">#{{ $spin->id }}</div><div class="max-w-44 truncate text-xs text-gray-500" title="{{ $spin->idempotency_key }}">{{ $spin->idempotency_key ?: 'No client key' }}</div></td>
              <td class="px-4 py-4"><div class="font-semibold text-gray-900 dark:text-white">{{ $spin->user?->name ?? 'User #'.$spin->user_id }}</div><div class="text-xs text-gray-500">#{{ $spin->user_id }} · {{ $spin->user?->email }}</div></td>
              <td class="px-4 py-4"><x-ui.badge :color="$spin->spin_type === 'free' ? 'success' : 'brand'">{{ ucfirst($spin->spin_type) }}</x-ui.badge><div class="mt-1 text-xs text-gray-500">{{ number_format((int) $spin->spin_cost_coins) }} coins</div></td>
              <td class="px-4 py-4"><div class="font-semibold text-gray-900 dark:text-white">{{ $rewardName }}</div><div class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $spin->reward_type)) }}</div></td>
              <td class="px-4 py-4">
                @if($grantId)
                  <div class="font-semibold text-gray-900 dark:text-white">Grant #{{ $grantId }}</div><div class="text-xs text-gray-500">{{ number_format((int) $spin->reward_duration_hours) }} hours</div>
                @elseif(in_array($spin->reward_type, ['entry_pack', 'subscription'], true))
                  <x-ui.badge color="error">Missing grant</x-ui.badge>
                @else
                  <span class="text-gray-400">—</span>
                @endif
              </td>
              <td class="px-4 py-4">{{ optional($spin->spun_for_date)->toDateString() }}</td>
              <td class="px-4 py-4">{{ optional($spin->created_at)->format('d M Y, H:i:s') }}</td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900"><td colspan="7" class="px-4 py-8 text-center text-gray-500">No spins match the selected filters.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($auditSpins)
      <div class="mt-4 flex justify-end">{{ $auditSpins->links() }}</div>
    @endif
  </x-common.component-card>

  <x-common.component-card title="Create Segment" desc="Each active segment is a real result. A 0 Coin segment is allowed and never grants Try Again.">
    <form method="post" action="{{ route('admin.games.fortune-wheel.segments.store') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-4" data-fortune-segment-form>
      @csrf
      @include('admin.games.fortune-wheel.segment-form', ['segment' => null])
      <div class="flex justify-end md:col-span-2 xl:col-span-4">
        <x-ui.button type="submit" size="sm">Add Segment</x-ui.button>
      </div>
    </form>
  </x-common.component-card>

  <x-common.component-card title="Wheel Segments" desc="Cards show actual runtime eligibility and approximate selection probability. Changes apply to future spins.">
    <div class="grid gap-4 xl:grid-cols-2">
      @forelse($segments as $segment)
        @php
          $isEligible = $eligibleSegmentIds->contains((int) $segment->id);
          $probability = $isEligible && $eligibleWeight > 0 ? ((int) $segment->weight / $eligibleWeight) * 100 : 0;
          $rewardDescription = match($segment->reward_type) {
            'coins' => number_format((int) $segment->reward_value_coins).' coins',
            'entry_pack' => ($segment->entryPack?->name ?? 'Entry pack missing').' for '.number_format((int) $segment->reward_duration_hours).'h',
            'subscription' => ($segment->subscriptionPlan?->name ?? 'Subscription missing').' for '.number_format((int) $segment->reward_duration_hours).'h',
            default => 'Unknown reward',
          };
        @endphp
        <article class="overflow-hidden rounded-2xl border {{ $isEligible ? 'border-gray-200 dark:border-gray-800' : 'border-warning-200 dark:border-warning-500/30' }} bg-white dark:bg-gray-900">
          <div class="flex flex-col gap-3 border-b border-gray-100 p-5 sm:flex-row sm:items-start sm:justify-between dark:border-gray-800">
            <div class="flex min-w-0 items-start gap-3">
              <span class="mt-1 h-10 w-10 shrink-0 rounded-xl border border-black/10 shadow-sm" style="background-color: {{ $segment->color ?: '#7C3AED' }}"></span>
              <div class="min-w-0">
                <h3 class="truncate font-semibold text-gray-900 dark:text-white">{{ $segment->label }}</h3>
                <p class="mt-1 text-xs text-gray-500">#{{ $segment->id }} · {{ ucfirst(str_replace('_', ' ', $segment->reward_type)) }} · {{ $rewardDescription }}</p>
              </div>
            </div>
            <div class="flex shrink-0 flex-wrap items-center gap-2">
              <x-ui.badge :color="$segment->is_active ? 'success' : 'dark'">{{ $segment->is_active ? 'Active' : 'Inactive' }}</x-ui.badge>
              <x-ui.badge :color="$isEligible ? 'brand' : 'warning'">{{ $isEligible ? number_format($probability, 2).'% chance' : 'Not selectable' }}</x-ui.badge>
            </div>
          </div>

          @if($segment->is_active && !$isEligible)
            <div class="border-b border-warning-200 bg-warning-50 px-5 py-3 text-xs font-medium text-warning-800 dark:border-warning-500/20 dark:bg-warning-500/10 dark:text-warning-300">
              This active segment is excluded from spins because its linked catalog reward is missing or inactive.
            </div>
          @endif

          <form id="segment-{{ $segment->id }}" method="post" action="{{ route('admin.games.fortune-wheel.segments.update', $segment) }}" class="grid gap-4 p-5 md:grid-cols-2" data-fortune-segment-form>
            @csrf
            @method('PUT')
            @include('admin.games.fortune-wheel.segment-form', ['segment' => $segment])
          </form>
          <div class="flex items-center justify-between gap-3 border-t border-gray-100 bg-gray-50 px-5 py-4 dark:border-gray-800 dark:bg-white/[0.02]">
            <div class="text-xs text-gray-500">Weight {{ number_format((int) $segment->weight) }} · Order {{ number_format((int) $segment->sort_order) }}</div>
            <div class="flex gap-2">
              <form method="post" action="{{ route('admin.games.fortune-wheel.segments.destroy', $segment) }}" onsubmit="return confirm('Delete this wheel segment?')">
                @csrf
                @method('DELETE')
                <x-ui.button type="submit" variant="outline" size="sm">Delete</x-ui.button>
              </form>
              <x-ui.button type="submit" form="segment-{{ $segment->id }}" size="sm">Save Changes</x-ui.button>
            </div>
          </div>
        </article>
      @empty
        <div class="rounded-2xl border border-dashed border-gray-300 px-6 py-12 text-center text-gray-500 dark:border-gray-700 xl:col-span-2">No Fortune Wheel segments configured yet.</div>
      @endforelse
    </div>
  </x-common.component-card>

</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const auditPeriod = document.querySelector('[data-fortune-audit-period]');
    document.querySelectorAll('[data-fortune-audit-date]').forEach((input) => {
      input.addEventListener('change', () => {
        if (auditPeriod) auditPeriod.value = 'custom';
      });
    });

    document.querySelectorAll('[data-fortune-segment-form]').forEach((form) => {
      const rewardType = form.querySelector('[data-fortune-reward-type]');
      const colorPicker = form.querySelector('[data-fortune-color-picker]');
      const colorText = form.querySelector('[data-fortune-color-text]');

      const syncRewardFields = () => {
        const type = rewardType?.value ?? 'coins';
        form.querySelectorAll('[data-fortune-field]').forEach((wrapper) => {
          const field = wrapper.dataset.fortuneField;
          const visible = field === type || (field === 'duration' && ['entry_pack', 'subscription'].includes(type));
          wrapper.classList.toggle('hidden', !visible);
          wrapper.querySelectorAll('input, select, textarea').forEach((input) => input.disabled = !visible);
        });
      };

      rewardType?.addEventListener('change', syncRewardFields);
      colorPicker?.addEventListener('input', () => colorText.value = colorPicker.value.toUpperCase());
      colorText?.addEventListener('input', () => {
        if (/^#[0-9A-Fa-f]{6}$/.test(colorText.value)) colorPicker.value = colorText.value;
      });
      syncRewardFields();
    });
  });
</script>
@endpush
