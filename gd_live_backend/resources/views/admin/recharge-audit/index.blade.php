@extends('layouts.admin-tailadmin')
@section('title', 'Recharge Audit')

@php
  $summary = $summary ?? null;
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('page_actions')
  <x-ui.button size="sm" href="{{ route('admin.recharge-audit.pdf', ['month' => $selectedMonthKey] + request()->only(['status', 'gateway', 'q'])) }}">
    Download PDF
  </x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div class="max-w-2xl">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Monthly Recharge Audit</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track recharge outcomes, gateway reliability, and month-wise order trends for finance reviews and reconciliation.</p>
        </div>

        <form method="get" action="{{ route('admin.recharge-audit.index') }}" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-[150px_160px_160px_220px_auto]">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Month</label>
            <input type="month" name="month" value="{{ request('month', $selectedMonthKey) }}" class="{{ $inputClass }}">
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
            <select name="status" class="{{ $inputClass }}">
              <option value="">Any</option>
              @foreach(['success','pending','failed','cancelled'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Gateway</label>
            <input type="text" name="gateway" value="{{ request('gateway') }}" class="{{ $inputClass }}" placeholder="razorpay">
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
            <input type="text" name="q" value="{{ request('q') }}" class="{{ $inputClass }}" placeholder="user, email, order ID">
          </div>
          <div class="flex items-end gap-3">
            <x-ui.button type="submit" size="sm">Apply</x-ui.button>
            <x-ui.button variant="outline" size="sm" href="{{ route('admin.recharge-audit.index') }}">Reset</x-ui.button>
          </div>
        </form>
      </div>
    </x-slot:header>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
      <x-admin.stat-card label="Orders" :value="number_format((int) ($summary->total_orders ?? 0))" :meta="$selectedMonth->format('F Y')" />
      <x-admin.stat-card label="Successful" :value="number_format((int) ($summary->successful_orders ?? 0))" :meta="'Pending '.number_format((int) ($summary->pending_orders ?? 0))" tone="success" />
      <x-admin.stat-card label="Gross Amount" :value="'Rs '.number_format((float) ($summary->rupees_total ?? 0), 2)" meta="GST inclusive" tone="dark" />
      <x-admin.stat-card label="Taxable" :value="'Rs '.number_format((float) ($summary->taxable_total ?? 0), 2)" meta="Base amount" />
      <x-admin.stat-card label="GST @ 18%" :value="'Rs '.number_format((float) ($summary->gst_total ?? 0), 2)" meta="Tax component" tone="warning" />
      <x-admin.stat-card label="Coins" :value="number_format((int) ($summary->coins_total ?? 0))" meta="Recharge credits" tone="brand" />
    </section>

    <div class="mt-6 flex flex-wrap gap-2">
      @forelse($monthTabs as $tab)
        <a href="{{ route('admin.recharge-audit.index', ['month' => $tab->month_key]) }}" class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-medium transition {{ $selectedMonthKey === $tab->month_key ? 'border-brand-500 bg-brand-500 text-white' : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800' }}">
          {{ \Carbon\Carbon::createFromFormat('Y-m', $tab->month_key)->format('M Y') }}
          <span class="rounded-full bg-black/10 px-2 py-0.5 text-xs {{ $selectedMonthKey === $tab->month_key ? 'text-white' : 'text-gray-600 dark:text-gray-300' }}">{{ number_format((int) $tab->order_count) }}</span>
        </a>
      @empty
        <div class="text-sm text-gray-500 dark:text-gray-400">No recharge history available yet.</div>
      @endforelse
    </div>
  </x-common.component-card>

  <div class="grid gap-6 xl:grid-cols-[360px_minmax(0,1fr)]">
    <x-common.component-card title="Gateway Breakdown" desc="Gateway-level performance for the selected month." padding="compact">
      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60">
            <tr>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Gateway</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Orders</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Success</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Rupees</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse($gatewayBreakdown as $gateway)
              <tr class="bg-white dark:bg-gray-900">
                <td class="px-4 py-3 font-medium capitalize text-gray-900 dark:text-white">{{ $gateway->gateway_name }}</td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ number_format((int) $gateway->order_count) }}</td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ number_format((int) $gateway->success_count) }}</td>
                <td class="px-4 py-3 text-gray-600 dark:text-gray-300">Rs {{ number_format((float) $gateway->rupees_total, 2) }}</td>
              </tr>
            @empty
              <tr class="bg-white dark:bg-gray-900">
                <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No recharge orders found for this month.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </x-common.component-card>

    <x-common.component-card>
      <x-slot:header>
        <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
          <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Recharge Orders</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Showing {{ $orders->firstItem() ?? 0 }}-{{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders for {{ $selectedMonth->format('F Y') }}.</p>
          </div>
        </div>
      </x-slot:header>

      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60">
            <tr>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Order</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">User</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Plan</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Gateway</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Value</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Coins</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Created</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse($orders as $order)
              <tr class="bg-white dark:bg-gray-900">
                <td class="px-4 py-4">
                  <div class="font-semibold text-gray-900 dark:text-white">{{ $order->order_id }}</div>
                  <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $order->gateway_order_id ?: ($order->gateway_payment_id ?: '—') }}</div>
                </td>
                <td class="px-4 py-4">
                  <div class="font-medium text-gray-900 dark:text-white">{{ $order->user?->name ?? 'User #'.$order->user_id }}</div>
                  <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $order->user?->email ?? '—' }}</div>
                </td>
                <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $order->rechargePlan?->title ?? 'Plan #'.$order->recharge_plan_id }}</td>
                <td class="px-4 py-4">
                  <x-ui.badge :color="match($order->status){'success' => 'success','pending' => 'warning','failed', 'cancelled' => 'error',default => 'dark'}">
                    {{ ucfirst($order->status) }}
                  </x-ui.badge>
                </td>
                <td class="px-4 py-4 text-gray-600 capitalize dark:text-gray-300">{{ $order->gateway ?: 'manual' }}</td>
                <td class="px-4 py-4 text-gray-600 dark:text-gray-300">Rs {{ number_format((float) $order->amount_rupees, 2) }}</td>
                <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ number_format((int) $order->total_coins) }}</td>
                <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $order->created_at?->format('d M Y, h:i A') }}</td>
              </tr>
            @empty
              <tr class="bg-white dark:bg-gray-900">
                <td colspan="8" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No recharge orders found for the selected month.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <x-slot:footer>
        <div class="flex justify-end">
          {{ $orders->links() }}
        </div>
      </x-slot:footer>
    </x-common.component-card>
  </div>
</div>
@endsection
