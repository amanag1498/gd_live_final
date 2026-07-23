@extends('layouts.admin-tailadmin')
@section('title', 'Banners')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $summaryCards = [
    ['label' => 'Impressions', 'value' => number_format($performance['impressions'] ?? 0), 'tone' => 'brand'],
    ['label' => 'Clicks', 'value' => number_format($performance['clicks'] ?? 0), 'tone' => 'success'],
    ['label' => 'CTR', 'value' => number_format((float) ($performance['ctr'] ?? 0), 2).'%', 'tone' => 'warning'],
    ['label' => 'Unique Reach', 'value' => number_format($performance['unique_impressions'] ?? 0), 'tone' => 'dark'],
    ['label' => 'Repeat Impressions', 'value' => number_format($performance['repeat_impressions'] ?? 0), 'tone' => 'danger'],
  ];
@endphp

@section('page_actions')
  <x-ui.button size="sm" href="{{ route('admin.banners.create') }}">New Banner</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="dark">Catalog</x-ui.badge>
            <x-ui.badge color="brand">Banners</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Promotional Banners</h2>
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Manage placements, creative assets, click-through actions, audience targeting, and campaign scheduling from one premium control surface.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
          @foreach($summaryCards as $card)
            <x-admin.stat-card :label="$card['label']" :value="$card['value']" :tone="$card['tone']" />
          @endforeach
        </div>
      </div>
    </div>
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Campaign Filters</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Search banner titles, narrow the reporting window, and inspect performance by placement or state.</p>
        </div>

        <form method="get" class="grid gap-3 xl:grid-cols-[minmax(0,1.1fr)_170px_170px_170px_150px_auto]">
          <input class="{{ $inputClass }}" name="s" value="{{ request('s') }}" placeholder="Search title">
          <input type="date" class="{{ $inputClass }}" name="from" value="{{ request('from', $performance['from']) }}">
          <input type="date" class="{{ $inputClass }}" name="to" value="{{ request('to', $performance['to']) }}">
          <select name="placement" class="{{ $inputClass }}">
            <option value="">Any placement</option>
            @foreach($placements as $placement)
              <option value="{{ $placement }}" @selected(request('placement') === $placement)>{{ ucfirst($placement) }}</option>
            @endforeach
          </select>
          <select name="active" class="{{ $inputClass }}">
            <option value="">Any status</option>
            <option value="1" @selected(request('active') === '1')>Active</option>
            <option value="0" @selected(request('active') === '0')>Inactive</option>
          </select>
          <div class="flex flex-wrap items-center justify-end gap-3">
            <x-ui.button type="submit" size="sm">Apply</x-ui.button>
            <x-ui.button variant="outline" href="{{ route('admin.banners.index') }}" size="sm">Reset</x-ui.button>
          </div>
        </form>
      </div>
    </x-slot:header>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Banner</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Placement</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">CTA</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Audience</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Performance</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Schedule</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Sort</th>
            <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($banners as $banner)
            @php
              $previewUrl = (string) ($banner->preview_url ?? '');
              $platforms = !empty($banner->platforms) ? implode(', ', $banner->platforms) : 'All platforms';
              $roles = !empty($banner->target_roles) ? implode(', ', $banner->target_roles) : 'All roles';
              $ctr = (int) $banner->impressions_count > 0
                ? round(((int) $banner->clicks_count * 100) / (int) $banner->impressions_count, 2)
                : 0;
            @endphp
            <tr class="bg-white align-top dark:bg-gray-900">
              <td class="px-4 py-4">
                <div class="flex items-start gap-4">
                  <div class="flex h-16 w-24 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-950">
                    @if($previewUrl !== '')
                      <img src="{{ $previewUrl }}" alt="{{ $banner->title ?: 'Banner image' }}" class="h-full w-full object-cover">
                    @else
                      <span class="text-xs text-gray-400 dark:text-gray-500">No image</span>
                    @endif
                  </div>
                  <div class="min-w-0 space-y-1">
                    <div class="font-semibold text-gray-900 dark:text-white">{{ $banner->title ?: 'Untitled banner' }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">Banner #{{ $banner->id }}</div>
                    @if($banner->button_text)
                      <x-ui.badge color="dark">{{ $banner->button_text }}</x-ui.badge>
                    @endif
                  </div>
                </div>
              </td>
              <td class="px-4 py-4">
                <div class="space-y-1">
                  <x-ui.badge color="brand">{{ ucfirst($banner->placement ?: 'home') }}</x-ui.badge>
                  <div class="text-xs text-gray-500 dark:text-gray-400">{{ strtoupper($banner->action_type ?: 'none') }}</div>
                </div>
              </td>
              <td class="px-4 py-4">
                <div class="max-w-xs space-y-1">
                  <div class="font-medium text-gray-900 dark:text-white">{{ $banner->action_value ?: ($banner->target_url ?: 'No action configured') }}</div>
                  @if($banner->target_url)
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($banner->target_url, 54) }}</div>
                  @endif
                </div>
              </td>
              <td class="px-4 py-4">
                <div class="space-y-1 text-xs text-gray-600 dark:text-gray-300">
                  <div><span class="font-semibold text-gray-900 dark:text-white">Platforms:</span> {{ $platforms }}</div>
                  <div><span class="font-semibold text-gray-900 dark:text-white">Roles:</span> {{ $roles }}</div>
                </div>
              </td>
              <td class="px-4 py-4">
                <div class="space-y-1 text-xs text-gray-600 dark:text-gray-300">
                  <div>Impressions: {{ number_format((int) $banner->impressions_count) }}</div>
                  <div>Clicks: {{ number_format((int) $banner->clicks_count) }}</div>
                  <div>Unique Reach: {{ number_format((int) ($banner->unique_impressions_count ?? 0)) }}</div>
                  <div>CTR: {{ number_format($ctr, 2) }}%</div>
                  <div>Last Click: {{ $banner->last_click_at ? \Carbon\Carbon::parse($banner->last_click_at)->diffForHumans() : '—' }}</div>
                </div>
              </td>
              <td class="px-4 py-4">
                <x-ui.badge :color="$banner->is_active ? 'success' : 'dark'">
                  {{ $banner->is_active ? 'Active' : 'Inactive' }}
                </x-ui.badge>
              </td>
              <td class="px-4 py-4">
                <div class="space-y-1 text-xs text-gray-600 dark:text-gray-300">
                  <div>Start: {{ $banner->starts_at?->format('d M Y H:i') ?? 'Any time' }}</div>
                  <div>End: {{ $banner->ends_at?->format('d M Y H:i') ?? 'No expiry' }}</div>
                </div>
              </td>
              <td class="px-4 py-4 font-medium text-gray-900 dark:text-white">{{ $banner->sort_order }}</td>
              <td class="px-4 py-4">
                <div class="flex flex-wrap justify-end gap-2">
                  <x-ui.button variant="outline" size="sm" href="{{ route('admin.banners.edit', $banner) }}">Edit</x-ui.button>
                  <form method="post" action="{{ route('admin.banners.destroy', $banner) }}" onsubmit="return confirm('Delete this banner?')">
                    @csrf
                    @method('DELETE')
                    <x-ui.button variant="danger" size="sm" type="submit">Delete</x-ui.button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="9" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No banners found for the current filter set.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <x-slot:footer>
      <div class="flex justify-end">
        {{ $banners->withQueryString()->links() }}
      </div>
    </x-slot:footer>
  </x-common.component-card>
</div>
@endsection
