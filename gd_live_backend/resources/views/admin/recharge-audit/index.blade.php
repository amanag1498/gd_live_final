@extends('layouts.admin-tailadmin')
@section('title', 'Recharge Audit')

@php
  $summary = $summary ?? null;
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('page_actions')
  <x-ui.button size="sm" href="{{ route('admin.recharge-audit.pdf', request()->only(['from', 'to', 'status', 'gateway', 'q', 'payment_method', 'vpa', 'rrn', 'contact', 'email', 'signature_verified'])) }}">
    Download PDF
  </x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div class="max-w-2xl">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Recharge Audit</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track recharge outcomes, gateway reliability, and custom date-range order trends for finance reviews and reconciliation.</p>
        </div>

        <form method="get" action="{{ route('admin.recharge-audit.index') }}" class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">From</label>
            <input type="date" name="from" value="{{ request('from', $fromDate->format('Y-m-d')) }}" class="{{ $inputClass }}">
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">To</label>
            <input type="date" name="to" value="{{ request('to', $toDate->format('Y-m-d')) }}" class="{{ $inputClass }}">
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
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Method</label>
            <input type="text" name="payment_method" value="{{ request('payment_method') }}" class="{{ $inputClass }}" placeholder="upi / card / netbanking">
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">VPA</label>
            <input type="text" name="vpa" value="{{ request('vpa') }}" class="{{ $inputClass }}" placeholder="user@bank">
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">RRN</label>
            <input type="text" name="rrn" value="{{ request('rrn') }}" class="{{ $inputClass }}" placeholder="Bank RRN">
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Gateway Contact</label>
            <input type="text" name="contact" value="{{ request('contact') }}" class="{{ $inputClass }}" placeholder="+91...">
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Gateway Email</label>
            <input type="text" name="email" value="{{ request('email') }}" class="{{ $inputClass }}" placeholder="payer email">
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Signature</label>
            <select name="signature_verified" class="{{ $inputClass }}">
              <option value="">Any</option>
              <option value="1" @selected(request('signature_verified') === '1')>Verified</option>
              <option value="0" @selected(request('signature_verified') === '0')>Not Verified</option>
            </select>
          </div>
          <div class="flex items-end gap-3">
            <x-ui.button type="submit" size="sm">Apply</x-ui.button>
            <x-ui.button variant="outline" size="sm" href="{{ route('admin.recharge-audit.index') }}">Reset</x-ui.button>
          </div>
        </form>
      </div>
    </x-slot:header>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
      <x-admin.stat-card label="Orders" :value="number_format((int) ($summary->total_orders ?? 0))" :meta="$selectedRangeLabel" />
      <x-admin.stat-card label="Successful" :value="number_format((int) ($summary->successful_orders ?? 0))" :meta="'Pending '.number_format((int) ($summary->pending_orders ?? 0))" tone="success" />
      <x-admin.stat-card label="Gross Amount" :value="'Rs '.number_format((float) ($summary->rupees_total ?? 0), 2)" meta="GST inclusive" tone="dark" />
      <x-admin.stat-card label="Taxable" :value="'Rs '.number_format((float) ($summary->taxable_total ?? 0), 2)" meta="Base amount" />
      <x-admin.stat-card label="GST @ 18%" :value="'Rs '.number_format((float) ($summary->gst_total ?? 0), 2)" meta="Tax component" tone="warning" />
      <x-admin.stat-card label="Coins" :value="number_format((int) ($summary->coins_total ?? 0))" meta="Recharge credits" tone="brand" />
    </section>
  </x-common.component-card>

  <div class="grid gap-6 xl:grid-cols-[360px_minmax(0,1fr)]">
    <x-common.component-card title="Gateway Breakdown" desc="Gateway-level performance for the selected date range." padding="compact">
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
                <td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No recharge orders found for this date range.</td>
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
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Showing {{ $orders->firstItem() ?? 0 }}-{{ $orders->lastItem() ?? 0 }} of {{ $orders->total() }} orders for {{ $selectedRangeLabel }}.</p>
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
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Payment Meta</th>
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
                <td class="px-4 py-4 text-xs text-gray-600 dark:text-gray-300">
                  @php($meta = $order->audit_meta ?? [])
                  <div><span class="font-semibold text-gray-900 dark:text-white">Method:</span> {{ $meta['method'] ?? '—' }}</div>
                  <div><span class="font-semibold text-gray-900 dark:text-white">RRN:</span> {{ $meta['rrn'] ?? '—' }}</div>
                  <div><span class="font-semibold text-gray-900 dark:text-white">VPA:</span> {{ $meta['vpa'] ?? '—' }}</div>
                  <div><span class="font-semibold text-gray-900 dark:text-white">Flow:</span> {{ $meta['upi_flow'] ?? '—' }}</div>
                  <div><span class="font-semibold text-gray-900 dark:text-white">Payer Type:</span> {{ $meta['payer_account_type'] ?? '—' }}</div>
                  <div><span class="font-semibold text-gray-900 dark:text-white">Contact:</span> {{ $meta['contact'] ?? '—' }}</div>
                  <div><span class="font-semibold text-gray-900 dark:text-white">Gateway Email:</span> {{ $meta['email'] ?? '—' }}</div>
                  <div><span class="font-semibold text-gray-900 dark:text-white">Gateway Status:</span> {{ $meta['payment_status'] ?? '—' }}</div>
                  <div><span class="font-semibold text-gray-900 dark:text-white">Signature:</span> {{ array_key_exists('signature_verified', $meta) ? (($meta['signature_verified'] ?? false) ? 'Verified' : 'No') : '—' }}</div>
                  <div><span class="font-semibold text-gray-900 dark:text-white">Fee:</span> {{ $meta['gateway_fee'] !== null ? 'Rs '.number_format(((float) $meta['gateway_fee']) / 100, 2) : '—' }}</div>
                  <div><span class="font-semibold text-gray-900 dark:text-white">Tax:</span> {{ $meta['gateway_tax'] !== null ? 'Rs '.number_format(((float) $meta['gateway_tax']) / 100, 2) : '—' }}</div>
                  @if(!empty($meta['error_code']) || !empty($meta['error_description']))
                    <div><span class="font-semibold text-gray-900 dark:text-white">Gateway Error:</span> {{ $meta['error_code'] ?? '—' }}{{ !empty($meta['error_description']) ? ' · '.$meta['error_description'] : '' }}</div>
                  @endif
                </td>
                <td class="px-4 py-4 text-gray-600 dark:text-gray-300">Rs {{ number_format((float) $order->amount_rupees, 2) }}</td>
                <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ number_format((int) $order->total_coins) }}</td>
                <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $order->created_at?->format('d M Y, h:i A') }}</td>
              </tr>
            @empty
              <tr class="bg-white dark:bg-gray-900">
                <td colspan="9" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No recharge orders found for the selected date range.</td>
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
