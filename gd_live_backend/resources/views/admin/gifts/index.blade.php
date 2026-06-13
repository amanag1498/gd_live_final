@extends('layouts.admin-tailadmin')
@section('title','Gifts')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $giftCollection = $gifts->getCollection();
  $activeCount = $giftCollection->where('is_active', true)->count();
  $avgCoins = $giftCollection->count() ? round($giftCollection->avg('coins')) : 0;
@endphp

@section('page_actions')
  <x-ui.button size="sm" href="{{ route('admin.gifts.create') }}">New Gift</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="dark">Catalog</x-ui.badge>
            <x-ui.badge color="brand">Gifts</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Gifts</h2>
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Manage gift pricing, animation settings, visibility, and catalog sorting for the live gifting system.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-3">
          <x-admin.stat-card label="Visible Gifts" :value="number_format($gifts->total())" meta="Filtered catalog results" />
          <x-admin.stat-card label="Active" :value="number_format($activeCount)" meta="Active gifts on this page" tone="success" />
          <x-admin.stat-card label="Average Cost" :value="number_format($avgCoins)" meta="Average coin price on this page" tone="dark" />
        </div>
      </div>
    </div>
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Gift Catalog</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Search gifts, filter by status, and manage animation tiers or pricing.</p>
        </div>

        <form method="get" class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_180px_auto]">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
            <input class="{{ $inputClass }}" name="s" value="{{ request('s') }}" placeholder="Search by gift name">
          </div>
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
            <select name="active" class="{{ $inputClass }}">
              <option value="">Any</option>
              <option value="1" @selected(request('active') === '1')>Active</option>
              <option value="0" @selected(request('active') === '0')>Inactive</option>
            </select>
          </div>
          <div class="flex flex-wrap items-end justify-end gap-3">
            <x-ui.button variant="outline" type="submit" size="sm">Filter</x-ui.button>
            <x-ui.button variant="outline" href="{{ route('admin.gifts.index') }}" size="sm">Reset</x-ui.button>
          </div>
        </form>
      </div>
    </x-slot:header>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">#</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Name</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Coins</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Preview</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Type</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Tier</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Duration</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Sort</th>
            <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($gifts as $g)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3">{{ $g->id }}</td>
              <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $g->name }}</td>
              <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ number_format($g->coins) }}</td>
              <td class="px-4 py-3">
                @if($g->gift_url)
                  <img src="{{ $g->gift_url }}" alt="gift" class="h-10 rounded-lg object-contain">
                @else
                  <span class="text-gray-500 dark:text-gray-400">—</span>
                @endif
              </td>
              <td class="px-4 py-3">{{ $g->gift_type ?: 'auto' }}</td>
              <td class="px-4 py-3">{{ $g->animation_tier ?: 'auto' }}</td>
              <td class="px-4 py-3">{{ $g->animation_duration_ms ? number_format($g->animation_duration_ms).' ms' : 'auto' }}</td>
              <td class="px-4 py-3"><x-ui.badge :color="$g->is_active ? 'success' : 'dark'">{{ $g->is_active ? 'Active' : 'Inactive' }}</x-ui.badge></td>
              <td class="px-4 py-3">{{ $g->sort_order }}</td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap justify-end gap-2">
                  <x-ui.button variant="outline" size="sm" href="{{ route('admin.gifts.edit',$g) }}">Edit</x-ui.button>
                  <form method="post" action="{{ route('admin.gifts.destroy',$g) }}" onsubmit="return confirm('Delete this gift?')">
                    @csrf
                    @method('DELETE')
                    <x-ui.button variant="danger" size="sm" type="submit">Delete</x-ui.button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="10" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No gifts found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <x-slot:footer>
      <div class="flex justify-end">
        {{ $gifts->withQueryString()->links() }}
      </div>
    </x-slot:footer>
  </x-common.component-card>
</div>
@endsection
