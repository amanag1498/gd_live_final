@extends('layouts.admin-tailadmin')
@section('title','Live Room #'.$live_room->id)

@php
  $statusColor = $live_room->status === 'live' ? 'success' : ($live_room->status === 'ended' ? 'dark' : 'warning');
@endphp

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ route('admin.live-rooms.edit',$live_room) }}">Edit</x-ui.button>
  @if($live_room->status !== 'ended')
    <form method="post" action="{{ route('admin.live-rooms.end',$live_room) }}" onsubmit="return confirm('Force end this room?')">
      @csrf
      <x-ui.button variant="danger" size="sm" type="submit">Force End</x-ui.button>
    </form>
  @endif
@endsection

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="dark">Room Detail</x-ui.badge>
            <x-ui.badge :color="$statusColor">{{ ucfirst($live_room->status) }}</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $live_room->room_id }}</h2>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $live_room->title ?: 'Untitled room' }}</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-3 xl:grid-cols-6">
          <x-admin.stat-card label="Participants" :value="number_format($stats['participants_open'])" meta="Currently connected" />
          <x-admin.stat-card label="On Camera" :value="number_format($stats['active_speaker_count'])" meta="Host plus accepted speakers" tone="warning" />
          <x-admin.stat-card label="Pending" :value="number_format($seatSnapshot['pending_count'])" meta="Seat requests waiting" tone="dark" />
          <x-admin.stat-card label="Peak Viewers" :value="number_format($live_room->peak_viewers)" meta="Room peak audience" />
          <x-admin.stat-card label="Duration" :value="$stats['duration_min'] ? $stats['duration_min'].'m' : '—'" meta="Observed session length" tone="success" />
          <x-admin.stat-card label="Gift Coins" :value="number_format($stats['gift_coins'])" meta="Coins spent via gifts" tone="brand" />
        </div>
      </div>
    </div>
  </section>

  <section class="grid gap-6 xl:grid-cols-[380px_minmax(0,1fr)]">
    <div class="space-y-6">
      <x-common.component-card title="Room Overview" desc="Operational metadata and lifecycle details for this room.">
        <div class="space-y-4 text-sm">
          <div class="flex items-start justify-between gap-4">
            <span class="text-gray-500 dark:text-gray-400">Host</span>
            <div class="text-right">
              @if($live_room->host?->user)
                <a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.users.show', $live_room->host->user) }}">{{ $live_room->host->user->name }}</a>
                <div class="text-xs text-gray-500 dark:text-gray-400">User #{{ $live_room->host->user->id }}</div>
              @else
                <span class="text-gray-500 dark:text-gray-400">—</span>
              @endif
            </div>
          </div>
          <div class="flex items-start justify-between gap-4">
            <span class="text-gray-500 dark:text-gray-400">Room ID</span>
            <code class="rounded-md bg-gray-100 px-2 py-1 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-200">{{ $live_room->room_id }}</code>
          </div>
          <div class="flex items-start justify-between gap-4">
            <span class="text-gray-500 dark:text-gray-400">Started</span>
            <span class="font-medium text-gray-900 dark:text-white">{{ $live_room->started_at?->format('d M Y H:i') ?? '—' }}</span>
          </div>
          <div class="flex items-start justify-between gap-4">
            <span class="text-gray-500 dark:text-gray-400">Ended</span>
            <span class="font-medium text-gray-900 dark:text-white">{{ $live_room->ended_at?->format('d M Y H:i') ?? '—' }}</span>
          </div>
          <div class="flex items-start justify-between gap-4">
            <span class="text-gray-500 dark:text-gray-400">End Reason</span>
            <span class="font-medium text-gray-900 dark:text-white">{{ $live_room->end_reason ?? '—' }}</span>
          </div>
          <div class="flex items-start justify-between gap-4">
            <span class="text-gray-500 dark:text-gray-400">Current Speakers</span>
            <span class="font-medium text-gray-900 dark:text-white">{{ $stats['active_speaker_count'] }} / {{ $live_room->max_speakers ?? config('live_rooms.' . ($live_room->room_type ?? 'video') . '.max_speakers', 4) }}</span>
          </div>
          <div class="flex items-start justify-between gap-4">
            <span class="text-gray-500 dark:text-gray-400">Pending Requests</span>
            <span class="font-medium text-gray-900 dark:text-white">{{ $seatSnapshot['pending_count'] }}</span>
          </div>
        </div>
      </x-common.component-card>

      <x-common.component-card title="Gift Earnings" desc="Revenue split generated by gift activity in this room.">
        <div class="grid gap-3">
          <x-admin.stat-card label="Host Earnings" :value="number_format($stats['gift_host_earnings'])" meta="Host payout coins" tone="success" />
          <x-admin.stat-card label="Agency Earnings" :value="number_format($stats['gift_agency_earnings'])" meta="Agency payout coins" tone="warning" />
          <x-admin.stat-card label="Platform Earnings" :value="number_format($stats['gift_platform_earnings'])" meta="Platform revenue coins" tone="dark" />
        </div>
      </x-common.component-card>

      <x-common.component-card title="Consistency Checks" desc="Quick anomaly scan for this room's state.">
        <div class="space-y-3 text-sm">
          <div class="flex items-center justify-between gap-3">
            <span class="text-gray-600 dark:text-gray-300">No active host</span>
            <x-ui.badge :color="$consistency['live_room_with_no_active_host'] ? 'danger' : 'success'">{{ $consistency['live_room_with_no_active_host'] ? 'Yes' : 'No' }}</x-ui.badge>
          </div>
          <div class="flex items-center justify-between gap-3">
            <span class="text-gray-600 dark:text-gray-300">Ended with open participants</span>
            <x-ui.badge :color="$consistency['ended_room_with_open_participants'] ? 'danger' : 'success'">{{ $consistency['ended_room_with_open_participants'] ? 'Yes' : 'No' }}</x-ui.badge>
          </div>
          <div class="flex items-center justify-between gap-3">
            <span class="text-gray-600 dark:text-gray-300">Pending request without participant</span>
            <x-ui.badge color="warning">{{ count($consistency['pending_request_for_user_not_in_room']) }}</x-ui.badge>
          </div>
          <div class="flex items-center justify-between gap-3">
            <span class="text-gray-600 dark:text-gray-300">Speaker without accepted request</span>
            <x-ui.badge color="warning">{{ count($consistency['speaker_role_without_accepted_request']) }}</x-ui.badge>
          </div>
          <div class="flex items-center justify-between gap-3">
            <span class="text-gray-600 dark:text-gray-300">Redis missing live room</span>
            <x-ui.badge :color="$consistency['redis_missing_live_room'] ? 'danger' : 'success'">{{ $consistency['redis_missing_live_room'] ? 'Yes' : 'No' }}</x-ui.badge>
          </div>
          <div class="flex items-center justify-between gap-3">
            <span class="text-gray-600 dark:text-gray-300">Redis has ended room</span>
            <x-ui.badge :color="$consistency['redis_has_ended_room'] ? 'danger' : 'success'">{{ $consistency['redis_has_ended_room'] ? 'Yes' : 'No' }}</x-ui.badge>
          </div>
        </div>
      </x-common.component-card>
    </div>

    <div class="space-y-6">
      <x-common.component-card title="Current Speakers" desc="Hosts and accepted speakers currently on camera.">
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60">
              <tr>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">User</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Joined</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Speaker Since</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Role</th>
                <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              @forelse($seatSnapshot['speakers'] as $speaker)
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3">
                    @if(!empty($speaker['user_id']))
                      <a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.users.show', $speaker['user_id']) }}">{{ $speaker['name'] ?? '—' }}</a>
                    @else
                      <span class="font-medium text-gray-900 dark:text-white">{{ $speaker['name'] ?? '—' }}</span>
                    @endif
                    <div class="text-sm text-gray-500 dark:text-gray-400">User #{{ $speaker['user_id'] }}</div>
                  </td>
                  <td class="px-4 py-3">{{ $speaker['joined_at'] ? \Carbon\Carbon::parse($speaker['joined_at'])->format('d M Y H:i') : '—' }}</td>
                  <td class="px-4 py-3">{{ $speaker['speaker_since'] ? \Carbon\Carbon::parse($speaker['speaker_since'])->format('d M Y H:i') : '—' }}</td>
                  <td class="px-4 py-3"><x-ui.badge color="dark">{{ ucfirst($speaker['role']) }}</x-ui.badge></td>
                  <td class="px-4 py-3">
                    <div class="flex justify-end">
                      <form method="post" action="{{ route('admin.live-rooms.speakers.remove', [$live_room, $speaker['user_id']]) }}" onsubmit="return confirm('Remove this speaker from camera?')">
                        @csrf
                        <input type="hidden" name="reason" value="admin_removed_speaker">
                        <x-ui.button variant="danger" size="sm" type="submit">Remove</x-ui.button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr class="bg-white dark:bg-gray-900">
                  <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No active speakers.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </x-common.component-card>

      <x-common.component-card>
        <x-slot:header>
          <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Seat Request History</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track request outcomes, moderation removals, and participant escalation over time.</p>
            </div>
            <div class="flex flex-wrap gap-3">
              <form method="get" class="flex flex-wrap gap-3">
                <select name="request_status" class="h-10 rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                  <option value="">All statuses</option>
                  @foreach(['pending','accepted','rejected','cancelled','removed','expired'] as $status)
                    <option value="{{ $status }}" @selected(request('request_status') === $status)>{{ ucfirst($status) }}</option>
                  @endforeach
                </select>
                <x-ui.button variant="outline" size="sm" type="submit">Filter</x-ui.button>
              </form>
              <x-ui.button variant="outline" size="sm" href="{{ route('admin.live-rooms.requests.export', $live_room) }}">Export CSV</x-ui.button>
            </div>
          </div>
        </x-slot:header>

        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60">
              <tr>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Request</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">User</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Requested</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Responded</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Removed</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Reason</th>
                <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              @forelse($seatSnapshot['requests'] as $row)
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3">#{{ $row['request_id'] }}</td>
                  <td class="px-4 py-3">
                    @if(!empty($row['user_id']))
                      <a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.users.show', $row['user_id']) }}">{{ data_get($row, 'user.name', '—') }}</a>
                    @else
                      <span class="font-medium text-gray-900 dark:text-white">{{ data_get($row, 'user.name', '—') }}</span>
                    @endif
                    <div class="text-sm text-gray-500 dark:text-gray-400">User #{{ $row['user_id'] }}</div>
                  </td>
                  <td class="px-4 py-3">
                    <x-ui.badge color="dark">{{ ucfirst($row['status']) }}</x-ui.badge>
                    <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">Role: {{ ucfirst($row['role']) }}</div>
                  </td>
                  <td class="px-4 py-3">{{ $row['requested_at'] ? \Carbon\Carbon::parse($row['requested_at'])->format('d M Y H:i') : '—' }}</td>
                  <td class="px-4 py-3">{{ $row['responded_at'] ? \Carbon\Carbon::parse($row['responded_at'])->format('d M Y H:i') : '—' }}</td>
                  <td class="px-4 py-3">{{ $row['removed_at'] ? \Carbon\Carbon::parse($row['removed_at'])->format('d M Y H:i') : '—' }}</td>
                  <td class="px-4 py-3">{{ $row['remove_reason'] ?? '—' }}</td>
                  <td class="px-4 py-3">
                    <div class="flex justify-end">
                      @if($row['status'] === 'pending')
                        <form method="post" action="{{ route('admin.live-rooms.seat-requests.reject', [$live_room, $row['request_id']]) }}" onsubmit="return confirm('Reject this pending request?')">
                          @csrf
                          <input type="hidden" name="reason" value="admin_force_reject">
                          <x-ui.button variant="warning" size="sm" type="submit">Reject</x-ui.button>
                        </form>
                      @else
                        <span class="text-xs text-gray-500 dark:text-gray-400">No action</span>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr class="bg-white dark:bg-gray-900">
                  <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No seat requests for this room.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </x-common.component-card>

      <x-common.component-card title="Audit Log" desc="Administrative actions recorded for this room.">
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60">
              <tr>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">When</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Admin</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Action</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Target</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Before</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">After</th>
                <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Reason</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              @forelse($live_room->adminAudits->sortByDesc('id') as $audit)
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3">{{ $audit->created_at?->format('d M Y H:i') }}</td>
                  <td class="px-4 py-3">{{ $audit->admin?->name ?? '—' }}</td>
                  <td class="px-4 py-3">{{ $audit->action }}</td>
                  <td class="px-4 py-3">{{ $audit->targetUser?->name ?? ($audit->target_user_id ? 'User #'.$audit->target_user_id : '—') }}</td>
                  <td class="px-4 py-3">{{ $audit->before_status ?? '—' }}</td>
                  <td class="px-4 py-3">{{ $audit->after_status ?? '—' }}</td>
                  <td class="px-4 py-3">{{ $audit->reason ?? '—' }}</td>
                </tr>
              @empty
                <tr class="bg-white dark:bg-gray-900">
                  <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No admin actions recorded for this room.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </x-common.component-card>
    </div>
  </section>
</div>
@endsection
