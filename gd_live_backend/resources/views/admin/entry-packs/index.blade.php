@extends('layouts.admin-tailadmin')
@section('title', 'Entry Packs')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('page_actions')
  <div class="flex gap-3">
    <x-ui.button variant="outline" size="sm" href="{{ route('admin.entry-packs.reports') }}">Open Reports</x-ui.button>
    <x-ui.button size="sm" href="{{ route('admin.entry-packs.create') }}">New Pack</x-ui.button>
  </div>
@endsection

@section('content')
<div class="space-y-6">
  <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <x-admin.stat-card label="Purchases" :value="number_format($report['purchases'] ?? 0)" tone="brand" />
    <x-admin.stat-card label="Coins Spent" :value="number_format($report['coins_spent'] ?? 0)" tone="dark" />
    <x-admin.stat-card label="Active Users" :value="number_format($report['active_users'] ?? 0)" tone="success" />
    <x-admin.stat-card label="Expiry Churn" :value="number_format($report['expiry_churn_rate'] ?? 0, 1).'%' " meta="Usage and retention" tone="warning" />
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div class="max-w-2xl">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Entry Pack Catalog</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage premium room entry effects, pricing, and runtime behavior from one catalog view.</p>
        </div>

        <form method="get" class="grid gap-3 sm:grid-cols-[220px_160px_auto]">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
            <input class="{{ $inputClass }}" name="s" value="{{ request('s') }}" placeholder="Search pack name">
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
            <select name="active" class="{{ $inputClass }}">
              <option value="">Any</option>
              <option value="1" @selected(request('active') === '1')>Active</option>
              <option value="0" @selected(request('active') === '0')>Inactive</option>
            </select>
          </div>
          <div class="flex items-end gap-3">
            <x-ui.button type="submit" size="sm">Filter</x-ui.button>
            <x-ui.button variant="outline" size="sm" href="{{ route('admin.entry-packs.index') }}">Reset</x-ui.button>
          </div>
        </form>
      </div>
    </x-slot:header>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Pack</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Coins</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Style</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Priority</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">FX</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Validity</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Sort</th>
            <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($packs as $pack)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-4">
                <div class="font-semibold text-gray-900 dark:text-white">{{ $pack->name }}</div>
                <div class="mt-1 max-w-[320px] truncate text-xs text-gray-500 dark:text-gray-400">{{ $pack->svg_url ?: 'No asset URL' }}</div>
              </td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ number_format($pack->price_coins) }}</td>
              <td class="px-4 py-4"><x-ui.badge color="dark">{{ strtoupper($pack->animation_style) }}</x-ui.badge></td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $pack->priority }}</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $pack->duration_ms }}ms</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $pack->duration_days }} days</td>
              <td class="px-4 py-4"><x-ui.badge :color="$pack->is_active ? 'success' : 'dark'">{{ $pack->is_active ? 'Active' : 'Inactive' }}</x-ui.badge></td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $pack->sort_order }}</td>
              <td class="px-4 py-4 text-right">
                <div class="flex justify-end gap-2">
                  <x-ui.button variant="outline" size="sm" href="{{ route('admin.entry-packs.edit', $pack) }}">Edit</x-ui.button>
                  <form method="post" action="{{ route('admin.entry-packs.destroy', $pack) }}" onsubmit="return confirm('Delete this entry pack?')">
                    @csrf
                    @method('DELETE')
                    <x-ui.button variant="danger" size="sm" type="submit">Delete</x-ui.button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="9" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No entry packs found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <x-slot:footer>
      <div class="flex justify-end">
        {{ $packs->withQueryString()->links() }}
      </div>
    </x-slot:footer>
  </x-common.component-card>
</div>
@endsection
