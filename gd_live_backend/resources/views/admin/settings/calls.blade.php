@extends('layouts.admin-tailadmin')
@section('title', 'Call Settings')

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
            <x-ui.badge color="brand">Calls</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Call Settings</h2>
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Set global rates, balance rules, and call timing used by video calling.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
          <x-admin.stat-card label="Legacy Fallback" :value="number_format($legacyFallbackRate) . ' coins/min'" meta="Fallback env rate" />
          <x-admin.stat-card label="Call Start Rule" value="max(balance, rate)" meta="Effective balance gate" tone="dark" />
        </div>
      </div>
    </div>
  </section>

  <form method="post" action="{{ route('admin.settings.calls.update') }}" class="space-y-6">
    @csrf
    @method('PUT')

    <x-common.component-card title="Rates, Balance Rules, and Timing" desc="Stored in the database and loaded into config('calls') on each request.">
      <div class="grid gap-4 xl:grid-cols-2">
        @foreach($definitions as $key => $definition)
          <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950/60">
            <label class="mb-2 block font-semibold text-gray-900 dark:text-white">{{ $definition['label'] }}</label>
            <input
              type="number"
              name="calls[{{ str_replace('calls.', '', $key) }}]"
              class="{{ $inputClass }}"
              value="{{ old('calls.' . str_replace('calls.', '', $key), $values[$key]) }}"
              min="{{ $definition['min'] ?? 0 }}"
              @if(isset($definition['max'])) max="{{ $definition['max'] }}" @endif
              @if(isset($definition['step'])) step="{{ $definition['step'] }}" @else step="1" @endif
            >
            @error('calls.' . str_replace('calls.', '', $key))
              <div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div>
            @enderror
            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">{{ $definition['hint'] }}</div>
          </div>
        @endforeach
      </div>
    </x-common.component-card>

    <div class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white px-5 py-4 dark:border-gray-800 dark:bg-gray-900">
      <div class="text-sm text-gray-500 dark:text-gray-400">Video call rates drive the call creation snapshot. Billing always uses the stored session rate.</div>
      <x-ui.button type="submit" size="sm">Save Call Settings</x-ui.button>
    </div>
  </form>
</div>
@endsection
