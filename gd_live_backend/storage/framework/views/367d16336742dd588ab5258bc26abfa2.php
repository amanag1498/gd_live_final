<?php $__env->startSection('title','Edit Gift'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-3">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header">
        <h6 class="mb-0"><i class="ti ti-edit me-2"></i>Edit Gift</h6>
      </div>
      <div class="card-body">

        
        <?php if($errors->any()): ?>
          <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Please fix the following:</div>
            <ul class="mb-0 ps-3">
              <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($e); ?></li>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
          </div>
        <?php endif; ?>

        <form method="post" action="<?php echo e(route('admin.gifts.update',$gift)); ?>" class="vstack gap-3" enctype="multipart/form-data">
          <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

          <div>
            <label class="form-label">Name</label>
            <input
              name="name"
              class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
              required
              maxlength="120"
              value="<?php echo e(old('name', $gift->name)); ?>"
            >
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div>
            <label class="form-label">Coins</label>
            <input
              type="number"
              name="coins"
              min="1"
              class="form-control <?php $__errorArgs = ['coins'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
              required
              value="<?php echo e(old('coins', $gift->coins)); ?>"
            >
            <?php $__errorArgs = ['coins'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>

          <div>
            <label class="form-label">Replace Gift Asset</label>
            <input
              type="file"
              name="gift_file"
              id="gift_file"
              accept=".svg,.svga,.gif,.png,.jpg,.jpeg,.webp"
              class="form-control <?php $__errorArgs = ['gift_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
            >
            <?php $__errorArgs = ['gift_file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <small class="text-muted d-block mt-1">
              Leave empty to keep the current file. Supported formats: SVG, SVGA, GIF, PNG, JPG, JPEG, WEBP.
            </small>
          </div>

          <div class="row g-2">
            <div class="col-md-4">
              <label class="form-label">Gift Type</label>
              <select name="gift_type" class="form-select <?php $__errorArgs = ['gift_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <option value="">Auto detect</option>
                <?php $__currentLoopData = $giftTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $giftType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($giftType); ?>" <?php if(old('gift_type', $gift->gift_type) === $giftType): echo 'selected'; endif; ?>><?php echo e(strtoupper($giftType)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php $__errorArgs = ['gift_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-4">
              <label class="form-label">Animation Tier</label>
              <select name="animation_tier" class="form-select <?php $__errorArgs = ['animation_tier'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                <option value="">Auto by coins</option>
                <?php $__currentLoopData = $animationTiers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $animationTier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($animationTier); ?>" <?php if(old('animation_tier', $gift->animation_tier) === $animationTier): echo 'selected'; endif; ?>><?php echo e(ucfirst($animationTier)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
              <?php $__errorArgs = ['animation_tier'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-md-4">
              <label class="form-label">Duration (ms)</label>
              <input
                type="number"
                name="animation_duration_ms"
                min="800"
                max="12000"
                step="100"
                class="form-control <?php $__errorArgs = ['animation_duration_ms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                value="<?php echo e(old('animation_duration_ms', $gift->animation_duration_ms)); ?>"
                placeholder="optional"
              >
              <?php $__errorArgs = ['animation_duration_ms'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
          </div>

          <div class="row g-2">
            <div class="col-6">
              <label class="form-label">Sort Order</label>
              <input
                type="number"
                name="sort_order"
                min="0"
                class="form-control <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                value="<?php echo e(old('sort_order', $gift->sort_order)); ?>"
              >
              <?php $__errorArgs = ['sort_order'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-6 d-flex align-items-end">
              <div class="form-check">
                
                <input type="hidden" name="is_active" value="0">
                <input
                  class="form-check-input"
                  type="checkbox"
                  id="is_active"
                  name="is_active"
                  value="1"
                  <?php echo e(old('is_active', (int)$gift->is_active) ? 'checked' : ''); ?>

                >
                <label for="is_active" class="form-check-label">Active</label>
              </div>
            </div>
          </div>

          <div class="d-flex gap-2">
            <button class="btn btn-primary">
              <i class="ti ti-check me-1"></i>Update
            </button>
            <a class="btn btn-light border" href="<?php echo e(route('admin.gifts.index')); ?>">
              Back
            </a>
          </div>
        </form>

      </div>
    </div>
  </div>

  <?php if($gift->gift_url): ?>
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header">
        <h6 class="mb-0"><i class="ti ti-eye me-2"></i>Preview</h6>
      </div>
      <div class="card-body">
        <?php
          $giftUrl = $gift->gift_url;
          $giftType = strtolower((string) ($gift->gift_type ?? ''));
          $looksSvga = $giftType === 'svga' || str_ends_with(strtolower((string) $giftUrl), '.svga');
          $looksSvg = $giftType === 'svg' || str_ends_with(strtolower((string) $giftUrl), '.svg');
        ?>
        <div id="gift-preview-shell"
             class="rounded-4 border d-flex align-items-center justify-content-center overflow-hidden"
             style="min-height: 320px; background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 100%); border-color: rgba(148, 163, 184, 0.24) !important;">
          <div id="gift-preview-empty" class="d-none text-center text-muted px-4">
            <div class="mb-2"><i class="ti ti-photo-search" style="font-size: 2rem;"></i></div>
            <div class="fw-semibold">No preview available</div>
          </div>
          <img id="gift-preview-image"
               alt="gift preview"
               class="<?php echo e($looksSvga || $looksSvg ? 'd-none' : ''); ?>"
               src="<?php echo e($looksSvga || $looksSvg ? '' : $giftUrl); ?>"
               style="max-width: 100%; max-height: 280px; object-fit: contain;">
          <object id="gift-preview-svg"
                  type="image/svg+xml"
                  data="<?php echo e($looksSvg ? $giftUrl : ''); ?>"
                  class="<?php echo e($looksSvg ? '' : 'd-none'); ?>"
                  style="width: 100%; height: 280px;"></object>
          <canvas id="gift-preview-svga"
                  data-src="<?php echo e($looksSvga ? $giftUrl : ''); ?>"
                  class="<?php echo e($looksSvga ? '' : 'd-none'); ?>"
                  style="width: 100%; height: 280px;"></canvas>
        </div>
        <div id="gift-preview-meta" class="small text-muted mt-3">
          Current asset: <?php echo e(basename(parse_url((string) $giftUrl, PHP_URL_PATH) ?: (string) $giftUrl)); ?>

          <?php if($gift->gift_type): ?>
            • <?php echo e(strtoupper($gift->gift_type)); ?>

          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php if (! $__env->hasRenderedOnce('5ffcb008-7987-4ed6-ae2c-8c8a12c0ddd8')): $__env->markAsRenderedOnce('5ffcb008-7987-4ed6-ae2c-8c8a12c0ddd8'); ?>
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

    const clearSvga = () => {
      if (activeSvgaPlayer && typeof activeSvgaPlayer.clear === 'function') {
        try { activeSvgaPlayer.clear(); } catch (_) {}
      }
      activeSvgaPlayer = null;
    };

    const hideAll = () => {
      image.classList.add('d-none');
      svg.classList.add('d-none');
      canvas.classList.add('d-none');
      empty.classList.add('d-none');
    };

    const showImage = (url, text) => {
      clearSvga();
      hideAll();
      image.src = url;
      image.classList.remove('d-none');
      meta.textContent = text;
    };

    const showSvg = (url, text) => {
      clearSvga();
      hideAll();
      svg.data = url;
      svg.classList.remove('d-none');
      meta.textContent = text;
    };

    const showSvga = (url, text, shouldRelease = true) => {
      if (shouldRelease) {
        releaseObjectUrl();
      }
      clearSvga();
      hideAll();
      canvas.classList.remove('d-none');
      meta.textContent = text;

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
          hideAll();
          empty.classList.remove('d-none');
        }
      );
    };

    const initialSvgaUrl = canvas.dataset.src;
    if (initialSvgaUrl) {
      showSvga(initialSvgaUrl, meta.textContent.trim(), false);
    }

    fileInput?.addEventListener('change', (event) => {
      const file = event.target.files?.[0];
      if (!file) {
        return;
      }

      clearSvga();
      releaseObjectUrl();

      const extension = (file.name.split('.').pop() || '').toLowerCase();
      activeObjectUrl = URL.createObjectURL(file);
      const sizeLabel = `${(file.size / 1024).toFixed(1)} KB`;
      const metaText = `Replacement preview: ${file.name} • ${sizeLabel} • ${extension.toUpperCase() || 'FILE'}`;

      if (extension === 'svga') {
        showSvga(activeObjectUrl, metaText, false);
      } else if (extension === 'svg') {
        showSvg(activeObjectUrl, metaText);
      } else {
        showImage(activeObjectUrl, metaText);
      }
    });
  })();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/gifts/edit.blade.php ENDPATH**/ ?>