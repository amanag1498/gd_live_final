@extends('layouts.admin-tailadmin')
@section('title', 'Agency Payout Report #' . $report->id)

@push('styles')
<style>
  .payout-grid-scroll {
    max-height: 72vh;
    overflow: auto;
    overscroll-behavior: contain;
    scrollbar-gutter: stable;
    -webkit-overflow-scrolling: touch;
  }
  .payout-grid-table {
    min-width: 2200px;
    white-space: nowrap;
  }
  .payout-grid-table thead th {
    position: sticky;
    top: 0;
    z-index: 20;
    background: #f9fafb;
    box-shadow: inset 0 -1px 0 #e5e7eb;
  }
  .payout-grid-sticky-left,
  .payout-grid-sticky-right {
    position: sticky;
    z-index: 15;
    background: #fff;
  }
  .payout-grid-sticky-left {
    left: 0;
    box-shadow: 1px 0 0 #e5e7eb;
  }
  .payout-grid-sticky-right {
    right: 0;
    box-shadow: -1px 0 0 #e5e7eb;
  }
  .payout-grid-table thead .payout-grid-sticky-left,
  .payout-grid-table thead .payout-grid-sticky-right {
    z-index: 30;
    background: #f3f4f6;
  }
  .payout-grid-table tfoot td {
    position: sticky;
    bottom: 0;
    z-index: 20;
    background: #f3f4f6;
    box-shadow: inset 0 1px 0 #d1d5db;
  }
  .payout-grid-table tfoot .payout-grid-sticky-left,
  .payout-grid-table tfoot .payout-grid-sticky-right {
    z-index: 30;
  }
  .dark .payout-grid-table thead th,
  .dark .payout-grid-table thead .payout-grid-sticky-left,
  .dark .payout-grid-table thead .payout-grid-sticky-right,
  .dark .payout-grid-table tfoot td {
    background: #101828;
    box-shadow: inset 0 -1px 0 #1f2937;
  }
  .dark .payout-grid-sticky-left,
  .dark .payout-grid-sticky-right {
    background: #111827;
  }
  .dark .payout-grid-sticky-left {
    box-shadow: 1px 0 0 #1f2937;
  }
  .dark .payout-grid-sticky-right {
    box-shadow: -1px 0 0 #1f2937;
  }
  @media (max-width: 991px) {
    .payout-grid-scroll {
      max-height: 68vh;
    }
  }
</style>
@endpush

@section('content')
@php
  $locked = $report->status === 'paid' || $report->paid_at;
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
  $settlementInputClass = $inputClass . ' min-w-[160px] appearance-none tabular-nums';
  $dashboardHref = ($report->agency_id && \Illuminate\Support\Facades\Route::has('admin.agencies.dashboard'))
      ? route('admin.agencies.dashboard', $report->agency_id)
      : null;
@endphp

