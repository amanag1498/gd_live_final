@extends('layouts.admin-tailadmin')
@section('title', 'Leaderboard Reports')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
@endphp

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Leaderboard Reports</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $rangeLabel }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.reports.leaderboards.export', array_merge(request()->query(), ['dataset' => 'users'])) }}">Users CSV</x-ui.button>
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.reports.leaderboards.export', array_merge(request()->query(), ['dataset' => 'hosts'])) }}">Hosts CSV</x-ui.button>
          <x-ui.button size="sm" href="{{ route('admin.reports.leaderboards.export', array_merge(request()->query(), ['dataset' => 'agencies'])) }}">Agencies CSV</x-ui.button>
        </div>
      </div>
    </x-slot:header>

    <form method="get" class="grid gap-3 md:grid-cols-2 xl:grid-cols-[150px_150px_150px_180px_180px_150px_100px_auto]">
      <input type="week" name="week" value="{{ $selectedWeek }}" class="{{ $inputClass }}">
      <input type="date" name="from" value="{{ $fromDate->format('Y-m-d') }}" class="{{ $inputClass }}">
      <input type="date" name="to" value="{{ $toDate->format('Y-m-d') }}" class="{{ $inputClass }}">
      <input type="text" name="user_q" value="{{ $userQuery }}" class="{{ $inputClass }}" placeholder="User search">
      <input type="text" name="host_q" value="{{ $hostQuery }}" class="{{ $inputClass }}" placeholder="Host search">
      <input type="text" name="agency_q" value="{{ $agencyQuery }}" class="{{ $inputClass }}" placeholder="Agency search">
      <input type="number" name="limit" min="1" max="200" value="{{ $limit }}" class="{{ $inputClass }}" placeholder="Top N">
      <div class="flex items-center gap-3">
        <x-ui.button type="submit" size="sm">Apply</x-ui.button>
        <x-ui.button variant="outline" size="sm" href="{{ route('admin.reports.leaderboards') }}">Reset</x-ui.button>
      </div>
    </form>
  </x-common.component-card>

  <div class="grid gap-6 xl:grid-cols-2">
    <x-common.component-card title="Weekly Top Hosts">
      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left text-gray-500">#</th><th class="px-4 py-3 text-left text-gray-500">Host</th><th class="px-4 py-3 text-left text-gray-500">Agency</th><th class="px-4 py-3 text-left text-gray-500">Gift</th><th class="px-4 py-3 text-left text-gray-500">Call</th><th class="px-4 py-3 text-left text-gray-500">Total</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">@forelse($hosts as $row)<tr class="bg-white dark:bg-gray-900"><td class="px-4 py-3 font-semibold">{{ $row['rank'] }}</td><td class="px-4 py-3"><div class="font-semibold text-gray-900 dark:text-white">{{ $row['name'] }}</div><div class="text-xs text-gray-500 dark:text-gray-400">Host #{{ $row['host_id'] }}</div></td><td class="px-4 py-3">{{ $row['agency_name'] }}</td><td class="px-4 py-3">{{ number_format($row['gift_coins']) }}</td><td class="px-4 py-3">{{ number_format($row['call_coins']) }}</td><td class="px-4 py-3 font-semibold">{{ number_format($row['total_coins']) }}</td></tr>@empty<tr class="bg-white dark:bg-gray-900"><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No host leaderboard data in this range.</td></tr>@endforelse</tbody>
        </table>
      </div>
    </x-common.component-card>

    <x-common.component-card title="Weekly Top Agencies">
      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left text-gray-500">#</th><th class="px-4 py-3 text-left text-gray-500">Agency</th><th class="px-4 py-3 text-left text-gray-500">Hosts</th><th class="px-4 py-3 text-left text-gray-500">Gift</th><th class="px-4 py-3 text-left text-gray-500">Call</th><th class="px-4 py-3 text-left text-gray-500">Total</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">@forelse($agencies as $row)<tr class="bg-white dark:bg-gray-900"><td class="px-4 py-3 font-semibold">{{ $row['rank'] }}</td><td class="px-4 py-3"><div class="font-semibold text-gray-900 dark:text-white">{{ $row['name'] }}</div><div class="text-xs text-gray-500 dark:text-gray-400">Agency #{{ $row['agency_id'] }}</div></td><td class="px-4 py-3">{{ number_format($row['host_count']) }}</td><td class="px-4 py-3">{{ number_format($row['gift_coins']) }}</td><td class="px-4 py-3">{{ number_format($row['call_coins']) }}</td><td class="px-4 py-3 font-semibold">{{ number_format($row['total_coins']) }}</td></tr>@empty<tr class="bg-white dark:bg-gray-900"><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No agency leaderboard data in this range.</td></tr>@endforelse</tbody>
        </table>
      </div>
    </x-common.component-card>
  </div>

  <x-common.component-card title="Weekly Top Users">
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left text-gray-500">#</th><th class="px-4 py-3 text-left text-gray-500">User</th><th class="px-4 py-3 text-left text-gray-500">Level</th><th class="px-4 py-3 text-left text-gray-500">Gift</th><th class="px-4 py-3 text-left text-gray-500">Call</th><th class="px-4 py-3 text-left text-gray-500">Subs</th><th class="px-4 py-3 text-left text-gray-500">Entry</th><th class="px-4 py-3 text-left text-gray-500">Total</th></tr></thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">@forelse($users as $row)<tr class="bg-white dark:bg-gray-900"><td class="px-4 py-3 font-semibold">{{ $row['rank'] }}</td><td class="px-4 py-3"><div class="font-semibold text-gray-900 dark:text-white">{{ $row['name'] }}</div><div class="text-xs text-gray-500 dark:text-gray-400">{{ $row['email'] ?: 'User #'.$row['id'] }}</div></td><td class="px-4 py-3">{{ $row['level'] ? 'L'.$row['level'] : '—' }}</td><td class="px-4 py-3">{{ number_format($row['gift_coins']) }}</td><td class="px-4 py-3">{{ number_format($row['call_coins']) }}</td><td class="px-4 py-3">{{ number_format($row['subscription_coins']) }}</td><td class="px-4 py-3">{{ number_format($row['entry_coins']) }}</td><td class="px-4 py-3 font-semibold">{{ number_format($row['total_coins']) }}</td></tr>@empty<tr class="bg-white dark:bg-gray-900"><td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No user leaderboard data in this range.</td></tr>@endforelse</tbody>
      </table>
    </div>
  </x-common.component-card>
</div>
@endsection
