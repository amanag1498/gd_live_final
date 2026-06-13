@extends('layouts.admin-tailadmin')
@section('title', 'New Subscription Plan')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $textareaClass = 'w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('content')
<div class="space-y-6">
  <x-common.component-card>
    <x-slot:header>
      <div class="flex items-start justify-between gap-3">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Create Subscription Plan</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Define pricing, duration, and structured perks for the premium subscription catalog.</p>
        </div>
        <x-ui.button variant="outline" size="sm" href="{{ route('admin.subscription-plans.index') }}">Back</x-ui.button>
      </div>
    </x-slot:header>

    @if($errors->any())
      <x-ui.alert variant="error" class="mb-5">
        <div class="font-medium">Please fix the following before saving:</div>
        <ul class="mt-2 list-disc pl-5">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </x-ui.alert>
    @endif

    <form method="post" action="{{ route('admin.subscription-plans.store') }}" class="space-y-5">
      @csrf

      <div class="grid gap-4 lg:grid-cols-2">
        <div class="lg:col-span-2">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
          <input name="name" class="{{ $inputClass }}" value="{{ old('name') }}" required>
        </div>
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Price (coins)</label>
          <input type="number" name="price_coins" class="{{ $inputClass }}" min="1" value="{{ old('price_coins') }}" required>
        </div>
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Duration (days)</label>
          <input type="number" name="duration_days" class="{{ $inputClass }}" min="1" value="{{ old('duration_days') }}" required>
        </div>
        <div class="lg:col-span-2">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Perks (JSON)</label>
          <textarea name="perks" class="{{ $textareaClass }}" rows="6" placeholder='{"badge":"Pro","limits":{"daily":5}}'>{{ old('perks') }}</textarea>
          <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Optional JSON payload. Example: <code>{"badge":"Pro","limits":{"daily":5}}</code></p>
        </div>
        <div class="lg:col-span-2">
          <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 dark:border-gray-800 dark:bg-gray-950/60 dark:text-gray-300">
            <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="is_active" value="1" checked>
            <span>Plan is active and available for purchase</span>
          </label>
        </div>
      </div>

      <div class="flex justify-end gap-3 border-t border-gray-100 pt-5 dark:border-gray-800">
        <x-ui.button variant="outline" size="sm" href="{{ route('admin.subscription-plans.index') }}">Cancel</x-ui.button>
        <x-ui.button type="submit" size="sm">Create Plan</x-ui.button>
      </div>
    </form>
  </x-common.component-card>
</div>
@endsection
