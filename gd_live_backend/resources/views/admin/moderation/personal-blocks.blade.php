@extends('layouts.admin-tailadmin')
@section('title', 'Personal Blocks')

@php
  $inputClass = 'block w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
@endphp

@section('content')
<div class="space-y-6">
  <section class="rounded-3xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
      <div>
        <div class="flex flex-wrap items-center gap-2">
          <x-ui.badge color="brand">Personal safety</x-ui.badge>
          <x-ui.badge color="dark">Separate from host moderation</x-ui.badge>
        </div>
        <h2 class="mt-3 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Personal User Blocks</h2>
        <p class="mt-2 max-w-3xl text-sm text-gray-600 dark:text-gray-300">Review user-to-user blocks used to hide profiles, rooms, calls, gifts, follows, and PK interactions. Removing a block is an audited support override and does not change host moderation.</p>
      </div>
      <a href="{{ route('admin.moderation.blocked-users') }}" class="inline-flex items-center justify-center rounded-2xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200 dark:hover:bg-gray-800">View Host Blocks</a>
    </div>
  </section>

  <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-admin.stat-card label="Active Blocks" :value="number_format($summary['total'])" meta="Current personal block relationships" tone="danger" />
    <x-admin.stat-card label="Blocking Users" :value="number_format($summary['blockers'])" meta="Unique users who blocked someone" tone="warning" />
    <x-admin.stat-card label="Blocked Users" :value="number_format($summary['blocked_users'])" meta="Unique users hidden by others" tone="dark" />
    <x-admin.stat-card label="Blocked Hosts" :value="number_format($summary['host_targets'])" meta="Blocks where the target is a host" tone="brand" />
  </section>

  <x-common.component-card title="Search and filters" desc="Search either side of the relationship by user ID, name, or email.">
    <form method="get" class="grid gap-3 lg:grid-cols-6">
      <input name="q" value="{{ request('q') }}" class="{{ $inputClass }} lg:col-span-2" placeholder="ID, name, or email">
      <input type="number" min="1" name="blocker_user_id" value="{{ request('blocker_user_id') }}" class="{{ $inputClass }}" placeholder="Blocker ID">
      <input type="number" min="1" name="blocked_user_id" value="{{ request('blocked_user_id') }}" class="{{ $inputClass }}" placeholder="Blocked ID">
      <input type="date" name="from" value="{{ request('from') }}" class="{{ $inputClass }}">
      <input type="date" name="to" value="{{ request('to') }}" class="{{ $inputClass }}">
      <div class="flex gap-3 lg:col-span-6">
        <button class="inline-flex items-center justify-center rounded-2xl bg-brand-500 px-5 py-3 text-sm font-semibold text-white hover:bg-brand-600">Apply filters</button>
        <a href="{{ route('admin.moderation.personal-blocks') }}" class="inline-flex items-center justify-center rounded-2xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-950 dark:text-gray-200">Reset</a>
      </div>
    </form>
  </x-common.component-card>

  <x-common.component-card title="Block relationships" :desc="number_format($rows->total()).' relationships match this view.'">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr class="text-left text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
            <th class="px-4 py-3">Blocking user</th>
            <th class="px-4 py-3">Blocked user</th>
            <th class="px-4 py-3">Effect</th>
            <th class="px-4 py-3">Created</th>
            <th class="px-4 py-3 text-right">Admin override</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @forelse($rows as $row)
            <tr class="align-top">
              <td class="px-4 py-4">
                <a href="{{ $row->blocker ? route('admin.users.show', $row->blocker) : '#' }}" class="font-semibold text-gray-900 hover:text-brand-500 dark:text-white">{{ $row->blocker?->name ?? 'Deleted user' }}</a>
                <div class="mt-1 text-xs text-gray-500">#{{ $row->blocker_user_id }} · {{ $row->blocker?->email ?? 'No email' }}</div>
                @if($row->blocker?->host)<div class="mt-2"><x-ui.badge color="brand">Host</x-ui.badge></div>@endif
              </td>
              <td class="px-4 py-4">
                <a href="{{ $row->blockedUser ? route('admin.users.show', $row->blockedUser) : '#' }}" class="font-semibold text-gray-900 hover:text-brand-500 dark:text-white">{{ $row->blockedUser?->name ?? 'Deleted user' }}</a>
                <div class="mt-1 text-xs text-gray-500">#{{ $row->blocked_user_id }} · {{ $row->blockedUser?->email ?? 'No email' }}</div>
                @if($row->blockedUser?->host)<div class="mt-2"><x-ui.badge color="warning">Host target</x-ui.badge></div>@endif
              </td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">Rooms, profiles, follows, calls, gifts, and PK interactions are restricted between these accounts.</td>
              <td class="whitespace-nowrap px-4 py-4 text-gray-600 dark:text-gray-300">{{ $row->created_at?->format('d M Y, H:i') ?? '—' }}</td>
              <td class="px-4 py-4">
                <form method="post" action="{{ route('admin.moderation.personal-blocks.destroy', $row) }}" class="ml-auto max-w-xs space-y-2" onsubmit="return confirm('Remove this personal block? This action is audited.')">
                  @csrf
                  @method('DELETE')
                  <input name="reason" class="{{ $inputClass }}" maxlength="500" placeholder="Support reason (optional)">
                  <button class="inline-flex w-full items-center justify-center rounded-2xl bg-error-500 px-4 py-2.5 text-sm font-semibold text-white hover:bg-error-600">Remove block</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No personal blocks match the current filters.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-5">{{ $rows->withQueryString()->links() }}</div>
  </x-common.component-card>
</div>
@endsection
