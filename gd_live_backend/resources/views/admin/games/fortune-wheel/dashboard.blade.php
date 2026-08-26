@extends('layouts.admin-tailadmin')

@section('title', 'Fortune Wheel')

@php
  $segments = collect($payload['segments'] ?? []);
  $recentSpins = collect($payload['recent_spins'] ?? []);
  $summary = $payload['summary'] ?? [];
  $settings = $payload['settings'] ?? [];
  $expected = $payload['expected_value'] ?? [];
  $entryPacks = collect($payload['entry_packs'] ?? []);
  $subscriptionPlans = collect($payload['subscription_plans'] ?? []);
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
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Configure weighted rewards for daily free spins and paid spins. Rewards can be coins, timed entry packs, or timed subscriptions.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.settings.games.edit', ['game' => 'fortune_wheel']) }}">Game Settings</x-ui.button>
        </div>
      </div>
    </div>
  </section>

  <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <x-admin.stat-card label="Status" :value="!empty($settings['enabled']) ? 'Enabled' : 'Disabled'" :meta="!empty($settings['paid_spins_enabled']) ? 'Paid spins enabled' : 'Paid spins disabled'" tone="success" />
    <x-admin.stat-card label="Spins Today" :value="number_format((int) ($summary['spins_today'] ?? 0))" :meta="number_format((int) ($summary['free_spins_today'] ?? 0)) . ' free, ' . number_format((int) ($summary['paid_spins_today'] ?? 0)) . ' paid'" tone="warning" />
    <x-admin.stat-card label="Coin Flow Today" :value="number_format((int) ($summary['coins_collected_today'] ?? 0))" :meta="'Rewarded ' . number_format((int) ($summary['coins_rewarded_today'] ?? 0)) . ' coins'" tone="dark" />
    <x-admin.stat-card label="Expected Margin" :value="number_format((float) ($expected['estimated_coin_margin'] ?? 0), 2)" :meta="'Avg coin reward ' . number_format((float) ($expected['average_coin_reward'] ?? 0), 2)" />
  </section>

  <x-common.component-card title="Create Segment" desc="Every active segment is a real result. Use 0 Coins when you want a zero-value reward without showing Try Again.">
    <form method="post" action="{{ route('admin.games.fortune-wheel.segments.store') }}" class="grid gap-4 xl:grid-cols-4">
      @csrf
      @include('admin.games.fortune-wheel.segment-form', ['segment' => null])
      <div class="xl:col-span-4 flex justify-end">
        <x-ui.button type="submit" size="sm">Add Segment</x-ui.button>
      </div>
    </form>
  </x-common.component-card>

  <x-common.component-card title="Wheel Segments" desc="Weight controls probability. Higher weight means this reward is selected more often.">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
        <thead>
          <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
            <th class="px-4 py-3">Segment</th>
            <th class="px-4 py-3">Reward</th>
            <th class="px-4 py-3">Weight</th>
            <th class="px-4 py-3">State</th>
            <th class="px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($segments as $segment)
            <tr class="bg-white align-top dark:bg-gray-900">
              <td class="px-4 py-4">
                <form id="segment-{{ $segment->id }}" method="post" action="{{ route('admin.games.fortune-wheel.segments.update', $segment) }}" class="grid min-w-[520px] gap-3 md:grid-cols-2">
                  @csrf
                  @method('PUT')
                  @include('admin.games.fortune-wheel.segment-form', ['segment' => $segment])
                </form>
              </td>
              <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                <div class="font-semibold">{{ ucfirst(str_replace('_', ' ', $segment->reward_type)) }}</div>
                <div class="mt-1 text-xs text-gray-500">
                  @if($segment->reward_type === 'coins')
                    {{ number_format((int) $segment->reward_value_coins) }} coins
                  @elseif($segment->reward_type === 'entry_pack')
                    {{ $segment->entryPack?->name ?? 'Entry pack missing' }} for {{ $segment->reward_duration_hours }}h
                  @else
                    {{ $segment->subscriptionPlan?->name ?? 'Subscription missing' }} for {{ $segment->reward_duration_hours }}h
                  @endif
                </div>
              </td>
              <td class="px-4 py-4">{{ number_format((int) $segment->weight) }}</td>
              <td class="px-4 py-4">
                <x-ui.badge :color="$segment->is_active ? 'success' : 'dark'">{{ $segment->is_active ? 'Active' : 'Inactive' }}</x-ui.badge>
              </td>
              <td class="px-4 py-4 text-right">
                <div class="flex justify-end gap-2">
                  <x-ui.button type="submit" form="segment-{{ $segment->id }}" size="sm">Save</x-ui.button>
                  <form method="post" action="{{ route('admin.games.fortune-wheel.segments.destroy', $segment) }}" onsubmit="return confirm('Delete this wheel segment?')">
                    @csrf
                    @method('DELETE')
                    <x-ui.button type="submit" variant="outline" size="sm">Delete</x-ui.button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900"><td colspan="5" class="px-4 py-8 text-center text-gray-500">No Fortune Wheel segments configured yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </x-common.component-card>

  <x-common.component-card title="Recent Spins" desc="Latest gameplay rows used for free-spin limits and reward references.">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-800">
        <thead>
          <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
            <th class="px-4 py-3">User</th>
            <th class="px-4 py-3">Type</th>
            <th class="px-4 py-3">Cost</th>
            <th class="px-4 py-3">Reward</th>
            <th class="px-4 py-3">Date</th>
            <th class="px-4 py-3">Time</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($recentSpins as $spin)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-4">
                <div class="font-semibold text-gray-900 dark:text-white">{{ $spin->user?->name ?? 'User #'.$spin->user_id }}</div>
                <div class="text-xs text-gray-500">{{ $spin->user?->email }}</div>
              </td>
              <td class="px-4 py-4">{{ ucfirst($spin->spin_type) }}</td>
              <td class="px-4 py-4">{{ number_format((int) $spin->spin_cost_coins) }}</td>
              <td class="px-4 py-4">
                <div class="font-semibold">{{ $spin->segment?->label ?? ucfirst(str_replace('_', ' ', $spin->reward_type)) }}</div>
                <div class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $spin->reward_type)) }}</div>
              </td>
              <td class="px-4 py-4">{{ optional($spin->spun_for_date)->toDateString() }}</td>
              <td class="px-4 py-4">{{ optional($spin->created_at)->format('d M H:i:s') }}</td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900"><td colspan="6" class="px-4 py-8 text-center text-gray-500">No spins yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </x-common.component-card>
</div>
@endsection
