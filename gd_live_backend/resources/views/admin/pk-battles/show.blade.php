@extends('layouts.admin-tailadmin')
@section('title','PK Battle Detail')

@php
  $surfaceClass = 'rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950/60';
@endphp

@section('page_actions')
  <a href="{{ route('admin.pk-battles.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">
    <i class="ti ti-arrow-left mr-2"></i>Back
  </a>
@endsection

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="flex flex-col gap-4 px-6 py-6 lg:flex-row lg:items-start lg:justify-between lg:px-8">
      <div>
        <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $pk_battle->battle_id }}</h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Status {{ strtoupper($pk_battle->status) }} · Winner {{ $pk_battle->winnerRoom?->room_id ?: 'Draw / N/A' }}</p>
      </div>
      <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
          <div class="text-sm text-gray-500 dark:text-gray-400">Duration</div>
          <div class="mt-2 text-xl font-semibold text-gray-900 dark:text-white">{{ $pk_battle->duration_seconds }}s</div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
          <div class="text-sm text-gray-500 dark:text-gray-400">Started</div>
          <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ optional($pk_battle->started_at)->format('d M Y H:i:s') ?: '—' }}</div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
          <div class="text-sm text-gray-500 dark:text-gray-400">Ended</div>
          <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ optional($pk_battle->ended_at)->format('d M Y H:i:s') ?: '—' }}</div>
        </div>
      </div>
    </div>
  </section>

  <section class="grid gap-6 md:grid-cols-2">
    <x-common.component-card title="Room A" desc="Host, room, and score details.">
      <div class="space-y-3">
        <div class="{{ $surfaceClass }}">
          <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Room</div>
          <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">@if($pk_battle->roomA)<a class="text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.live-rooms.show', $pk_battle->roomA) }}">{{ $pk_battle->roomA->room_id }}</a>@else—@endif</div>
        </div>
        <div class="{{ $surfaceClass }}">
          <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Host</div>
          <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">@if($pk_battle->hostA?->user)<a class="text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.users.show', $pk_battle->hostA->user) }}">{{ $pk_battle->hostA?->stage_name ?: $pk_battle->hostA->user->name }}</a>@else{{ $pk_battle->hostA?->stage_name ?: $pk_battle->hostA?->user?->name ?: '—' }}@endif</div>
        </div>
        <div class="{{ $surfaceClass }}">
          <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Score</div>
          <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">{{ number_format($pk_battle->score_a) }}</div>
        </div>
      </div>
    </x-common.component-card>

    <x-common.component-card title="Room B" desc="Host, room, and score details.">
      <div class="space-y-3">
        <div class="{{ $surfaceClass }}">
          <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Room</div>
          <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">@if($pk_battle->roomB)<a class="text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.live-rooms.show', $pk_battle->roomB) }}">{{ $pk_battle->roomB->room_id }}</a>@else—@endif</div>
        </div>
        <div class="{{ $surfaceClass }}">
          <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Host</div>
          <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">@if($pk_battle->hostB?->user)<a class="text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.users.show', $pk_battle->hostB->user) }}">{{ $pk_battle->hostB?->stage_name ?: $pk_battle->hostB->user->name }}</a>@else{{ $pk_battle->hostB?->stage_name ?: $pk_battle->hostB?->user?->name ?: '—' }}@endif</div>
        </div>
        <div class="{{ $surfaceClass }}">
          <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Score</div>
          <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white">{{ number_format($pk_battle->score_b) }}</div>
        </div>
      </div>
    </x-common.component-card>
  </section>

  <x-common.component-card title="Gift Contributors" desc="Top users contributing coins during the PK battle.">
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">User</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Total Coins</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Contributions</th></tr></thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($contributors as $row)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3">@if($row->user)<a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.users.show', $row->user) }}">{{ $row->user->name }}</a>@else{{ 'User #'.$row->user_id }}@endif</td>
              <td class="px-4 py-3">{{ number_format($row->total_coins) }}</td>
              <td class="px-4 py-3">{{ number_format($row->contributions) }}</td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900"><td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No contributors yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </x-common.component-card>

  <x-common.component-card title="Event Log" desc="Room-scoped PK battle event history.">
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">ID</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Type</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Room</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Coins</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Wallet Tx</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Created At</th></tr></thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($pk_battle->events as $event)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3">{{ $event->id }}</td>
              <td class="px-4 py-3">{{ strtoupper($event->event_type) }}</td>
              <td class="px-4 py-3">@if($event->room)<a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.live-rooms.show', $event->room) }}">{{ $event->room->room_id }}</a>@else{{ $event->room_id }}@endif</td>
              <td class="px-4 py-3">{{ number_format($event->coins) }}</td>
              <td class="px-4 py-3">{{ $event->wallet_transaction_id ?: '—' }}</td>
              <td class="px-4 py-3">{{ optional($event->created_at)->format('d M Y H:i:s') }}</td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900"><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No PK events logged.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </x-common.component-card>
</div>
@endsection
