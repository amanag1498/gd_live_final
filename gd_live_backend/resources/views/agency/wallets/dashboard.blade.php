@extends('layouts.agency-tailadmin')
@section('title', 'Agency Wallet')
@section('page_intro', 'Separate treasury balance for agency loads and user coin credits with linked wallet ledger visibility.')

@php
  $inputClass = 'block w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
@endphp

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ $walletRoute ?? (request()->routeIs('admin.*') ? route('admin.agencies.wallet.show', $agency) : route('agency.wallet.show')) }}">Refresh</x-ui.button>
  @if(request()->routeIs('admin.*'))
    <x-ui.button size="sm" href="{{ route('admin.reports.agency-wallets.index', ['agency_id' => $agency->id]) }}">Global Report</x-ui.button>
  @endif
@endsection

@section('content')
  <div class="space-y-6">
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <x-admin.stat-card label="Treasury Balance" :value="number_format($walletSummary['balance'] ?? 0)" meta="Current agency coin balance" />
      <x-admin.stat-card label="Admin Loaded" :value="number_format($walletSummary['total_loaded'] ?? 0)" :meta="number_format($walletSummary['loads_recorded'] ?? 0).' load events'" tone="brand" />
      <x-admin.stat-card label="Distributed" :value="number_format($walletSummary['total_distributed'] ?? 0)" meta="Coins moved to users" tone="warning" />
      <x-admin.stat-card label="User Credits" :value="number_format($walletSummary['credits_issued'] ?? 0)" meta="Completed transfer records" tone="dark" />
    </section>

    <section class="grid gap-6 xl:grid-cols-[minmax(320px,0.75fr)_minmax(0,1.25fr)]">
      <div class="space-y-6">
        @if($canLoadWallet ?? false)
          <x-common.component-card title="Admin Load Agency Wallet" desc="Load treasury coins into the agency balance with a traceable reference.">
            <form method="post" action="{{ route('admin.agencies.wallet.load', $agency) }}" class="space-y-4">
              @csrf
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Coins</label>
                <input type="number" name="coins" min="1" class="{{ $inputClass }}" required>
              </div>
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Reference</label>
                <input type="text" name="reference" class="{{ $inputClass }}" placeholder="Invoice / batch / remark">
              </div>
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Note</label>
                <textarea name="note" rows="4" class="{{ $inputClass }}" placeholder="Why this load was made"></textarea>
              </div>
              <button class="inline-flex w-full items-center justify-center rounded-2xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-600">Load Wallet</button>
            </form>
          </x-common.component-card>
        @endif

        @if($canCreditUsers ?? false)
          <x-common.component-card title="Credit User From Agency Wallet" desc="Move treasury balance into a user wallet and keep the transfer linked.">
            <form method="post" action="{{ request()->routeIs('admin.*') ? route('admin.agencies.wallet.credit-user', $agency) : route('agency.wallet.credit-user') }}" class="space-y-4">
              @csrf
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Target User ID</label>
                <input type="number" name="target_user_id" min="1" class="{{ $inputClass }}" required>
              </div>
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Coins</label>
                <input type="number" name="coins" min="1" class="{{ $inputClass }}" required>
              </div>
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Reference</label>
                <input type="text" name="reference" class="{{ $inputClass }}" placeholder="Campaign / support / recharge ref">
              </div>
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Note</label>
                <textarea name="note" rows="4" class="{{ $inputClass }}" placeholder="Why this user is being credited"></textarea>
              </div>
              <button class="inline-flex w-full items-center justify-center rounded-2xl bg-success-500 px-4 py-3 text-sm font-semibold text-white hover:bg-success-600">Credit User</button>
            </form>
          </x-common.component-card>
        @endif
      </div>

      <div class="space-y-6">
        <x-common.component-card>
          <x-slot:header>
            <div class="flex items-center justify-between gap-3">
              <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Agency Wallet Ledger</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Separate agency treasury transactions.</p>
              </div>
            </div>
          </x-slot:header>
          <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
              <thead class="bg-gray-50 dark:bg-gray-950/60">
                <tr>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Type</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Category</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Coins</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Balance</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Target User</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Actor</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Time</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($walletTransactions as $tx)
                  <tr class="bg-white dark:bg-gray-900">
                    <td class="px-4 py-3">{{ $tx->id }}</td>
                    <td class="px-4 py-3"><x-ui.badge :color="$tx->type === 'credit' ? 'success' : 'warning'">{{ strtoupper($tx->type) }}</x-ui.badge></td>
                    <td class="px-4 py-3">{{ str_replace('_', ' ', ucfirst($tx->category)) }}</td>
                    <td class="px-4 py-3">{{ number_format($tx->coins) }}</td>
                    <td class="px-4 py-3">{{ number_format($tx->balance_before) }} → {{ number_format($tx->balance_after) }}</td>
                    <td class="px-4 py-3">
                      @if($tx->targetUser)
                        <div class="font-semibold text-gray-900 dark:text-white">{{ $tx->targetUser->name }}</div>
                        <div class="text-gray-500 dark:text-gray-400">#{{ $tx->targetUser->id }}</div>
                      @else
                        <span class="text-gray-500 dark:text-gray-400">—</span>
                      @endif
                    </td>
                    <td class="px-4 py-3">
                      @if($tx->admin)
                        <div class="font-semibold text-gray-900 dark:text-white">{{ $tx->admin->name }}</div>
                        <div class="text-gray-500 dark:text-gray-400">Admin</div>
                      @elseif($tx->agencyUser)
                        <div class="font-semibold text-gray-900 dark:text-white">{{ $tx->agencyUser->name }}</div>
                        <div class="text-gray-500 dark:text-gray-400">Agency</div>
                      @else
                        <span class="text-gray-500 dark:text-gray-400">System</span>
                      @endif
                    </td>
                    <td class="px-4 py-3">{{ optional($tx->created_at)->format('d M Y, h:i A') }}</td>
                  </tr>
                @empty
                  <tr class="bg-white dark:bg-gray-900"><td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No agency wallet transactions yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="mt-4 flex justify-end">{{ $walletTransactions->withQueryString()->links() }}</div>
        </x-common.component-card>

        <x-common.component-card>
          <x-slot:header>
            <div class="flex items-center justify-between gap-3">
              <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Agency Coin Transfers</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Linked agency treasury and user wallet flow.</p>
              </div>
            </div>
          </x-slot:header>
          <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
              <thead class="bg-gray-50 dark:bg-gray-950/60">
                <tr>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Direction</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Coins</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">User</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Actor</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Linked Wallet Tx</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Time</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse($walletTransfers as $transfer)
                  <tr class="bg-white dark:bg-gray-900">
                    <td class="px-4 py-3">{{ $transfer->id }}</td>
                    <td class="px-4 py-3">{{ str_replace('_', ' ', ucfirst($transfer->direction)) }}</td>
                    <td class="px-4 py-3">{{ number_format($transfer->coins) }}</td>
                    <td class="px-4 py-3">
                      @if($transfer->targetUser)
                        <div class="font-semibold text-gray-900 dark:text-white">{{ $transfer->targetUser->name }}</div>
                        <div class="text-gray-500 dark:text-gray-400">#{{ $transfer->targetUser->id }}</div>
                      @else
                        <span class="text-gray-500 dark:text-gray-400">Agency treasury load</span>
                      @endif
                    </td>
                    <td class="px-4 py-3">
                      @if($transfer->admin)
                        <div class="font-semibold text-gray-900 dark:text-white">{{ $transfer->admin->name }}</div>
                        <div class="text-gray-500 dark:text-gray-400">Admin</div>
                      @elseif($transfer->agencyUser)
                        <div class="font-semibold text-gray-900 dark:text-white">{{ $transfer->agencyUser->name }}</div>
                        <div class="text-gray-500 dark:text-gray-400">Agency</div>
                      @else
                        <span class="text-gray-500 dark:text-gray-400">—</span>
                      @endif
                    </td>
                    <td class="px-4 py-3">
                      <div class="text-gray-500 dark:text-gray-400">Agency tx #{{ $transfer->agency_wallet_transaction_id }}</div>
                      @if($transfer->user_wallet_transaction_id)
                        <div class="text-gray-500 dark:text-gray-400">User tx #{{ $transfer->user_wallet_transaction_id }}</div>
                      @endif
                    </td>
                    <td class="px-4 py-3">{{ optional($transfer->created_at)->format('d M Y, h:i A') }}</td>
                  </tr>
                @empty
                  <tr class="bg-white dark:bg-gray-900"><td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No agency wallet transfers yet.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="mt-4 flex justify-end">{{ $walletTransfers->withQueryString()->links() }}</div>
        </x-common.component-card>
      </div>
    </section>

    @if(request()->routeIs('admin.*'))
      <x-common.component-card title="Recent Admin Audit" desc="Admin-visible audit trail for treasury operations.">
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60">
              <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Action</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Admin</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Target User</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Reason</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Time</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              @forelse($walletAudits as $audit)
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3">{{ $audit->id }}</td>
                  <td class="px-4 py-3">{{ str_replace('_', ' ', ucfirst($audit->action)) }}</td>
                  <td class="px-4 py-3">{{ $audit->admin?->name ?? '—' }}</td>
                  <td class="px-4 py-3">{{ $audit->targetUser?->name ? $audit->targetUser->name.' (#'.$audit->targetUser->id.')' : '—' }}</td>
                  <td class="px-4 py-3">{{ $audit->reason ?: '—' }}</td>
                  <td class="px-4 py-3">{{ optional($audit->created_at)->format('d M Y, h:i A') }}</td>
                </tr>
              @empty
                <tr class="bg-white dark:bg-gray-900"><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No audit records yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </x-common.component-card>
    @endif
  </div>
@endsection
