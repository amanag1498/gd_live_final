@extends('layouts.admin-tailadmin')
@section('title', 'Subscription Plans')

@section('page_actions')
  <x-ui.button size="sm" href="{{ route('admin.subscription-plans.create') }}">New Plan</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  @if(session('success'))
    <x-ui.alert variant="success">{{ session('success') }}</x-ui.alert>
  @endif

  <x-common.component-card>
    <x-slot:header>
      <div class="flex items-start justify-between gap-3">
        <div class="max-w-2xl">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Subscription Plans</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage recurring premium plans, their pricing, duration, and perks payload used throughout the product.</p>
        </div>
        <x-ui.badge color="dark">{{ number_format($plans->total()) }} plans</x-ui.badge>
      </div>
    </x-slot:header>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Name</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Price</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Duration</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($plans as $p)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-4 font-semibold text-gray-900 dark:text-white">{{ $p->name }}</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ number_format($p->price_coins) }} coins</td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">{{ number_format($p->duration_days) }} days</td>
              <td class="px-4 py-4"><x-ui.badge :color="$p->is_active ? 'success' : 'dark'">{{ $p->is_active ? 'Active' : 'Inactive' }}</x-ui.badge></td>
              <td class="px-4 py-4 text-right">
                <div class="flex justify-end gap-2">
                  <x-ui.button variant="outline" size="sm" href="{{ route('admin.subscription-plans.edit',$p) }}">Edit</x-ui.button>
                  <form method="post" action="{{ route('admin.subscription-plans.destroy',$p) }}" onsubmit="return confirm('Delete plan?')">
                    @csrf
                    @method('DELETE')
                    <x-ui.button variant="danger" size="sm" type="submit">Delete</x-ui.button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="5" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No subscription plans found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <x-slot:footer>
      <div class="flex justify-end">
        {{ $plans->links() }}
      </div>
    </x-slot:footer>
  </x-common.component-card>
</div>
@endsection
