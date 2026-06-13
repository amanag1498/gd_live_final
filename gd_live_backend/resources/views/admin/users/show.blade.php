@extends('layouts.admin-tailadmin')
@section('title', 'User 360 · '.$user->name)

@php
  $inputClass = 'block w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
  $labelClass = 'mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300';
  $surfaceClass = 'rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950/60';
  $actionButtonClass = 'inline-flex items-center justify-center rounded-2xl px-4 py-2.5 text-sm font-semibold transition';
@endphp

@section('page_actions')
  <a href="{{ route('admin.wallets.show', $user) }}" class="{{ $actionButtonClass }} bg-brand-500 text-white hover:bg-brand-600">Wallet</a>
  <a href="{{ route('admin.users.notifications', $user) }}" class="{{ $actionButtonClass }} border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Notifications</a>
  @if($user->is_blocked)
    <form method="post" action="{{ route('admin.users.unblock', $user) }}">
      @csrf
      <button class="{{ $actionButtonClass }} bg-success-500 text-white hover:bg-success-600">Unblock</button>
    </form>
  @else
    <form method="post" action="{{ route('admin.users.block', $user) }}">
      @csrf
      <button class="{{ $actionButtonClass }} bg-error-500 text-white hover:bg-error-600">Block</button>
    </form>
  @endif
