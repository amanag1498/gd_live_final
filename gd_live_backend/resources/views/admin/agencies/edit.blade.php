@extends('layouts.admin-tailadmin')
@section('title','Edit Agency #'.$agency->id)

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $textareaClass = 'w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('content')
<form method="post" action="{{ route('admin.agencies.update',$agency) }}" class="space-y-6">
  @csrf
  @method('PUT')

  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <x-ui.badge color="dark">Agency</x-ui.badge>
            <x-ui.badge color="brand">Profile</x-ui.badge>
          </div>
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ $agency->name }}</h2>
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Update contact details and internal notes for this agency record.</p>
        </div>
      </div>
    </div>
  </section>

  <x-common.component-card>
    <x-slot:header>
      <div class="flex items-center justify-between gap-3">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Agency Profile</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Keep agency identity and contacts accurate for downstream reporting and operations.</p>
        </div>
        <x-ui.button variant="outline" size="sm" href="{{ route('admin.agencies.index') }}">Back</x-ui.button>
      </div>
    </x-slot:header>

    <div class="grid gap-4 lg:grid-cols-12">
      <div class="lg:col-span-6">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
        <input class="{{ $inputClass }}" name="name" value="{{ old('name',$agency->name) }}">
      </div>
      <div class="lg:col-span-6">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Email</label>
        <input type="email" class="{{ $inputClass }}" name="contact_email" value="{{ old('contact_email',$agency->contact_email) }}">
      </div>
      <div class="lg:col-span-6">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Contact Phone</label>
        <input class="{{ $inputClass }}" name="contact_phone" value="{{ old('contact_phone',$agency->contact_phone) }}">
      </div>
      <div class="lg:col-span-12">
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Notes</label>
        <textarea class="{{ $textareaClass }}" name="notes" rows="4">{{ old('notes',$agency->notes) }}</textarea>
      </div>
    </div>

    <x-slot:footer>
      <div class="flex flex-wrap justify-end gap-3">
        <x-ui.button variant="outline" size="sm" href="{{ route('admin.agencies.index') }}">Cancel</x-ui.button>
        <x-ui.button type="submit" size="sm">Save Changes</x-ui.button>
      </div>
    </x-slot:footer>
  </x-common.component-card>
</form>
@endsection
