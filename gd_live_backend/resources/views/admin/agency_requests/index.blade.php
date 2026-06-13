@extends('layouts.admin-tailadmin')
@section('title','Agency Requests')

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Agency Requests</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Review incoming agency applications and route them into operations.</p>
        </div>
        <x-ui.badge color="dark">Total {{ $requests->total() }}</x-ui.badge>
      </div>
    </x-slot:header>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">User</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Agency</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Applied</th>
            <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Action</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($requests as $r)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3">{{ $r->id }}</td>
              <td class="px-4 py-3">
                <div class="font-medium text-gray-900 dark:text-white">{{ $r->user?->name }}</div>
                <div class="text-gray-500 dark:text-gray-400">{{ $r->user?->email }}</div>
              </td>
              <td class="px-4 py-3">{{ $r->agency_name }}</td>
              <td class="px-4 py-3"><x-ui.badge :color="$r->status==='pending'?'warning':($r->status==='approved'?'success':'error')">{{ ucfirst($r->status) }}</x-ui.badge></td>
              <td class="px-4 py-3">{{ $r->created_at->format('d M Y') }}</td>
              <td class="px-4 py-3 text-right"><x-ui.button size="sm" href="{{ route('admin.agency-requests.show',$r) }}">Review</x-ui.button></td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900"><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No agency requests.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-4 flex justify-end">{{ $requests->links() }}</div>
  </x-common.component-card>
</div>
@endsection
