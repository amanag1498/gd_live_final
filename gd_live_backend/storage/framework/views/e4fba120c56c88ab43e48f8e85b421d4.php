<?php $__env->startSection('title', 'New Gift'); ?>

<?php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <form method="post" action="<?php echo e(route('admin.gifts.store')); ?>" enctype="multipart/form-data" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
    <?php echo csrf_field(); ?>

    <?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
       <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-start justify-between gap-3">
          <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Create Gift</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configure the gift economy value, upload the animation asset, and keep the preview consistent with the in-app experience.</p>
          </div>
          <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.gifts.index')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.gifts.index')).'']); ?>Back <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
        </div>
       <?php $__env->endSlot(); ?>

      <?php if($errors->any()): ?>
        <?php if (isset($component)) { $__componentOriginal746de018ded8594083eb43be3f1332e1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal746de018ded8594083eb43be3f1332e1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.alert','data' => ['variant' => 'error','class' => 'mb-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'error','class' => 'mb-5']); ?>
          <div class="font-medium">Please fix the following before saving:</div>
          <ul class="mt-2 list-disc pl-5">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><?php echo e($e); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </ul>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $attributes = $__attributesOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__attributesOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal746de018ded8594083eb43be3f1332e1)): ?>
<?php $component = $__componentOriginal746de018ded8594083eb43be3f1332e1; ?>
<?php unset($__componentOriginal746de018ded8594083eb43be3f1332e1); ?>
<?php endif; ?>
      <?php endif; ?>

      <div class="grid gap-4 lg:grid-cols-2">
        <div class="lg:col-span-2">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
          <input name="name" class="<?php echo e($inputClass); ?>" value="<?php echo e(old('name')); ?>" required maxlength="120">
          <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mt-2 text-sm text-error-600 dark:text-error-300"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Coins</label>
          <input type="number" name="coins" min="1" class="<?php echo e($inputClass); ?>" value="<?php echo e(old('coins')); ?>" required>
          <?php $__errorArgs = ['coins'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mt-2 text-sm text-error-600 dark:text-error-300"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Sort Order</label>
          <input type="number" name="sort_order" min="0" class="<?php echo e($inputClass); ?>" value="<?php echo e(old('sort_order', 0)); ?>">
          <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mt-2 text-sm text-error-600 dark:text-error-300"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Gift Type</label>
          <select name="gift_type" class="<?php echo e($inputClass); ?>">
            <option value="">Auto detect</option>
            <?php $__currentLoopData = $giftTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $giftType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($giftType); ?>" <?php if(old('gift_type') === $giftType): echo 'selected'; endif; ?>><?php echo e(strtoupper($giftType)); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <?php $__errorArgs = ['gift_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mt-2 text-sm text-error-600 dark:text-error-300"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Animation Tier</label>
          <select name="animation_tier" class="<?php echo e($inputClass); ?>">
            <option value="">Auto by coins</option>
            <?php $__currentLoopData = $animationTiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $animationTier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($animationTier); ?>" <?php if(old('animation_tier') === $animationTier): echo 'selected'; endif; ?>><?php echo e(ucfirst($animationTier)); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <?php $__errorArgs = ['animation_tier'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mt-2 text-sm text-error-600 dark:text-error-300"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Duration (ms)</label>
          <input type="number" name="animation_duration_ms" min="800" max="12000" step="100" class="<?php echo e($inputClass); ?>" value="<?php echo e(old('animation_duration_ms')); ?>" placeholder="optional">
          <?php $__errorArgs = ['animation_duration_ms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mt-2 text-sm text-error-600 dark:text-error-300"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="lg:col-span-2">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Gift Asset</label>
          <input type="file" name="gift_file" id="gift_file" accept=".svg,.svga,.gif,.png,.jpg,.jpeg,.webp" class="<?php echo e($inputClass); ?> py-2.5" required>
          <?php $__errorArgs = ['gift_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="mt-2 text-sm text-error-600 dark:text-error-300"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Supported: SVG, SVGA, GIF, PNG, JPG, JPEG, WEBP. Max file size follows your PHP and nginx upload limits.</p>
        </div>

        <div class="lg:col-span-2">
          <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm font-medium text-gray-700 dark:border-gray-800 dark:bg-gray-950/60 dark:text-gray-300">
            <input type="hidden" name="is_active" value="0">
            <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" id="is_active" name="is_active" value="1" <?php echo e(old('is_active', 1) ? 'checked' : ''); ?>>
            <span>Gift is active and available in the catalog</span>
          </label>
        </div>
      </div>

       <?php $__env->slot('footer', null, []); ?> 
        <div class="flex flex-wrap justify-end gap-3">
          <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.gifts.index')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.gifts.index')).'']); ?>Cancel <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
          <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm']); ?>Save Gift <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
        </div>
       <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb8dfe58016103e374219da4cf072c7cf)): ?>
<?php $attributes = $__attributesOriginalb8dfe58016103e374219da4cf072c7cf; ?>
<?php unset($__attributesOriginalb8dfe58016103e374219da4cf072c7cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb8dfe58016103e374219da4cf072c7cf)): ?>
<?php $component = $__componentOriginalb8dfe58016103e374219da4cf072c7cf; ?>
<?php unset($__componentOriginalb8dfe58016103e374219da4cf072c7cf); ?>
<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Preview','desc' => 'Verify how the uploaded asset will frame in the catalog before saving.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Preview','desc' => 'Verify how the uploaded asset will frame in the catalog before saving.']); ?>
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
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb8dfe58016103e374219da4cf072c7cf)): ?>
<?php $attributes = $__attributesOriginalb8dfe58016103e374219da4cf072c7cf; ?>
<?php unset($__attributesOriginalb8dfe58016103e374219da4cf072c7cf); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb8dfe58016103e374219da4cf072c7cf)): ?>
<?php $component = $__componentOriginalb8dfe58016103e374219da4cf072c7cf; ?>
<?php unset($__componentOriginalb8dfe58016103e374219da4cf072c7cf); ?>
<?php endif; ?>
  </form>
</div>

<?php if (! $__env->hasRenderedOnce('ef351ba9-e52f-4446-a5f5-176305fbc469')): $__env->markAsRenderedOnce('ef351ba9-e52f-4446-a5f5-176305fbc469'); ?>
  <script src="https://cdn.jsdelivr.net/npm/svgaplayerweb@2.3.0/build/svga.min.js"></script>
<?php endif; ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/gifts/create.blade.php ENDPATH**/ ?>