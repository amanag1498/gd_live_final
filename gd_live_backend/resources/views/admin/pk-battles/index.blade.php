@extends('layouts.admin-tailadmin')
@section('title', 'PK Battles')

@php
  $selectClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
@endphp

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ route('admin.pk-battles.export', ['status' => request('status')]) }}">
    Export CSV
  </x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
    <x-admin.stat-card label="Active" :value="number_format($summary['active'])" tone="brand" />
    <x-admin.stat-card label="Pending" :value="number_format($summary['pending'])" tone="warning" />
    <x-admin.stat-card label="Completed" :value="number_format($summary['completed'])" tone="success" />
    <x-admin.stat-card label="Cancelled / Failed" :value="number_format($summary['failed'])" tone="danger" />
    <x-admin.stat-card label="Total PK Coins" :value="number_format($summary['total_pk_coins'])" tone="dark" />
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div class="max-w-2xl">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Video PK Battle Ledger</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Review active and completed PK sessions, compare host performance, and export filtered battle history for audits.</p>
        </div>

        <form method="get" class="grid gap-3 sm:grid-cols-[180px_auto_auto]">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
            <select name="status" class="{{ $selectClass }}">
              <option value="">Any status</option>
              @foreach(['pending','active','completed','cancelled','rejected','expired','failed'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
              @endforeach
            </select>
          </div>

          <div class="flex items-end gap-3">
            <x-ui.button type="submit" size="sm">Apply Filter</x-ui.button>
            <x-ui.button variant="outline" size="sm" href="{{ route('admin.pk-battles.index') }}">Reset</x-ui.button>
          </div>
        </form>
      </div>
    </x-slot:header>

    @if($topHosts->isNotEmpty())
      <div class="mb-5 flex flex-wrap gap-2">
        @foreach($topHosts as $hostId => $wins)
          <x-ui.badge color="dark">Host {{ $hostId }} · {{ $wins }} wins</x-ui.badge>
        @endforeach
      </div>
    @endif

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Battle</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Host A</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Host B</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Score</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Winner</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Duration</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Started</th>
            <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($battles as $battle)
            <tr class="bg-white align-top dark:bg-gray-900">
              <td class="px-4 py-4">
                <div class="font-semibold text-gray-900 dark:text-white">{{ $battle->battle_id }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  {{ optional($battle->ended_at)->format('d M Y H:i') ?: 'Still running' }}
                </div>
              </td>
              <td class="px-4 py-4">
                <div class="font-medium text-gray-900 dark:text-white">
                  @if($battle->hostA?->user)
                    <a class="text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.users.show', $battle->hostA->user) }}">
                      {{ $battle->hostA?->stage_name ?: $battle->hostA->user->name }}
                    </a>
                  @else
                    {{ $battle->hostA?->stage_name ?: $battle->hostA?->user?->name ?: '—' }}
                  @endif
                </div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  Room:
                  @if($battle->roomA)
                    <a class="text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.live-rooms.show', $battle->roomA) }}">{{ $battle->roomA->room_id }}</a>
                  @else
                    —
                  @endif
                </div>
              </td>
              <td class="px-4 py-4">
                <div class="font-medium text-gray-900 dark:text-white">
                  @if($battle->hostB?->user)
                    <a class="text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.users.show', $battle->hostB->user) }}">
                      {{ $battle->hostB?->stage_name ?: $battle->hostB->user->name }}
                    </a>
                  @else
                    {{ $battle->hostB?->stage_name ?: $battle->hostB?->user?->name ?: '—' }}
                  @endif
                </div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                  Room:
                  @if($battle->roomB)
                    <a class="text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.live-rooms.show', $battle->roomB) }}">{{ $battle->roomB->room_id }}</a>
                  @else
                    —
                  @endif
                </div>
              </td>
              <td class="px-4 py-4">
                @php
                  $statusColor = match($battle->status) {
                    'active' => 'success',
                    'pending' => 'warning',
                    'completed' => 'primary',
                    default => 'error',
                  };
                @endphp
                <x-ui.badge :color="$statusColor">{{ str_replace('_', ' ', $battle->status) }}</x-ui.badge>
              </td>
              <td class="px-4 py-4 font-medium text-gray-900 dark:text-white">{{ number_format($battle->score_a) }} - {{ number_format($battle->score_b) }}</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ $battle->winnerRoom?->room_id ?: 'Draw / N/A' }}</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ number_format($battle->duration_seconds) }}s</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ optional($battle->started_at)->format('d M Y H:i') ?: '—' }}</td>
              <td class="px-4 py-4 text-right">
                <x-ui.button variant="outline" size="sm" href="{{ route('admin.pk-battles.show', $battle) }}">View</x-ui.button>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="9" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No PK battles found for the selected filters.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <x-slot:footer>
      <div class="flex justify-end">
        {{ $battles->withQueryString()->links() }}
      </div>
    </x-slot:footer>
  </x-common.component-card>
</div>
@endsection
