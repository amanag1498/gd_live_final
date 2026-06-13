@extends('layouts.admin-tailadmin')
@section('title','Users')

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Users</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Search users, inspect device and host relationships, and trigger account actions.</p>
        </div>
        <form class="flex flex-col gap-2 sm:flex-row" method="get">
          <input
            class="h-11 rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden ring-0 placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500"
            name="s"
            value="{{ request('s') }}"
            placeholder="Search user ID, name, or email"
          >
          <x-ui.button size="sm" type="submit">Search</x-ui.button>
        </form>
      </div>
    </x-slot:header>

    <div class="space-y-4">
      @foreach($users as $u)
        @php
          $deviceId = $u->device_id;
          $deviceBlocked = $deviceId ? ($blockedDevices[$deviceId] ?? null) : null;
          $roles = $u->getRoleNames();
        @endphp

        <details class="group overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-950">
          <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4">
            <div class="flex min-w-0 items-center gap-4">
              <x-ui.avatar :initials="$u->name ?: 'U'" size="md" />
              <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2">
                  <span class="font-semibold text-gray-900 dark:text-white">{{ $u->name }}</span>
                  <span class="text-sm text-gray-400">#{{ $u->id }}</span>
                  @if($u->is_blocked)
                    <x-ui.badge color="error">Blocked</x-ui.badge>
                  @else
                    <x-ui.badge color="success">Active</x-ui.badge>
                  @endif
                </div>
                <div class="truncate text-sm text-gray-500 dark:text-gray-400">{{ $u->email }}</div>
              </div>
            </div>

            <div class="hidden flex-wrap items-center justify-end gap-2 lg:flex">
              @forelse($roles as $r)
                <x-ui.badge color="dark">{{ $r }}</x-ui.badge>
              @empty
                <span class="text-sm text-gray-400">No roles</span>
              @endforelse
            </div>

            <div class="flex items-center gap-2">
              <x-ui.button href="{{ route('admin.users.show', $u) }}" size="sm">Profile</x-ui.button>
              <x-ui.button href="{{ route('admin.wallets.show', $u) }}" variant="outline" size="sm">Wallet</x-ui.button>
              <span class="flex h-9 w-9 items-center justify-center rounded-xl border border-gray-200 text-gray-500 transition group-open:rotate-180 dark:border-gray-700 dark:text-gray-300">
                <i class="ti ti-chevron-down"></i>
              </span>
            </div>
          </summary>

          <div class="border-t border-gray-200 px-5 py-5 dark:border-gray-800">
            <div class="mb-4 flex flex-wrap gap-2 lg:hidden">
              @forelse($roles as $r)
                <x-ui.badge color="dark">{{ $r }}</x-ui.badge>
              @empty
                <span class="text-sm text-gray-400">No roles</span>
              @endforelse
            </div>

            <div class="grid gap-4 xl:grid-cols-3">
              <x-common.component-card title="Identity" padding="compact">
                <div class="space-y-2 text-sm">
                  <div><span class="text-gray-500 dark:text-gray-400">User ID:</span> <code>{{ $u->id }}</code></div>
                  <div><span class="text-gray-500 dark:text-gray-400">Firebase UID:</span> <code>{{ $u->firebase_uid ?? '—' }}</code></div>
                  <div class="flex items-center gap-2">
                    <span class="text-gray-500 dark:text-gray-400">Provider:</span>
                    <x-ui.badge color="info">{{ $u->provider ?? '—' }}</x-ui.badge>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="text-gray-500 dark:text-gray-400">Email verified:</span>
                    @if($u->email_verified_at)
                      <x-ui.badge color="success">{{ $u->email_verified_at->format('Y-m-d H:i') }}</x-ui.badge>
                    @else
                      <x-ui.badge color="warning">No</x-ui.badge>
                    @endif
                  </div>
                  <div><span class="text-gray-500 dark:text-gray-400">Created:</span> {{ $u->created_at?->format('Y-m-d H:i') ?? '—' }}</div>
                  <div><span class="text-gray-500 dark:text-gray-400">Updated:</span> {{ $u->updated_at?->format('Y-m-d H:i') ?? '—' }}</div>
                </div>
              </x-common.component-card>

              <x-common.component-card title="Device" padding="compact">
                <div class="space-y-3 text-sm">
                  <div>
                    <div class="text-gray-500 dark:text-gray-400">Device ID</div>
                    @if($deviceId)
                      <code class="mt-1 block break-all">{{ $deviceId }}</code>
                    @else
                      <div class="mt-1 text-gray-400">—</div>
                    @endif
                  </div>
                  <div class="flex flex-wrap items-center gap-2">
                    <span class="text-gray-500 dark:text-gray-400">Device status:</span>
                    @if($deviceId)
                      @if($deviceBlocked)
                        <x-ui.badge color="error">Blocked</x-ui.badge>
                        @if($deviceBlocked->expires_at)
                          <span class="text-gray-500 dark:text-gray-400">until {{ $deviceBlocked->expires_at->format('Y-m-d H:i') }}</span>
                        @endif
                      @else
                        <x-ui.badge color="success">OK</x-ui.badge>
                      @endif
                    @else
                      <span class="text-gray-400">—</span>
                    @endif
                  </div>

                  @if($deviceId)
                    <div class="flex flex-wrap gap-2 pt-1">
                      @if($deviceBlocked)
                        <form method="post" action="{{ route('admin.users.device.unblock',$u) }}">
                          @csrf
                          <x-ui.button variant="outline" size="sm" type="submit">Unblock Device</x-ui.button>
                        </form>
                      @else
                        <form method="post" action="{{ route('admin.users.device.block',$u) }}" onsubmit="return confirm('Block this device_id for all accounts?')">
                          @csrf
                          <x-ui.button variant="danger" size="sm" type="submit">Block Device</x-ui.button>
                        </form>
                      @endif
                    </div>
                  @endif
                </div>
              </x-common.component-card>

              <x-common.component-card title="Host / Agency" padding="compact">
                @if($u->host)
                  <div class="space-y-3 text-sm">
                    <div class="grid gap-2 sm:grid-cols-2">
                      <div><span class="text-gray-500 dark:text-gray-400">Host ID:</span> <code>{{ $u->host->id }}</code></div>
                      <div><span class="text-gray-500 dark:text-gray-400">Stage name:</span> {{ $u->host->stage_name ?? '—' }}</div>
                      <div><span class="text-gray-500 dark:text-gray-400">Contact:</span> {{ $u->host->contact_phone ?? '—' }}</div>
                      <div><span class="text-gray-500 dark:text-gray-400">Location:</span> {{ trim(($u->host->city ?? '').' '.($u->host->country ?? '')) ?: '—' }}</div>
                    </div>

                    <div class="flex items-center gap-2">
                      <span class="text-gray-500 dark:text-gray-400">Host status:</span>
                      @if($u->host->is_blocked)
                        <x-ui.badge color="error">Blocked</x-ui.badge>
                      @else
                        <x-ui.badge color="success">Active</x-ui.badge>
                      @endif
                    </div>

                    <div>
                      <div class="mb-1 text-gray-500 dark:text-gray-400">KYC</div>
                      @if(is_array($u->host->kyc))
                        <pre class="overflow-auto rounded-xl border border-gray-200 bg-gray-50 p-3 text-xs dark:border-gray-800 dark:bg-gray-900">{{ json_encode($u->host->kyc, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
                      @else
                        <div class="text-gray-400">—</div>
                      @endif
                    </div>

                    <div class="border-t border-gray-200 pt-3 dark:border-gray-800">
                      <div class="mb-2 font-medium text-gray-900 dark:text-white">Agency</div>
                      @if($u->host->agency)
                        <div class="space-y-1">
                          <div><span class="text-gray-500 dark:text-gray-400">Agency ID:</span> <code>{{ $u->host->agency->id }}</code></div>
                          <div><span class="text-gray-500 dark:text-gray-400">Name:</span> {{ $u->host->agency->name ?? '—' }}</div>
                          <div><span class="text-gray-500 dark:text-gray-400">Legal:</span> {{ $u->host->agency->legal_name ?? '—' }}</div>
                          <div><span class="text-gray-500 dark:text-gray-400">Contact:</span> {{ $u->host->agency->contact_email ?? '—' }} / {{ $u->host->agency->contact_phone ?? '—' }}</div>
                          <div class="flex items-center gap-2">
                            <span class="text-gray-500 dark:text-gray-400">Status:</span>
                            @if($u->host->agency->is_blocked)
                              <x-ui.badge color="error">Blocked</x-ui.badge>
                            @else
                              <x-ui.badge color="success">Active</x-ui.badge>
                            @endif
                          </div>
                        </div>
                      @else
                        <div class="text-gray-400">No agency</div>
                      @endif
                    </div>
                  </div>
                @else
                  <div class="text-sm text-gray-500 dark:text-gray-400">Not a host</div>
                @endif
              </x-common.component-card>
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
              @if($u->is_blocked)
                <form method="post" action="{{ route('admin.users.unblock',$u) }}">@csrf
                  <x-ui.button variant="success" size="sm" type="submit">Unblock User</x-ui.button>
                </form>
              @else
                <x-ui.button href="{{ route('admin.users.notifications', $u) }}" variant="outline" size="sm">Notifications</x-ui.button>
                <form method="post" action="{{ route('admin.users.block',$u) }}">@csrf
                  <x-ui.button variant="danger" size="sm" type="submit">Block User</x-ui.button>
                </form>
              @endif
            </div>
          </div>
        </details>
      @endforeach
    </div>

    <div class="flex justify-end border-t border-gray-100 pt-4 dark:border-gray-800">
      {{ $users->links() }}
    </div>
  </x-common.component-card>
</div>
@endsection
