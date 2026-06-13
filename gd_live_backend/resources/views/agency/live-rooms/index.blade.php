@extends('layouts.agency-tailadmin')
@section('title', ucfirst($roomType) . ' Rooms')
@section('page_intro', 'Agency-scoped ' . $roomType . ' room activity with host, status, participants, and gift visibility.')

@php
  $roomCollection = $rooms->getCollection();
  $liveCount = $roomCollection->where('status', 'live')->count();
  $openSpeakers = (int) $roomCollection->sum(fn ($room) => (int) ($room->open_host_count ?? 0) + (int) ($room->open_speaker_count ?? 0));
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ $overviewRoute ?? route('agency.dashboard') }}">Dashboard</x-ui.button>
  <x-ui.button variant="outline" size="sm" href="{{ $callsRoute ?? route('agency.calls.index') }}">Call Reports</x-ui.button>
  <x-ui.button size="sm" href="{{ $videoRoomsRoute ?? route('agency.video-rooms.index') }}">Video Rooms</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <section class="grid gap-4 md:grid-cols-3">
    <x-admin.stat-card label="Visible Rooms" :value="number_format($rooms->total())" meta="Current filtered scope" />
    <x-admin.stat-card label="Live Now" :value="number_format($liveCount)" meta="Rooms actively live" tone="success" />
    <x-admin.stat-card label="Open Speakers" :value="number_format($openSpeakers)" meta="Host plus speakers currently on stage" tone="warning" />
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ ucfirst($roomType) }} Rooms</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Search room, host, or title and inspect current agency room activity.</p>
        </div>
        <form method="get" class="flex flex-wrap gap-3">
          <input class="{{ $inputClass }}" name="s" value="{{ request('s') }}" placeholder="Search room, host, title">
          <select name="status" class="{{ $inputClass }}">
            <option value="">Any status</option>
            @foreach(['live','ended'] as $st)
              <option value="{{ $st }}" @selected(request('status') === $st)>{{ ucfirst($st) }}</option>
            @endforeach
          </select>
          <x-ui.button variant="outline" type="submit" size="sm">Filter</x-ui.button>
        </form>
      </div>
    </x-slot:header>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Room</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Host</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Open</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">On Stage</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Peak</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Started</th>
            <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($rooms as $room)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3">
                <div class="font-medium text-gray-900 dark:text-white">{{ $room->room_id }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $room->title ?: '—' }}</div>
              </td>
              <td class="px-4 py-3">
                <div class="font-medium text-gray-900 dark:text-white">{{ $room->host?->user?->name ?? '—' }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $room->host?->stage_name ?: '—' }}</div>
              </td>
              <td class="px-4 py-3"><x-ui.badge color="dark">{{ ucfirst($room->status) }}</x-ui.badge></td>
              <td class="px-4 py-3">{{ $room->open_participant_count ?? 0 }}</td>
              <td class="px-4 py-3">{{ (int) ($room->open_host_count ?? 0) + (int) ($room->open_speaker_count ?? 0) }}</td>
              <td class="px-4 py-3">{{ number_format((int) $room->peak_viewers) }}</td>
              <td class="px-4 py-3">{{ optional($room->started_at)->format('d M Y H:i') ?: '—' }}</td>
              <td class="px-4 py-3">
                <div class="flex justify-end">
                  <x-ui.button variant="outline" size="sm" href="{{ request()->routeIs('admin.*') ? route('admin.agencies.' . $roomType . '-rooms.show', ['agency' => $agency->id, 'live_room' => $room->id]) : route('agency.' . $roomType . '-rooms.show', $room) }}">View</x-ui.button>
                </div>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No {{ $roomType }} rooms found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <x-slot:footer>
      <div class="flex justify-end">
        {{ $rooms->links() }}
      </div>
    </x-slot:footer>
  </x-common.component-card>
</div>
@endsection