@endsection

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="flex flex-col gap-6 px-6 py-6 lg:flex-row lg:items-start lg:justify-between lg:px-8">
      <div class="min-w-0">
        <div class="mb-3 flex flex-wrap items-center gap-2">
          @if($user->is_blocked)
            <x-ui.badge color="error">Blocked</x-ui.badge>
          @else
            <x-ui.badge color="success">Active</x-ui.badge>
          @endif
          @foreach($user->getRoleNames() as $role)
            <x-ui.badge color="dark">{{ $role }}</x-ui.badge>
          @endforeach
        </div>
        <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $user->name }}</h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $user->email }}</p>
        <div class="mt-3 flex flex-wrap gap-3 text-sm text-gray-500 dark:text-gray-400">
          <span>User #{{ $user->id }}</span>
          @if($user->device_id)
            <span>Device <code>{{ $user->device_id }}</code></span>
          @endif
          <span>Joined {{ $user->created_at?->format('d M Y, H:i') }}</span>
        </div>
      </div>
      <div class="grid gap-3 sm:grid-cols-2 xl:min-w-[320px]">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
          <div class="text-sm text-gray-500 dark:text-gray-400">Firebase UID</div>
          <div class="mt-2 break-all text-sm font-medium text-gray-900 dark:text-white">{{ $user->firebase_uid ?? '—' }}</div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
          <div class="text-sm text-gray-500 dark:text-gray-400">Provider</div>
          <div class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ $user->provider ?? '—' }}</div>
        </div>
      </div>
    </div>
  </section>

  <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
    <x-admin.stat-card label="Wallet" :value="number_format($walletSummary['balance'])" meta="Current coin balance" />
    <x-admin.stat-card label="Following" :value="number_format($followingCount)" meta="Accounts followed" tone="dark" />
    <x-admin.stat-card label="Followers" :value="number_format($followersCount)" meta="Followers on profile" tone="brand" />
    <x-admin.stat-card label="Rooms Joined" :value="number_format($overviewStats['live_rooms_joined'])" meta="Live room participation" tone="warning" />
    <x-admin.stat-card label="Calls" :value="number_format($overviewStats['calls_total'])" meta="Call history volume" tone="dark" />
    <x-admin.stat-card label="Gift Spend" :value="number_format($overviewStats['gifts_sent'])" meta="Total coins spent on gifts" tone="danger" />
  </section>

  <section class="grid gap-6 xl:grid-cols-[minmax(320px,0.9fr)_minmax(0,1.4fr)]">
    <div class="space-y-6">
      <x-common.component-card>
        <x-slot:header>
          <div class="flex items-center justify-between gap-3">
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Identity</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Authentication, verification, and device metadata.</p>
            </div>
          </div>
        </x-slot:header>
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Firebase UID</div><div class="mt-2 break-all text-sm font-medium text-gray-900 dark:text-white">{{ $user->firebase_uid ?? '—' }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Provider</div><div class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ $user->provider ?? '—' }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Device</div><div class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ $user->device_id ?? '—' }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Email Verified</div><div class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ $user->email_verified_at?->format('d M Y, H:i') ?? 'No' }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Updated</div><div class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ $user->updated_at?->format('d M Y, H:i') }}</div></div>
        </div>
      </x-common.component-card>

      <x-common.component-card>
        <x-slot:header>
          <div class="flex items-center justify-between gap-3">
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Wallet Controls</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Credit or debit the wallet with an explicit audit reason.</p>
            </div>
            <a href="{{ route('admin.wallets.show', $user) }}" class="inline-flex items-center justify-center rounded-2xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Full Ledger</a>
          </div>
        </x-slot:header>
        <div class="grid gap-3 sm:grid-cols-2">
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Credits</div><div class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($walletSummary['credits']) }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Debits</div><div class="mt-2 text-lg font-semibold text-gray-900 dark:text-white">{{ number_format($walletSummary['debits']) }}</div></div>
        </div>
        <div class="mt-4 space-y-4">
          <form method="post" action="{{ route('admin.wallets.credit', $user) }}" class="grid gap-3 sm:grid-cols-[140px_minmax(0,1fr)]">
            @csrf
            <input type="number" name="amount" min="1" class="{{ $inputClass }}" placeholder="Coins" required>
            <input type="text" name="note" class="{{ $inputClass }}" placeholder="Credit reason">
            <div class="sm:col-span-2">
              <button class="inline-flex w-full items-center justify-center rounded-2xl bg-success-500 px-4 py-3 text-sm font-semibold text-white hover:bg-success-600">Credit Wallet</button>
            </div>
          </form>
          <form method="post" action="{{ route('admin.wallets.debit', $user) }}" class="grid gap-3 sm:grid-cols-[140px_minmax(0,1fr)]">
            @csrf
            <input type="number" name="amount" min="1" class="{{ $inputClass }}" placeholder="Coins" required>
            <input type="text" name="note" class="{{ $inputClass }}" placeholder="Debit reason">
            <div class="sm:col-span-2">
              <button class="inline-flex w-full items-center justify-center rounded-2xl bg-error-500 px-4 py-3 text-sm font-semibold text-white hover:bg-error-600">Debit Wallet</button>
            </div>
          </form>
        </div>
      </x-common.component-card>

      <x-common.component-card title="Level Controls" desc="Current level placement and manual override controls.">
        <div class="space-y-4">
          <div>
            @if($user->level)
              <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold text-white" style="background: {{ $user->level->badge_color ?: '#64748b' }}">L{{ $user->level->level }} · {{ $user->level->title }}</span>
            @else
              <span class="text-sm text-gray-500 dark:text-gray-400">No level assigned</span>
            @endif
          </div>
          <div class="text-sm text-gray-500 dark:text-gray-400">
            Lifetime spend {{ number_format($levelProgress['lifetime_spend_coins'] ?? 0) }}
            @if(!empty($levelProgress['next_level']))
              · {{ number_format($levelProgress['remaining_spend_to_next_level'] ?? 0) }} to next
            @endif
          </div>
          <div class="h-2.5 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
            <div class="h-full rounded-full bg-brand-500" style="width: {{ (float) ($levelProgress['progress_percent'] ?? 0) }}%"></div>
          </div>
          <form method="post" action="{{ route('admin.users.level.set', $user) }}" class="space-y-3">
            @csrf
            <div>
              <label class="{{ $labelClass }}">Level</label>
              <select name="level_id" class="{{ $inputClass }}" required>
                @foreach($availableLevels as $level)
                  <option value="{{ $level->id }}" @selected((int) $user->level_id === (int) $level->id)>L{{ $level->level }} · {{ $level->title }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="{{ $labelClass }}">Reason</label>
              <input type="text" name="reason" class="{{ $inputClass }}" placeholder="Reason">
            </div>
            <button class="inline-flex w-full items-center justify-center rounded-2xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-600">Update Level</button>
          </form>
        </div>
      </x-common.component-card>

      <x-common.component-card title="Game Access" desc="Control which game APIs this user can access.">
        <form method="post" action="{{ route('admin.users.games.update', $user) }}" class="space-y-4">
          @csrf
          <div class="{{ $surfaceClass }}">
            <div class="flex items-center justify-between gap-4">
              <div>
                <div class="text-sm font-semibold text-gray-900 dark:text-white">Teen Patti</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Unlock game access for user #{{ $user->id }}</div>
              </div>
              <label class="inline-flex items-center gap-3">
                <input type="hidden" name="teen_patti" value="0">
                <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="teen_patti" value="1" @checked($gameAccessMap['teen_patti'] ?? false)>
              </label>
            </div>
          </div>
          <div class="{{ $surfaceClass }}">
            <div class="flex items-center justify-between gap-4">
              <div>
                <div class="text-sm font-semibold text-gray-900 dark:text-white">Greedy</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Unlock game access for user #{{ $user->id }}</div>
              </div>
              <label class="inline-flex items-center gap-3">
                <input type="hidden" name="greedy" value="0">
                <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="greedy" value="1" @checked($gameAccessMap['greedy'] ?? false)>
              </label>
            </div>
          </div>
          <div>
            <label class="{{ $labelClass }}">Reason</label>
            <input type="text" name="reason" class="{{ $inputClass }}" placeholder="Reason for access change">
          </div>
          <button class="inline-flex w-full items-center justify-center rounded-2xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-600">Save Game Access</button>
        </form>
      </x-common.component-card>
    </div>

    <div class="space-y-6">
      <x-common.component-card>
        <x-slot:header>
          <div class="flex items-center justify-between gap-3">
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Subscriptions</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Grant, monitor, and cancel subscription access.</p>
            </div>
            <a href="{{ route('admin.user-subscriptions.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">All Subscriptions</a>
          </div>
        </x-slot:header>
        <div class="grid gap-3 md:grid-cols-3">
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Active</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $activeSubscription?->plan?->name ?? 'None' }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Status</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $activeSubscription?->status ?? '—' }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Ends</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $activeSubscription?->ends_at?->format('d M Y') ?? '—' }}</div></div>
        </div>
        <form method="post" action="{{ route('admin.users.subscriptions.store', $user) }}" class="mt-4 grid gap-3 md:grid-cols-5">
          @csrf
          <select name="plan_id" class="{{ $inputClass }} md:col-span-2" required>
            @foreach($availablePlans as $plan)
              <option value="{{ $plan->id }}">{{ $plan->name }} · {{ number_format($plan->price_coins) }} coins / {{ $plan->duration_days }}d</option>
            @endforeach
          </select>
          <select name="status" class="{{ $inputClass }}">
            <option value="active">Active</option>
            <option value="cancelled">Cancelled</option>
            <option value="expired">Expired</option>
          </select>
          <input type="datetime-local" name="starts_at" class="{{ $inputClass }}">
          <input type="datetime-local" name="ends_at" class="{{ $inputClass }}">
          <input type="text" name="reason" class="{{ $inputClass }} md:col-span-4" placeholder="Reason">
          <button class="inline-flex items-center justify-center rounded-2xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-600">Grant</button>
        </form>
        <div class="mt-4 overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60">
              <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">ID</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Plan</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Starts</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Ends</th>
                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              @forelse($subscriptions as $subscription)
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3">{{ $subscription->id }}</td>
                  <td class="px-4 py-3">{{ $subscription->plan?->name ?? '—' }}</td>
                  <td class="px-4 py-3"><x-ui.badge color="dark">{{ strtoupper($subscription->status) }}</x-ui.badge></td>
                  <td class="px-4 py-3">{{ $subscription->starts_at?->format('d M Y H:i') }}</td>
                  <td class="px-4 py-3">{{ $subscription->ends_at?->format('d M Y H:i') }}</td>
                  <td class="px-4 py-3 text-right">
                    <div class="flex justify-end gap-2">
                      <a href="{{ route('admin.user-subscriptions.edit', $subscription) }}" class="inline-flex items-center justify-center rounded-2xl border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Edit</a>
                      @if($subscription->status === 'active')
                        <form method="post" action="{{ route('admin.users.subscriptions.cancel', [$user, $subscription]) }}">
                          @csrf
                          <button class="inline-flex items-center justify-center rounded-2xl border border-warning-200 bg-warning-50 px-3 py-2 text-xs font-semibold text-warning-700 hover:bg-warning-100 dark:border-warning-500/30 dark:bg-warning-500/10 dark:text-warning-300">Cancel</button>
                        </form>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr class="bg-white dark:bg-gray-900"><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No subscriptions.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </x-common.component-card>

      <x-common.component-card>
        <x-slot:header>
          <div class="flex items-center justify-between gap-3">
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Entry Packs</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Assign entrance packs and inspect ownership history.</p>
            </div>
            <a href="{{ route('admin.entry-packs.reports') }}" class="inline-flex items-center justify-center rounded-2xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Ownership Reports</a>
          </div>
        </x-slot:header>
        <div class="grid gap-3 md:grid-cols-3">
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Active Pack</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $activeEntryPack?->entryPack?->name ?? 'None' }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Style</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ strtoupper($activeEntryPack?->entryPack?->animation_style ?? '—') }}</div></div>
          <div class="{{ $surfaceClass }}"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Expires</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $activeEntryPack?->expires_at?->format('d M Y') ?? '—' }}</div></div>
        </div>
        <form method="post" action="{{ route('admin.users.entry-packs.store', $user) }}" class="mt-4 grid gap-3 md:grid-cols-5">
          @csrf
          <select name="entry_pack_id" class="{{ $inputClass }} md:col-span-2" required>
            @foreach($availableEntryPacks as $pack)
              <option value="{{ $pack->id }}">{{ $pack->name }} · {{ number_format($pack->price_coins) }} coins · {{ $pack->duration_days }}d</option>
            @endforeach
          </select>
          <input type="datetime-local" name="purchased_at" class="{{ $inputClass }}">
          <input type="datetime-local" name="expires_at" class="{{ $inputClass }}">
          <label class="flex items-center gap-3 rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200">
            <input type="checkbox" name="is_active" value="1" id="is_active_entry" checked class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900">
            Active
          </label>
          <input type="text" name="reason" class="{{ $inputClass }} md:col-span-4" placeholder="Reason">
          <button class="inline-flex items-center justify-center rounded-2xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-600">Assign Entry Pack</button>
        </form>
        <div class="mt-4 overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60">
              <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">ID</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Pack</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Purchased</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Expires</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              @forelse($entryHistory as $entry)
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3">{{ $entry->id }}</td>
                  <td class="px-4 py-3">{{ $entry->entryPack?->name ?? '—' }}</td>
                  <td class="px-4 py-3">
                    @if($entry->is_currently_usable)
                      <x-ui.badge color="success">Active</x-ui.badge>
                    @elseif($entry->expires_at && $entry->expires_at->isPast())
                      <x-ui.badge color="warning">Expired</x-ui.badge>
                    @else
                      <x-ui.badge color="dark">Inactive</x-ui.badge>
                    @endif
                  </td>
                  <td class="px-4 py-3">{{ $entry->purchased_at?->format('d M Y H:i') }}</td>
                  <td class="px-4 py-3">{{ $entry->expires_at?->format('d M Y H:i') ?? '—' }}</td>
                </tr>
              @empty
                <tr class="bg-white dark:bg-gray-900"><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No entry packs.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </x-common.component-card>

      <x-common.component-card title="Host / Agency Linkage" desc="Current host profile and agency relationship for this user.">
        @if($user->host)
          <div class="grid gap-3 md:grid-cols-2">
            <div class="{{ $surfaceClass }}">
              <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Host</div>
              <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">#{{ $user->host->id }} · {{ $user->host->stage_name ?: $user->name }}</div>
              <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ trim(($user->host->city ?? '').' '.($user->host->country ?? '')) ?: '—' }}</div>
            </div>
            <div class="{{ $surfaceClass }}">
              <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Agency</div>
              <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">{{ $user->host->agency?->name ?? 'No agency' }}</div>
              <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $user->host->agency?->contact_email ?? '—' }}</div>
            </div>
          </div>
        @else
          <div class="rounded-2xl border border-dashed border-gray-300 px-4 py-6 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">User is not linked to a host profile.</div>
        @endif
      </x-common.component-card>
    </div>
  </section>

  <section class="grid gap-6 xl:grid-cols-2">
    <x-common.component-card title="Recent Activity" desc="Participation across live rooms, calls, and gifts.">
      <div class="space-y-4">
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Room</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Role</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Joined</th></tr></thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              @forelse($recentLiveParticipations as $row)
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3">@if($row->room)<a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.live-rooms.show', $row->room) }}">{{ $row->room->room_id }}</a>@else—@endif</td>
                  <td class="px-4 py-3">{{ $row->role }}</td>
                  <td class="px-4 py-3">{{ $row->joined_at?->format('d M Y H:i') }}</td>
                </tr>
              @empty
                <tr class="bg-white dark:bg-gray-900"><td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No live participation.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Call</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Type</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Coins</th></tr></thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              @forelse($recentCalls as $call)
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3">#{{ $call->id }}</td>
                  <td class="px-4 py-3">{{ strtoupper($call->type) }}</td>
                  <td class="px-4 py-3">{{ strtoupper($call->status) }}</td>
                  <td class="px-4 py-3">{{ number_format($call->total_coins_charged ?? 0) }}</td>
                </tr>
              @empty
                <tr class="bg-white dark:bg-gray-900"><td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No calls.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Gift</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Room</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Total Coins</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">When</th></tr></thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              @forelse($recentGifts as $gift)
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3">{{ $gift->gift?->name ?? 'Gift' }}</td>
                  <td class="px-4 py-3">@if($gift->room)<a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.live-rooms.show', $gift->room) }}">{{ $gift->room->room_id }}</a>@else—@endif</td>
                  <td class="px-4 py-3">{{ number_format($gift->total_coins) }}</td>
                  <td class="px-4 py-3">{{ $gift->created_at?->format('d M Y H:i') }}</td>
                </tr>
              @empty
                <tr class="bg-white dark:bg-gray-900"><td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No gifts sent.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </x-common.component-card>

    <x-common.component-card title="Hosted / PK / Audit Trail" desc="Hosted rooms, PK battles, and administrative actions.">
      <div class="space-y-4">
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Hosted Room</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Started</th></tr></thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              @forelse($recentHostedRooms as $room)
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3"><a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.live-rooms.show', $room) }}">{{ $room->room_id }}</a></td>
                  <td class="px-4 py-3">{{ strtoupper($room->status) }}</td>
                  <td class="px-4 py-3">{{ $room->started_at?->format('d M Y H:i') ?: '—' }}</td>
                </tr>
              @empty
                <tr class="bg-white dark:bg-gray-900"><td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No hosted rooms.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">PK Battle</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Score</th></tr></thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              @forelse($pkBattles as $battle)
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3"><a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="{{ route('admin.pk-battles.show', $battle) }}">{{ $battle->battle_id }}</a></td>
                  <td class="px-4 py-3">{{ strtoupper($battle->status) }}</td>
                  <td class="px-4 py-3">{{ number_format($battle->score_a) }} - {{ number_format($battle->score_b) }}</td>
                </tr>
              @empty
                <tr class="bg-white dark:bg-gray-900"><td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No PK participation.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">When</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Area</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Action</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Admin</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Reason</th></tr></thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              @forelse($auditTrail as $audit)
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3">{{ $audit->created_at?->format('d M Y H:i') }}</td>
                  <td class="px-4 py-3">{{ strtoupper(str_replace('_', ' ', $audit->area)) }}</td>
                  <td class="px-4 py-3">{{ str_replace('_', ' ', $audit->action) }}</td>
                  <td class="px-4 py-3">{{ $audit->admin?->name ?? 'System' }}</td>
                  <td class="px-4 py-3">{{ $audit->reason ?: '—' }}</td>
                </tr>
              @empty
                <tr class="bg-white dark:bg-gray-900"><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No admin audit entries.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </x-common.component-card>
  </section>
</div>
@endsection
