@extends('layouts.admin-tailadmin')
@section('title', 'Recharge Plans')

@php
  $planCollection = $plans instanceof \Illuminate\Support\Collection ? $plans : collect($plans);
  $activeCount = $planCollection->where('is_active', true)->count();
  $avgCoins = $planCollection->count() ? round($planCollection->avg('total_coins')) : 0;
@endphp

@section('page_actions')
  <x-ui.button size="sm" href="{{ route('admin.recharge-plans.create') }}">Create Recharge Plan</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="dark">Recharge</x-ui.badge>
            <x-ui.badge color="brand">Wallet Packs</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Recharge Plans</h2>
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Create, sort, and activate the recharge packs exposed to the wallet top-up flow in the app.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-3">
          <x-admin.stat-card label="Plans" :value="number_format($planCollection->count())" meta="Configured recharge packs" />
          <x-admin.stat-card label="Active" :value="number_format($activeCount)" meta="Currently available to users" tone="success" />
          <x-admin.stat-card label="Average Coins" :value="number_format($avgCoins)" meta="Average total coin value" tone="dark" />
        </div>
      </div>
    </div>
  </section>

  @if(session('ok'))
    <x-ui.alert type="success">{{ session('ok') }}</x-ui.alert>
  @endif
  @if(session('error'))
    <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
  @endif

  <x-common.component-card title="Configured Recharge Plans" desc="Manage pricing, bonus coins, display order, and active state.">
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Title</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Amount</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Coins</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Bonus</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Total</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Sort</th>
            <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($plans as $plan)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $plan->title }}</td>
              <td class="px-4 py-3">₹{{ number_format((float) $plan->amount_rupees, 2) }}</td>
              <td class="px-4 py-3">{{ number_format($plan->coins) }}</td>
              <td class="px-4 py-3">{{ number_format($plan->bonus_coins) }}</td>
              <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ number_format($plan->total_coins) }}</td>
              <td class="px-4 py-3"><x-ui.badge :color="$plan->is_active ? 'success' : 'dark'">{{ $plan->is_active ? 'Active' : 'Inactive' }}</x-ui.badge></td>
              <td class="px-4 py-3">{{ $plan->sort_order }}</td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap justify-end gap-2">
                  <x-ui.button variant="outline" size="sm" href="{{ route('admin.recharge-plans.edit', $plan) }}">Edit</x-ui.button>
                  <form method="post" action="{{ route('admin.recharge-plans.destroy', $plan) }}" onsubmit="return confirm('Delete this recharge plan?');">
                    @csrf
                    @method('DELETE')
                    <x-ui.button variant="danger" size="sm" type="submit">Delete</x-ui.button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No recharge plans configured.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </x-common.component-card>
</div>
@endsection
