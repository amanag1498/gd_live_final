@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

<x-common.component-card>
  <x-slot:header>
    <div class="flex items-center justify-between gap-3">
      <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $mode === 'create' ? 'Create Recharge Plan' : 'Edit Recharge Plan' }}</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Define price, base coins, bonus coins, and display order for this top-up pack.</p>
      </div>
      <x-ui.button variant="outline" size="sm" href="{{ route('admin.recharge-plans.index') }}">Back</x-ui.button>
    </div>
  </x-slot:header>

  <div class="grid gap-4 lg:grid-cols-12">
    <div class="lg:col-span-5">
      <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
      <input type="text" name="title" class="{{ $inputClass }}" value="{{ old('title', $plan->title) }}" required>
      @error('title')<div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div>@enderror
    </div>
    <div class="lg:col-span-3">
      <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Amount (₹)</label>
      <input type="number" step="0.01" min="1" name="amount_rupees" class="{{ $inputClass }}" value="{{ old('amount_rupees', $plan->amount_rupees) }}" required>
      @error('amount_rupees')<div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div>@enderror
    </div>
    <div class="lg:col-span-2">
      <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Sort Order</label>
      <input type="number" min="0" name="sort_order" class="{{ $inputClass }}" value="{{ old('sort_order', $plan->sort_order) }}" required>
      @error('sort_order')<div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div>@enderror
    </div>
    <div class="lg:col-span-2">
      <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Active</label>
      <label class="flex h-11 items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 dark:border-gray-800 dark:bg-gray-950/60">
        <input type="hidden" name="is_active" value="0">
        <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', $plan->is_active))>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Enabled</span>
      </label>
    </div>

    <div class="lg:col-span-4">
      <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Base Coins</label>
      <input type="number" min="1" name="coins" class="{{ $inputClass }}" value="{{ old('coins', $plan->coins) }}" required>
      @error('coins')<div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div>@enderror
    </div>
    <div class="lg:col-span-4">
      <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Bonus Coins</label>
      <input type="number" min="0" name="bonus_coins" class="{{ $inputClass }}" value="{{ old('bonus_coins', $plan->bonus_coins) }}">
      @error('bonus_coins')<div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div>@enderror
    </div>
    <div class="lg:col-span-4">
      <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Computed Total</label>
      <input type="text" class="{{ $inputClass }}" value="{{ number_format((int) ($plan->total_coins ?? (($plan->coins ?? 0) + ($plan->bonus_coins ?? 0)))) }}" disabled>
      <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Saved automatically as base coins plus bonus coins.</p>
    </div>
  </div>

  <x-slot:footer>
    <div class="flex flex-wrap justify-end gap-3">
      <x-ui.button variant="outline" size="sm" href="{{ route('admin.recharge-plans.index') }}">Cancel</x-ui.button>
      <x-ui.button type="submit" size="sm">{{ $mode === 'create' ? 'Create Plan' : 'Save Changes' }}</x-ui.button>
    </div>
  </x-slot:footer>
</x-common.component-card>
