@extends('layouts.admin-tailadmin')
@section('title','User Notifications')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $pageItems = $items->getCollection();
  $unreadCount = $pageItems->whereNull('read_at')->count();
  $typeCount = $pageItems->pluck('type')->filter()->unique()->count();
@endphp

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ route('admin.notifications.index') }}">All Notifications</x-ui.button>
  <x-ui.button size="sm" href="{{ route('admin.notifications.compose', ['audience' => 'user', 'user_id' => $user->id]) }}">
    Send Notification
  </x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="min-w-0">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="dark">User Inbox</x-ui.badge>
            <x-ui.badge color="brand">User #{{ $user->id }}</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $user->name }}</h2>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $user->email ?: 'No email on file' }}</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-3">
          <x-admin.stat-card label="Total Notifications" :value="number_format($items->total())" meta="Messages currently visible in this filtered view" />
          <x-admin.stat-card label="Unread" :value="number_format($unreadCount)" meta="Unread notifications on this page" tone="warning" />
          <x-admin.stat-card label="Types" :value="number_format($typeCount)" meta="Distinct notification types on this page" tone="dark" />
        </div>
      </div>
    </div>
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Notification History</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Review delivery history, narrow the timeline, and resend individual messages to this user.</p>
        </div>

        <form class="grid gap-3 lg:grid-cols-[200px_auto_180px_180px_auto]" method="get">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
            <input class="{{ $inputClass }}" name="type" value="{{ request('type') }}" placeholder="Type" list="typeList">
            @isset($types)
              <datalist id="typeList">
                @foreach($types as $t)
                  <option value="{{ $t }}">{{ $t }}</option>
                @endforeach
              </datalist>
            @endisset
          </div>

          <label class="flex items-end gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 dark:border-gray-800 dark:bg-gray-950/60 dark:text-gray-300">
            <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" id="unreadOnly" name="unread" value="1" {{ request('unread') ? 'checked' : '' }}>
            <span>Unread only</span>
          </label>

          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">From</label>
            <input type="date" class="{{ $inputClass }}" name="created_from" value="{{ request('created_from') }}">
          </div>

          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">To</label>
            <input type="date" class="{{ $inputClass }}" name="created_to" value="{{ request('created_to') }}">
          </div>

          <div class="flex flex-wrap items-end justify-end gap-3">
            <x-ui.button variant="outline" type="submit" size="sm">Filter</x-ui.button>
            <x-ui.button variant="outline" href="{{ route('admin.users.notifications', $user->id) }}" size="sm">Reset</x-ui.button>
          </div>
        </form>
      </div>
    </x-slot:header>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">#</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Title</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Type</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Body</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Read</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Created</th>
            <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($items as $n)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3">{{ $n->id }}</td>
              <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $n->title }}</td>
              <td class="px-4 py-3">
                <code class="rounded-md bg-gray-100 px-2 py-1 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-200">{{ $n->type ?? '—' }}</code>
              </td>
              <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ \Illuminate\Support\Str::limit($n->body, 110) }}</td>
              <td class="px-4 py-3">
                @if($n->read_at)
                  <x-ui.badge color="success">Read</x-ui.badge>
                @else
                  <x-ui.badge color="dark">Unread</x-ui.badge>
                @endif
              </td>
              <td class="px-4 py-3">{{ $n->created_at?->format('Y-m-d H:i') }}</td>
              <td class="px-4 py-3 text-right">
                <x-ui.button
                  variant="outline"
                  size="sm"
                  href="{{ route('admin.notifications.compose', [
                    'audience' => 'user',
                    'user_id' => $user->id,
                    'type' => $n->type,
                    'title' => $n->title,
                    'body' => $n->body,
                    'meta' => is_array($n->meta) ? json_encode($n->meta) : $n->meta,
                  ]) }}">
                  Resend
                </x-ui.button>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No notifications found for this user.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <x-slot:footer>
      <div class="flex justify-end">
        {{ $items->withQueryString()->links() }}
      </div>
    </x-slot:footer>
  </x-common.component-card>
</div>
@endsection