<div class="space-y-6">
  <div id="payout-update-feedback" class="hidden rounded-xl border px-4 py-3 text-sm" role="status" aria-live="polite"></div>
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
            Status: <span id="report-status-label">{{ ucwords(str_replace('_', ' ', $report->status)) }}</span> ·
            Agency visibility: <span id="report-visibility-label">{{ $report->published_at ? 'Published' : 'Draft only' }}</span>
          </p>
        </div>
        <div class="flex flex-wrap gap-2">
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.agency-payout-reports.index') }}">Back</x-ui.button>
          @if($dashboardHref)
            <x-ui.button variant="outline" size="sm" href="{{ $dashboardHref }}">Open Agency Dashboard</x-ui.button>
          @endif
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.agency-payout-reports.preview', $report) }}" target="_blank" rel="noopener">View PDF</x-ui.button>
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.agency-payout-reports.export', $report) }}">Download PDF</x-ui.button>
        </div>
      </div>
    </x-slot:header>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <x-admin.stat-card data-summary-key="total_hosts" data-summary-format="integer" label="Total Hosts" :value="number_format($report->total_hosts)" />
      <x-admin.stat-card data-summary-key="active_hosts_count" data-summary-format="integer" label="Active Hosts" :value="number_format($report->active_hosts_count)" tone="brand" />
      <x-admin.stat-card data-summary-key="total_coins" data-summary-format="integer" label="Total Coins" :value="number_format($report->total_coins)" tone="dark" />
      <x-admin.stat-card data-summary-key="final_payable" data-summary-format="integer" label="Final Payable" :value="number_format($report->final_payable)" tone="success" />
      <x-admin.stat-card data-summary-key="total_video_room_minutes" data-summary-format="minutes" label="Video Room Timing" :value="number_format($report->total_video_room_minutes) . ' min'" />
      <x-admin.stat-card data-summary-key="total_video_gift_coins" data-summary-format="integer" label="Video Room Gifts" :value="number_format($report->total_video_gift_coins)" />
      <x-admin.stat-card data-summary-key="total_pk_gift_coins" data-summary-format="integer" label="PK Gifts" :value="number_format($report->total_pk_gift_coins)" tone="warning" />
      <x-admin.stat-card data-summary-key="total_video_call_coins" data-summary-format="integer" data-summary-meta-key="total_video_call_minutes" label="Video Calls" :value="number_format($report->total_video_call_coins)" :meta="number_format($report->total_video_call_minutes) . ' min'" />
      <x-admin.stat-card data-summary-key="total_bonus_coins" data-summary-format="integer" label="Bonus Coins" :value="number_format($report->total_bonus_coins)" />
      <x-admin.stat-card data-summary-key="total_host_payout_inr" data-summary-format="money" label="Host Payout INR" :value="number_format($report->total_host_payout_inr, 2)" />
      <x-admin.stat-card data-summary-key="total_agency_commission_inr" data-summary-format="money" label="Agency Commission INR" :value="number_format($report->total_agency_commission_inr, 2)" />
      <x-admin.stat-card data-summary-key="total_inr" data-summary-format="money" label="Total INR" :value="number_format($report->total_inr, 2)" tone="success" />
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
        <x-ui.button id="report-review-button" type="submit" size="sm" :disabled="!in_array($report->status, ['generated', 'pending_review'])">Save Pending Review</x-ui.button>
      </form>
    </x-common.component-card>

    <x-common.component-card title="Actions" desc="Publish only after row-level numbers are finalized.">
      <div class="space-y-4">
        <form method="post" action="{{ route('admin.agency-payout-reports.approve', $report) }}" class="grid gap-4">
          @csrf
          <input type="hidden" name="deductions" value="{{ $report->deductions }}">
          <input type="text" name="admin_remarks" class="{{ $inputClass }}" value="{{ $report->admin_remarks }}" placeholder="Approval remarks">
          <x-ui.button id="report-approve-button" type="submit" size="sm" :disabled="!in_array($report->status, ['generated', 'pending_review'])">Approve Report</x-ui.button>
        </form>

        <form method="post" action="{{ route('admin.agency-payout-reports.publish', $report) }}" class="grid gap-4">
          @csrf
          <input type="text" name="admin_remarks" class="{{ $inputClass }}" value="{{ $report->admin_remarks }}" placeholder="Publish remarks">
          <x-ui.button id="report-publish-button" type="submit" variant="secondary" size="sm" :disabled="$report->status !== 'approved' || $report->published_at">Publish To Agency</x-ui.button>
        </form>

        <form method="post" action="{{ route('admin.agency-payout-reports.mark-paid', $report) }}" class="grid gap-4">
          @csrf
          <input type="text" name="admin_remarks" class="{{ $inputClass }}" value="{{ $report->admin_remarks }}" placeholder="Paid remarks">
          <x-ui.button id="report-paid-button" type="submit" variant="success" size="sm" :disabled="$report->status !== 'approved' || !$report->published_at || $report->status === 'paid'">Mark Paid</x-ui.button>
        </form>

        <form method="post" action="{{ route('admin.agency-payout-reports.reject', $report) }}" class="grid gap-4">
          @csrf
          <textarea id="report-reject-remarks" name="admin_remarks" rows="3" class="w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="Rejection reason" @disabled(!in_array($report->status, ['generated', 'pending_review']))></textarea>
          <x-ui.button id="report-reject-button" type="submit" variant="danger" size="sm" :disabled="!in_array($report->status, ['generated', 'pending_review'])">Reject Report</x-ui.button>
        </form>
      </div>
    </x-common.component-card>
  </div>

  <x-common.component-card title="Host Settlement Grid" desc="Format matches the desktop GD payout workflow with only the required fields.">
    <div class="mb-4 flex flex-col gap-2 rounded-2xl bg-gray-50 px-4 py-3 text-xs text-gray-500 sm:flex-row sm:items-center sm:justify-between dark:bg-gray-950/60 dark:text-gray-400">
      <span>Edit values directly; row and grand totals update while you type.</span>
      <span class="sm:text-right">Scroll vertically across hosts and horizontally across settlement fields. Header, host, totals, and Save stay visible.</span>
    </div>
    <div class="payout-grid-scroll max-w-full touch-pan-x rounded-2xl border border-gray-200 dark:border-gray-800">
      <table id="payout-settlement-grid" class="payout-grid-table table-auto divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="payout-grid-sticky-left min-w-[180px] whitespace-nowrap px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Host</th>
            <th class="min-w-[190px] whitespace-nowrap px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Total Video Room Timing</th>
            <th class="min-w-[190px] whitespace-nowrap px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Total Video Room Gifts</th>
            <th class="min-w-[180px] whitespace-nowrap px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Total PK Gifts</th>
            <th class="min-w-[180px] whitespace-nowrap px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Video Calls Coins</th>
            <th class="min-w-[180px] whitespace-nowrap px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Video Calls Min</th>
            <th class="min-w-[170px] whitespace-nowrap px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Bonus Coins</th>
            <th class="min-w-[150px] whitespace-nowrap px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Total Coins</th>
            <th class="min-w-[180px] whitespace-nowrap px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Host Payout INR</th>
            <th class="min-w-[210px] whitespace-nowrap px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Agency Commission INR</th>
            <th class="min-w-[150px] whitespace-nowrap px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Total INR</th>
            <th class="min-w-[250px] whitespace-nowrap px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Admin Notes</th>
            <th class="payout-grid-sticky-right min-w-[110px] whitespace-nowrap px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Save</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($report->items as $item)
            @php($formId = 'payout-row-' . $item->id)
            <tr id="payout-item-{{ $item->id }}" data-payout-row data-hidden-coins="{{ (int) data_get($item->meta, 'audio_gift_coins', data_get($item->meta, 'audio_gift_gross', 0)) + (int) data_get($item->meta, 'audio_call_coins', data_get($item->meta, 'audio_call_gross', 0)) }}" class="bg-white transition-opacity dark:bg-gray-900">
              <td class="payout-grid-sticky-left min-w-[180px] px-4 py-4">
                <form id="{{ $formId }}" class="js-payout-row-form" method="post" action="{{ route('admin.agency-payout-reports.items.update', [$report, $item]) }}" data-row-id="{{ $item->id }}">
                  @csrf
                </form>
                <div class="font-semibold text-gray-900 dark:text-white">{{ $item->host?->user?->name ?? $item->host?->stage_name ?? '—' }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $item->host?->stage_name ?? '—' }}</div>
              </td>
              <td class="min-w-[190px] px-4 py-4"><input type="number" inputmode="numeric" min="0" name="video_room_minutes" form="{{ $formId }}" class="{{ $settlementInputClass }}" value="{{ old('video_room_minutes', $item->video_room_minutes) }}" @disabled($locked)></td>
              <td class="min-w-[190px] px-4 py-4"><input type="number" inputmode="numeric" min="0" name="video_gift_coins" form="{{ $formId }}" class="{{ $settlementInputClass }}" value="{{ old('video_gift_coins', $item->video_gift_coins) }}" @disabled($locked)></td>
              <td class="min-w-[180px] px-4 py-4"><input type="number" inputmode="numeric" min="0" name="pk_gift_coins" form="{{ $formId }}" class="{{ $settlementInputClass }}" value="{{ old('pk_gift_coins', $item->pk_gift_coins) }}" @disabled($locked)></td>
              <td class="min-w-[180px] px-4 py-4"><input type="number" inputmode="numeric" min="0" name="video_call_coins" form="{{ $formId }}" class="{{ $settlementInputClass }}" value="{{ old('video_call_coins', $item->video_call_coins) }}" @disabled($locked)></td>
              <td class="min-w-[180px] px-4 py-4"><input type="number" inputmode="numeric" min="0" name="video_call_minutes" form="{{ $formId }}" class="{{ $settlementInputClass }}" value="{{ old('video_call_minutes', $item->video_call_minutes) }}" @disabled($locked)></td>
              <td class="min-w-[170px] px-4 py-4"><input type="number" inputmode="numeric" min="0" name="bonus_coins" form="{{ $formId }}" class="{{ $settlementInputClass }}" value="{{ old('bonus_coins', $item->bonus_coins) }}" @disabled($locked)></td>
              <td data-row-total-coins class="min-w-[150px] whitespace-nowrap px-4 py-4 tabular-nums text-gray-700 dark:text-gray-200">{{ number_format($item->total_coins) }}</td>
              <td class="min-w-[180px] px-4 py-4"><input type="number" inputmode="decimal" step="0.01" min="0" name="host_payout_inr" form="{{ $formId }}" class="{{ $settlementInputClass }}" value="{{ old('host_payout_inr', number_format($item->host_payout_inr, 2, '.', '')) }}" @disabled($locked)></td>
              <td class="min-w-[210px] px-4 py-4"><input type="number" inputmode="decimal" step="0.01" min="0" name="agency_commission_inr" form="{{ $formId }}" class="{{ $settlementInputClass }}" value="{{ old('agency_commission_inr', number_format($item->agency_commission_inr, 2, '.', '')) }}" @disabled($locked)></td>
              <td data-row-total-inr class="min-w-[150px] whitespace-nowrap px-4 py-4 tabular-nums text-gray-700 dark:text-gray-200">{{ number_format($item->total_inr, 2) }}</td>
              <td class="px-4 py-4">
                <textarea name="admin_note" form="{{ $formId }}" rows="2" class="min-w-[220px] rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white" placeholder="Admin note" @disabled($locked)>{{ old('admin_note', $item->admin_note) }}</textarea>
              </td>
              <td class="payout-grid-sticky-right min-w-[110px] px-4 py-4 text-right">
                <x-ui.button data-row-save type="submit" size="sm" form="{{ $formId }}" :disabled="$locked">Save</x-ui.button>
                <div data-row-feedback class="mt-2 min-h-4 text-xs font-medium" aria-live="polite"></div>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="13" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No host rows in this report.</td>
            </tr>
          @endforelse
        </tbody>
        <tfoot>
          <tr class="font-semibold text-gray-900 dark:text-white">
            <td class="payout-grid-sticky-left min-w-[180px] px-4 py-3">Grand Total</td>
            <td data-grid-total="video_room_minutes" class="px-4 py-3">{{ number_format($report->total_video_room_minutes) }}</td>
            <td data-grid-total="video_gift_coins" class="px-4 py-3">{{ number_format($report->total_video_gift_coins) }}</td>
            <td data-grid-total="pk_gift_coins" class="px-4 py-3">{{ number_format($report->total_pk_gift_coins) }}</td>
            <td data-grid-total="video_call_coins" class="px-4 py-3">{{ number_format($report->total_video_call_coins) }}</td>
            <td data-grid-total="video_call_minutes" class="px-4 py-3">{{ number_format($report->total_video_call_minutes) }}</td>
            <td data-grid-total="bonus_coins" class="px-4 py-3">{{ number_format($report->total_bonus_coins) }}</td>
            <td data-grid-total="total_coins" class="px-4 py-3">{{ number_format($report->total_coins) }}</td>
            <td data-grid-total="host_payout_inr" class="px-4 py-3">{{ number_format($report->total_host_payout_inr, 2) }}</td>
            <td data-grid-total="agency_commission_inr" class="px-4 py-3">{{ number_format($report->total_agency_commission_inr, 2) }}</td>
            <td data-grid-total="total_inr" class="px-4 py-3">{{ number_format($report->total_inr, 2) }}</td>
            <td class="px-4 py-3">—</td>
            <td class="payout-grid-sticky-right px-4 py-3 text-right">—</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </x-common.component-card>
