@extends('layouts.admin-tailadmin')
@section('title', 'Agency Wallet Report')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
@endphp

@section('content')
<div class="space-y-6">
  <section class="grid gap-4 md:grid-cols-3">
    <x-admin.stat-card label="Rows" :value="number_format($summary['total_rows'] ?? 0)" tone="brand" />
    <x-admin.stat-card label="Total Loaded" :value="number_format($summary['total_loaded'] ?? 0)" tone="success" />
    <x-admin.stat-card label="Total Distributed" :value="number_format($summary['total_distributed'] ?? 0)" tone="warning" />
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div class="max-w-2xl">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Agency Wallet Transfers</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Audit fund loads, internal agency distributions, and user-directed coin transfers across the agency wallet system.</p>
        </div>
        <form method="get" class="grid gap-3 md:grid-cols-2 xl:grid-cols-[220px_180px_140px_150px_150px_auto]">
          <select name="agency_id" class="{{ $inputClass }}">
            <option value="">All agencies</option>
            @foreach($agencies as $agency)
              <option value="{{ $agency->id }}" @selected(($filters['agency_id'] ?? null) == $agency->id)>{{ $agency->name }}</option>
            @endforeach
          </select>
          <select name="direction" class="{{ $inputClass }}">
            <option value="">All directions</option>
            <option value="admin_to_agency" @selected(($filters['direction'] ?? null) === 'admin_to_agency')>Admin to Agency</option>
            <option value="agency_to_user" @selected(($filters['direction'] ?? null) === 'agency_to_user')>Agency to User</option>
          </select>
          <input type="number" name="target_user_id" value="{{ $filters['target_user_id'] ?? '' }}" class="{{ $inputClass }}" placeholder="User ID">
          <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="{{ $inputClass }}">
          <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="{{ $inputClass }}">
          <div class="flex items-center gap-3">
            <x-ui.button type="submit" size="sm">Apply</x-ui.button>
            <x-ui.button variant="outline" size="sm" href="{{ route('admin.reports.agency-wallets.index') }}">Reset</x-ui.button>
          </div>
        </form>
      </div>
    </x-slot:header>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Transfer</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Agency</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Direction</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Coins</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Actor</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Note</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Time</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($transfers as $transfer)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-4 font-semibold text-gray-900 dark:text-white">#{{ $transfer->id }}</td>
              <td class="px-4 py-4">
                <div class="font-medium text-gray-900 dark:text-white">{{ $transfer->agency?->name ?? '—' }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">#{{ $transfer->agency_id }}</div>
              </td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ str_replace('_', ' ', ucfirst($transfer->direction)) }}</td>
              <td class="px-4 py-4 text-gray-900 dark:text-white">{{ number_format($transfer->coins) }}</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
                @if($transfer->admin)
                  {{ $transfer->admin->name }} <span class="text-xs text-gray-500 dark:text-gray-400">(Admin)</span>
                @elseif($transfer->agencyUser)
                  {{ $transfer->agencyUser->name }} <span class="text-xs text-gray-500 dark:text-gray-400">(Agency)</span>
                @else
                  —
                @endif
                @if($transfer->targetUser)
                  <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Target: {{ $transfer->targetUser->name }} (#{{ $transfer->targetUser->id }})</div>
                @endif
              </td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $transfer->note ?: '—' }}</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ optional($transfer->created_at)->format('d M Y, h:i A') }}</td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900"><td colspan="7" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No agency wallet transfers found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <x-slot:footer>
      <div class="flex justify-end">{{ $transfers->links() }}</div>
    </x-slot:footer>
  </x-common.component-card>
</div>
@endsection
