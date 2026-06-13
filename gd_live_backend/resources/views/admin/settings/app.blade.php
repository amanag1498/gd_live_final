@extends('layouts.admin-tailadmin')
@section('title', 'App Settings')

@php
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
            <x-ui.badge color="brand">App Controls</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">App Settings</h2>
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Control maintenance mode, upgrades, and cross-platform feature toggles from a single settings surface.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
          <x-admin.stat-card label="Bootstrap Endpoint" value="/api/app-config" meta="Public app config source" />
          <x-admin.stat-card label="Maintenance Bypass" value="Admin and health routes" meta="Safe paths during maintenance" tone="dark" />
        </div>
      </div>
    </div>
  </section>

  <form method="post" action="{{ route('admin.settings.app.update') }}" class="space-y-6">
    @csrf
    @method('PUT')

    @foreach($groups as $groupKey => $groupLabel)
      <x-common.component-card :title="$groupLabel" desc="Stored in app_settings and loaded into runtime config.">
        <div class="grid gap-4 xl:grid-cols-2">
          @foreach($definitions as $key => $definition)
            @continue(($definition['group'] ?? 'general') !== $groupKey)
            @php($field = str_replace('app_features.', '', $key))
            @php($fieldName = str_replace('.', '][', $field))
            @php($inputName = "app_features[{$fieldName}]")
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
                    <option value="{{ $option }}" @selected(old($key, $values[$key] ?? $definition['default'] ?? null) === $option)>{{ ucfirst($option) }}</option>
                  @endforeach
                </select>
              @elseif(($definition['type'] ?? 'boolean') === 'csv_integer_list' || ($definition['type'] ?? 'boolean') === 'string')
                <input type="text" class="{{ $inputClass }}" name="{{ $inputName }}" value="{{ old($key, $values[$key] ?? $definition['default'] ?? '') }}">
              @elseif(($definition['type'] ?? 'boolean') === 'integer')
                <input
                  type="number"
                  class="{{ $inputClass }}"
                  name="{{ $inputName }}"
                  value="{{ old($key, $values[$key] ?? $definition['default'] ?? '') }}"
                  @if(array_key_exists('min', $definition)) min="{{ $definition['min'] }}" @endif
                  @if(array_key_exists('max', $definition)) max="{{ $definition['max'] }}" @endif
                  step="1"
                >
              @else
                <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-900">
                  <input type="hidden" name="{{ $inputName }}" value="0">
                  <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="{{ $inputName }}" value="1" @checked(old($key, $values[$key] ?? false))>
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Enabled</span>
                </label>
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
      <div class="text-sm text-gray-500 dark:text-gray-400">These toggles are stored in <code>app_settings</code> and loaded into config at boot.</div>
      <x-ui.button type="submit" size="sm">Save App Settings</x-ui.button>
    </div>
  </form>
</div>
@endsection
