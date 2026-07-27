@extends('layouts.admin-tailadmin')
@section('title', 'Transaction Ledger')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $creditCoins = (int) ($summary->credit_coins ?? 0);
  $debitCoins = (int) ($summary->debit_coins ?? 0);
  $moneyLabel = $moneyTotals->isEmpty()
      ? 'No cash-linked rows'
      : $moneyTotals->map(fn ($row) => $row->currency_code.' '.number_format((float) $row->amount_total, 2))->join(' · ');
@endphp

@section('page_actions')
  <x-ui.button size="sm" variant="outline" href="{{ route('admin.wallets.index') }}">Wallets</x-ui.button>
  <x-ui.button size="sm" href="{{ route('admin.wallet-transactions.export', request()->query()) }}">Export CSV</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div class="max-w-3xl">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Platform Transaction Ledger</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Every user-wallet credit and debit across recharges, gifts, calls, games, subscriptions, entry packs, agency transfers, and admin adjustments.</p>
      </div>
    </x-slot:header>

    <form method="get" action="{{ route('admin.wallet-transactions.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
      <div class="md:col-span-2">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
        <input name="q" value="{{ request('q') }}" class="{{ $inputClass }}" placeholder="User, email, ledger ID, reference, gateway transaction">
      </div>
      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
        <select name="type" class="{{ $inputClass }}">
          <option value="">Credit and debit</option>
          <option value="credit" @selected(request('type') === 'credit')>Credit</option>
          <option value="debit" @selected(request('type') === 'debit')>Debit</option>
        </select>
      </div>
      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
        <select name="category" class="{{ $inputClass }}">
          <option value="">All categories</option>
          @foreach($options['categories'] as $category)
            <option value="{{ $category }}" @selected(request('category') === $category)>{{ str($category)->replace('_', ' ')->title() }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Integrity</label>
        <select name="integrity" class="{{ $inputClass }}">
          <option value="">Any state</option>
          <option value="balanced" @selected(request('integrity') === 'balanced')>Balanced</option>
          <option value="mismatch" @selected(request('integrity') === 'mismatch')>Mismatch</option>
          <option value="missing" @selected(request('integrity') === 'missing')>Missing snapshot</option>
        </select>
      </div>
      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">From</label>
        <input type="date" name="from" value="{{ request('from') }}" class="{{ $inputClass }}">
      </div>
      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">To</label>
        <input type="date" name="to" value="{{ request('to') }}" class="{{ $inputClass }}">
      </div>
      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">User ID</label>
        <input type="number" min="1" name="user_id" value="{{ request('user_id') }}" class="{{ $inputClass }}" placeholder="Any user">
      </div>
      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Wallet ID</label>
        <input type="number" min="1" name="wallet_id" value="{{ request('wallet_id') }}" class="{{ $inputClass }}" placeholder="Any wallet">
      </div>
      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Counterparty ID</label>
        <input type="number" min="1" name="counterparty_user_id" value="{{ request('counterparty_user_id') }}" class="{{ $inputClass }}" placeholder="Any counterparty">
      </div>
      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Gateway</label>
        <select name="gateway" class="{{ $inputClass }}">
          <option value="">All gateways</option>
          @foreach($options['gateways'] as $gateway)
            <option value="{{ $gateway }}" @selected(request('gateway') === $gateway)>{{ ucfirst($gateway) }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Reference type</label>
        <select name="reference_type" class="{{ $inputClass }}">
          <option value="">All reference types</option>
          @foreach($options['reference_types'] as $referenceType)
            <option value="{{ $referenceType }}" @selected(request('reference_type') === $referenceType)>{{ $referenceType }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Minimum coins</label>
        <input type="number" min="0" name="min_coins" value="{{ request('min_coins') }}" class="{{ $inputClass }}" placeholder="0">
      </div>
      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Maximum coins</label>
        <input type="number" min="0" name="max_coins" value="{{ request('max_coins') }}" class="{{ $inputClass }}" placeholder="No maximum">
      </div>
      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Rows per page</label>
        <select name="per_page" class="{{ $inputClass }}">
          @foreach([25, 50, 100] as $size)
            <option value="{{ $size }}" @selected((int) request('per_page', 25) === $size)>{{ $size }}</option>
          @endforeach
        </select>
      </div>
      <div class="flex items-end gap-3">
        <x-ui.button type="submit" size="sm">Apply filters</x-ui.button>
        <x-ui.button variant="outline" size="sm" href="{{ route('admin.wallet-transactions.index') }}">Reset</x-ui.button>
      </div>
    </form>
  </x-common.component-card>

  <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
    <x-admin.stat-card label="Transactions" :value="number_format((int) ($summary->transaction_count ?? 0))" :meta="number_format((int) ($summary->wallet_count ?? 0)).' wallets'" />
    <x-admin.stat-card label="Credits" :value="number_format($creditCoins)" meta="Coins added" tone="success" />
    <x-admin.stat-card label="Debits" :value="number_format($debitCoins)" meta="Coins spent" tone="warning" />
    <x-admin.stat-card label="Net ledger flow" :value="number_format($creditCoins - $debitCoins)" meta="Credits minus debits" tone="brand" />
    <x-admin.stat-card label="Integrity issues" :value="number_format((int) ($summary->anomaly_count ?? 0))" :meta="$moneyLabel" :tone="((int) ($summary->anomaly_count ?? 0)) > 0 ? 'danger' : 'dark'" />
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Ledger entries</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Showing {{ $transactions->firstItem() ?? 0 }}-{{ $transactions->lastItem() ?? 0 }} of {{ number_format($transactions->total()) }} matching entries. Times use {{ config('app.timezone') }}.</p>
      </div>
    </x-slot:header>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-[1320px] w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            @foreach(['When / ID', 'User / Wallet', 'Type', 'Category', 'Coins', 'Money', 'Balance movement', 'Reference', 'Integrity', ''] as $heading)
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-gray-400">{{ $heading }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($transactions as $transaction)
            @php
              $expected = $transaction->balance_before === null
                  ? null
                  : ($transaction->type === 'credit' ? (int) $transaction->balance_before + (int) $transaction->coins : (int) $transaction->balance_before - (int) $transaction->coins);
              $integrityStatus = $transaction->balance_before === null || $transaction->balance_after === null
                  ? 'missing'
                  : ($expected === (int) $transaction->balance_after ? 'balanced' : 'mismatch');
              $user = $transaction->wallet?->user;
            @endphp
            <tr class="bg-white align-top transition hover:bg-gray-50 dark:bg-gray-900 dark:hover:bg-gray-800/60">
              <td class="whitespace-nowrap px-4 py-4">
                <div class="font-medium text-gray-900 dark:text-white">{{ $transaction->created_at?->timezone(config('app.timezone'))->format('d M Y, h:i:s A') }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Ledger #{{ $transaction->id }}</div>
              </td>
              <td class="px-4 py-4">
                <div class="font-semibold text-gray-900 dark:text-white">{{ $user?->name ?? 'Deleted user' }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $user?->email ?? 'User unavailable' }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">User #{{ $user?->id ?? '—' }} · Wallet #{{ $transaction->wallet_id }}</div>
              </td>
              <td class="px-4 py-4">
                <x-ui.badge :color="$transaction->type === 'credit' ? 'success' : 'warning'">{{ ucfirst($transaction->type) }}</x-ui.badge>
              </td>
              <td class="px-4 py-4 text-gray-700 dark:text-gray-300">{{ str($transaction->category ?: 'uncategorized')->replace('_', ' ')->title() }}</td>
              <td class="whitespace-nowrap px-4 py-4 font-semibold {{ $transaction->type === 'credit' ? 'text-success-600 dark:text-success-400' : 'text-warning-600 dark:text-warning-400' }}">
                {{ $transaction->type === 'credit' ? '+' : '-' }}{{ number_format((int) $transaction->coins) }}
              </td>
              <td class="whitespace-nowrap px-4 py-4 text-gray-600 dark:text-gray-300">
                {{ $transaction->amount !== null ? (($transaction->currency ?: '—').' '.number_format((float) $transaction->amount, 2)) : '—' }}
              </td>
              <td class="whitespace-nowrap px-4 py-4 text-gray-600 dark:text-gray-300">
                {{ $transaction->balance_before === null ? '—' : number_format((int) $transaction->balance_before) }}
                <span class="px-1 text-gray-400">→</span>
                {{ $transaction->balance_after === null ? '—' : number_format((int) $transaction->balance_after) }}
              </td>
              <td class="max-w-xs px-4 py-4">
                <div class="break-all text-gray-700 dark:text-gray-300">{{ $transaction->reference ?: '—' }}</div>
                @if($transaction->reference_type || $transaction->reference_id)
                  <div class="mt-1 break-all text-xs text-gray-500 dark:text-gray-400">{{ $transaction->reference_type ?: 'reference' }} #{{ $transaction->reference_id ?: '—' }}</div>
                @endif
              </td>
              <td class="px-4 py-4">
                <x-ui.badge :color="match($integrityStatus) {'balanced' => 'success', 'mismatch' => 'error', default => 'warning'}">{{ ucfirst($integrityStatus) }}</x-ui.badge>
              </td>
              <td class="px-4 py-4">
                <a href="{{ route('admin.wallet-transactions.show', $transaction) }}" class="font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">Audit</a>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="10" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">No wallet transactions match the selected filters.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <x-slot:footer>
      <div class="flex justify-end">{{ $transactions->links() }}</div>
    </x-slot:footer>
  </x-common.component-card>
</div>
@endsection
