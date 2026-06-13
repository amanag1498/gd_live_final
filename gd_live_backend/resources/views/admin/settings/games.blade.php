@extends('layouts.admin-tailadmin')
@section('title', 'Game Settings')

@php
  $selectedGame = request('game', 'teen_patti');
  if (!in_array($selectedGame, ['teen_patti', 'greedy'], true)) {
    $selectedGame = 'teen_patti';
  }

  $gameMeta = [
    'teen_patti' => [
      'label' => 'Teen Patti',
      'subtitle' => 'Cards, pot flow, fake bets, payout rule, and room-strip visibility.',
      'dashboard_route' => 'admin.games.teen-patti.dashboard',
      'settings_route' => route('admin.settings.games.edit', ['game' => 'teen_patti']),
    ],
    'greedy' => [
      'label' => 'Greedy',
      'subtitle' => 'Spinner timing, fake bets, weighted pots, multipliers, and sector distribution.',
      'dashboard_route' => 'admin.games.greedy.dashboard',
      'settings_route' => route('admin.settings.games.edit', ['game' => 'greedy']),
    ],
  ];

  $selectedMeta = $gameMeta[$selectedGame];
  $prefix = "games.{$selectedGame}.";
  $filteredDefinitions = collect($definitions)->filter(fn ($definition, $key) => str_starts_with($key, $prefix));
  $groupedDefinitions = [];
  foreach ($groups as $groupKey => $groupLabel) {
    $items = $filteredDefinitions->filter(fn ($definition) => ($definition['group'] ?? 'general') === $groupKey);
    if ($items->isNotEmpty()) {
      $groupedDefinitions[$groupKey] = ['label' => $groupLabel, 'items' => $items];
    }
  }
  $enabledKey = "games.{$selectedGame}.enabled";
  $visibleKey = "games.{$selectedGame}.visible_in_video_room_strip";
  $fakeKey = "games.{$selectedGame}.fake_bets_enabled";
  $minKey = "games.{$selectedGame}.min_bet";
  $maxKey = "games.{$selectedGame}.max_bet";
  $durationKey = "games.{$selectedGame}.round_duration_seconds";
  $lockKey = "games.{$selectedGame}.betting_lock_seconds";
  $displayKey = "games.{$selectedGame}.result_display_seconds";
  $inputClass = 'h-10 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="dark">Config</x-ui.badge>
            <x-ui.badge color="brand">Games</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Game Settings</h2>
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Separate control surfaces for Teen Patti and Greedy. Keep round engines, fake bets, timing, and payout controls isolated.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.games.teen-patti.dashboard') }}">Teen Patti Dashboard</x-ui.button>
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.games.greedy.dashboard') }}">Greedy Dashboard</x-ui.button>
        </div>
      </div>
    </div>
  </section>

  <div class="flex flex-wrap gap-2">
    @foreach($gameMeta as $gameKey => $meta)
      <a href="{{ $meta['settings_route'] }}" class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-medium {{ $selectedGame === $gameKey ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'border border-gray-300 bg-white text-gray-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200' }}">
        {{ $meta['label'] }}
      </a>
    @endforeach
  </div>

  <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <x-admin.stat-card label="Current Game" :value="$selectedMeta['label']" :meta="$selectedMeta['subtitle']" />
    <x-admin.stat-card label="Status" :value="!empty($values[$enabledKey]) ? 'Enabled' : 'Disabled'" :meta="!empty($values[$visibleKey]) ? 'Visible in video room strip' : 'Hidden from room strip'" tone="success" />
    <x-admin.stat-card label="Bet Window" :value="($values[$durationKey] ?? '—') . 's'" :meta="'Lock ' . ($values[$lockKey] ?? '—') . 's, display ' . ($values[$displayKey] ?? '—') . 's'" tone="warning" />
    <x-admin.stat-card label="Bet Range" :value="number_format((int) ($values[$minKey] ?? 0)) . ' - ' . number_format((int) ($values[$maxKey] ?? 0))" :meta="!empty($values[$fakeKey]) ? 'Fake bets enabled' : 'Fake bets disabled'" tone="dark" />
  </section>

  <form method="post" action="{{ route('admin.settings.games.update', ['game' => $selectedGame]) }}" class="space-y-6">
    @csrf
    @method('PUT')

    @foreach($groupedDefinitions as $groupKey => $group)
      <x-common.component-card :title="$group['label']" :desc="$selectedMeta['subtitle']">
        <div class="grid gap-4 xl:grid-cols-2">
          @foreach($group['items'] as $key => $definition)
            @php($field = str_replace('games.', '', $key))
            @php($fieldName = str_replace('.', '][', $field))
            @php($inputName = "games[{$fieldName}]")
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950/60">
              <div class="mb-3">
                <div class="font-semibold text-gray-900 dark:text-white">{{ $definition['label'] }}</div>
                @if(!empty($definition['hint']))
                  <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $definition['hint'] }}</div>
                @endif
              </div>
              @if(($definition['type'] ?? 'boolean') === 'string' && !empty($definition['options']))
                <select class="{{ $inputClass }}" name="{{ $inputName }}">
                  @foreach(($definition['options'] ?? []) as $option)
                    <option value="{{ $option }}" @selected(old($key, $values[$key] ?? $definition['default'] ?? null) === $option)>{{ ucfirst(str_replace('_', ' ', $option)) }}</option>
                  @endforeach
                </select>
              @elseif(($definition['type'] ?? 'boolean') === 'boolean')
                <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-900">
                  <input type="hidden" name="{{ $inputName }}" value="0">
                  <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="{{ $inputName }}" value="1" @checked(old($key, $values[$key] ?? false))>
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Enabled</span>
                </label>
              @else
                <input
                  type="number"
                  step="{{ $definition['step'] ?? 1 }}"
                  min="{{ $definition['min'] ?? 0 }}"
                  max="{{ $definition['max'] ?? '' }}"
                  class="{{ $inputClass }}"
                  name="{{ $inputName }}"
                  value="{{ old($key, $values[$key] ?? $definition['default'] ?? '') }}"
                >
              @endif
              @error($key)
                <div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div>
              @enderror
            </div>
          @endforeach
        </div>
      </x-common.component-card>
    @endforeach

    <div class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white px-5 py-4 dark:border-gray-800 dark:bg-gray-900">
      <div class="text-sm text-gray-500 dark:text-gray-400">These values are stored in <code>app_settings</code> and used by Laravel, the websocket service, and the Android client.</div>
      <x-ui.button type="submit" size="sm">Save {{ $selectedMeta['label'] }} Settings</x-ui.button>
    </div>
  </form>
</div>
@endsection
