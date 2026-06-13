@extends('layouts.admin-tailadmin')
@section('title','Live Presence')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $ttlValues = collect($rows)->pluck('ttl_ms')->filter();
  $criticalCount = $ttlValues->filter(fn ($ttl) => $ttl < 10000)->count();
  $bestTtl = $ttlValues->max();
@endphp

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="dark">Realtime</x-ui.badge>
            <x-ui.badge color="brand">Presence Monitor</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Live Presence</h2>
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Monitor connected users, TTL health, and online volume without leaving the admin panel. The table refreshes automatically every five seconds.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-3">
          <x-admin.stat-card label="Online Users" :value="number_format($count)" meta="Current presence records" />
          <x-admin.stat-card label="Low TTL" :value="number_format($criticalCount)" meta="Users expiring in under 10s" tone="warning" />
          <x-admin.stat-card label="Best TTL" :value="$bestTtl ? ceil($bestTtl / 1000) . 's' : '—'" meta="Strongest remaining presence TTL" tone="dark" />
        </div>
      </div>
    </div>
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Connected Users</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This view automatically refreshes and highlights records that are close to expiring.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
          <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-2 text-sm text-gray-700 dark:border-gray-800 dark:bg-gray-950/60 dark:text-gray-300">
            Last update:
            <span id="presenceUpdated" class="ml-1 font-semibold text-gray-900 dark:text-white">{{ now()->format('H:i:s') }}</span>
          </div>
          <div class="rounded-xl border border-brand-200 bg-brand-50 px-4 py-2 text-sm font-semibold text-brand-700 dark:border-brand-500/30 dark:bg-brand-500/10 dark:text-brand-300">
            <span id="presenceCount">{{ $count }}</span> online
          </div>
        </div>
      </div>
    </x-slot:header>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">User ID</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Name / Email</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">TTL</th>
          </tr>
        </thead>
        <tbody id="presenceBody" class="divide-y divide-gray-200 dark:divide-gray-800">
          @forelse($rows as $r)
            @php $ttl = $r['ttl_ms'] ?? null; @endphp
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">#{{ $r['user_id'] }}</td>
              <td class="px-4 py-3">
                @if(!empty($r['user']))
                  <div class="font-medium text-gray-900 dark:text-white">{{ $r['user']['name'] ?? 'User' }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">{{ $r['user']['email'] ?? '' }}</div>
                @else
                  <span class="text-gray-500 dark:text-gray-400">Unknown</span>
                @endif
              </td>
              <td class="px-4 py-3">
                @if($ttl)
                  <x-ui.badge :color="$ttl < 10000 ? 'warning' : 'dark'">{{ ceil($ttl / 1000) }}s</x-ui.badge>
                @else
                  <span class="text-gray-500 dark:text-gray-400">—</span>
                @endif
              </td>
            </tr>
          @empty
            <tr class="bg-white dark:bg-gray-900">
              <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No one online right now.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </x-common.component-card>
</div>

@push('scripts')
<script>
(function(){
  const countEl = document.getElementById('presenceCount');
  const bodyEl  = document.getElementById('presenceBody');
  const updEl   = document.getElementById('presenceUpdated');

  function esc(s){return (s||'').replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[c]));}

  async function refresh(){
    try{
      const res = await fetch(`{{ route('admin.presence.stats') }}?withUsers=1`, { headers:{'X-Requested-With':'XMLHttpRequest'} });
      if(!res.ok) return;
      const data = await res.json();

      countEl.textContent = data.count ?? 0;
      updEl.textContent = new Date().toLocaleTimeString();

      const rows = data.rows || [];
      if(!rows.length){
        bodyEl.innerHTML = '<tr class="bg-white dark:bg-gray-900"><td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No one online right now.</td></tr>';
        return;
      }

      bodyEl.innerHTML = rows.map(r=>{
        const user = r.user || {};
        const ttl = r.ttl_ms ? Math.ceil(r.ttl_ms/1000)+'s' : '—';
        const badgeClass = (r.ttl_ms && r.ttl_ms < 10000)
          ? 'bg-warning-50 text-warning-700 border border-warning-200 dark:border-warning-500/30 dark:bg-warning-500/10 dark:text-warning-300'
          : 'bg-gray-100 text-gray-700 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200';

        return `
          <tr class="bg-white dark:bg-gray-900">
            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">#${r.user_id}</td>
            <td class="px-4 py-3">
              ${user.name ? `<div class="font-medium text-gray-900 dark:text-white">${esc(user.name)}</div>` : '<span class="text-gray-500 dark:text-gray-400">Unknown</span>'}
              ${user.email ? `<div class="text-sm text-gray-500 dark:text-gray-400">${esc(user.email)}</div>` : ''}
            </td>
            <td class="px-4 py-3">${r.ttl_ms ? `<span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ${badgeClass}">${ttl}</span>` : '<span class="text-gray-500 dark:text-gray-400">—</span>'}</td>
          </tr>
        `;
      }).join('');
    }catch(e){}
  }
  setInterval(refresh, 5000);
})();
</script>
@endpush
@endsection
