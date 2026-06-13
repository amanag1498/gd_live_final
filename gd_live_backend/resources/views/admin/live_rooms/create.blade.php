@extends('layouts.admin-tailadmin')
@section('title','New Live Room')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('content')
<form method="post" action="{{ route('admin.live-rooms.store') }}" class="space-y-6">
  @csrf

  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="dark">Provisioning</x-ui.badge>
            <x-ui.badge color="brand">Live Room</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Create Live Room</h2>
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Manually create a room record, assign a host, and define the lifecycle metadata used by the live-room operations stack.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
          <x-admin.stat-card label="Default Max Speakers" :value="number_format(data_get($roomSettings, 'video.max_speakers', 4))" meta="Current video room cap" />
          <x-admin.stat-card label="Hosts Loaded" :value="number_format($hosts->count())" meta="Recent hosts available for quick assignment" tone="dark" />
        </div>
      </div>
    </div>
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex items-center justify-between gap-3">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Room Configuration</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">This creates the admin-side room record and sets the initial operational state.</p>
        </div>
        <x-ui.button variant="outline" size="sm" href="{{ route('admin.live-rooms.index') }}">Back</x-ui.button>
      </div>
    </x-slot:header>

    <div class="grid gap-4 lg:grid-cols-12">
      <div class="lg:col-span-6">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Host</label>
        <select name="host_id" class="{{ $inputClass }}" required>
          <option value="">Select host</option>
          @foreach($hosts as $h)
            <option value="{{ $h->id }}" @selected(old('host_id') == $h->id)>{{ $h->user?->name }} ({{ $h->stage_name ?? '—' }})</option>
          @endforeach
        </select>
      </div>

      <div class="lg:col-span-6">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Room ID (provider)</label>
        <input name="room_id" class="{{ $inputClass }}" required maxlength="100" placeholder="Unique room ID from provider" value="{{ old('room_id') }}">
      </div>

      <div class="lg:col-span-6">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
        <input name="title" class="{{ $inputClass }}" maxlength="150" placeholder="Optional room title" value="{{ old('title') }}">
      </div>

      <div class="lg:col-span-3">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
        <select name="status" class="{{ $inputClass }}">
          <option value="live" @selected(old('status', 'live') === 'live')>Live</option>
          <option value="ended" @selected(old('status') === 'ended')>Ended</option>
        </select>
      </div>

      <div class="lg:col-span-3">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Peak Viewers</label>
        <input type="number" name="peak_viewers" min="0" value="{{ old('peak_viewers', 0) }}" class="{{ $inputClass }}">
      </div>

      <div class="lg:col-span-4">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Started At</label>
        <input type="datetime-local" name="started_at" class="{{ $inputClass }}" value="{{ old('started_at') }}">
      </div>

      <div class="lg:col-span-4">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Ended At</label>
        <input type="datetime-local" name="ended_at" class="{{ $inputClass }}" value="{{ old('ended_at') }}">
      </div>

      <div class="lg:col-span-4">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Max Speakers</label>
        <input type="number" name="max_speakers" min="1" max="{{ data_get($roomSettings, 'video.max_speakers', 4) }}" value="{{ old('max_speakers', data_get($roomSettings, 'video.max_speakers', 4)) }}" class="{{ $inputClass }}">
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Default video room cap from Live Room Settings.</p>
      </div>

      <div class="lg:col-span-12">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">End Reason</label>
        <input name="end_reason" class="{{ $inputClass }}" maxlength="50" placeholder="Optional end reason" value="{{ old('end_reason') }}">
      </div>
    </div>

    <x-slot:footer>
      <div class="flex flex-wrap justify-end gap-3">
        <x-ui.button variant="outline" size="sm" href="{{ route('admin.live-rooms.index') }}">Cancel</x-ui.button>
        <x-ui.button type="submit" size="sm">Create Room</x-ui.button>
      </div>
    </x-slot:footer>
  </x-common.component-card>
</form>
@endsection
