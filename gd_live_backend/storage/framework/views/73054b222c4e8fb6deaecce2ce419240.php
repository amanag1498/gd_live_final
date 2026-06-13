<?php $__env->startSection('title','Notifications · Compose'); ?>

<?php
  $aud = old('audience', request('audience','user'));
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
  $textareaClass = 'w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
?>

<?php $__env->startSection('page_actions'); ?>
  <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.notifications.index')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.notifications.index')).'']); ?>Back to Recent <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
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
      <div>
        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Compose Notification</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Send stored inbox notifications, push notifications, or both to a single user, a role, or everyone.</p>
      </div>
     <?php $__env->endSlot(); ?>

    <form method="post" action="<?php echo e(route('admin.notifications.send')); ?>" class="space-y-6">
      <?php echo csrf_field(); ?>

      <div class="grid gap-4 xl:grid-cols-4">
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Audience</label>
          <select name="audience" class="<?php echo e($inputClass); ?>" id="audienceSel" data-initial="<?php echo e($aud); ?>">
            <option value="user" <?php echo e($aud === 'user' ? 'selected' : ''); ?>>Single user</option>
            <option value="role" <?php echo e($aud === 'role' ? 'selected' : ''); ?>>Role</option>
            <option value="all"  <?php echo e($aud === 'all'  ? 'selected' : ''); ?>>All users</option>
          </select>
        </div>

        <div class="audience-user <?php echo e($aud === 'user' ? '' : 'hidden'); ?>">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">User ID</label>
          <input type="number" class="<?php echo e($inputClass); ?>" name="user_id" placeholder="e.g. 15" value="<?php echo e(old('user_id', request('user_id'))); ?>">
        </div>

        <div class="audience-role <?php echo e($aud === 'role' ? '' : 'hidden'); ?>">
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Role</label>
          <input type="text" class="<?php echo e($inputClass); ?>" name="role" placeholder="e.g. host" value="<?php echo e(old('role', request('role'))); ?>">
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Type</label>
          <input type="text" class="<?php echo e($inputClass); ?>" name="type" placeholder="host_approved" value="<?php echo e(old('type', request('type'))); ?>">
        </div>
      </div>

      <div class="grid gap-4">
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
          <input type="text" class="<?php echo e($inputClass); ?>" name="title" required value="<?php echo e(old('title', request('title'))); ?>">
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Body</label>
          <textarea class="<?php echo e($textareaClass); ?>" name="body" rows="4"><?php echo e(old('body', request('body'))); ?></textarea>
        </div>
      </div>

      <div class="grid gap-4 xl:grid-cols-3">
        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Deep-link screen</label>
          <input type="text" class="<?php echo e($inputClass); ?>" name="screen" placeholder="notifications | room" value="<?php echo e(old('screen', request('screen'))); ?>">
          <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Use `notifications` to open inbox or `room` to deep-link to a room.</p>
        </div>

        <div>
          <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Room ID</label>
          <input type="text" class="<?php echo e($inputClass); ?>" name="room_id" placeholder="abc123" value="<?php echo e(old('room_id', request('room_id'))); ?>">
        </div>

        <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950/60">
          <div class="mb-3 text-sm font-medium text-gray-700 dark:text-gray-300">Delivery Options</div>
          <input type="hidden" name="persist" value="0">
          <label class="mb-3 flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
            <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="persist" value="1" <?php echo e(old('persist', request('persist','1')) == '1' ? 'checked' : ''); ?>>
            <span>Store in user notifications (DB)</span>
          </label>

          <input type="hidden" name="push" value="0">
          <label class="flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
            <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="push" value="1" <?php echo e(old('push', request('push','1')) == '1' ? 'checked' : ''); ?>>
            <span>Send Firebase push</span>
          </label>
        </div>
      </div>

      <div>
        <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Meta (JSON, optional)</label>
        <textarea class="<?php echo e($textareaClass); ?>" name="meta" rows="4" placeholder='{"foo":"bar"}'><?php echo e(old('meta', request('meta'))); ?></textarea>
      </div>

      <div class="flex flex-wrap justify-end gap-3">
        <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'outline','href' => ''.e(route('admin.notifications.index')).'','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','href' => ''.e(route('admin.notifications.index')).'','size' => 'sm']); ?>Cancel <?php echo $__env->renderComponent(); ?>
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
<?php $component->withAttributes(['type' => 'submit','size' => 'sm']); ?>Send <?php echo $__env->renderComponent(); ?>
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
    </form>
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
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
  (function () {
    const sel = document.getElementById('audienceSel');
    const user = document.querySelector('.audience-user');
    const role = document.querySelector('.audience-role');
    if (!sel || !user || !role) return;

    function sync() {
      user.classList.toggle('hidden', sel.value !== 'user');
      role.classList.toggle('hidden', sel.value !== 'role');
    }

    sel.value = sel.dataset.initial || sel.value || 'user';
    sync();
    sel.addEventListener('change', sync);
  })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/notifications/compose.blade.php ENDPATH**/ ?>