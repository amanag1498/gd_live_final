@extends('layouts.admin-tailadmin')
@section('title', 'Weekly Agency Payout Reports')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('page_actions')
  <x-ui.button size="sm" href="{{ route('admin.reports.agencies') }}">Agency Reports</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  @if(session('status'))
    <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
  @endif
  @if($errors->any())
    <x-ui.alert variant="error">
      <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </x-ui.alert>
  @endif

  <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
    <x-common.component-card>
      <x-slot:header>
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Weekly Agency Payout Reports</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Review weekly settlement drafts, publication status, and payable totals before releasing reports to agencies.</p>
        </div>
      </x-slot:header>

      <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <x-admin.stat-card label="Reports" :value="number_format($summary['reports'])" tone="brand" />
        <x-admin.stat-card label="Gross Earnings" :value="number_format($summary['gross_earnings'])" tone="dark" />
        <x-admin.stat-card label="Agency Commission" :value="number_format($summary['agency_commission'])" tone="warning" />
        <x-admin.stat-card label="Final Payable" :value="number_format($summary['final_payable'])" tone="success" />
        <x-admin.stat-card label="Published" :value="number_format($summary['published'])" />
        <x-admin.stat-card label="Paid" :value="number_format($summary['paid'])" tone="danger" />
      </section>
    </x-common.component-card>

    <x-common.component-card title="Generate Reports" desc="Create or regenerate weekly payout drafts for a selected agency range.">
      <form method="post" action="{{ route('admin.agency-payout-reports.generate') }}" class="space-y-4">
        @csrf
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Start</label>
            <input type="date" name="start" class="{{ $inputClass }}" value="{{ request('date_from') }}">
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">End</label>
            <input type="date" name="end" class="{{ $inputClass }}" value="{{ request('date_to') }}">
          </div>
        </div>
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Agency</label>
          <select name="agency_id" class="{{ $inputClass }}">
            <option value="">All Agencies</option>
            @foreach($agencies as $agency)
              <option value="{{ $agency->id }}" @selected((string) request('agency_id') === (string) $agency->id)>{{ $agency->name }}</option>
            @endforeach
          </select>
        </div>
        <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 dark:border-gray-800 dark:bg-gray-950/60 dark:text-gray-300">
          <input type="checkbox" class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" id="force-regenerate" name="force" value="1">
          <span>Force regenerate if an unpaid draft already exists</span>
        </label>
        <x-ui.button type="submit" size="sm">Generate Reports</x-ui.button>
      </form>
    </x-common.component-card>
  </div>

  <x-common.component-card>
    <x-slot:header>
      <form method="get" class="grid gap-3 md:grid-cols-2 xl:grid-cols-[220px_180px_160px_160px_160px_auto]">
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Agency</label>
          <select name="agency_id" class="{{ $inputClass }}">
            <option value="">All</option>
            @foreach($agencies as $agency)
              <option value="{{ $agency->id }}" @selected((string) request('agency_id') === (string) $agency->id)>{{ $agency->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
          <select name="status" class="{{ $inputClass }}">
            <option value="">All</option>
            @foreach($statuses as $status)
              <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Week Start</label>
          <input type="date" name="week_start" class="{{ $inputClass }}" value="{{ request('week_start') }}">
        </div>
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">From</label>
          <input type="date" name="date_from" class="{{ $inputClass }}" value="{{ request('date_from') }}">
        </div>
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">To</label>
          <input type="date" name="date_to" class="{{ $inputClass }}" value="{{ request('date_to') }}">
        </div>
        <div class="flex items-end gap-3">
          <x-ui.button type="submit" size="sm">Apply Filters</x-ui.button>
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.agency-payout-reports.index') }}">Reset</x-ui.button>
        </div>
      </form>
    </x-slot:header>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Agency</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Week</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Hosts</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Gross</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Final Payable</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Visibility</th>
            <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($reports as $report)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-4">
                <div class="font-semibold text-gray-900 dark:text-white">{{ $report->agency?->name ?? '—' }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $report->agency?->owner?->name ?? '—' }}</div>
              </td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ optional($report->period_start)->format('d M Y') }} - {{ optional($report->period_end)->format('d M Y') }}</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
                {{ number_format($report->total_hosts) }} total
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ number_format($report->active_hosts_count) }} active</div>
              </td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
                {{ number_format($report->gross_earnings) }}
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Comm: {{ number_format($report->agency_commission) }}</div>
              </td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
                {{ number_format($report->final_payable) }}
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Deductions: {{ number_format($report->deductions) }}</div>
              </td>
              <td class="px-4 py-4"><x-ui.badge color="dark">{{ ucwords(str_replace('_', ' ', $report->status)) }}</x-ui.badge></td>
              <td class="px-4 py-4">
                @if($report->published_at)
                  <x-ui.badge color="success">Published</x-ui.badge>
                  <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ optional($report->published_at)->format('d M Y H:i') }}</div>
                @else
                  <x-ui.badge color="warning">Draft Only</x-ui.badge>
                @endif
              </td>
              <td class="px-4 py-4 text-right">
                <div class="flex flex-wrap justify-end gap-2">
                  <x-ui.button variant="outline" size="sm" href="{{ route('admin.agency-payout-reports.show', $report) }}">View</x-ui.button>
                  <x-ui.button variant="outline" size="sm" href="{{ route('admin.agency-payout-reports.export', $report) }}">PDF</x-ui.button>
                  @if($report->status === 'approved' && !$report->published_at)
                    <form method="post" action="{{ route('admin.agency-payout-reports.publish', $report) }}">
                      @csrf
                      <x-ui.button variant="secondary" size="sm" type="submit">Publish</x-ui.button>
                    </form>
                  @endif
                  @if($report->status === 'approved' && $report->published_at && $report->status !== 'paid')
                    <form method="post" action="{{ route('admin.agency-payout-reports.mark-paid', $report) }}">
                      @csrf
                      <x-ui.button variant="success" size="sm" type="submit">Mark Paid</x-ui.button>
                    </form>
                  @endif
                  @if($report->status !== 'paid')
                    <form method="post" action="{{ route('admin.agency-payout-reports.destroy', $report) }}" onsubmit="return confirm('Delete this payout report draft? This cannot be undone.');">
                      @csrf
                      @method('DELETE')
                      <x-ui.button variant="danger" size="sm" type="submit">Delete</x-ui.button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="8" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No payout reports found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <x-slot:footer>
      <div class="flex justify-end">
        {{ $reports->links() }}
      </div>
    </x-slot:footer>
  </x-common.component-card>
</div>
@endsection
