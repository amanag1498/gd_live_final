@extends('layouts.admin-tailadmin')
@section('title', 'Online Alerts')

@section('content')
<div class="space-y-6">
<x-common.component-card title="Online Alerts" desc="Track persisted host-online notifications sent to followers.">
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">User</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Type</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Message</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Created</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($items as $item)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-4">
                <div class="font-semibold text-gray-900 dark:text-white">{{ $item->user?->name ?: 'User #'.$item->user_id }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $item->user?->email }}</div>
              </td>
              <td class="px-4 py-4"><x-ui.badge color="dark">{{ $item->type }}</x-ui.badge></td>
              <td class="px-4 py-4">
                <div class="font-medium text-gray-900 dark:text-white">{{ $item->title }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $item->body }}</div>
              </td>
              <td class="px-4 py-4"><x-ui.badge :color="$item->read_at ? 'success' : 'warning'">{{ $item->read_at ? 'Read' : 'Unread' }}</x-ui.badge></td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ optional($item->created_at)->format('d M Y, H:i') }}</td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900"><td colspan="5" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No online alerts recorded.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <x-slot:footer><div class="flex justify-end">{{ $items->links() }}</div></x-slot:footer>
  </x-common.component-card>
</div>
@endsection
