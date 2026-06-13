@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $previewAsset = old('svg_url', $pack?->svg_url);
@endphp

<div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
  <x-common.component-card>
    <x-slot:header>
      <div class="flex items-start justify-between gap-3">
        <div>
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $pack ? 'Edit Entry Pack' : 'Create Entry Pack' }}</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Define pack pricing, animation timing, and asset behavior for premium room entry effects.</p>
        </div>
        <x-ui.button variant="outline" size="sm" href="{{ route('admin.entry-packs.index') }}">Back</x-ui.button>
      </div>
    </x-slot:header>

    @if($errors->any())
      <x-ui.alert variant="error" class="mb-5">
        <div class="font-medium">Please fix the following before saving:</div>
        <ul class="mt-2 list-disc pl-5">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </x-ui.alert>
    @endif

    <form method="post" action="{{ $route }}" enctype="multipart/form-data" class="space-y-5">
      @csrf
      @if($method !== 'POST') @method($method) @endif

      <div class="grid gap-4 lg:grid-cols-2">
        <div class="lg:col-span-2">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
          <input name="name" class="{{ $inputClass }}" value="{{ old('name', $pack?->name) }}" required maxlength="120">
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Price Coins</label>
          <input type="number" min="1" name="price_coins" class="{{ $inputClass }}" value="{{ old('price_coins', $pack?->price_coins ?? 150) }}" required>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Animation Style</label>
          <select name="animation_style" class="{{ $inputClass }}">
            @foreach(['banner','center','fullscreen'] as $style)
              <option value="{{ $style }}" @selected(old('animation_style', $pack?->animation_style ?? 'banner') === $style)>{{ ucfirst($style) }}</option>
            @endforeach
          </select>
        </div>

        <div class="lg:col-span-2">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $pack ? 'Replace Entry Asset' : 'Entry Asset' }}</label>
          <input type="file" name="asset_file" accept=".svg,.svga" class="{{ $inputClass }} py-2.5" {{ $pack ? '' : 'required' }}>
          @error('asset_file') <div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div> @enderror
          <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Upload SVG or SVGA. Leave empty during edits to keep the current asset.</p>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Priority</label>
          <input type="number" min="1" name="priority" class="{{ $inputClass }}" value="{{ old('priority', $pack?->priority ?? 1) }}">
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Duration (ms)</label>
          <input type="number" min="2000" step="100" name="duration_ms" class="{{ $inputClass }}" value="{{ old('duration_ms', $pack?->duration_ms ?? 3000) }}">
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Validity (days)</label>
          <input type="number" min="1" max="3650" name="duration_days" class="{{ $inputClass }}" value="{{ old('duration_days', $pack?->duration_days ?? 30) }}">
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Sort Order</label>
          <input type="number" min="0" name="sort_order" class="{{ $inputClass }}" value="{{ old('sort_order', $pack?->sort_order ?? 0) }}">
        </div>

        <div class="lg:col-span-2">
          <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 dark:border-gray-800 dark:bg-gray-950/60 dark:text-gray-300">
            <input type="hidden" name="is_active" value="0">
            <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $pack ? (int) $pack->is_active : 1) ? 'checked' : '' }}>
            <span>Entry pack is active and purchasable</span>
          </label>
        </div>
      </div>

      <div class="flex flex-wrap justify-end gap-3 border-t border-gray-100 pt-5 dark:border-gray-800">
        <x-ui.button variant="outline" size="sm" href="{{ route('admin.entry-packs.index') }}">Cancel</x-ui.button>
        <x-ui.button type="submit" size="sm">{{ $pack ? 'Update Pack' : 'Create Pack' }}</x-ui.button>
      </div>
    </form>
  </x-common.component-card>

  <x-common.component-card title="Preview" desc="Validate how the entry artwork will appear before publishing the pack.">
    <div class="flex min-h-[320px] items-center justify-center overflow-hidden rounded-2xl border border-gray-200 bg-linear-to-br from-gray-50 to-brand-50/60 p-6 dark:border-gray-800 dark:from-gray-950 dark:to-brand-500/10">
      @if($previewAsset)
        @if(str_ends_with(strtolower($previewAsset), '.svga'))
          <div class="text-center text-sm text-gray-500 dark:text-gray-400">
            <div class="mb-3 text-4xl text-brand-500"><i class="ti ti-player-play"></i></div>
            SVGA asset uploaded. Preview is available in the app runtime.
          </div>
        @else
          <object data="{{ $previewAsset }}" type="image/svg+xml" class="h-[260px] w-full rounded-2xl bg-white dark:bg-gray-900"></object>
        @endif
      @else
        <div class="text-center text-sm text-gray-500 dark:text-gray-400">
          <div class="mb-3 text-4xl text-brand-500"><i class="ti ti-photo-search"></i></div>
          Upload an SVG or SVGA file to preview the entry artwork.
        </div>
      @endif
    </div>
  </x-common.component-card>
</div>
