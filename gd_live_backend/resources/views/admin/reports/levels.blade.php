@extends('layouts.admin-tailadmin')
@section('title', 'User Levels')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
@endphp

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-2xl">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">User Levels</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Review the configured level ladder, top spenders, and the most recent level-up history across the user base.</p>
        </div>
        <form method="get" class="grid gap-3 md:grid-cols-[220px_220px_auto]">
          <input type="text" name="q" value="{{ request('q') }}" class="{{ $inputClass }}" placeholder="Name or email">
          <select name="level_id" class="{{ $inputClass }}">
            <option value="">Any level</option>
            @foreach($levels as $level)
              <option value="{{ $level->id }}" @selected((int) request('level_id') === $level->id)>L{{ $level->level }} · {{ $level->title }}</option>
            @endforeach
          </select>
          <div class="flex items-center gap-3">
            <x-ui.button type="submit" size="sm">Apply</x-ui.button>
            <x-ui.button variant="outline" size="sm" href="{{ route('admin.reports.levels') }}">Reset</x-ui.button>
          </div>
        </form>
      </div>
    </x-slot:header>

    <div class="grid gap-6 xl:grid-cols-[340px_minmax(0,1fr)]">
      <div class="space-y-3">
        @foreach($levels as $level)
          <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between gap-3">
              <div>
                <div class="font-semibold text-gray-900 dark:text-white">Level {{ $level->level }} · {{ $level->title }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Min spend: {{ number_format($level->min_spend_coins) }} coins</div>
              </div>
              <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold text-white" style="background: {{ $level->badge_color ?: '#6b7280' }}">{{ $distribution[$level->id] ?? 0 }} users</span>
            </div>
          </div>
        @endforeach
      </div>

      <div class="space-y-6">
        <x-common.component-card title="Top Spenders" padding="compact">
          <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
              <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left text-gray-500">User</th><th class="px-4 py-3 text-left text-gray-500">Level</th><th class="px-4 py-3 text-left text-gray-500">Lifetime Spend</th><th class="px-4 py-3 text-right text-gray-500">Action</th></tr></thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-800">@forelse($topSpenders as $user)<tr class="bg-white dark:bg-gray-900"><td class="px-4 py-3"><div class="font-semibold text-gray-900 dark:text-white">{{ $user->name }}</div><div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div></td><td class="px-4 py-3">@if($user->level)<span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold text-white" style="background: {{ $user->level->badge_color ?: '#6b7280' }}">L{{ $user->level->level }} · {{ $user->level->title }}</span>@else<span class="text-gray-500 dark:text-gray-400">Unassigned</span>@endif</td><td class="px-4 py-3 font-semibold">{{ number_format($user->lifetime_spend_coins) }}</td><td class="px-4 py-3 text-right"><x-ui.button variant="outline" size="sm" href="{{ route('admin.wallets.show', $user) }}">Open Wallet</x-ui.button></td></tr>@empty<tr class="bg-white dark:bg-gray-900"><td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No users found.</td></tr>@endforelse</tbody>
            </table>
          </div>
          <x-slot:footer><div class="flex justify-end">{{ $topSpenders->links() }}</div></x-slot:footer>
        </x-common.component-card>

        <x-common.component-card title="Recent Level-Up History" padding="compact">
          <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
              <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left text-gray-500">User</th><th class="px-4 py-3 text-left text-gray-500">From</th><th class="px-4 py-3 text-left text-gray-500">To</th><th class="px-4 py-3 text-left text-gray-500">Spend</th><th class="px-4 py-3 text-left text-gray-500">Trigger</th><th class="px-4 py-3 text-left text-gray-500">When</th></tr></thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-800">@forelse($history as $row)<tr class="bg-white dark:bg-gray-900"><td class="px-4 py-3">{{ $row->user?->name ?? 'User #'.$row->user_id }}</td><td class="px-4 py-3">{{ $row->oldLevel?->title ? 'L'.$row->oldLevel->level.' · '.$row->oldLevel->title : '—' }}</td><td class="px-4 py-3">{{ 'L'.$row->newLevel->level.' · '.$row->newLevel->title }}</td><td class="px-4 py-3">{{ number_format($row->lifetime_spend_coins) }}</td><td class="px-4 py-3">{{ $row->triggered_by_transaction_id ? 'Transaction #'.$row->triggered_by_transaction_id : 'Recalculate' }}</td><td class="px-4 py-3">{{ $row->created_at?->format('d M Y, H:i') }}</td></tr>@empty<tr class="bg-white dark:bg-gray-900"><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No level history yet.</td></tr>@endforelse</tbody>
            </table>
          </div>
        </x-common.component-card>
      </div>
    </div>
  </x-common.component-card>
</div>
@endsection
