@extends('layouts.admin-tailadmin')
@section('title', 'Levels')

@section('page_actions')
  <x-ui.button variant="outline" size="sm" href="{{ route('admin.reports.levels') }}">Open Level Report</x-ui.button>
  <x-ui.button size="sm" href="{{ route('admin.levels.create') }}">Create Level</x-ui.button>
@endsection

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="max-w-4xl">
        <div class="mb-3 flex flex-wrap gap-2">
          <x-ui.badge color="dark">Level Management</x-ui.badge>
          <x-ui.badge color="brand">Spend Engine</x-ui.badge>
        </div>
        <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">User Levels</h2>
        <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Create, update, activate, sort, and maintain the `user_levels` configuration used by the spend-based level engine.</p>
      </div>
    </div>
  </section>

  <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <x-admin.stat-card label="Configured Levels" :value="number_format($summary['levels'] ?? 0)" meta="Total rows in configuration" />
    <x-admin.stat-card label="Active Levels" :value="number_format($summary['active_levels'] ?? 0)" meta="Currently available in app" tone="brand" />
    <x-admin.stat-card label="Users Mapped" :value="number_format($summary['users_mapped'] ?? 0)" meta="Users assigned by spend engine" tone="warning" />
    <x-admin.stat-card label="Top Threshold" :value="number_format($summary['highest_threshold'] ?? 0)" meta="Highest minimum spend" tone="dark" />
  </section>

  <x-common.component-card title="Configured Levels" desc="Thresholds, rewards, and activation controls for level progression.">
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Level</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Title</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Minimum Spend</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Badge</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Benefits</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Status</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Sort</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Users</th>
            <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($levels as $level)
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">L{{ $level->level }}</td>
              <td class="px-4 py-3">{{ $level->title }}</td>
              <td class="px-4 py-3">{{ number_format($level->min_spend_coins) }}</td>
              <td class="px-4 py-3">
                <div class="flex items-center gap-3">
                  <span class="inline-flex min-w-[72px] items-center justify-center rounded-full px-3 py-2 text-xs font-semibold text-gray-900" style="background: {{ $level->badge_color ?: '#6c757d' }}20; color: {{ $level->badge_color ?: '#6c757d' }}">
                    {{ $level->badge_icon ?: 'default' }}
                  </span>
                  <span class="text-xs text-gray-500 dark:text-gray-400">{{ $level->badge_color ?: '—' }}</span>
                </div>
              </td>
              <td class="px-4 py-3">
                @if(!empty($level->benefits))
                  <div class="space-y-1">
                    @foreach(array_slice($level->benefits, 0, 3) as $benefit)
                      <div class="text-gray-700 dark:text-gray-300">{{ $benefit }}</div>
                    @endforeach
                    @if(count($level->benefits) > 3)
                      <div class="text-xs text-gray-500 dark:text-gray-400">+{{ count($level->benefits) - 3 }} more</div>
                    @endif
                  </div>
                @else
                  <span class="text-gray-500 dark:text-gray-400">—</span>
                @endif
              </td>
              <td class="px-4 py-3">
                <x-ui.badge :color="$level->is_active ? 'success' : 'dark'">
                  {{ $level->is_active ? 'Active' : 'Inactive' }}
                </x-ui.badge>
              </td>
              <td class="px-4 py-3">{{ $level->sort_order }}</td>
              <td class="px-4 py-3">{{ number_format($level->users_count) }}</td>
              <td class="px-4 py-3 text-right">
                <div class="flex flex-wrap justify-end gap-2">
                  <x-ui.button href="{{ route('admin.levels.edit', $level) }}" variant="outline" size="sm">Edit</x-ui.button>
                  <form method="post" action="{{ route('admin.levels.destroy', $level) }}" onsubmit="return confirm('Delete this level?');">
                    @csrf
                    @method('DELETE')
                    <x-ui.button variant="danger" size="sm" type="submit" :disabled="$level->users_count > 0">Delete</x-ui.button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900"><td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No levels configured.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </x-common.component-card>
</div>
@endsection
