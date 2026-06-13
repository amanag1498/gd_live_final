@extends('layouts.admin-tailadmin')
@section('title', 'User Subscriptions')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $sourceLabel = static function (?string $value): string {
    if ($value === null || $value === '') {
      return 'Unknown';
    }

    return match ($value) {
      'USER_PURCHASE' => 'User Purchase',
      'signup_gift' => 'Signup Gift',
      'admin_user_360' => 'Admin Grant',
      'admin' => 'Admin',
      default => ucwords(str_replace(['_', '-'], ' ', strtolower($value))),
    };
  };
@endphp

@section('page_actions')
  <div class="flex gap-3">
    <x-ui.button variant="outline" size="sm" href="{{ route('admin.subscription-plans.index') }}">Plans</x-ui.button>
    <x-ui.button size="sm" href="{{ route('admin.user-subscriptions.create') }}">Create Subscription</x-ui.button>
  </div>
@endsection

@section('content')
<div class="space-y-6">
  @if(session('success'))
    <x-ui.alert variant="success">{{ session('success') }}</x-ui.alert>
  @endif

  @if(session('error'))
    <x-ui.alert variant="danger">{{ session('error') }}</x-ui.alert>
  @endif

  <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
    <x-admin.stat-card label="Active" :value="number_format($summary['active'] ?? 0)" tone="success" />
    <x-admin.stat-card label="Expired" :value="number_format($summary['expired'] ?? 0)" tone="dark" />
    <x-admin.stat-card label="Cancelled" :value="number_format($summary['cancelled'] ?? 0)" tone="warning" />
    <x-admin.stat-card label="Gifted" :value="number_format($summary['gifted'] ?? 0)" tone="brand" />
    <x-admin.stat-card label="Expiring Soon" :value="number_format($summary['expiring_soon'] ?? 0)" :meta="number_format($summary['renewal_rate'] ?? 0, 1).'% renewal rate'" tone="dark" />
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div class="max-w-2xl">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">User Subscriptions</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track plan ownership, grant source, expiry windows, and account-level subscription access from one filtered admin view.</p>
        </div>
        <x-ui.badge color="dark">{{ number_format($subs->total()) }} matching records</x-ui.badge>
      </div>
    </x-slot:header>

    <form method="get" class="grid gap-3 border-b border-gray-200 pb-5 dark:border-gray-800 sm:grid-cols-2 xl:grid-cols-4">
      <div class="xl:col-span-2">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
        <input
          name="q"
          value="{{ request('q') }}"
          class="{{ $inputClass }}"
          placeholder="Subscription ID, user name, email, or plan">
      </div>

      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
        <select name="status" class="{{ $inputClass }}">
          <option value="">Any status</option>
          @foreach(['active' => 'Active', 'expired' => 'Expired', 'cancelled' => 'Cancelled'] as $value => $label)
            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Plan</label>
        <select name="plan_id" class="{{ $inputClass }}">
          <option value="">Any plan</option>
          @foreach($plans as $plan)
            <option value="{{ $plan->id }}" @selected((string) request('plan_id') === (string) $plan->id)>{{ $plan->name }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Source</label>
        <select name="source" class="{{ $inputClass }}">
          <option value="">Any source</option>
          @foreach($sources as $source)
            <option value="{{ $source }}" @selected(request('source') === $source)>{{ $sourceLabel($source) }}</option>
          @endforeach
        </select>
      </div>

      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Starts From</label>
        <input type="date" name="starts_from" value="{{ request('starts_from') }}" class="{{ $inputClass }}">
      </div>

      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Starts To</label>
        <input type="date" name="starts_to" value="{{ request('starts_to') }}" class="{{ $inputClass }}">
      </div>

      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Ends From</label>
        <input type="date" name="ends_from" value="{{ request('ends_from') }}" class="{{ $inputClass }}">
      </div>

      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Ends To</label>
        <input type="date" name="ends_to" value="{{ request('ends_to') }}" class="{{ $inputClass }}">
      </div>

      <div class="flex items-end gap-3 sm:col-span-2 xl:col-span-4">
        <x-ui.button type="submit" size="sm">Apply Filters</x-ui.button>
        <x-ui.button variant="outline" size="sm" href="{{ route('admin.user-subscriptions.index') }}">Reset</x-ui.button>
      </div>

      <div class="sm:col-span-2 xl:col-span-4">
        <p class="text-xs text-gray-500 dark:text-gray-400">Date filters apply to the subscription window fields: <span class="font-semibold">starts_at</span> and <span class="font-semibold">ends_at</span>.</p>
      </div>
    </form>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Subscriber</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Plan</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">State</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Starts</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Ends</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Source</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Meta</th>
            <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($subs as $subscription)
            @php
              $meta = is_array($subscription->meta ?? null)
                ? $subscription->meta
                : (is_string($subscription->meta ?? null) ? json_decode($subscription->meta, true) : []);
              $meta = is_array($meta) ? $meta : [];
              $metaSource = $meta['source'] ?? '—';
              $metaEvent = $meta['event'] ?? null;
              $metaJson = json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            @endphp
            <tr class="bg-white align-top dark:bg-gray-900">
              <td class="px-4 py-4">
                <div class="font-semibold text-gray-900 dark:text-white">{{ $subscription->user?->name ?? 'Unknown user' }}</div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $subscription->user?->email ?? 'No email' }}</div>
                <div class="mt-2 flex flex-wrap gap-2 text-xs">
                  <x-ui.badge color="dark">User #{{ $subscription->user_id }}</x-ui.badge>
                  <x-ui.badge color="dark">Sub #{{ $subscription->id }}</x-ui.badge>
                </div>
              </td>
              <td class="px-4 py-4">
                <div class="font-semibold text-gray-900 dark:text-white">{{ $subscription->plan?->name ?? '—' }}</div>
                @if($subscription->plan)
                  <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ number_format((int) $subscription->plan->price_coins) }} coins · {{ number_format((int) $subscription->plan->duration_days) }} days
                  </div>
                @endif
              </td>
              <td class="px-4 py-4">
                <x-ui.badge :color="$subscription->status === 'active' ? 'success' : ($subscription->status === 'cancelled' ? 'warning' : 'dark')">
                  {{ ucfirst($subscription->status) }}
                </x-ui.badge>
                @if($subscription->status === 'active' && $subscription->ends_at && $subscription->ends_at->isFuture() && $subscription->ends_at->lte(now()->copy()->addDays(7)))
                  <div class="mt-2 text-xs font-medium text-amber-600 dark:text-amber-400">Expiring within 7 days</div>
                @endif
              </td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
                {{ $subscription->starts_at?->format('d M Y, h:i A') ?? '—' }}
              </td>
              <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
                <div>{{ $subscription->ends_at?->format('d M Y, h:i A') ?? '—' }}</div>
                @if($subscription->last_purchased_at)
                  <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">Purchased {{ $subscription->last_purchased_at->format('d M Y') }}</div>
                @endif
              </td>
              <td class="px-4 py-4">
                @php($normalizedSourceLabel = $sourceLabel(is_string($metaSource) ? $metaSource : null))
                <x-ui.badge :color="$metaSource === 'signup_gift' ? 'warning' : 'dark'">{{ $normalizedSourceLabel }}</x-ui.badge>
                @if($metaEvent)
                  <div class="mt-2 max-w-[180px] truncate text-xs text-gray-500 dark:text-gray-400">{{ $metaEvent }}</div>
                @endif
              </td>
              <td class="px-4 py-4">
                @if(empty($meta))
                  <span class="text-sm text-gray-400 dark:text-gray-500">No meta</span>
                @else
                  <div class="max-w-[260px] rounded-2xl border border-gray-200 bg-gray-50 p-3 text-xs text-gray-600 dark:border-gray-800 dark:bg-gray-950/60 dark:text-gray-300">
                    @foreach(array_slice($meta, 0, 3, true) as $key => $value)
                      <div class="flex gap-2">
                        <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $key }}</span>
                        <span class="truncate">{{ is_scalar($value) || $value === null ? ($value === null ? 'null' : (is_bool($value) ? ($value ? 'true' : 'false') : (string) $value)) : json_encode($value, JSON_UNESCAPED_SLASHES) }}</span>
                      </div>
                    @endforeach
                    <details class="mt-3">
                      <summary class="cursor-pointer font-medium text-brand-600 dark:text-brand-400">View full meta</summary>
                      <pre class="mt-2 overflow-x-auto whitespace-pre-wrap rounded-xl bg-white p-3 text-[11px] leading-5 text-gray-600 dark:bg-gray-900 dark:text-gray-300">{{ $metaJson }}</pre>
                    </details>
                  </div>
                @endif
              </td>
              <td class="px-4 py-4 text-right">
                <div class="flex justify-end gap-2">
                  <x-ui.button variant="outline" size="sm" href="{{ route('admin.user-subscriptions.edit', $subscription) }}">Edit</x-ui.button>
                  <x-ui.button variant="outline" size="sm" href="{{ route('admin.users.show', $subscription->user) }}">Profile</x-ui.button>

                  @if($subscription->status === 'active')
                    <form method="post" action="{{ route('admin.user-subscriptions.cancel', $subscription->id) }}" onsubmit="return confirm('Cancel this subscription now?')">
                      @csrf
                      <x-ui.button variant="warning" size="sm" type="submit">Cancel</x-ui.button>
                    </form>
                  @endif

                  <form method="post" action="{{ route('admin.user-subscriptions.destroy', $subscription) }}" onsubmit="return confirm('Delete this subscription? This cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <x-ui.button variant="danger" size="sm" type="submit">Delete</x-ui.button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="8" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No subscriptions matched these filters.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <x-slot:footer>
      <div class="flex justify-end">
        {{ $subs->links() }}
      </div>
    </x-slot:footer>
  </x-common.component-card>
</div>
@endsection
