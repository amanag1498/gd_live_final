@extends('layouts.admin-tailadmin')
@section('title', 'New Gift')

@php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
@endphp

@section('content')
<div class="space-y-6">
  <form method="post" action="{{ route('admin.gifts.store') }}" enctype="multipart/form-data" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
    @csrf

    <x-common.component-card>
      <x-slot:header>
        <div class="flex items-start justify-between gap-3">
          <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Create Gift</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configure the gift economy value, upload the animation asset, and keep the preview consistent with the in-app experience.</p>
          </div>
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.gifts.index') }}">Back</x-ui.button>
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

      <div class="grid gap-4 lg:grid-cols-2">
        <div class="lg:col-span-2">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
          <input name="name" class="{{ $inputClass }}" value="{{ old('name') }}" required maxlength="120">
          @error('name') <div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Coins</label>
          <input type="number" name="coins" min="1" class="{{ $inputClass }}" value="{{ old('coins') }}" required>
          @error('coins') <div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Sort Order</label>
          <input type="number" name="sort_order" min="0" class="{{ $inputClass }}" value="{{ old('sort_order', 0) }}">
          @error('sort_order') <div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Gift Type</label>
          <select name="gift_type" class="{{ $inputClass }}">
            <option value="">Auto detect</option>
            @foreach($giftTypes as $giftType)
              <option value="{{ $giftType }}" @selected(old('gift_type') === $giftType)>{{ strtoupper($giftType) }}</option>
            @endforeach
          </select>
          @error('gift_type') <div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Animation Tier</label>
          <select name="animation_tier" class="{{ $inputClass }}">
            <option value="">Auto by coins</option>
            @foreach($animationTiers as $animationTier)
              <option value="{{ $animationTier }}" @selected(old('animation_tier') === $animationTier)>{{ ucfirst($animationTier) }}</option>
            @endforeach
          </select>
          @error('animation_tier') <div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div> @enderror
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Duration (ms)</label>
          <input type="number" name="animation_duration_ms" min="800" max="12000" step="100" class="{{ $inputClass }}" value="{{ old('animation_duration_ms') }}" placeholder="optional">
          @error('animation_duration_ms') <div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div> @enderror
        </div>

        <div class="lg:col-span-2">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Gift Asset</label>
          <input type="file" name="gift_file" id="gift_file" accept=".svg,.svga,.gif,.png,.jpg,.jpeg,.webp" class="{{ $inputClass }} py-2.5" required>
          @error('gift_file') <div class="mt-2 text-sm text-error-600 dark:text-error-300">{{ $message }}</div> @enderror
          <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Supported: SVG, SVGA, GIF, PNG, JPG, JPEG, WEBP. Max file size follows your PHP and nginx upload limits.</p>
        </div>

        <div class="lg:col-span-2">
          <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 dark:border-gray-800 dark:bg-gray-950/60 dark:text-gray-300">
            <input type="hidden" name="is_active" value="0">
            <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
            <span>Gift is active and available in the catalog</span>
          </label>
        </div>
      </div>

      <x-slot:footer>
        <div class="flex flex-wrap justify-end gap-3">
          <x-ui.button variant="outline" size="sm" href="{{ route('admin.gifts.index') }}">Cancel</x-ui.button>
          <x-ui.button type="submit" size="sm">Save Gift</x-ui.button>
        </div>
      </x-slot:footer>
    </x-common.component-card>

    <x-common.component-card title="Preview" desc="Verify how the uploaded asset will frame in the catalog before saving.">
      <div id="gift-preview-shell" class="flex min-h-[320px] items-center justify-center overflow-hidden rounded-2xl border border-gray-200 bg-linear-to-br from-gray-50 to-brand-50/60 p-6 dark:border-gray-800 dark:from-gray-950 dark:to-brand-500/10">
        <div id="gift-preview-empty" class="text-center text-gray-500 dark:text-gray-400">
          <div class="mb-3 text-4xl text-brand-500"><i class="ti ti-photo-search"></i></div>
          <div class="font-semibold text-gray-900 dark:text-white">Upload a gift asset to preview it</div>
          <div class="mt-1 text-sm">SVG, SVGA, GIF, PNG, JPG, JPEG, and WEBP are supported.</div>
        </div>
        <img id="gift-preview-image" alt="gift preview" class="hidden max-h-[280px] max-w-full object-contain">
        <object id="gift-preview-svg" type="image/svg+xml" class="hidden h-[280px] w-full"></object>
        <canvas id="gift-preview-svga" class="hidden h-[280px] w-full"></canvas>
      </div>
      <div id="gift-preview-meta" class="mt-4 hidden text-sm text-gray-500 dark:text-gray-400"></div>
    </x-common.component-card>
  </form>
