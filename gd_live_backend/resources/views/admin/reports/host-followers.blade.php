@extends('layouts.admin-tailadmin')
@section('title', 'Host Followers')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
@endphp

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ route('admin.reports.follow-notifications') }}">Online Alerts</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-2xl">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Host Followers</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Review follower relationships, online alert preferences, and the hosts drawing the strongest follow graph.</p>
        </div>
        <form method="get" class="flex gap-3">
          <select name="host_id" class="{{ $inputClass }}">
            <option value="">All hosts</option>
            @foreach($hosts as $host)
              <option value="{{ $host->id }}" @selected((string) $hostId === (string) $host->id)>{{ $host->stage_name ?: $host->user?->name ?: 'Host #'.$host->id }}</option>
            @endforeach
          </select>
          <x-ui.button type="submit" size="sm">Filter</x-ui.button>
        </form>
      </div>
    </x-slot:header>

    <div class="grid gap-6 xl:grid-cols-[340px_minmax(0,1fr)]">
      <div class="space-y-3">
        @forelse($topHosts as $host)
          <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between gap-3">
              <div>
                <div class="font-semibold text-gray-900 dark:text-white">{{ $host->stage_name ?: $host->user?->name ?: 'Host #'.$host->id }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $host->user?->email }}</div>
              </div>
              <x-ui.badge color="dark">{{ $host->followers_count }} followers</x-ui.badge>
            </div>
          </div>
        @empty
          <div class="rounded-2xl border border-dashed border-gray-300 px-4 py-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">No followers yet.</div>
        @endforelse
      </div>

      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60">
            <tr>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Host</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Follower</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Online Alerts</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Followed</th>
              <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @forelse($rows as $row)
              <tr class="bg-white dark:bg-gray-900">
                <td class="px-4 py-4">
                  <div class="font-semibold text-gray-900 dark:text-white">{{ $row->host?->stage_name ?: $row->host?->user?->name ?: 'Host #'.$row->host_id }}</div>
                  <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $row->host?->user?->email }}</div>
                </td>
                <td class="px-4 py-4">
                  <div class="font-semibold text-gray-900 dark:text-white">{{ $row->user?->name ?: 'User #'.$row->user_id }}</div>
                  <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $row->user?->email }}</div>
                </td>
                <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
                  <div>Online: {{ $row->notify_when_online ? 'Yes' : 'No' }}</div>
                </td>
                <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ optional($row->created_at)->format('d M Y, H:i') }}</td>
                <td class="px-4 py-4 text-right">
                  <form method="post" action="{{ route('admin.reports.host-followers.destroy', $row) }}">
                    @csrf
                    @method('DELETE')
                    <x-ui.button variant="danger" size="sm" type="submit">Remove</x-ui.button>
                  </form>
                </td>
              </tr>
            @empty
              <tr class="bg-white dark:bg-gray-900"><td colspan="5" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No follower relationships found.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <x-slot:footer>
      <div class="flex justify-end">{{ $rows->links() }}</div>
    </x-slot:footer>
  </x-common.component-card>
</div>
@endsection
