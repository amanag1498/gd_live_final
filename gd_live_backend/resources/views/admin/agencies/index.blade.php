@extends('layouts.admin-tailadmin')
@section('title','Agencies')

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Agencies</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage partner agencies, dashboards, and wallet status.</p>
        </div>
        <form class="flex flex-col gap-2 sm:flex-row" method="get">
          <input class="h-11 rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden ring-0 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500" name="s" value="{{ request('s') }}" placeholder="Search name/owner">
          <x-ui.button type="submit" variant="outline" size="sm">Search</x-ui.button>
        </form>
      </div>
    </x-slot:header>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Name</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Owner</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Phone</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($agencies as $a)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3">{{ $a->id }}</td>
              <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $a->name }}</td>
              <td class="px-4 py-3">
                <div class="font-medium text-gray-900 dark:text-white">{{ $a->owner?->name ?? '—' }}</div>
                <div class="text-gray-500 dark:text-gray-400">{{ $a->owner?->email ?? '' }}</div>
              </td>
              <td class="px-4 py-3">{{ $a->contact_phone ?? '—' }}</td>
              <td class="px-4 py-3">
                @if($a->is_blocked)
                  <x-ui.badge color="error">Blocked</x-ui.badge>
                @else
                  <x-ui.badge color="success">Active</x-ui.badge>
                @endif
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex flex-wrap justify-end gap-2">
                  <x-ui.button href="{{ route('admin.agencies.dashboard',$a) }}" variant="outline" size="sm">Dashboard</x-ui.button>
                  <x-ui.button href="{{ route('admin.agencies.hosts.index',$a) }}" variant="outline" size="sm">Hosts</x-ui.button>
                  <x-ui.button href="{{ route('admin.agencies.wallet.show',$a) }}" variant="outline" size="sm">Wallet</x-ui.button>
                  <x-ui.button href="{{ route('admin.agencies.edit',$a) }}" size="sm">Edit</x-ui.button>
                  @if($a->is_blocked)
                    <form method="post" action="{{ route('admin.agencies.unblock',$a) }}" class="inline">@csrf<x-ui.button variant="success" size="sm" type="submit">Unblock</x-ui.button></form>
                  @else
                    <form method="post" action="{{ route('admin.agencies.block',$a) }}" class="inline">@csrf<x-ui.button variant="danger" size="sm" type="submit">Block</x-ui.button></form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900"><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No agencies found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-4 flex justify-end">{{ $agencies->links() }}</div>
  </x-common.component-card>
</div>
@endsection
