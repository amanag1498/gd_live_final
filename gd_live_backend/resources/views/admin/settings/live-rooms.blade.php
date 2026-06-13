@extends('layouts.admin-tailadmin')
@section('title', 'Live Room Settings')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('content')
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="dark">Config</x-ui.badge>
            <x-ui.badge color="brand">Live Rooms</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Live Room Settings</h2>
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Define default participant and speaker limits used when new video rooms and PK rooms are created.</p>
        </div>
      </div>
    </div>
  </section>

  <form method="post" action="{{ route('admin.settings.live-rooms.update') }}" class="space-y-6">
    @csrf
    @method('PUT')

    <div class="grid gap-6 xl:grid-cols-2">
      <x-common.component-card title="Video Rooms" desc="Default video room capacity and validation ceiling.">
        <div class="grid gap-4">
          @foreach ($definitions as $key => $definition)
            @continue(!str_starts_with($key, 'live_rooms.video.'))
            @php($field = str_replace('live_rooms.video.', '', $key))
            <div>
              <label class="mb-2 block font-semibold text-gray-900 dark:text-white">{{ $definition['label'] }}</label>
              <input
                type="number"
                name="live_rooms[video][{{ $field }}]"
                class="{{ $inputClass }}"
                value="{{ old("live_rooms.video.{$field}", $values[$key]) }}"
                min="{{ $definition['min'] ?? 0 }}"
                @if(isset($definition['max'])) max="{{ $definition['max'] }}" @endif
                step="1"
              >
              @error("live_rooms.video.{$field}")
                <div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div>
              @enderror
              <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $definition['hint'] }}</div>
            </div>
          @endforeach
        </div>
      </x-common.component-card>

      <x-common.component-card title="PK Battles" desc="Capacity defaults used by PK room provisioning.">
        <div class="grid gap-4">
          @foreach ($definitions as $key => $definition)
            @continue(!str_starts_with($key, 'live_rooms.pk.'))
            @php($field = str_replace('live_rooms.pk.', '', $key))
            <div>
              <label class="mb-2 block font-semibold text-gray-900 dark:text-white">{{ $definition['label'] }}</label>
              <input
                type="number"
                name="live_rooms[pk][{{ $field }}]"
                class="{{ $inputClass }}"
                value="{{ old("live_rooms.pk.{$field}", $values[$key]) }}"
                min="{{ $definition['min'] ?? 0 }}"
                @if(isset($definition['max'])) max="{{ $definition['max'] }}" @endif
                step="1"
              >
              @error("live_rooms.pk.{$field}")
                <div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div>
              @enderror
              <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $definition['hint'] }}</div>
            </div>
          @endforeach
        </div>
      </x-common.component-card>
    </div>

    <div class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white px-5 py-4 dark:border-gray-800 dark:bg-gray-900">
      <div class="text-sm text-gray-500 dark:text-gray-400">Hosts can still choose lower values per room. These settings define backend defaults and validation ceilings.</div>
      <x-ui.button type="submit" size="sm">Save Live Room Settings</x-ui.button>
    </div>
  </form>
</div>
@endsection
