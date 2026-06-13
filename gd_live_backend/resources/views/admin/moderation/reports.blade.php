@extends('layouts.admin-tailadmin')
@section('title', 'Moderation Reports')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
          <div class="max-w-2xl">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Moderation Review Queue</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Triaging abuse reports, recording action notes, and keeping the moderation backlog actionable for the ops team.</p>
          </div>
          <x-ui.badge color="dark">{{ number_format($rows->total()) }} reports</x-ui.badge>
        </div>

        <form method="get" class="grid gap-3 lg:grid-cols-[180px_180px_180px_180px_auto]">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
            <select name="status" class="{{ $inputClass }}">
              <option value="">Any status</option>
              @foreach(['pending','reviewed','dismissed','action_taken'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Reason Type</label>
            <input name="reason_type" value="{{ request('reason_type') }}" placeholder="spam, abuse, etc." class="{{ $inputClass }}">
          </div>

          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">From</label>
            <input type="date" name="from" value="{{ request('from') }}" class="{{ $inputClass }}">
          </div>

          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">To</label>
            <input type="date" name="to" value="{{ request('to') }}" class="{{ $inputClass }}">
          </div>

          <div class="flex items-end gap-3">
            <x-ui.button type="submit" size="sm">Apply Filter</x-ui.button>
            <x-ui.button variant="outline" size="sm" href="{{ route('admin.moderation.reports') }}">Reset</x-ui.button>
          </div>
        </form>
      </div>
    </x-slot:header>

    <div class="space-y-4">
      @forelse($rows as $row)
        <article class="rounded-2xl border border-gray-200 bg-white p-5 shadow-theme-xs dark:border-gray-800 dark:bg-gray-900">
          <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
            <div class="grid flex-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
              <div>
                <div class="text-xs font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Reporter</div>
                <div class="mt-2 font-semibold text-gray-900 dark:text-white">{{ $row->reporter?->name ?? '—' }}</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">ID {{ $row->reporter_id ?? '—' }}</div>
              </div>
              <div>
                <div class="text-xs font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Reported User</div>
                <div class="mt-2 font-semibold text-gray-900 dark:text-white">{{ $row->reportedUser?->name ?? '—' }}</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">ID {{ $row->reported_user_id ?? '—' }}</div>
              </div>
              <div>
                <div class="text-xs font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Reason</div>
                <div class="mt-2 font-semibold text-gray-900 dark:text-white">{{ $row->reason_type }}</div>
                @if($row->description)
                  <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $row->description }}</p>
                @endif
              </div>
              <div>
                <div class="text-xs font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Context</div>
                <div class="mt-2 font-semibold text-gray-900 dark:text-white">{{ $row->room_id ?: 'No room attached' }}</div>
                @if($row->hostUser)
                  <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Host: {{ $row->hostUser->name }}</div>
                @endif
                <div class="mt-2"><x-ui.badge :color="match($row->status){'pending' => 'warning','reviewed' => 'primary','action_taken' => 'success',default => 'error'}">{{ str_replace('_', ' ', $row->status) }}</x-ui.badge></div>
              </div>
            </div>

            <div class="w-full xl:max-w-md">
              <div class="mb-2 text-xs font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Review Action</div>
              <form method="post" action="{{ route('admin.moderation.reports.review', $row) }}" class="space-y-3 rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950/40">
                @csrf
                <select name="status" class="{{ $inputClass }}">
                  @foreach(['reviewed','dismissed','action_taken'] as $status)
                    <option value="{{ $status }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</option>
                  @endforeach
                </select>
                <input name="admin_notes" class="{{ $inputClass }}" placeholder="Add admin notes for the audit trail">
                <div class="flex items-center justify-between gap-3">
                  <div class="text-sm text-gray-500 dark:text-gray-400">{{ optional($row->created_at)->format('d M Y · h:i A') }}</div>
                  <x-ui.button type="submit" size="sm">Save Review</x-ui.button>
                </div>
              </form>
            </div>
          </div>
        </article>
      @empty
        <div class="rounded-2xl border border-dashed border-gray-300 px-6 py-12 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">
          No moderation reports match the current filter set.
        </div>
      @endforelse
    </div>

    <x-slot:footer>
      <div class="flex justify-end">
        {{ $rows->withQueryString()->links() }}
      </div>
    </x-slot:footer>
  </x-common.component-card>
</div>
@endsection
