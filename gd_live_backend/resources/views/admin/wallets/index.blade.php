@extends('layouts.admin-tailadmin')
@section('title','Wallets')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-gray-900 via-gray-900 to-brand-900 text-white dark:border-gray-800">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="light">Treasury</x-ui.badge>
            <x-ui.badge color="brand">Wallet Control</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-white">Wallets</h2>
          <p class="mt-3 text-sm text-gray-300">Monitor user balances, supply distribution, recharge quality, and billing reconciliation from a single finance operations panel.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
          <x-admin.stat-card label="Coin Supply" :value="number_format($coinSupply ?? 0)" meta="Users plus agencies" />
          <x-admin.stat-card label="Credits" :value="number_format($walletSummary['total_credits'] ?? 0)" meta="All credited coins recorded" tone="success" />
          <x-admin.stat-card label="Debits" :value="number_format($walletSummary['total_debits'] ?? 0)" meta="All debited coins recorded" tone="warning" />
          <x-admin.stat-card label="Recharge Conversion" :value="number_format($walletSummary['recharge_conversion'] ?? 0, 1) . '%'" meta="Successful payment orders" tone="dark" />
        </div>
      </div>
    </div>
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Wallet Search</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Find balances by user identity and narrow the list to blocked accounts or only users carrying coins.</p>
        </div>

        <form method="get" action="{{ route('admin.wallets.index') }}" class="grid gap-3 xl:grid-cols-[minmax(0,1fr)_200px_200px_auto]">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
            <input type="text" name="q" value="{{ request('q') }}" class="{{ $inputClass }}" placeholder="Search by name or email">
          </div>

          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Has Balance</label>
            <select name="has_balance" class="{{ $inputClass }}">
              <option value="">Any</option>
              <option value="1" @selected(request('has_balance') == '1')>Yes (> 0)</option>
              <option value="0" @selected(request('has_balance') === '0')>Zero only</option>
            </select>
          </div>

          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Blocked</label>
            <select name="blocked" class="{{ $inputClass }}">
              <option value="">Any</option>
              <option value="1" @selected(request('blocked') == '1')>Only blocked</option>
              <option value="0" @selected(request('blocked') === '0')>Only active</option>
            </select>
          </div>

          <div class="flex flex-wrap items-end justify-end gap-3">
            <x-ui.button variant="outline" type="submit" size="sm">Filter</x-ui.button>
            <x-ui.button variant="outline" href="{{ route('admin.wallets.index') }}" size="sm">Reset</x-ui.button>
          </div>
        </form>
      </div>
    </x-slot:header>
  </x-common.component-card>

  <x-common.component-card title="Billing Reconciliation" desc="Fast anomaly scan across wallet, call, and ledger records.">
    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
      <x-admin.stat-card label="Missing Wallet Tx" :value="number_format($reconciliation['calls_missing_wallet_transaction'] ?? 0)" meta="Calls without wallet transactions" />
      <x-admin.stat-card label="Missing Ledger" :value="number_format($reconciliation['calls_missing_earning_ledger'] ?? 0)" meta="Calls without earning ledger rows" tone="warning" />
      <x-admin.stat-card label="Duplicate Billing" :value="number_format($reconciliation['duplicate_billing_references'] ?? 0)" meta="Repeated billing references" tone="dark" />
      <x-admin.stat-card label="Failed Calls Billed" :value="number_format($reconciliation['failed_calls_with_billing_entries'] ?? 0)" meta="Billing created for failed calls" tone="warning" />
      <x-admin.stat-card label="Ended Calls Missing" :value="number_format($reconciliation['completed_calls_missing_billing'] ?? 0)" meta="Completed calls missing billing" tone="danger" />
    </div>
  </x-common.component-card>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Wallet Directory</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Showing {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users.</p>
        </div>
        <div class="flex items-center gap-2">
          <x-ui.badge color="dark">Positive wallets: {{ number_format($walletSummary['positive_wallets'] ?? 0) }}</x-ui.badge>
          <x-ui.badge color="brand">Top spenders: {{ number_format($walletSummary['top_spenders'] ?? 0) }}</x-ui.badge>
        </div>
      </div>
    </x-slot:header>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">#</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">User</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Email</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Balance</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Updated</th>
            <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($users as $u)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3">{{ $u->id }}</td>
              <td class="px-4 py-3">
                <a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.users.show', $u) }}">{{ $u->name ?? '—' }}</a>
              </td>
              <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ $u->email ?? '—' }}</td>
              <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ number_format($u->wallet?->balance ?? 0) }}</td>
              <td class="px-4 py-3">
                <x-ui.badge :color="$u->is_blocked ? 'danger' : 'success'">{{ $u->is_blocked ? 'Blocked' : 'Active' }}</x-ui.badge>
              </td>
              <td class="px-4 py-3">{{ $u->wallet?->updated_at?->diffForHumans() ?? '—' }}</td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap justify-end gap-2">
                  <x-ui.button variant="outline" size="sm" href="{{ route('admin.users.show', $u) }}">Profile</x-ui.button>
                  <x-ui.button size="sm" href="{{ route('admin.wallets.show', $u) }}">View Wallet</x-ui.button>
                </div>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No wallets found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <x-slot:footer>
      <div class="flex justify-end">
        {{ $users->withQueryString()->links() }}
      </div>
    </x-slot:footer>
  </x-common.component-card>
</div>
@endsection