</div>

@once
  <script src="https://cdn.jsdelivr.net/npm/svgaplayerweb@2.3.0/build/svga.min.js"></script>
@endonce
<script>
  (() => {
    const fileInput = document.getElementById('gift_file');
    const empty = document.getElementById('gift-preview-empty');
    const image = document.getElementById('gift-preview-image');
    const svg = document.getElementById('gift-preview-svg');
    const canvas = document.getElementById('gift-preview-svga');
    const meta = document.getElementById('gift-preview-meta');
    let activeObjectUrl = null;
    let activeSvgaPlayer = null;

    const releaseObjectUrl = () => {
      if (activeObjectUrl) {
        URL.revokeObjectURL(activeObjectUrl);
        activeObjectUrl = null;
      }
    };

    const reset = () => {
      if (activeSvgaPlayer && typeof activeSvgaPlayer.clear === 'function') {
        try { activeSvgaPlayer.clear(); } catch (_) {}
      }
      activeSvgaPlayer = null;
      empty.classList.remove('hidden');
      image.classList.add('hidden');
      svg.classList.add('hidden');
      canvas.classList.add('hidden');
      image.removeAttribute('src');
      svg.removeAttribute('data');
      meta.classList.add('hidden');
      meta.textContent = '';
    };

    const showMeta = (text) => {
      meta.textContent = text;
      meta.classList.remove('hidden');
    };

    const showImage = (url, text) => {
      reset();
      empty.classList.add('hidden');
      image.src = url;
      image.classList.remove('hidden');
      showMeta(text);
    };

    const showSvg = (url, text) => {
      reset();
      empty.classList.add('hidden');
      svg.data = url;
      svg.classList.remove('hidden');
      showMeta(text);
    };

    const showSvga = (url, text) => {
      reset();
      empty.classList.add('hidden');
      canvas.classList.remove('hidden');
      showMeta(text);

      if (!window.SVGA || !window.SVGA.Player || !window.SVGA.Parser) {
        meta.textContent = 'SVGA preview library did not load.';
        return;
      }

      activeSvgaPlayer = new window.SVGA.Player(canvas);
      const parser = new window.SVGA.Parser(canvas);
      parser.load(
        url,
        (videoItem) => {
          activeSvgaPlayer.setVideoItem(videoItem);
          activeSvgaPlayer.startAnimation();
        },
        () => {
          meta.textContent = 'Unable to load this SVGA preview.';
        }
      );
    };

    fileInput?.addEventListener('change', (event) => {
      const file = event.target.files?.[0];
      if (!file) {
        releaseObjectUrl();
        reset();
        return;
      }

      releaseObjectUrl();
      const url = URL.createObjectURL(file);
      activeObjectUrl = url;
      const ext = file.name.split('.').pop()?.toLowerCase() ?? '';
      const details = `${file.name} · ${(file.size / 1024).toFixed(1)} KB`;

      if (ext === 'svg') {
        showSvg(url, details);
        return;
      }

      if (ext === 'svga') {
        showSvga(url, details);
        return;
      }

      showImage(url, details);
    });
  })();
</script>
@endsection
