@extends('layouts.admin-tailadmin')
@section('title','Hosts')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $hostCollection = $hosts->getCollection();
  $blockedCount = $hostCollection->where('is_blocked', true)->count();
  $agencyLinkedCount = $hostCollection->filter(fn ($host) => !empty($host->agency_id))->count();
  $photoCount = (int) $hostCollection->sum(fn ($host) => $host->relationLoaded('photos') ? $host->photos->count() : (int) ($host->photos_count ?? 0));
@endphp

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="dark">Talent</x-ui.badge>
            <x-ui.badge color="brand">Host Operations</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Hosts</h2>
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Manage host accounts, agency assignments, follower strength, and moderation status from one premium control surface.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-3">
          <x-admin.stat-card label="Visible Hosts" :value="number_format($hosts->total())" meta="Matching the current search scope" />
          <x-admin.stat-card label="Blocked" :value="number_format($blockedCount)" meta="Blocked hosts on this page" tone="warning" />
          <x-admin.stat-card label="Agency Linked" :value="number_format($agencyLinkedCount)" meta="Hosts attached to an agency on this page" tone="dark" />
        </div>
      </div>
    </div>
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
          <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Host Directory</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Search by host identity, review account health, and jump directly into moderation or payout edits.</p>
          </div>
          <div class="flex items-center gap-2">
            <x-ui.badge color="dark">{{ number_format($photoCount) }} photos referenced</x-ui.badge>
          </div>
        </div>

        <form class="grid gap-3 lg:grid-cols-[minmax(0,1fr)_auto]" method="get">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
            <input class="{{ $inputClass }}" name="s" value="{{ request('s') }}" placeholder="Search stage name, user name, or email">
          </div>
          <div class="flex flex-wrap items-end justify-end gap-3">
            <x-ui.button variant="outline" type="submit" size="sm">Search</x-ui.button>
            <x-ui.button variant="outline" href="{{ route('admin.hosts.index') }}" size="sm">Reset</x-ui.button>
          </div>
        </form>
      </div>
    </x-slot:header>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">#</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Stage Name</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">User</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Phone</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Agency</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Followers</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Photos</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($hosts as $h)
            @php
              $count = $h->relationLoaded('photos') ? $h->photos->count() : ($h->photos_count ?? 0);
            @endphp
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3">{{ $h->id }}</td>
              <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $h->stage_name ?: '—' }}</td>
              <td class="px-4 py-3">
                <div class="font-medium text-gray-900 dark:text-white">{{ $h->user?->name ?? '—' }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $h->user?->email ?? '' }}</div>
              </td>
              <td class="px-4 py-3">{{ $h->contact_phone ?: '—' }}</td>
              <td class="px-4 py-3">{{ $h->agency?->name ?: '—' }}</td>
              <td class="px-4 py-3"><x-ui.badge color="brand">{{ number_format($h->followers_count ?? 0) }}</x-ui.badge></td>
              <td class="px-4 py-3"><x-ui.badge color="dark">{{ $count }}</x-ui.badge></td>
              <td class="px-4 py-3">
                <x-ui.badge :color="$h->is_blocked ? 'danger' : 'success'">{{ $h->is_blocked ? 'Blocked' : 'Active' }}</x-ui.badge>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex flex-wrap justify-end gap-2">
                  <x-ui.button href="{{ route('admin.hosts.edit', $h) }}" size="sm">Edit</x-ui.button>
                  @if($h->is_blocked)
                    <form method="post" action="{{ route('admin.hosts.unblock', $h) }}">
                      @csrf
                      <x-ui.button variant="success" size="sm" type="submit">Unblock</x-ui.button>
                    </form>
                  @else
                    <form method="post" action="{{ route('admin.hosts.block', $h) }}">
                      @csrf
                      <x-ui.button variant="danger" size="sm" type="submit">Block</x-ui.button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No hosts match the current filters.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <x-slot:footer>
      <div class="flex justify-end">
        {{ $hosts->withQueryString()->links() }}
      </div>
    </x-slot:footer>
  </x-common.component-card>
</div>
@endsection
