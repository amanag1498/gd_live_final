@extends('layouts.admin-tailadmin')
@section('title', 'Entry Pack Reports')

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ route('admin.entry-packs.index') }}">Back to Packs</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
    <x-admin.stat-card label="Purchases" :value="number_format($report['purchases'] ?? 0)" tone="brand" />
    <x-admin.stat-card label="Coins Spent" :value="number_format($report['coins_spent'] ?? 0)" tone="dark" />
    <x-admin.stat-card label="Active Users" :value="number_format($report['active_users'] ?? 0)" tone="success" />
    <x-admin.stat-card label="Expired Ownerships" :value="number_format($report['expired_owned'] ?? 0)" tone="warning" />
    <x-admin.stat-card label="Expiry Churn" :value="number_format($report['expiry_churn_rate'] ?? 0, 1).'%' " tone="danger" />
  </section>

  <div class="grid gap-6 xl:grid-cols-[360px_minmax(0,1fr)]">
    <x-common.component-card title="Most Used Packs" desc="Top-performing entry packs by purchase volume.">
      <div class="space-y-3">
        @forelse(($report['most_used_packs'] ?? []) as $pack)
          <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-start justify-between gap-3">
              <div>
                <div class="font-semibold text-gray-900 dark:text-white">{{ $pack['name'] }}</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pack #{{ $pack['id'] }}</div>
              </div>
              <x-ui.badge color="dark">{{ number_format($pack['purchases']) }} purchases</x-ui.badge>
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
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Recent Purchases</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Recent entry-pack purchases and renewal activity, with quick access to user and purchase controls.</p>
        </div>
      </x-slot:header>

      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60">
            <tr>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Purchase</th>
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
                <td colspan="7" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No purchases yet.</td>
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
</div>
@endsection
