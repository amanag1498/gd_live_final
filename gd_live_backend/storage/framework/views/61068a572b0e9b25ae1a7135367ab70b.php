<?php $__env->startSection('title', 'Host Followers'); ?>

<?php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
?>

<?php $__env->startSection('page_actions'); ?>
  <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.reports.follow-notifications')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.reports.follow-notifications')).'']); ?>Online Alerts <?php echo $__env->renderComponent(); ?>
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
      <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div class="max-w-2xl">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Host Followers</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Review follower relationships, online alert preferences, and the hosts drawing the strongest follow graph.</p>
        </div>
        <form method="get" class="flex gap-3">
          <select name="host_id" class="<?php echo e($inputClass); ?>">
            <option value="">All hosts</option>
            <?php $__currentLoopData = $hosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $host): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($host->id); ?>" <?php if((string) $hostId === (string) $host->id): echo 'selected'; endif; ?>><?php echo e($host->stage_name ?: $host->user?->name ?: 'Host #'.$host->id); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm']); ?>Filter <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
        </form>
      </div>
     <?php $__env->endSlot(); ?>

    <div class="grid gap-6 xl:grid-cols-[340px_minmax(0,1fr)]">
      <div class="space-y-3">
        <?php $__empty_1 = true; $__currentLoopData = $topHosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $host): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between gap-3">
              <div>
                <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($host->stage_name ?: $host->user?->name ?: 'Host #'.$host->id); ?></div>
                <div class="mt-1 text-xs text-gray-500 dark:text-gray-400"><?php echo e($host->user?->email); ?></div>
              </div>
              <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['color' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'dark']); ?><?php echo e($host->followers_count); ?> followers <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
            </div>
          </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <div class="rounded-2xl border border-dashed border-gray-300 px-4 py-10 text-center text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">No followers yet.</div>
        <?php endif; ?>
      </div>

      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60">
            <tr>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Host</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Follower</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Online Alerts</th>
              <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Followed</th>
              <th class="px-4 py-3 text-right font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr class="bg-white dark:bg-gray-900">
                <td class="px-4 py-4">
                  <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($row->host?->stage_name ?: $row->host?->user?->name ?: 'Host #'.$row->host_id); ?></div>
                  <div class="mt-1 text-xs text-gray-500 dark:text-gray-400"><?php echo e($row->host?->user?->email); ?></div>
                </td>
                <td class="px-4 py-4">
                  <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($row->user?->name ?: 'User #'.$row->user_id); ?></div>
                  <div class="mt-1 text-xs text-gray-500 dark:text-gray-400"><?php echo e($row->user?->email); ?></div>
                </td>
                <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
                  <div>Online: <?php echo e($row->notify_when_online ? 'Yes' : 'No'); ?></div>
                </td>
                <td class="px-4 py-4 text-gray-600 dark:text-gray-300"><?php echo e(optional($row->created_at)->format('d M Y, H:i')); ?></td>
                <td class="px-4 py-4 text-right">
                  <form method="post" action="<?php echo e(route('admin.reports.host-followers.destroy', $row)); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'danger','size' => 'sm','type' => 'submit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'danger','size' => 'sm','type' => 'submit']); ?>Remove <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
                  </form>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr class="bg-white dark:bg-gray-900"><td colspan="5" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No follower relationships found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

     <?php $__env->slot('footer', null, []); ?> 
      <div class="flex justify-end"><?php echo e($rows->links()); ?></div>
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
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/reports/host-followers.blade.php ENDPATH**/ ?>