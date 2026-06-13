@extends('layouts.admin-tailadmin')
@section('title', 'Host Reports')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
@endphp

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ route('admin.reports.hosts.csv', request()->query()) }}">Export CSV</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div class="max-w-2xl">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Host Performance Reports</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track room output, call spend, gift activity, PK performance, and earned payout totals across daily or weekly slices.</p>
        </div>
        <form class="grid gap-3 md:grid-cols-2 xl:grid-cols-[220px_160px_150px_150px_auto]" method="get">
          <select name="host_id" class="{{ $inputClass }}">
            <option value="">All hosts</option>
            @foreach($hosts as $h)
              <option value="{{ $h->id }}" @selected($hostId == $h->id)>{{ $h->user?->name }} (ID {{ $h->id }})</option>
            @endforeach
          </select>
          <select name="range" class="{{ $inputClass }}">
            <option value="daily" @selected($range==='daily')>Daily</option>
            <option value="weekly" @selected($range==='weekly')>Weekly</option>
          </select>
          <input type="date" name="from" class="{{ $inputClass }}" value="{{ $from->format('Y-m-d') }}">
          <input type="date" name="to" class="{{ $inputClass }}" value="{{ $to->format('Y-m-d') }}">
          <div class="flex items-center gap-3">
            <x-ui.button type="submit" size="sm">Apply</x-ui.button>
            <x-ui.button variant="outline" size="sm" href="{{ route('admin.reports.hosts') }}">Reset</x-ui.button>
          </div>
        </form>
      </div>
    </x-slot:header>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">{{ $range==='weekly' ? 'Week' : 'Date' }}</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Host</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Rooms</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Participants</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Call Coins</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Gift / PK</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Gross</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Host Payable</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Agency Payable</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
        @forelse($rows as $r)
          @php($reportHost = $hosts->firstWhere('id', $r['host_id']))
          <tr class="bg-white dark:bg-gray-900">
            <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $range==='weekly' ? \Carbon\Carbon::parse($r['week_start'])->format('d M Y') : \Carbon\Carbon::parse($r['date'])->format('d M Y') }}</td>
            <td class="px-4 py-4">
              <a href="{{ route('admin.reports.hosts.show', ['host' => $r['host_id'], 'from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')]) }}" class="font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-300">
                {{ $reportHost?->user?->name ?? 'Host #'.$r['host_id'] }}
              </a>
              <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $reportHost?->agency?->name ?? 'Independent' }}</div>
            </td>
            <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
              {{ $r['rooms'] }}
              <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $r['duration_min'] }} min</div>
            </td>
            <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
              {{ $r['participants_total'] }}
              <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $r['participants_unique'] }} unique</div>
            </td>
            <td class="px-4 py-4 text-gray-900 dark:text-white">{{ number_format($r['call_coins']) }}</td>
            <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
              {{ number_format($r['gift_coins']) }}
              <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">PK {{ number_format($r['pk_coins']) }} · {{ number_format($r['pk_events']) }} events</div>
            </td>
            <td class="px-4 py-4 font-semibold text-gray-900 dark:text-white">{{ number_format($r['gross_coins']) }}</td>
            <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
              {{ number_format($r['host_payable']) }}
            </td>
            <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ number_format($r['agency_payable']) }}</td>
          </tr>
        @empty
          <tr class="bg-white dark:bg-gray-900"><td colspan="9" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No host data in this range.</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </x-common.component-card>
</div>
@endsection
