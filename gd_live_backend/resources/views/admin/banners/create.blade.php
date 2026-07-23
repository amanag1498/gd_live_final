@extends('layouts.admin-tailadmin')
@section('title','New Banner')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $textareaClass = 'w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('content')
<form method="post" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="space-y-6">
  @csrf

  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="dark">Promotion</x-ui.badge>
            <x-ui.badge color="brand">Banner</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Create Banner</h2>
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Create a campaign banner with media, targeting, placement, and delivery controls.</p>
        </div>
        <div class="grid gap-3 sm:grid-cols-2">
          <x-admin.stat-card label="Placements" :value="number_format(count($placements))" meta="Available banner surfaces" />
          <x-admin.stat-card label="Platforms" :value="number_format(count($platforms))" meta="Supported delivery platforms" tone="dark" />
        </div>
      </div>
    </div>
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex items-center justify-between gap-3">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Banner Details</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Fill the campaign metadata, upload media, and set the audience scope before publishing.</p>
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
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Title <span class="font-normal text-gray-400">(optional)</span></label>
        <input name="title" class="{{ $inputClass }}" value="{{ old('title') }}" maxlength="120">
      </div>
      <div class="lg:col-span-6">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Upload Image</label>
        <input type="file" name="image_file" accept="image/*" class="{{ $inputClass }}">
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">JPG, PNG, or WEBP up to 4MB.</p>
      </div>
      <div class="lg:col-span-6">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Image URL</label>
        <input name="image_url" class="{{ $inputClass }}" value="{{ old('image_url') }}" placeholder="https://..." maxlength="2048">
      </div>
      <div class="lg:col-span-6">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Target URL</label>
        <input name="target_url" class="{{ $inputClass }}" value="{{ old('target_url') }}" placeholder="https://...">
      </div>
      <div class="lg:col-span-4">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Placement</label>
        <select name="placement" class="{{ $inputClass }}" required>
          @foreach($placements as $placement)
            <option value="{{ $placement }}" @selected(old('placement', 'home') === $placement)>{{ ucfirst($placement) }}</option>
          @endforeach
        </select>
      </div>
      <div class="lg:col-span-4">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Action Type</label>
        <select name="action_type" class="{{ $inputClass }}" required>
          @foreach($actionTypes as $actionType)
            <option value="{{ $actionType }}" @selected(old('action_type', 'none') === $actionType)>{{ strtoupper($actionType) }}</option>
          @endforeach
        </select>
      </div>
      <div class="lg:col-span-4">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Button Text</label>
        <input name="button_text" class="{{ $inputClass }}" value="{{ old('button_text') }}" maxlength="60" placeholder="Open">
      </div>
      <div class="lg:col-span-8">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Action Value</label>
        <input name="action_value" class="{{ $inputClass }}" value="{{ old('action_value') }}" placeholder="URL, deep link, or route">
      </div>
      <div class="lg:col-span-4">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Sort Order</label>
        <input type="number" min="0" name="sort_order" class="{{ $inputClass }}" value="{{ old('sort_order', 0) }}">
      </div>
      <div class="lg:col-span-6">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Starts At</label>
        <input type="datetime-local" name="starts_at" class="{{ $inputClass }}" value="{{ old('starts_at') }}">
      </div>
      <div class="lg:col-span-6">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Ends At</label>
        <input type="datetime-local" name="ends_at" class="{{ $inputClass }}" value="{{ old('ends_at') }}">
      </div>

      <div class="lg:col-span-6">
        <label class="mb-3 block text-sm font-medium text-gray-700 dark:text-gray-300">Platforms</label>
        <div class="grid gap-2 sm:grid-cols-3">
          @foreach($platforms as $platform)
            <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 dark:border-gray-800 dark:bg-gray-950/60 dark:text-gray-300">
              <input class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="platforms[]" value="{{ $platform }}" @checked(in_array($platform, old('platforms', []), true))>
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
              <input class="h-4 w-4 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="target_roles[]" value="{{ $role }}" @checked(in_array($role, old('target_roles', []), true))>
              <span>{{ ucfirst($role) }}</span>
            </label>
          @endforeach
        </div>
      </div>

      <div class="lg:col-span-12">
        <label class="mb-3 block text-sm font-medium text-gray-700 dark:text-gray-300">Active</label>
        <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 dark:border-gray-800 dark:bg-gray-950/60 dark:text-gray-300">
          <input type="hidden" name="is_active" value="0">
          <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
          <span>Banner is active and available for delivery</span>
        </label>
      </div>
    </div>

    <x-slot:footer>
      <div class="flex flex-wrap justify-end gap-3">
        <x-ui.button variant="outline" size="sm" href="{{ route('admin.banners.index') }}">Cancel</x-ui.button>
        <x-ui.button type="submit" size="sm">Save Banner</x-ui.button>
      </div>
    </x-slot:footer>
  </x-common.component-card>
</form>
@endsection
