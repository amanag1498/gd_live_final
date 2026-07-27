@extends('layouts.admin-tailadmin')
@section('title', 'Ledger Entry #'.$transaction->id)

@php
  $user = $transaction->wallet?->user;
  $metadata = $transaction->meta ?? [];
  $labelClass = 'text-xs font-medium uppercase tracking-[0.16em] text-gray-500 dark:text-gray-400';
  $valueClass = 'mt-1 break-words text-sm font-medium text-gray-900 dark:text-white';
@endphp

@section('page_actions')
  <x-ui.button size="sm" variant="outline" href="{{ route('admin.wallet-transactions.index') }}">Back to ledger</x-ui.button>
  @if($user)
    <x-ui.button size="sm" href="{{ route('admin.wallets.show', $user) }}">User wallet</x-ui.button>
  @endif
@endsection

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
          <div class="flex flex-wrap items-center gap-2">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Ledger entry #{{ $transaction->id }}</h3>
            <x-ui.badge :color="$transaction->type === 'credit' ? 'success' : 'warning'">{{ ucfirst($transaction->type) }}</x-ui.badge>
            <x-ui.badge :color="match($integrity['status']) {'balanced' => 'success', 'mismatch' => 'error', default => 'warning'}">{{ ucfirst($integrity['status']) }}</x-ui.badge>
          </div>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Read-only audit view of the recorded wallet movement and its source context.</p>
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $transaction->created_at?->timezone(config('app.timezone'))->format('d M Y, h:i:s A T') }}</div>
      </div>
    </x-slot:header>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <x-admin.stat-card label="Coins" :value="($transaction->type === 'credit' ? '+' : '-').number_format((int) $transaction->coins)" :meta="str($transaction->category ?: 'uncategorized')->replace('_', ' ')->title()" :tone="$transaction->type === 'credit' ? 'success' : 'warning'" />
      <x-admin.stat-card label="Balance before" :value="$transaction->balance_before === null ? 'Not recorded' : number_format((int) $transaction->balance_before)" meta="Stored snapshot" />
      <x-admin.stat-card label="Balance after" :value="$transaction->balance_after === null ? 'Not recorded' : number_format((int) $transaction->balance_after)" meta="Stored snapshot" />
      <x-admin.stat-card label="Expected after" :value="$integrity['expected'] === null ? 'Unavailable' : number_format($integrity['expected'])" :meta="$integrity['difference'] === null ? 'Snapshots incomplete' : 'Difference '.number_format($integrity['difference'])" :tone="$integrity['status'] === 'balanced' ? 'dark' : 'danger'" />
    </section>
  </x-common.component-card>

  <div class="grid gap-6 xl:grid-cols-2">
    <x-common.component-card title="Wallet owner" desc="The user wallet affected by this entry.">
      <dl class="grid gap-5 sm:grid-cols-2">
        <div><dt class="{{ $labelClass }}">User</dt><dd class="{{ $valueClass }}">{{ $user?->name ?? 'Deleted or unavailable user' }}</dd></div>
        <div><dt class="{{ $labelClass }}">Email</dt><dd class="{{ $valueClass }}">{{ $user?->email ?? '—' }}</dd></div>
        <div><dt class="{{ $labelClass }}">User ID</dt><dd class="{{ $valueClass }}">{{ $user?->id ?? '—' }}</dd></div>
        <div><dt class="{{ $labelClass }}">Wallet ID</dt><dd class="{{ $valueClass }}">{{ $transaction->wallet_id }}</dd></div>
        <div><dt class="{{ $labelClass }}">Current wallet balance</dt><dd class="{{ $valueClass }}">{{ $transaction->wallet ? number_format((int) $transaction->wallet->balance) : 'Wallet unavailable' }}</dd></div>
        <div><dt class="{{ $labelClass }}">Counterparty</dt><dd class="{{ $valueClass }}">{{ $transaction->counterparty?->name ?? '—' }}{{ $transaction->counterparty_user_id ? ' (#'.$transaction->counterparty_user_id.')' : '' }}</dd></div>
      </dl>
    </x-common.component-card>

    <x-common.component-card title="Financial classification" desc="How the movement was classified and valued.">
      <dl class="grid gap-5 sm:grid-cols-2">
        <div><dt class="{{ $labelClass }}">Direction</dt><dd class="{{ $valueClass }}">{{ ucfirst($transaction->type) }}</dd></div>
        <div><dt class="{{ $labelClass }}">Category</dt><dd class="{{ $valueClass }}">{{ str($transaction->category ?: 'uncategorized')->replace('_', ' ')->title() }}</dd></div>
        <div><dt class="{{ $labelClass }}">Coins</dt><dd class="{{ $valueClass }}">{{ number_format((int) $transaction->coins) }}</dd></div>
        <div><dt class="{{ $labelClass }}">Cash value</dt><dd class="{{ $valueClass }}">{{ $transaction->amount !== null ? (($transaction->currency ?: '—').' '.number_format((float) $transaction->amount, 2)) : '—' }}</dd></div>
        <div><dt class="{{ $labelClass }}">Gateway</dt><dd class="{{ $valueClass }}">{{ $transaction->gateway ?: '—' }}</dd></div>
        <div><dt class="{{ $labelClass }}">Gateway transaction</dt><dd class="{{ $valueClass }}">{{ $transaction->transaction_id ?: '—' }}</dd></div>
      </dl>
    </x-common.component-card>

    <x-common.component-card title="Source reference" desc="Identifiers used to trace this entry back to the producing flow.">
      <dl class="grid gap-5 sm:grid-cols-2">
        <div class="sm:col-span-2"><dt class="{{ $labelClass }}">Reference</dt><dd class="{{ $valueClass }}">{{ $transaction->reference ?: '—' }}</dd></div>
        <div><dt class="{{ $labelClass }}">Reference type</dt><dd class="{{ $valueClass }}">{{ $transaction->reference_type ?: '—' }}</dd></div>
        <div><dt class="{{ $labelClass }}">Reference ID</dt><dd class="{{ $valueClass }}">{{ $transaction->reference_id ?: '—' }}</dd></div>
        <div class="sm:col-span-2"><dt class="{{ $labelClass }}">Description</dt><dd class="{{ $valueClass }}">{{ $transaction->description ?: '—' }}</dd></div>
        <div><dt class="{{ $labelClass }}">Created</dt><dd class="{{ $valueClass }}">{{ $transaction->created_at?->timezone(config('app.timezone'))->format('d M Y, h:i:s A') }}</dd></div>
        <div><dt class="{{ $labelClass }}">Updated</dt><dd class="{{ $valueClass }}">{{ $transaction->updated_at?->timezone(config('app.timezone'))->format('d M Y, h:i:s A') }}</dd></div>
      </dl>
    </x-common.component-card>

    <x-common.component-card title="Balance integrity" desc="Verification against the immutable before/after snapshots.">
      <div class="rounded-2xl border p-5 {{ $integrity['status'] === 'balanced' ? 'border-success-200 bg-success-50 dark:border-success-900/50 dark:bg-success-950/20' : 'border-error-200 bg-error-50 dark:border-error-900/50 dark:bg-error-950/20' }}">
        <div class="flex items-center justify-between gap-4">
          <div>
            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $integrity['status'] === 'balanced' ? 'Balance movement reconciles' : ($integrity['status'] === 'missing' ? 'Balance snapshot is incomplete' : 'Balance movement does not reconcile') }}</p>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
              @if($integrity['expected'] !== null)
                Expected {{ number_format($integrity['expected']) }}, recorded {{ number_format((int) $transaction->balance_after) }}, difference {{ number_format($integrity['difference']) }}.
              @else
                One or both balance snapshots were not stored, so this entry cannot be mathematically verified.
              @endif
            </p>
          </div>
          <x-ui.badge :color="match($integrity['status']) {'balanced' => 'success', 'mismatch' => 'error', default => 'warning'}">{{ ucfirst($integrity['status']) }}</x-ui.badge>
        </div>
      </div>
    </x-common.component-card>
  </div>

  @if($relatedRecords !== [])
    <x-common.component-card title="Linked source records" desc="Operational records found using this ledger entry's reference and metadata.">
      <div class="grid gap-4 lg:grid-cols-2">
        @foreach($relatedRecords as $record)
          <div class="rounded-2xl border border-gray-200 p-5 dark:border-gray-800">
            <h4 class="font-semibold text-gray-900 dark:text-white">{{ $record['title'] }}</h4>
            <dl class="mt-4 grid gap-3 sm:grid-cols-2">
              @foreach($record['fields'] as $label => $value)
                <div>
                  <dt class="{{ $labelClass }}">{{ $label }}</dt>
                  <dd class="{{ $valueClass }}">{{ filled($value) ? $value : '—' }}</dd>
                </div>
              @endforeach
            </dl>
          </div>
        @endforeach
      </div>
    </x-common.component-card>
  @endif

  <x-common.component-card title="Metadata" desc="Raw producer context stored with the transaction.">
    @if($metadata === [])
      <p class="text-sm text-gray-500 dark:text-gray-400">No metadata was recorded for this entry.</p>
    @else
      <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach($metadata as $key => $value)
          <div class="rounded-2xl border border-gray-200 p-4 dark:border-gray-800">
            <div class="{{ $labelClass }}">{{ str((string) $key)->replace('_', ' ')->title() }}</div>
            <div class="{{ $valueClass }}">{{ is_array($value) || is_object($value) ? json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : ($value === null ? 'null' : (is_bool($value) ? ($value ? 'true' : 'false') : $value)) }}</div>
          </div>
        @endforeach
      </div>
      <details class="mt-5">
        <summary class="cursor-pointer text-sm font-semibold text-brand-600 dark:text-brand-400">View raw JSON</summary>
        <pre class="mt-3 overflow-x-auto rounded-2xl bg-gray-950 p-4 text-xs text-gray-100">{{ json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}</pre>
      </details>
    @endif
  </x-common.component-card>
</div>
@endsection
