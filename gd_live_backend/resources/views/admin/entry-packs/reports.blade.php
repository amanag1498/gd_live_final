@extends('layouts.admin-tailadmin')
@section('title', 'Entry Pack Reports')

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ route('admin.entry-packs.index') }}">Back to Packs</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
    <x-admin.stat-card label="Ownership Records" :value="number_format($report['ownerships'] ?? 0)" tone="brand" />
    <x-admin.stat-card label="Paid Purchases" :value="number_format($report['paid_purchases'] ?? 0)" :meta="number_format($report['coins_spent'] ?? 0).' coins spent'" tone="dark" />
    <x-admin.stat-card label="Wheel Grants" :value="number_format($report['wheel_grants'] ?? 0)" :meta="number_format($report['wheel_grant_hours'] ?? 0).' hours granted'" tone="brand" />
    <x-admin.stat-card label="Active Users" :value="number_format($report['active_users'] ?? 0)" tone="success" />
    <x-admin.stat-card label="Expired Ownerships" :value="number_format($report['expired_owned'] ?? 0)" tone="warning" />
  </section>

  <div class="grid gap-6 xl:grid-cols-[360px_minmax(0,1fr)]">
    <x-common.component-card title="Most Owned Packs" desc="Current ownership records by entry pack.">
      <div class="space-y-3">
        @forelse(($report['most_used_packs'] ?? []) as $pack)
          <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-start justify-between gap-3">
              <div>
                <div class="font-semibold text-gray-900 dark:text-white">{{ $pack['name'] }}</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pack #{{ $pack['id'] }}</div>
              </div>
              <x-ui.badge color="dark">{{ number_format($pack['ownerships']) }} ownerships</x-ui.badge>
            </div>
            <div class="mt-3 text-sm text-gray-600 dark:text-gray-300">Price: {{ number_format($pack['price_coins']) }} coins</div>
          </div>
        @empty
          <div class="rounded-2xl border border-dashed border-gray-300 px-4 py-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
            No pack usage yet.
          </div>
        @endforelse
      </div>
    </x-common.component-card>

    <x-common.component-card>
      <x-slot:header>
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Current Ownership Records</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Current pack state and accumulated expiry. Purchase and wheel-grant events are audited separately below.</p>
        </div>
      </x-slot:header>

      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60">
            <tr>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Ownership</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">User</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Pack</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Purchased</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Expires</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
              <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse($recentPurchases as $purchase)
              <tr class="bg-white dark:bg-gray-900">
                <td class="px-4 py-4 font-medium text-gray-900 dark:text-white">#{{ $purchase->id }}</td>
                <td class="px-4 py-4">
                  <div class="font-semibold text-gray-900 dark:text-white">
                    @if($purchase->user)
                      <a class="text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.users.show', $purchase->user) }}">{{ $purchase->user->name }}</a>
                    @else
                      User
                    @endif
                  </div>
                  <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $purchase->user?->email }}</div>
                </td>
                <td class="px-4 py-4">
                  <div class="font-medium text-gray-900 dark:text-white">{{ $purchase->entryPack?->name ?? 'Pack' }}</div>
                  <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ strtoupper($purchase->entryPack?->animation_style ?? 'banner') }}</div>
                </td>
                <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ optional($purchase->purchased_at)->format('d M Y H:i') }}</td>
                <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ optional($purchase->expires_at)->format('d M Y H:i') ?: 'Not set' }}</td>
                <td class="px-4 py-4">
                  <x-ui.badge :color="$purchase->is_active ? 'success' : 'dark'">{{ $purchase->is_active ? 'Active' : 'Inactive' }}</x-ui.badge>
                </td>
                <td class="px-4 py-4 text-right">
                  <div class="flex justify-end gap-2">
                    @if($purchase->user)
                      <x-ui.button variant="outline" size="sm" href="{{ route('admin.users.show', $purchase->user) }}">Profile</x-ui.button>
                    @endif
                    <x-ui.button variant="outline" size="sm" href="{{ route('admin.entry-packs.purchases.edit', $purchase) }}">Edit</x-ui.button>
                  </div>
                </td>
              </tr>
            @empty
              <tr class="bg-white dark:bg-gray-900">
                <td colspan="7" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No ownership records yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <x-slot:footer>
        <div class="flex justify-end">
          {{ $recentPurchases->links() }}
        </div>
      </x-slot:footer>
    </x-common.component-card>
  </div>

  <x-common.component-card>
    <x-slot:header>
      <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Paid Purchase History</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Wallet-backed entry-pack purchases only. Fortune Wheel grants are excluded from this table.</p>
      </div>
    </x-slot:header>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60"><tr>
          <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Transaction</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">User</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Pack</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Coins</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Created</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($paidPurchases as $purchaseEvent)
            @php($purchasePack = $purchasePacks->get((int) data_get($purchaseEvent->meta, 'entry_pack_id')))
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-4 font-medium text-gray-900 dark:text-white">Txn #{{ $purchaseEvent->id }}</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $purchaseEvent->wallet?->user?->name ?? 'User' }}<div class="text-xs text-gray-500">#{{ $purchaseEvent->wallet?->user?->id }} · {{ $purchaseEvent->wallet?->user?->email }}</div></td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $purchasePack?->name ?? data_get($purchaseEvent->meta, 'entry_pack_name', 'Pack') }}</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ number_format((int) $purchaseEvent->coins) }}</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ optional($purchaseEvent->created_at)->format('d M Y H:i:s') }}</td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900"><td colspan="5" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No paid purchases yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <x-slot:footer><div class="flex justify-end">{{ $paidPurchases->links() }}</div></x-slot:footer>
  </x-common.component-card>

  <x-common.component-card>
    <x-slot:header>
      <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Fortune Wheel Grant History</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">One immutable row per winning spin, even when repeated wins extend the same ownership record.</p>
      </div>
    </x-slot:header>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60"><tr>
          <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Spin</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">User</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Pack</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Grant</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Duration</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Business Date</th>
          <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Created</th>
        </tr></thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($wheelGrants as $grant)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-4 font-medium text-gray-900 dark:text-white">#{{ $grant->id }}</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $grant->user?->name ?? 'User' }}<div class="text-xs text-gray-500">#{{ $grant->user_id }} · {{ $grant->user?->email }}</div></td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $grant->entryPack?->name ?? 'Pack' }}</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">#{{ $grant->user_entry_pack_id }}</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ number_format((int) $grant->reward_duration_hours) }} hours</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ optional($grant->spun_for_date)->format('Y-m-d') }}</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ optional($grant->created_at)->format('d M Y H:i:s') }}</td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900"><td colspan="7" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No Fortune Wheel entry-pack grants yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <x-slot:footer><div class="flex justify-end">{{ $wheelGrants->links() }}</div></x-slot:footer>
  </x-common.component-card>
</div>
@endsection