</div>
@endsection

@push('scripts')
<script>
(() => {
  const integerFormatter = new Intl.NumberFormat('en-IN', { maximumFractionDigits: 0 });
  const moneyFormatter = new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  const feedback = document.getElementById('payout-update-feedback');
  const grid = document.getElementById('payout-settlement-grid');

  const format = (value, type = 'integer') => {
    if (type === 'money') return moneyFormatter.format(Number(value || 0));
    if (type === 'minutes') return `${integerFormatter.format(Number(value || 0))} min`;
    return integerFormatter.format(Number(value || 0));
  };

  const numberValue = (row, name) => {
    const value = Number.parseFloat(row.querySelector(`[name="${name}"]`)?.value || '0');
    return Number.isFinite(value) ? Math.max(0, value) : 0;
  };

  const recalculateGrid = () => {
    const totals = {
      video_room_minutes: 0,
      video_gift_coins: 0,
      pk_gift_coins: 0,
      video_call_coins: 0,
      video_call_minutes: 0,
      bonus_coins: 0,
      total_coins: 0,
      host_payout_inr: 0,
      agency_commission_inr: 0,
      total_inr: 0,
    };

    grid.querySelectorAll('tbody tr[data-payout-row]').forEach((row) => {
      const hiddenCoins = Number.parseInt(row.dataset.hiddenCoins || '0', 10) || 0;
      const rowTotalCoins = hiddenCoins
        + numberValue(row, 'video_gift_coins')
        + numberValue(row, 'pk_gift_coins')
        + numberValue(row, 'video_call_coins')
        + numberValue(row, 'bonus_coins');
      const rowTotalInr = numberValue(row, 'host_payout_inr') + numberValue(row, 'agency_commission_inr');

      row.querySelector('[data-row-total-coins]').textContent = format(rowTotalCoins);
      row.querySelector('[data-row-total-inr]').textContent = format(rowTotalInr, 'money');

      totals.video_room_minutes += numberValue(row, 'video_room_minutes');
      totals.video_gift_coins += numberValue(row, 'video_gift_coins');
      totals.pk_gift_coins += numberValue(row, 'pk_gift_coins');
      totals.video_call_coins += numberValue(row, 'video_call_coins');
      totals.video_call_minutes += numberValue(row, 'video_call_minutes');
      totals.bonus_coins += numberValue(row, 'bonus_coins');
      totals.total_coins += rowTotalCoins;
      totals.host_payout_inr += numberValue(row, 'host_payout_inr');
      totals.agency_commission_inr += numberValue(row, 'agency_commission_inr');
      totals.total_inr += rowTotalInr;
    });

    Object.entries(totals).forEach(([key, value]) => {
      const cell = grid.querySelector(`[data-grid-total="${key}"]`);
      if (!cell) return;
      cell.textContent = ['host_payout_inr', 'agency_commission_inr', 'total_inr'].includes(key)
        ? format(value, 'money')
        : format(value);
    });
  };

  const showFeedback = (message, isError = false) => {
    feedback.textContent = message;
    feedback.className = isError
      ? 'rounded-xl border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700 dark:border-error-900/50 dark:bg-error-950/20 dark:text-error-300'
      : 'rounded-xl border border-success-200 bg-success-50 px-4 py-3 text-sm text-success-700 dark:border-success-900/50 dark:bg-success-950/20 dark:text-success-300';
  };

  const updateReport = (report) => {
    document.getElementById('report-status-label').textContent = report.status_label;
    document.getElementById('report-visibility-label').textContent = report.visibility_label;

    document.querySelectorAll('[data-summary-key]').forEach((card) => {
      const value = card.querySelector('.text-3xl');
      if (value && Object.hasOwn(report.totals, card.dataset.summaryKey)) {
        value.textContent = format(report.totals[card.dataset.summaryKey], card.dataset.summaryFormat);
      }

      if (card.dataset.summaryMetaKey) {
        const meta = card.querySelector('.mt-3.text-sm');
        if (meta && Object.hasOwn(report.totals, card.dataset.summaryMetaKey)) {
          meta.textContent = format(report.totals[card.dataset.summaryMetaKey], 'minutes');
        }
      }
    });

    const actions = {
      'report-review-button': report.actions.can_review,
      'report-approve-button': report.actions.can_approve,
      'report-publish-button': report.actions.can_publish,
      'report-paid-button': report.actions.can_mark_paid,
      'report-reject-button': report.actions.can_reject,
    };
    Object.entries(actions).forEach(([id, enabled]) => {
      const button = document.getElementById(id);
      if (button) button.disabled = !enabled;
    });
    const rejectRemarks = document.getElementById('report-reject-remarks');
    if (rejectRemarks) rejectRemarks.disabled = !report.actions.can_reject;
  };

  const updateRow = (form, item, preserveEditedFields = false) => {
    const row = document.getElementById(`payout-item-${item.id}`);
    [
      'video_room_minutes',
      'video_gift_coins',
      'pk_gift_coins',
      'video_call_coins',
      'video_call_minutes',
      'bonus_coins',
      'host_payout_inr',
      'agency_commission_inr',
      'admin_note',
    ].forEach((name) => {
      if (preserveEditedFields) return;
      const field = document.querySelector(`[name="${name}"][form="${form.id}"]`);
      if (field && Object.hasOwn(item, name)) {
        field.value = ['host_payout_inr', 'agency_commission_inr'].includes(name)
          ? Number(item[name] || 0).toFixed(2)
          : item[name];
      }
    });
    row.querySelector('[data-row-total-coins]').textContent = format(item.total_coins);
    row.querySelector('[data-row-total-inr]').textContent = format(item.total_inr, 'money');
    recalculateGrid();
  };

  document.querySelectorAll('.js-payout-row-form').forEach((form) => {
    let autoSaveTimer;
    const rowFields = document.querySelectorAll(`[form="${form.id}"]:not([type="hidden"]):not([type="submit"])`);

    rowFields.forEach((field) => {
      field.addEventListener('input', () => {
        const row = document.getElementById(`payout-item-${form.dataset.rowId}`);
        const rowFeedback = row.querySelector('[data-row-feedback]');
        if (form.dataset.saving === 'true') form.dataset.resave = 'true';
        recalculateGrid();
        rowFeedback.textContent = 'Changes pending…';
        rowFeedback.className = 'mt-2 min-h-4 text-xs font-medium text-warning-600 dark:text-warning-400';
      });

      field.addEventListener('change', () => {
        window.clearTimeout(autoSaveTimer);
        autoSaveTimer = window.setTimeout(() => form.requestSubmit(), 500);
      });

      field.addEventListener('keydown', (event) => {
        const submitFromField = event.key === 'Enter'
          && (field.tagName !== 'TEXTAREA' || event.ctrlKey || event.metaKey);
        if (!submitFromField) return;

        event.preventDefault();
        window.clearTimeout(autoSaveTimer);
        form.requestSubmit();
      });
    });

    form.addEventListener('submit', async (event) => {
      event.preventDefault();
      if (form.dataset.saving === 'true') {
        form.dataset.resave = 'true';
        return;
      }
      window.clearTimeout(autoSaveTimer);
      form.dataset.saving = 'true';

      const row = document.getElementById(`payout-item-${form.dataset.rowId}`);
      const button = document.querySelector(`[data-row-save][form="${form.id}"]`);
      const rowFeedback = row.querySelector('[data-row-feedback]');
      const originalLabel = button.textContent;
      button.disabled = true;
      button.textContent = 'Saving…';
      rowFeedback.textContent = '';
      row.classList.add('opacity-60');
      row.setAttribute('aria-busy', 'true');
      let savedSuccessfully = false;

      try {
        const response = await fetch(form.action, {
          method: 'POST',
          body: new FormData(form),
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });
        const payload = await response.json().catch(() => ({
          message: response.status === 419
            ? 'Your admin session expired. Refresh this page and try again.'
            : 'The server returned an unexpected response.',
        }));

        if (!response.ok) {
          const errors = payload.errors ? Object.values(payload.errors).flat().join(' ') : payload.message;
          throw new Error(errors || 'Unable to update this payout row.');
        }

        const hasNewerChanges = form.dataset.resave === 'true';
        updateRow(form, payload.item, hasNewerChanges);
        updateReport(payload.report);
        showFeedback(payload.message);
        savedSuccessfully = true;
        rowFeedback.textContent = hasNewerChanges ? 'Saving latest…' : 'Saved';
        rowFeedback.className = hasNewerChanges
          ? 'mt-2 min-h-4 text-xs font-medium text-warning-600 dark:text-warning-400'
          : 'mt-2 min-h-4 text-xs font-medium text-success-600 dark:text-success-400';
      } catch (error) {
        showFeedback(error.message || 'Unable to update this payout row.', true);
        rowFeedback.textContent = 'Failed';
        rowFeedback.className = 'mt-2 min-h-4 text-xs font-medium text-error-600 dark:text-error-400';
      } finally {
        button.disabled = false;
        button.textContent = originalLabel;
        row.classList.remove('opacity-60');
        row.removeAttribute('aria-busy');
        delete form.dataset.saving;
        if (savedSuccessfully && form.dataset.resave === 'true') {
          delete form.dataset.resave;
          window.setTimeout(() => form.requestSubmit(), 0);
        } else {
          delete form.dataset.resave;
          window.setTimeout(() => {
            rowFeedback.textContent = '';
          }, 3000);
        }
      }
    });
  });

  recalculateGrid();
})();
</script>
@endpush
