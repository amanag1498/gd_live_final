@extends('layouts.admin-tailadmin')
@section('title', 'Agency Payout Report #' . $report->id)

@section('content')
@php
  $locked = $report->published_at || $report->status === 'paid';
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
@endphp

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

  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $report->agency?->name ?? 'Agency' }} · Report #{{ $report->id }}</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            {{ optional($report->period_start)->format('d M Y H:i') }} to {{ optional($report->period_end)->format('d M Y H:i') }} ·
            Status: {{ ucwords(str_replace('_', ' ', $report->status)) }} ·
            Agency visibility: {{ $report->published_at ? 'Published' : 'Draft only' }}
          </p>
        </div>
        <div class="flex flex-wrap gap-2">
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.agency-payout-reports.index') }}">Back</x-ui.button>
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.agencies.dashboard', $report->agency_id) }}">Open Agency Dashboard</x-ui.button>
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.agency-payout-reports.export', $report) }}">Download PDF</x-ui.button>
        </div>
      </div>
    </x-slot:header>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <x-admin.stat-card label="Total Hosts" :value="number_format($report->total_hosts)" />
      <x-admin.stat-card label="Active Hosts" :value="number_format($report->active_hosts_count)" tone="brand" />
      <x-admin.stat-card label="Total Coins" :value="number_format($report->total_coins)" tone="dark" />
      <x-admin.stat-card label="Final Payable" :value="number_format($report->final_payable)" tone="success" />
      <x-admin.stat-card label="Video Room Timing" :value="number_format($report->total_video_room_minutes) . ' min'" />
      <x-admin.stat-card label="Video Room Gifts" :value="number_format($report->total_video_gift_coins)" />
      <x-admin.stat-card label="PK Gifts" :value="number_format($report->total_pk_gift_coins)" tone="warning" />
      <x-admin.stat-card label="Video Calls" :value="number_format($report->total_video_call_coins)" :meta="number_format($report->total_video_call_minutes) . ' min'" />
      <x-admin.stat-card label="Bonus Coins" :value="number_format($report->total_bonus_coins)" />
      <x-admin.stat-card label="Host Payout INR" :value="number_format($report->total_host_payout_inr, 2)" />
      <x-admin.stat-card label="Agency Commission INR" :value="number_format($report->total_agency_commission_inr, 2)" />
      <x-admin.stat-card label="Total INR" :value="number_format($report->total_inr, 2)" tone="success" />
    </section>
  </x-common.component-card>

  <div class="grid gap-6 xl:grid-cols-2">
    <x-common.component-card title="Review" desc="Keep deductions and remarks at report level before approval.">
      <form method="post" action="{{ route('admin.agency-payout-reports.review', $report) }}" class="space-y-4">
        @csrf
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Deductions</label>
          <input type="number" min="0" name="deductions" class="{{ $inputClass }}" value="{{ old('deductions', $report->deductions) }}">
        </div>
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Admin Remarks</label>
          <textarea name="admin_remarks" rows="4" class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white">{{ old('admin_remarks', $report->admin_remarks) }}</textarea>
        </div>
        <x-ui.button type="submit" size="sm" @disabled(!in_array($report->status, ['generated', 'pending_review']))>Save Pending Review</x-ui.button>
      </form>
    </x-common.component-card>

    <x-common.component-card title="Actions" desc="Publish only after row-level numbers are finalized.">
      <div class="space-y-4">
        <form method="post" action="{{ route('admin.agency-payout-reports.approve', $report) }}" class="grid gap-4">
          @csrf
          <input type="hidden" name="deductions" value="{{ $report->deductions }}">
          <input type="text" name="admin_remarks" class="{{ $inputClass }}" value="{{ $report->admin_remarks }}" placeholder="Approval remarks">
          <x-ui.button type="submit" size="sm" @disabled(!in_array($report->status, ['generated', 'pending_review']))>Approve Report</x-ui.button>
        </form>

        <form method="post" action="{{ route('admin.agency-payout-reports.publish', $report) }}" class="grid gap-4">
          @csrf
          <input type="text" name="admin_remarks" class="{{ $inputClass }}" value="{{ $report->admin_remarks }}" placeholder="Publish remarks">
          <x-ui.button type="submit" variant="secondary" size="sm" @disabled($report->status !== 'approved' || $report->published_at)>Publish To Agency</x-ui.button>
        </form>

        <form method="post" action="{{ route('admin.agency-payout-reports.mark-paid', $report) }}" class="grid gap-4">
          @csrf
          <input type="text" name="admin_remarks" class="{{ $inputClass }}" value="{{ $report->admin_remarks }}" placeholder="Paid remarks">
          <x-ui.button type="submit" variant="success" size="sm" @disabled($report->status !== 'approved' || !$report->published_at || $report->status === 'paid')>Mark Paid</x-ui.button>
        </form>

        <form method="post" action="{{ route('admin.agency-payout-reports.reject', $report) }}" class="grid gap-4">
          @csrf
          <textarea name="admin_remarks" rows="3" class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="Rejection reason" @disabled(!in_array($report->status, ['generated', 'pending_review']))></textarea>
          <x-ui.button type="submit" variant="danger" size="sm" @disabled(!in_array($report->status, ['generated', 'pending_review']))>Reject Report</x-ui.button>
        </form>
      </div>
    </x-common.component-card>
  </div>

  <x-common.component-card title="Host Settlement Grid" desc="Format matches the desktop GD payout workflow with only the required fields.">
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-[1500px] divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="sticky left-0 z-10 bg-gray-50 px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:bg-gray-950/60 dark:text-gray-400">Host</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Total Video Room Timing</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Total Video Room Gifts</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Total PK Gifts</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Video Calls Coins</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Video Calls Min</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Bonus Coins</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Total Coins</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Host Payout INR</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Agency Commission INR</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Total INR</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Admin Notes</th>
            <th class="sticky right-0 z-10 bg-gray-50 px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:bg-gray-950/60 dark:text-gray-400">Save</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($report->items as $item)
            @php($formId = 'payout-row-' . $item->id)
            <tr class="bg-white dark:bg-gray-900">
              <td class="sticky left-0 z-10 bg-white px-4 py-4 dark:bg-gray-900">
                <form id="{{ $formId }}" method="post" action="{{ route('admin.agency-payout-reports.items.update', [$report, $item]) }}">
                  @csrf
                </form>
                <div class="font-semibold text-gray-900 dark:text-white">{{ $item->host?->user?->name ?? $item->host?->stage_name ?? '—' }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $item->host?->stage_name ?? '—' }}</div>
              </td>
              <td class="px-4 py-4"><input type="number" min="0" name="video_room_minutes" form="{{ $formId }}" class="{{ $inputClass }}" value="{{ old('video_room_minutes', $item->video_room_minutes) }}" @disabled($locked)></td>
              <td class="px-4 py-4"><input type="number" min="0" name="video_gift_coins" form="{{ $formId }}" class="{{ $inputClass }}" value="{{ old('video_gift_coins', $item->video_gift_coins) }}" @disabled($locked)></td>
              <td class="px-4 py-4"><input type="number" min="0" name="pk_gift_coins" form="{{ $formId }}" class="{{ $inputClass }}" value="{{ old('pk_gift_coins', $item->pk_gift_coins) }}" @disabled($locked)></td>
              <td class="px-4 py-4"><input type="number" min="0" name="video_call_coins" form="{{ $formId }}" class="{{ $inputClass }}" value="{{ old('video_call_coins', $item->video_call_coins) }}" @disabled($locked)></td>
              <td class="px-4 py-4"><input type="number" min="0" name="video_call_minutes" form="{{ $formId }}" class="{{ $inputClass }}" value="{{ old('video_call_minutes', $item->video_call_minutes) }}" @disabled($locked)></td>
              <td class="px-4 py-4"><input type="number" min="0" name="bonus_coins" form="{{ $formId }}" class="{{ $inputClass }}" value="{{ old('bonus_coins', $item->bonus_coins) }}" @disabled($locked)></td>
              <td class="px-4 py-4 text-gray-700 dark:text-gray-200">{{ number_format($item->total_coins) }}</td>
              <td class="px-4 py-4"><input type="number" step="0.01" min="0" name="host_payout_inr" form="{{ $formId }}" class="{{ $inputClass }}" value="{{ old('host_payout_inr', number_format($item->host_payout_inr, 2, '.', '')) }}" @disabled($locked)></td>
              <td class="px-4 py-4"><input type="number" step="0.01" min="0" name="agency_commission_inr" form="{{ $formId }}" class="{{ $inputClass }}" value="{{ old('agency_commission_inr', number_format($item->agency_commission_inr, 2, '.', '')) }}" @disabled($locked)></td>
              <td class="px-4 py-4 text-gray-700 dark:text-gray-200">{{ number_format($item->total_inr, 2) }}</td>
              <td class="px-4 py-4">
                <textarea name="admin_note" form="{{ $formId }}" rows="2" class="min-w-[220px] rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="Admin note" @disabled($locked)>{{ old('admin_note', $item->admin_note) }}</textarea>
              </td>
              <td class="sticky right-0 z-10 bg-white px-4 py-4 text-right dark:bg-gray-900">
                <x-ui.button type="submit" size="sm" form="{{ $formId }}" @disabled($locked)>Save</x-ui.button>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="13" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No host rows in this report.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </x-common.component-card>
</div>
@endsection
