@extends('layouts.admin-tailadmin')
@section('title','Notifications · Recent')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('page_actions')
  <x-ui.button size="sm" href="{{ route('admin.notifications.compose') }}">Compose</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
          <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Recent Notifications</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Inspect delivery history, resend important messages, and filter by recipient or date range.</p>
          </div>
          <div class="flex items-center gap-2">
            <x-ui.badge color="dark">{{ number_format($items->total()) }} records</x-ui.badge>
          </div>
        </div>

        <form method="get" class="grid gap-3 lg:grid-cols-[140px_180px_auto_180px_180px_auto] xl:grid-cols-[140px_180px_170px_180px_180px_auto]">
          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">User ID</label>
            <input class="{{ $inputClass }}" name="user_id" value="{{ request('user_id') }}" placeholder="User ID">
          </div>

          <div>
            <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
            <input class="{{ $inputClass }}" name="type" value="{{ request('type') }}" placeholder="Type" list="typeList">
            @isset($types)
              <datalist id="typeList">
                @foreach($types as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach
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

          <div class="flex flex-wrap items-end gap-3 lg:justify-end">
            <x-ui.button variant="outline" type="submit" size="sm">Filter</x-ui.button>
            <x-ui.button variant="outline" href="{{ route('admin.notifications.index') }}" size="sm">Reset</x-ui.button>
            <x-ui.button href="{{ route('admin.notifications.compose') }}" size="sm">Compose</x-ui.button>
          </div>
        </form>
      </div>
    </x-slot:header>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">#</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">User</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Title</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Type</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Read</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Created</th>
            <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($items as $n)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3">{{ $n->id }}</td>
              <td class="px-4 py-3">
                <a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.users.notifications',$n->user_id) }}">
                  User #{{ $n->user_id }}
                </a>
              </td>
              <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $n->title }}</td>
              <td class="px-4 py-3"><code class="rounded-md bg-gray-100 px-2 py-1 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-200">{{ $n->type ?? '—' }}</code></td>
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
                    'user_id'  => $n->user_id,
                    'type'     => $n->type,
                    'title'    => $n->title,
                    'body'     => $n->body,
                    'meta'     => is_array($n->meta) ? json_encode($n->meta) : $n->meta,
                  ]) }}">
                  Resend
                </x-ui.button>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900"><td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No notifications found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-4 flex justify-end">
      {{ $items->withQueryString()->links() }}
    </div>
  </x-common.component-card>
</div>
@endsection
