@extends('layouts.admin-tailadmin')
@section('title','Live Rooms')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $roomCollection = $rooms->getCollection();
  $liveCount = $roomCollection->where('status', 'live')->count();
  $openSpeakers = (int) $roomCollection->sum(fn ($room) => (int) ($room->open_host_count ?? 0) + (int) ($room->open_speaker_count ?? 0));
  $pendingRequests = (int) $roomCollection->sum('pending_request_count');
  $peakViewers = (int) $roomCollection->sum('peak_viewers');
@endphp

@section('page_actions')
  <x-ui.button size="sm" href="{{ route('admin.live-rooms.create') }}">New Room</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="dark">Live Control</x-ui.badge>
            <x-ui.badge color="brand">Rooms</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Live Rooms</h2>
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Review room health, host activity, seat pressure, and lifecycle status from a single clean operations view.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
          <x-admin.stat-card label="Visible Rooms" :value="number_format($rooms->total())" meta="Matching the current filters" />
          <x-admin.stat-card label="Live Now" :value="number_format($liveCount)" meta="Rooms currently active on this page" tone="success" />
          <x-admin.stat-card label="Current Speakers" :value="number_format($openSpeakers)" meta="Hosts and accepted speakers on camera" tone="warning" />
          <x-admin.stat-card label="Pending Requests" :value="number_format($pendingRequests)" meta="Seat requests still waiting for action" tone="dark" />
        </div>
      </div>
    </div>
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
          <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Room Directory</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Search by room, host, or title and then drill into moderation or operational details.</p>
          </div>
          <div class="flex items-center gap-2">
            <x-ui.badge color="dark">{{ number_format($peakViewers) }} combined peak viewers</x-ui.badge>
          </div>
        </div>

        <form method="get" class="grid gap-3 xl:grid-cols-[minmax(0,1fr)_180px_180px_180px_auto]">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
            <input class="{{ $inputClass }}" name="s" value="{{ request('s') }}" placeholder="Search room ID, host, or title">
          </div>

          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
            <select name="status" class="{{ $inputClass }}">
              <option value="">Any status</option>
              @foreach(['live','ended'] as $st)
                <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">From</label>
            <input type="date" class="{{ $inputClass }}" name="from" value="{{ request('from') }}">
          </div>

          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">To</label>
            <input type="date" class="{{ $inputClass }}" name="to" value="{{ request('to') }}">
          </div>

          <div class="flex flex-wrap items-end justify-end gap-3">
            <x-ui.button variant="outline" type="submit" size="sm">Filter</x-ui.button>
            <x-ui.button variant="outline" href="{{ route('admin.live-rooms.index') }}" size="sm">Reset</x-ui.button>
          </div>
        </form>
      </div>
    </x-slot:header>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">#</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Room</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Host</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Open</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">On Camera</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Pending</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Max Speakers</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Peak</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Started</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Ended</th>
            <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($rooms as $r)
            @php
              $cameraCount = (int) ($r->open_host_count ?? 0) + (int) ($r->open_speaker_count ?? 0);
              $statusColor = $r->status === 'live' ? 'success' : ($r->status === 'ended' ? 'dark' : 'warning');
            @endphp
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3">{{ $r->id }}</td>
              <td class="px-4 py-3">
                <div class="font-medium text-gray-900 dark:text-white">{{ $r->room_id }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $r->title ?: 'Untitled room' }}</div>
                <div class="text-xs text-gray-400 dark:text-gray-500">{{ $r->end_reason ? ucfirst(str_replace('_',' ',$r->end_reason)) : 'No end reason' }}</div>
              </td>
              <td class="px-4 py-3">
                @if($r->host?->user)
                  <a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.users.show', $r->host->user) }}">{{ $r->host->user->name }}</a>
                  <div class="text-sm text-gray-500 dark:text-gray-400">User #{{ $r->host->user->id }}</div>
                @else
                  <span class="text-gray-500 dark:text-gray-400">—</span>
                @endif
              </td>
              <td class="px-4 py-3">
                <x-ui.badge :color="$statusColor">{{ ucfirst($r->status) }}</x-ui.badge>
                <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">Host {{ ($r->open_host_count ?? 0) > 0 ? 'active' : 'offline' }}</div>
              </td>
              <td class="px-4 py-3">{{ $r->open_participant_count ?? 0 }}</td>
              <td class="px-4 py-3">{{ $cameraCount }}</td>
              <td class="px-4 py-3">{{ $r->pending_request_count ?? 0 }}</td>
              <td class="px-4 py-3">{{ $r->max_speakers ?? config('live_rooms.' . ($r->room_type ?? 'video') . '.max_speakers', 4) }}</td>
              <td class="px-4 py-3">{{ number_format($r->peak_viewers) }}</td>
              <td class="px-4 py-3">{{ $r->started_at?->format('d M Y H:i') ?? '—' }}</td>
              <td class="px-4 py-3">{{ $r->ended_at?->format('d M Y H:i') ?? '—' }}</td>
              <td class="px-4 py-3">
                <div class="flex flex-wrap justify-end gap-2">
                  <x-ui.button variant="outline" size="sm" href="{{ route('admin.live-rooms.show', $r) }}">View</x-ui.button>
                  <x-ui.button size="sm" href="{{ route('admin.live-rooms.edit', $r) }}">Edit</x-ui.button>
                  @if($r->status !== 'ended')
                    <form method="post" action="{{ route('admin.live-rooms.end', $r) }}" onsubmit="return confirm('Force end this room?')">
                      @csrf
                      <x-ui.button variant="danger" size="sm" type="submit">Force End</x-ui.button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="12" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No rooms match the current filters.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <x-slot:footer>
      <div class="flex justify-end">
        {{ $rooms->withQueryString()->links() }}
      </div>
    </x-slot:footer>
  </x-common.component-card>
</div>
@endsection
