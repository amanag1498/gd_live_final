@extends('layouts.admin-tailadmin')
@section('title','Wallet · '.$user->name)

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $textareaClass = 'w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $adminWalletCategoryOptions = [
    'recharge' => 'Recharge',
    'purchase' => 'Wallet purchase',
    'gift' => 'Gift',
    'subscription' => 'Subscription purchase',
    'entry_pack_purchase' => 'Entry pack purchase',
    'game_bet_debit' => 'Teen Patti bet debit',
    'game_payout_credit' => 'Teen Patti payout credit',
    'game_refund_credit' => 'Teen Patti refund credit',
    'agency_credit' => 'Agency wallet credit',
    'video_call' => 'Video call',
    'adjustment' => 'Adjustment',
    'other' => 'Other',
  ];

  $adminWalletTxLabel = function ($tx) {
      $reference = trim((string) ($tx->reference ?? ''));
      $category = strtolower(trim((string) ($tx->category ?? '')));
      $type = strtolower(trim((string) ($tx->type ?? '')));

      if (str_starts_with($reference, 'ENTRY_PACK_PURCHASE:')) {
          return 'Entry pack purchase';
      }

      return match ($category) {
          'subscription' => 'Subscription purchase',
          'recharge', 'purchase' => 'Wallet recharge',
          'gift' => 'Gift sent',
          'game_bet_debit' => 'Teen Patti bet debit',
          'game_payout_credit' => 'Teen Patti payout credit',
          'game_refund_credit' => 'Teen Patti refund credit',
          'agency_credit' => 'Agency wallet credit',
          'video_call' => 'Video call spend',
          'adjustment' => $type === 'credit' ? 'Wallet credit' : 'Wallet debit',
          'other' => $type === 'credit' ? 'Wallet credit' : 'Wallet spend',
          default => str_replace('_', ' ', ucfirst($category)),
      };
  };

  $rows = isset($transactions) ? $transactions->items() : $wallet->transactions;
  $credited = collect($rows)->where('type','credit')->sum('coins');
  $debited = collect($rows)->where('type','debit')->sum('coins');
