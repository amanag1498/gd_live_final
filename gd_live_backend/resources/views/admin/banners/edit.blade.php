@extends('layouts.admin-tailadmin')
@section('title','Edit Banner')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $selectedPlatforms = old('platforms', $banner->platforms ?? []);
  $selectedRoles = old('target_roles', $banner->target_roles ?? []);
  $previewUrl = $previewUrl ?? (function () use ($banner) {
      $img = (string) ($banner->image_url ?? '');
      if ($img === '') {
          return '';
      }
      return \Illuminate\Support\Str::startsWith($img, ['http://', 'https://', '/'])
          ? $img
          : \Illuminate\Support\Facades\Storage::url($img);
  })();
@endphp

@section('content')
<form method="post" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data" class="space-y-6">
  @csrf
  @method('PUT')

  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="dark">Promotion</x-ui.badge>
            <x-ui.badge :color="$banner->is_active ? 'success' : 'dark'">{{ $banner->is_active ? 'Active' : 'Inactive' }}</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $banner->title }}</h2>
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Update media, audience targeting, timing, and CTA configuration for this banner.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
          <x-admin.stat-card label="Placement" :value="ucfirst($banner->placement)" meta="Current banner surface" />
          <x-admin.stat-card label="Action Type" :value="strtoupper($banner->action_type)" meta="Current CTA behaviour" tone="dark" />
        </div>
      </div>
    </div>
  </section>

  <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
    <x-common.component-card>
      <x-slot:header>
        <div class="flex items-center justify-between gap-3">
          <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Banner Details</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Adjust creative and delivery settings without losing the current record.</p>
          </div>
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.banners.index') }}">Back</x-ui.button>
        </div>
      </x-slot:header>

      @if($errors->any())
        <div class="mb-6 rounded-2xl border border-error-200 bg-error-50 px-4 py-3 text-sm text-error-700 dark:border-error-500/30 dark:bg-error-500/10 dark:text-error-300">
          <div class="mb-1 font-semibold">Please fix the following:</div>
          <ul class="list-disc space-y-1 pl-5">
            @foreach($errors->all() as $e)
              <li>{{ $e }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="grid gap-4 lg:grid-cols-12">
        <div class="lg:col-span-6">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
          <input name="title" class="{{ $inputClass }}" value="{{ old('title', $banner->title) }}" required maxlength="120">
        </div>
        <div class="lg:col-span-6">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Upload New Image</label>
          <input type="file" name="image_file" accept="image/*" class="{{ $inputClass }}">
          <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Leave empty to keep the current banner image.</p>
        </div>
        <div class="lg:col-span-6">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Image URL</label>
          <input name="image_url" class="{{ $inputClass }}" value="{{ old('image_url', $banner->image_url) }}" maxlength="2048">
        </div>
        <div class="lg:col-span-6">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Target URL</label>
          <input name="target_url" class="{{ $inputClass }}" value="{{ old('target_url', $banner->target_url) }}">
        </div>
        <div class="lg:col-span-4">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Placement</label>
          <select name="placement" class="{{ $inputClass }}" required>
            @foreach($placements as $placement)
              <option value="{{ $placement }}" @selected(old('placement', $banner->placement) === $placement)>{{ ucfirst($placement) }}</option>
            @endforeach
          </select>
        </div>
        <div class="lg:col-span-4">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Action Type</label>
          <select name="action_type" class="{{ $inputClass }}" required>
            @foreach($actionTypes as $actionType)
              <option value="{{ $actionType }}" @selected(old('action_type', $banner->action_type) === $actionType)>{{ strtoupper($actionType) }}</option>
            @endforeach
          </select>
        </div>
        <div class="lg:col-span-4">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Button Text</label>
          <input name="button_text" class="{{ $inputClass }}" value="{{ old('button_text', $banner->button_text) }}" maxlength="60" placeholder="Open">
        </div>
        <div class="lg:col-span-8">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Action Value</label>
          <input name="action_value" class="{{ $inputClass }}" value="{{ old('action_value', $banner->action_value) }}" placeholder="URL, deep link, or route">
        </div>
        <div class="lg:col-span-4">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Sort Order</label>
          <input type="number" min="0" name="sort_order" class="{{ $inputClass }}" value="{{ old('sort_order', $banner->sort_order) }}">
        </div>
        <div class="lg:col-span-6">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Starts At</label>
          <input type="datetime-local" name="starts_at" class="{{ $inputClass }}" value="{{ old('starts_at', $banner->starts_at?->format('Y-m-d\\TH:i')) }}">
        </div>
        <div class="lg:col-span-6">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Ends At</label>
          <input type="datetime-local" name="ends_at" class="{{ $inputClass }}" value="{{ old('ends_at', $banner->ends_at?->format('Y-m-d\\TH:i')) }}">
        </div>

        <div class="lg:col-span-6">
          <label class="mb-3 block text-sm font-medium text-gray-700 dark:text-gray-300">Platforms</label>
          <div class="grid gap-2 sm:grid-cols-3">
            @foreach($platforms as $platform)
              <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 dark:border-gray-800 dark:bg-gray-950/60 dark:text-gray-300">
                <input class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="platforms[]" value="{{ $platform }}" @checked(in_array($platform, $selectedPlatforms, true))>
                <span>{{ strtoupper($platform) }}</span>
              </label>
            @endforeach
          </div>
        </div>

        <div class="lg:col-span-6">
          <label class="mb-3 block text-sm font-medium text-gray-700 dark:text-gray-300">Target Roles</label>
          <div class="grid gap-2 sm:grid-cols-3">
            @foreach($roles as $role)
              <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 dark:border-gray-800 dark:bg-gray-950/60 dark:text-gray-300">
                <input class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="target_roles[]" value="{{ $role }}" @checked(in_array($role, $selectedRoles, true))>
                <span>{{ ucfirst($role) }}</span>
              </label>
            @endforeach
          </div>
        </div>

        <div class="lg:col-span-12">
          <label class="mb-3 block text-sm font-medium text-gray-700 dark:text-gray-300">Active</label>
          <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 dark:border-gray-800 dark:bg-gray-950/60 dark:text-gray-300">
            <input type="hidden" name="is_active" value="0">
            <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="is_active" value="1" {{ old('is_active', (int) $banner->is_active) ? 'checked' : '' }}>
            <span>Banner is active and available for delivery</span>
          </label>
        </div>
      </div>

      <x-slot:footer>
        <div class="flex flex-wrap justify-end gap-3">
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.banners.index') }}">Cancel</x-ui.button>
          <x-ui.button type="submit" size="sm">Update Banner</x-ui.button>
        </div>
      </x-slot:footer>
    </x-common.component-card>

    <x-common.component-card title="Preview" desc="Current creative preview for this banner.">
      <div class="flex min-h-[220px] items-center justify-center rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-950/60">
        @if($previewUrl !== '')
          <img src="{{ $previewUrl }}" alt="banner" class="max-h-[180px] w-full rounded-2xl object-contain" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
          <div class="hidden text-sm text-gray-500 dark:text-gray-400">Image unavailable. Save a new image.</div>
        @else
          <div class="text-sm text-gray-500 dark:text-gray-400">No image on this banner yet.</div>
        @endif
      </div>
    </x-common.component-card>
  </section>
</form>
@endsection
