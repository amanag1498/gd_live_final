@extends('layouts.admin-tailadmin')
@section('title','Host Requests')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $activeFilterCount = collect([
    $filters['q'] ?? '',
    $filters['status'] ?? '',
    $filters['agency_id'] ?? null,
    $filters['date_from'] ?? '',
    $filters['date_to'] ?? '',
    ($filters['per_page'] ?? 20) !== 20 ? $filters['per_page'] : null,
  ])->filter(fn ($value) => $value !== null && $value !== '')->count();
@endphp

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-5">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
          <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Host Requests</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track host onboarding demand and triage pending reviews fast.</p>
          </div>
          <div class="flex flex-wrap items-center gap-2">
            @if($activeFilterCount > 0)
              <x-ui.badge color="brand">{{ $activeFilterCount }} active {{ $activeFilterCount === 1 ? 'filter' : 'filters' }}</x-ui.badge>
            @endif
            <x-ui.badge color="dark">Matching {{ $requests->total() }}</x-ui.badge>
          </div>
        </div>

        <form method="get" action="{{ route('admin.host-requests.index') }}" class="grid gap-3 md:grid-cols-2 xl:grid-cols-12">
          <div class="md:col-span-2 xl:col-span-4">
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
            <input
              class="{{ $inputClass }}"
              name="q"
              value="{{ $filters['q'] ?? '' }}"
              placeholder="Request ID, user, email, stage, phone, location…"
            >
          </div>
          <div class="xl:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
            <select name="status" class="{{ $inputClass }}">
              <option value="">All statuses</option>
              @foreach(['pending', 'approved', 'rejected'] as $status)
                <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
              @endforeach
            </select>
          </div>
          <div class="xl:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Agency</label>
            <select name="agency_id" class="{{ $inputClass }}">
              <option value="">All agencies</option>
              @foreach($agencies as $agency)
                <option value="{{ $agency->id }}" @selected(($filters['agency_id'] ?? null) === $agency->id)>{{ $agency->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="xl:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Submitted From</label>
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="{{ $inputClass }}">
          </div>
          <div class="xl:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Submitted To</label>
            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="{{ $inputClass }}">
          </div>
          <div class="xl:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Rows</label>
            <select name="per_page" class="{{ $inputClass }}">
              @foreach([20, 50, 100] as $pageSize)
                <option value="{{ $pageSize }}" @selected(($filters['per_page'] ?? 20) === $pageSize)>{{ $pageSize }} per page</option>
              @endforeach
            </select>
          </div>
          <div class="flex flex-wrap items-end gap-2 md:col-span-2 xl:col-span-10 xl:justify-end">
            <x-ui.button type="submit" size="sm">Apply Filters</x-ui.button>
            <x-ui.button variant="outline" href="{{ route('admin.host-requests.index') }}" size="sm">Reset</x-ui.button>
          </div>
        </form>
      </div>
    </x-slot:header>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">User</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Agency</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Stage Name</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Applied</th>
            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($requests as $r)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3">{{ $r->id }}</td>
              <td class="px-4 py-3">
                <div class="font-medium text-gray-900 dark:text-white">{{ $r->user?->name ?? '—' }}</div>
                <div class="text-gray-500 dark:text-gray-400">User #{{ $r->user_id }} · {{ $r->user?->email ?? '' }}</div>
              </td>
              <td class="px-4 py-3">{{ $r->agency?->name ?? '—' }}</td>
              <td class="px-4 py-3">{{ $r->stage_name ?: '—' }}</td>
              <td class="px-4 py-3"><x-ui.badge :color="$r->status==='pending' ? 'warning' : ($r->status==='approved' ? 'success' : 'error')">{{ ucfirst($r->status) }}</x-ui.badge></td>
              <td class="px-4 py-3">{{ $r->created_at?->format('d M Y') }}</td>
              <td class="px-4 py-3 text-right"><x-ui.button size="sm" href="{{ route('admin.host-requests.show',$r) }}">Review</x-ui.button></td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900"><td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No host requests match the selected filters.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-4 flex justify-end">{{ $requests->links() }}</div>
  </x-common.component-card>
</div>
@endsection