@endphp

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ route('admin.users.show', $user) }}">User Profile</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-gray-900 via-gray-900 to-brand-900 text-white dark:border-gray-800">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="light">Wallet</x-ui.badge>
            <x-ui.badge :color="$user->is_blocked ? 'danger' : 'success'">{{ $user->is_blocked ? 'Blocked' : 'Active' }}</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-white">{{ $user->name }}</h2>
          <p class="mt-2 text-sm text-gray-300">{{ $user->email }}</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
          <x-admin.stat-card label="Balance" :value="number_format($wallet->balance)" meta="Current coin balance" />
          <x-admin.stat-card label="Lifetime Spend" :value="number_format($levelProgress['lifetime_spend_coins'] ?? 0)" meta="Coins counted toward level progression" tone="warning" />
          <x-admin.stat-card label="Transactions" :value="number_format($transactions?->total() ?? $wallet->transactions->count())" meta="Ledger rows available" tone="dark" />
          <x-admin.stat-card label="Credited" :value="number_format($credited)" meta="Credits in current filtered ledger" tone="success" />
          <x-admin.stat-card label="Debited" :value="number_format($debited)" meta="Debits in current filtered ledger" tone="danger" />
        </div>
      </div>
    </div>
  </section>

  <section class="grid gap-6 xl:grid-cols-[380px_minmax(0,1fr)]">
    <div class="space-y-6">
      <x-common.component-card title="Wallet Profile" desc="Snapshot of balance state and progression for this user.">
        <div class="space-y-4 text-sm">
          <div class="flex items-start justify-between gap-4">
            <span class="text-gray-500 dark:text-gray-400">Balance</span>
            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($wallet->balance) }} coins</span>
          </div>
          <div class="flex items-start justify-between gap-4">
            <span class="text-gray-500 dark:text-gray-400">Level</span>
            <div class="text-right">
              @if($user->level)
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold text-white" style="background: {{ $user->level->badge_color ?: '#6c757d' }}">L{{ $user->level->level }} · {{ $user->level->title }}</span>
              @else
                <span class="text-gray-500 dark:text-gray-400">Unassigned</span>
              @endif
            </div>
          </div>
          <div class="flex items-start justify-between gap-4">
            <span class="text-gray-500 dark:text-gray-400">Lifetime Spend</span>
            <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($levelProgress['lifetime_spend_coins'] ?? 0) }}</span>
          </div>
          <div class="flex items-start justify-between gap-4">
            <span class="text-gray-500 dark:text-gray-400">Next Level</span>
            <div class="text-right text-gray-900 dark:text-white">
              @if(!empty($levelProgress['next_level']))
                <div class="font-semibold">L{{ $levelProgress['next_level'] }} · {{ $levelProgress['next_level_title'] }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($levelProgress['remaining_spend_to_next_level'] ?? 0) }} coins remaining</div>
              @else
                <span class="text-gray-500 dark:text-gray-400">Top active level reached</span>
              @endif
            </div>
          </div>
        </div>

        @if(!empty($levelProgress['next_level_required_spend']))
          <div class="mt-5">
            <div class="mb-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
              <span>Level progress</span>
              <span>{{ number_format($levelProgress['lifetime_spend_coins'] ?? 0) }} / {{ number_format($levelProgress['next_level_required_spend']) }}</span>
            </div>
            <div class="h-2.5 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
              <div class="h-full rounded-full bg-brand-500" style="width: {{ (float) ($levelProgress['progress_percent'] ?? 0) }}%"></div>
            </div>
          </div>
        @endif
      </x-common.component-card>

      <x-common.component-card title="Record Purchase" desc="Convert money to coins and attach gateway-level metadata.">
        <form method="post" action="{{ route('admin.wallets.purchase',$user) }}" class="space-y-4">
          @csrf
          <div class="grid gap-4 sm:grid-cols-2">
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Coins</label>
              <input type="number" name="coins" min="1" class="{{ $inputClass }}" required>
            </div>
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Amount</label>
              <input type="number" name="amount" step="0.01" min="0.01" class="{{ $inputClass }}" required>
            </div>
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Currency</label>
              <input type="text" name="currency" value="INR" maxlength="3" class="{{ $inputClass }}">
            </div>
            <div>
              <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Gateway</label>
              <input type="text" name="gateway" class="{{ $inputClass }}" placeholder="razorpay / stripe">
            </div>
          </div>
          <input type="text" name="transaction_id" class="{{ $inputClass }}" placeholder="Gateway transaction ID">
          <input type="text" name="reference" class="{{ $inputClass }}" placeholder="Reference (optional)">
          <textarea name="note" rows="3" class="{{ $textareaClass }}" placeholder="Note (optional)"></textarea>
          <div class="flex justify-end">
            <x-ui.button type="submit" size="sm">Record Purchase</x-ui.button>
          </div>
        </form>
      </x-common.component-card>

      <x-common.component-card title="Manual Coin Actions" desc="Direct wallet adjustments and spend simulation for support and reconciliation.">
        <div class="space-y-5">
          <form method="post" action="{{ route('admin.wallets.credit',$user) }}" class="space-y-3 rounded-2xl border border-success-200 bg-success-50/50 p-4 dark:border-success-500/20 dark:bg-success-500/5">
            @csrf
            <div class="text-sm font-semibold text-gray-900 dark:text-white">Admin Credit</div>
            <input type="number" name="amount" min="1" class="{{ $inputClass }}" placeholder="Coins" required>
            <input type="text" name="reference" class="{{ $inputClass }}" placeholder="Reference (optional)">
            <textarea name="note" rows="2" class="{{ $textareaClass }}" placeholder="Note (optional)"></textarea>
            <div class="flex justify-end">
              <x-ui.button variant="success" size="sm" type="submit">Credit</x-ui.button>
            </div>
          </form>

          <form method="post" action="{{ route('admin.wallets.debit',$user) }}" class="space-y-3 rounded-2xl border border-error-200 bg-error-50/50 p-4 dark:border-error-500/20 dark:bg-error-500/5">
            @csrf
            <div class="text-sm font-semibold text-gray-900 dark:text-white">Admin Debit</div>
            <input type="number" name="amount" min="1" class="{{ $inputClass }}" placeholder="Coins" required>
            <input type="text" name="reference" class="{{ $inputClass }}" placeholder="Reference (optional)">
            <textarea name="note" rows="2" class="{{ $textareaClass }}" placeholder="Note (optional)"></textarea>
            <div class="flex justify-end">
              <x-ui.button variant="danger" size="sm" type="submit">Debit</x-ui.button>
            </div>
          </form>

          <form method="post" action="{{ route('admin.wallets.spend',$user) }}" class="space-y-3 rounded-2xl border border-warning-200 bg-warning-50/50 p-4 dark:border-warning-500/20 dark:bg-warning-500/5">
            @csrf
            <div class="text-sm font-semibold text-gray-900 dark:text-white">Record Spend</div>
            <div class="grid gap-4 sm:grid-cols-2">
              <input type="number" name="coins" min="1" class="{{ $inputClass }}" placeholder="Coins" required>
              <select name="category" class="{{ $inputClass }}">
                <option value="gift">Gift</option>
                <option value="video_call">Video Call</option>
                <option value="other">Other</option>
              </select>
            </div>
            <input type="number" name="counterparty_user_id" class="{{ $inputClass }}" placeholder="Counterparty user ID (optional)">
            <input type="text" name="reference" class="{{ $inputClass }}" placeholder="Reference (optional)">
            <textarea name="note" rows="2" class="{{ $textareaClass }}" placeholder="Note (optional)"></textarea>
            <div class="flex justify-end">
              <x-ui.button variant="warning" size="sm" type="submit">Record Spend</x-ui.button>
            </div>
          </form>
        </div>
      </x-common.component-card>
    </div>

    <div class="space-y-6">
      <x-common.component-card title="Level Progress" desc="Current progression state and recent level updates.">
        @if($levelHistory->isNotEmpty())
          <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
              <thead class="bg-gray-50 dark:bg-gray-950/60">
                <tr>
                  <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">From</th>
                  <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">To</th>
                  <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Spend</th>
                  <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Triggered By</th>
                  <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">When</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @foreach($levelHistory as $row)
                  <tr class="bg-white dark:bg-gray-900">
                    <td class="px-4 py-3">{{ $row->oldLevel?->title ? 'L'.$row->oldLevel->level.' · '.$row->oldLevel->title : '—' }}</td>
                    <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ 'L'.$row->newLevel->level.' · '.$row->newLevel->title }}</td>
                    <td class="px-4 py-3">{{ number_format($row->lifetime_spend_coins) }}</td>
                    <td class="px-4 py-3">{{ $row->triggered_by_transaction_id ? '#'.$row->triggered_by_transaction_id : 'Recalculate' }}</td>
                    <td class="px-4 py-3">{{ $row->created_at?->format('d M Y, H:i') }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="rounded-2xl border border-dashed border-gray-300 px-4 py-8 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">No level history recorded for this user yet.</div>
        @endif
      </x-common.component-card>

      <x-common.component-card>
        <x-slot:header>
          <div class="flex flex-col gap-4">
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Ledger Filters</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Narrow the transaction stream by type, category, linked call, recharge status, or date range.</p>
            </div>
            <form method="get" class="grid gap-3 xl:grid-cols-6">
              <select name="type" class="{{ $inputClass }}">
                <option value="">Any type</option>
                <option value="credit" @selected(request('type') === 'credit')>Credit</option>
                <option value="debit" @selected(request('type') === 'debit')>Debit</option>
              </select>
              <select name="category" class="{{ $inputClass }}">
                <option value="">Any category</option>
                @foreach($adminWalletCategoryOptions as $category => $label)
                  <option value="{{ $category }}" @selected(request('category') === $category)>{{ $label }}</option>
                @endforeach
              </select>
              <input type="number" name="call_id" value="{{ request('call_id') }}" class="{{ $inputClass }}" placeholder="Call ID">
              <select name="recharge_status" class="{{ $inputClass }}">
                <option value="">Any recharge</option>
                @foreach(['created','pending','success','failed','cancelled'] as $status)
                  <option value="{{ $status }}" @selected(request('recharge_status') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
              </select>
              <input type="date" name="date_from" value="{{ request('date_from') }}" class="{{ $inputClass }}">
              <input type="date" name="date_to" value="{{ request('date_to') }}" class="{{ $inputClass }}">
              <div class="xl:col-span-6 flex flex-wrap justify-end gap-3">
                <x-ui.button variant="outline" size="sm" type="submit">Apply</x-ui.button>
                <x-ui.button variant="outline" size="sm" href="{{ route('admin.wallets.show', $user) }}">Clear</x-ui.button>
              </div>
            </form>
          </div>
        </x-slot:header>
      </x-common.component-card>

      <x-common.component-card title="Reconciliation Snapshot" desc="Anomaly counts from the billing reconciliation service.">
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
          @foreach($reconciliation as $label => $value)
            <x-admin.stat-card :label="str_replace('_', ' ', ucfirst($label))" :value="number_format($value)" meta="Current anomaly count" />
          @endforeach
        </div>
      </x-common.component-card>

      <x-common.component-card>
        <x-slot:header>
          <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Ledger</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Transaction stream for this wallet with recharge, call, gift, and manual adjustment entries.</p>
            </div>
            <div class="flex items-center gap-2 text-sm">
              <x-ui.badge color="success">Credited: {{ number_format($credited) }}</x-ui.badge>
              <x-ui.badge color="danger">Debited: {{ number_format($debited) }}</x-ui.badge>
            </div>
          </div>
        </x-slot:header>

        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60">
              <tr>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">#</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Type</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Category</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Coins</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Amount</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Txn</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Gateway</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Counterparty</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Ref / Details</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Balance</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">When</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              @php $txs = $transactions ?? $wallet->transactions; @endphp
              @forelse($txs as $tx)
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3">{{ $tx->id }}</td>
                  <td class="px-4 py-3"><x-ui.badge :color="$tx->type === 'credit' ? 'success' : 'danger'">{{ ucfirst($tx->type) }}</x-ui.badge></td>
                  <td class="px-4 py-3"><x-ui.badge color="dark">{{ $adminWalletTxLabel($tx) }}</x-ui.badge></td>
                  <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ number_format($tx->coins) }}</td>
                  <td class="px-4 py-3 text-nowrap">
                    @if(!is_null($tx->amount))
                      {{ $tx->currency ?? 'INR' }} {{ number_format($tx->amount, 2) }}
                    @else
                      —
                    @endif
                  </td>
                  <td class="px-4 py-3"><code class="rounded-md bg-gray-100 px-2 py-1 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-200">{{ $tx->transaction_id ?? '-' }}</code></td>
                  <td class="px-4 py-3">{{ $tx->gateway ?? '—' }}</td>
                  <td class="px-4 py-3">
                    @if($tx->counterparty)
                      <div class="font-medium text-gray-900 dark:text-white">{{ $tx->counterparty->name }}</div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">{{ $tx->counterparty->email }}</div>
                    @else
                      —
                    @endif
                  </td>
                  <td class="px-4 py-3">
                    <code class="block rounded-md bg-gray-100 px-2 py-1 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-200">{{ $tx->reference ?? '-' }}</code>
                    @if($tx->reference_type && $tx->reference_id)
                      <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ $tx->reference_type }} #{{ $tx->reference_id }}</div>
                    @endif
                    @if($tx->description)
                      <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $tx->description }}</div>
                    @endif
                    @if(data_get($tx->meta, 'agency_id'))
                      <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Agency #{{ data_get($tx->meta, 'agency_id') }}{{ data_get($tx->meta, 'agency_name') ? ' · '.data_get($tx->meta, 'agency_name') : '' }}</div>
                    @endif
                    @if(data_get($tx->meta, 'credited_by_admin_user_id'))
                      <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Credited by admin #{{ data_get($tx->meta, 'credited_by_admin_user_id') }}{{ data_get($tx->meta, 'credited_by_admin_name') ? ' · '.data_get($tx->meta, 'credited_by_admin_name') : '' }}</div>
                    @endif
                    @if(data_get($tx->meta, 'credited_by_agency_user_id'))
                      <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Credited by agency user #{{ data_get($tx->meta, 'credited_by_agency_user_id') }}{{ data_get($tx->meta, 'credited_by_agency_user_name') ? ' · '.data_get($tx->meta, 'credited_by_agency_user_name') : '' }}</div>
                    @endif
                    @if(data_get($tx->meta, 'note'))
                      <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ data_get($tx->meta, 'note') }}</div>
                    @endif
                  </td>
                  <td class="px-4 py-3 text-nowrap">
                    @if(!is_null($tx->balance_before) || !is_null($tx->balance_after))
                      <span class="text-sm text-gray-600 dark:text-gray-300">{{ number_format((int) $tx->balance_before) }} → {{ number_format((int) $tx->balance_after) }}</span>
                    @else
                      —
                    @endif
                  </td>
                  <td class="px-4 py-3">{{ $tx->created_at?->format('d M Y, H:i') }}</td>
                </tr>
              @empty
                <tr class="bg-white dark:bg-gray-900">
                  <td colspan="11" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No transactions.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        @if(isset($transactions) && method_exists($transactions,'links'))
          <x-slot:footer>
            <div class="flex justify-end">
              {{ $transactions->withQueryString()->links() }}
            </div>
          </x-slot:footer>
        @endif
      </x-common.component-card>
    </div>
  </section>
</div>
@endsection
