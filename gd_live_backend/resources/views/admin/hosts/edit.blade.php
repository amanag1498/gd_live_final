@extends('layouts.admin-tailadmin')
@section('title','Edit Host #'.$host->id)

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $textareaClass = 'w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('content')
<form method="post" action="{{ route('admin.hosts.update',$host) }}" enctype="multipart/form-data" class="space-y-6">
  @csrf
  @method('PUT')

  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="dark">Host Profile</x-ui.badge>
            <x-ui.badge color="brand">User #{{ $host->user_id }}</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $host->stage_name ?: ($host->user?->name ?? 'Unnamed Host') }}</h2>
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Adjust agency assignment, media, and monetization rules for this host profile.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-3">
          <x-admin.stat-card label="Followers" :value="number_format($host->followers->count())" meta="Linked followers" />
          <x-admin.stat-card label="Video Rate" :value="($host->video_call_rate_per_minute ?: config('calls.video_coin_rate_per_minute')).' / min'" meta="Effective video coin rate" tone="dark" />
        </div>
      </div>
    </div>
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex items-center justify-between gap-3">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Host Configuration</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">These values control assignment, media, and profile presentation for this host.</p>
        </div>
        <x-ui.button variant="outline" size="sm" href="{{ route('admin.hosts.index') }}">Back</x-ui.button>
      </div>
    </x-slot:header>

    <div class="grid gap-4 lg:grid-cols-12">
      <div class="lg:col-span-4">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Stage Name</label>
        <input class="{{ $inputClass }}" name="stage_name" value="{{ old('stage_name',$host->stage_name) }}">
      </div>

      <div class="lg:col-span-4">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Agency Assignment</label>
        <select class="{{ $inputClass }}" name="agency_id">
          <option value="">No agency</option>
          @foreach($agencies as $agency)
            <option value="{{ $agency->id }}" @selected((string) old('agency_id', $host->agency_id) === (string) $agency->id)>{{ $agency->name }}</option>
          @endforeach
        </select>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Admin can directly detach or reassign this host without an enroll request.</p>
      </div>

      <div class="lg:col-span-4">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Video Call Rate / min</label>
        <input type="number" min="1" class="{{ $inputClass }}" name="video_call_rate_per_minute" value="{{ old('video_call_rate_per_minute', $host->video_call_rate_per_minute) }}" placeholder="{{ config('calls.video_coin_rate_per_minute') }}">
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Leave blank to use the global video rate: {{ config('calls.video_coin_rate_per_minute') }} coins / minute.</p>
      </div>

      <div class="lg:col-span-12">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Photos (max 6)</label>
        <input type="file" name="photos[]" class="{{ $inputClass }}" multiple accept="image/*">
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Uploading new photos will replace the existing set. Select up to six images in the order you want them shown.</p>

        @if($host->photos->count())
          <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6">
            @foreach($host->photos as $p)
              <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <img src="{{ Storage::url($p->path) }}" class="h-32 w-full object-cover" alt="Host photo">
              </div>
            @endforeach
          </div>
        @endif
      </div>

      <div class="lg:col-span-12">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Bio</label>
        <textarea class="{{ $textareaClass }}" name="bio" rows="4">{{ old('bio',$host->bio) }}</textarea>
      </div>
    </div>

    <x-slot:footer>
      <div class="flex flex-wrap justify-end gap-3">
        <x-ui.button variant="outline" size="sm" href="{{ route('admin.hosts.index') }}">Cancel</x-ui.button>
        <x-ui.button type="submit" size="sm">Save Changes</x-ui.button>
      </div>
    </x-slot:footer>
  </x-common.component-card>

  @if($host->followers->isNotEmpty())
    <x-common.component-card title="Recent Followers" desc="A quick view of the newest followers and their notification preferences.">
      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60">
            <tr>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">User</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Notify Online</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Followed At</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            @foreach($host->followers->sortByDesc('id')->take(10) as $follow)
              <tr class="bg-white dark:bg-gray-900">
                <td class="px-4 py-3">
                  <div class="font-medium text-gray-900 dark:text-white">{{ $follow->user?->name ?: 'User #'.$follow->user_id }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">{{ $follow->user?->email }}</div>
                </td>
                <td class="px-4 py-3">
                  <x-ui.badge :color="$follow->notify_when_online ? 'success' : 'dark'">{{ $follow->notify_when_online ? 'Yes' : 'No' }}</x-ui.badge>
                </td>
                <td class="px-4 py-3">{{ optional($follow->created_at)->format('d M Y, H:i') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </x-common.component-card>
  @endif
</form>
@endsection
