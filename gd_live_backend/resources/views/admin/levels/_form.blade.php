@php
  $benefitsValue = old('benefits');
  if ($benefitsValue === null) {
      $benefitsValue = collect($level->benefits ?? [])->implode("\n");
  }

  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $textareaClass = 'w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

<x-common.component-card>
  <x-slot:header>
    <div class="flex items-center justify-between gap-3">
      <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $mode === 'create' ? 'Create Level' : 'Edit Level' }}</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Define thresholds, badge visuals, and user-facing benefits for this level.</p>
      </div>
      <x-ui.button variant="outline" size="sm" href="{{ route('admin.levels.index') }}">Back</x-ui.button>
    </div>
  </x-slot:header>

  <div class="grid gap-4 lg:grid-cols-12">
    <div class="lg:col-span-3">
      <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Level Number</label>
      <input type="number" min="1" name="level" class="{{ $inputClass }}" value="{{ old('level', $level->level) }}" required>
      @error('level')<div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div>@enderror
    </div>

    <div class="lg:col-span-5">
      <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
      <input type="text" name="title" class="{{ $inputClass }}" value="{{ old('title', $level->title) }}" required>
      @error('title')<div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div>@enderror
    </div>

    <div class="lg:col-span-4">
      <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Minimum Spend Coins</label>
      <input type="number" min="0" name="min_spend_coins" class="{{ $inputClass }}" value="{{ old('min_spend_coins', $level->min_spend_coins) }}" required>
      @error('min_spend_coins')<div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div>@enderror
    </div>

    <div class="lg:col-span-4">
      <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Badge Icon</label>
      <input type="text" name="badge_icon" class="{{ $inputClass }}" value="{{ old('badge_icon', $level->badge_icon) }}" placeholder="trending_up">
      @error('badge_icon')<div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div>@enderror
    </div>

    <div class="lg:col-span-4">
      <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Badge Color</label>
      <input type="text" name="badge_color" class="{{ $inputClass }}" value="{{ old('badge_color', $level->badge_color) }}" placeholder="#4BE3C2">
      @error('badge_color')<div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div>@enderror
    </div>

    <div class="lg:col-span-2">
      <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Sort Order</label>
      <input type="number" min="0" name="sort_order" class="{{ $inputClass }}" value="{{ old('sort_order', $level->sort_order) }}" required>
      @error('sort_order')<div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div>@enderror
    </div>

    <div class="lg:col-span-2">
      <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Active</label>
      <div class="flex h-11 items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 dark:border-gray-800 dark:bg-gray-950/60">
        <input type="hidden" name="is_active" value="0">
        <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="is_active" value="1" @checked((bool) old('is_active', $level->is_active))>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Enabled</span>
      </div>
    </div>

    <div class="lg:col-span-12">
      <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Benefits</label>
      <textarea name="benefits" rows="8" class="{{ $textareaClass }}" placeholder="One benefit per line or JSON array">{{ $benefitsValue }}</textarea>
      @error('benefits')<div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div>@enderror
      <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Accepted format: one benefit per line, or a JSON array of strings.</p>
    </div>
  </div>

  <x-slot:footer>
    <div class="flex flex-wrap justify-end gap-3">
      <x-ui.button variant="outline" size="sm" href="{{ route('admin.levels.index') }}">Cancel</x-ui.button>
      <x-ui.button type="submit" size="sm">{{ $mode === 'create' ? 'Create Level' : 'Save Changes' }}</x-ui.button>
    </div>
  </x-slot:footer>
</x-common.component-card>
