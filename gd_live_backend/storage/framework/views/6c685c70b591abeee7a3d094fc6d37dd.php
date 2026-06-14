<?php $__env->startSection('title', 'Host Reports'); ?>

<?php
  $inputClass = 'h-11 w-full rounded-xl border border-gray-300 bg-white px-4 text-sm text-gray-900 shadow-theme-xs outline-hidden focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
?>

<?php $__env->startSection('page_actions'); ?>
  <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.reports.hosts.csv', request()->query())).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.reports.hosts.csv', request()->query())).'']); ?>Export CSV <?php echo $__env->renderComponent(); ?>
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
      <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
        <div class="max-w-2xl">
          <h3 class="text-base font-semibold text-gray-900 dark:text-white">Host Performance Reports</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track room output, video call coins, room gifts, PK gifts, and gross host performance across daily or weekly slices.</p>
        </div>
        <form class="grid gap-3 md:grid-cols-2 xl:grid-cols-[220px_160px_150px_150px_auto]" method="get">
          <select name="host_id" class="<?php echo e($inputClass); ?>">
            <option value="">All hosts</option>
            <?php $__currentLoopData = $hosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $h): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($h->id); ?>" <?php if($hostId == $h->id): echo 'selected'; endif; ?>><?php echo e($h->user?->name); ?> (ID <?php echo e($h->id); ?>)</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <select name="range" class="<?php echo e($inputClass); ?>">
            <option value="daily" <?php if($range==='daily'): echo 'selected'; endif; ?>>Daily</option>
            <option value="weekly" <?php if($range==='weekly'): echo 'selected'; endif; ?>>Weekly</option>
          </select>
          <input type="date" name="from" class="<?php echo e($inputClass); ?>" value="<?php echo e($from->format('Y-m-d')); ?>">
          <input type="date" name="to" class="<?php echo e($inputClass); ?>" value="<?php echo e($to->format('Y-m-d')); ?>">
          <div class="flex items-center gap-3">
            <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm']); ?>Apply <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.reports.hosts')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.reports.hosts')).'']); ?>Reset <?php echo $__env->renderComponent(); ?>
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
      </div>
     <?php $__env->endSlot(); ?>

    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60">
          <tr>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400"><?php echo e($range==='weekly' ? 'Week' : 'Date'); ?></th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Host</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Rooms</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Participants</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Video Call</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Room Gifts / Coins</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Gift / PK</th>
            <th class="px-4 py-3 text-left font-medium uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">Gross</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
        <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <?php ($reportHost = $hosts->firstWhere('id', $r['host_id'])); ?>
          <tr class="bg-white dark:bg-gray-900">
            <td class="px-4 py-4 text-gray-600 dark:text-gray-300"><?php echo e($range==='weekly' ? \Carbon\Carbon::parse($r['week_start'])->format('d M Y') : \Carbon\Carbon::parse($r['date'])->format('d M Y')); ?></td>
            <td class="px-4 py-4">
              <a href="<?php echo e(route('admin.reports.hosts.show', ['host' => $r['host_id'], 'from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')])); ?>" class="font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-300">
                <?php echo e($reportHost?->user?->name ?? 'Host #'.$r['host_id']); ?>

              </a>
              <div class="mt-1 text-xs text-gray-500 dark:text-gray-400"><?php echo e($reportHost?->agency?->name ?? 'Independent'); ?></div>
            </td>
            <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
              <?php echo e($r['rooms']); ?>

              <div class="mt-1 text-xs text-gray-500 dark:text-gray-400"><?php echo e($r['duration_min']); ?> min</div>
            </td>
            <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
              <?php echo e($r['participants_total']); ?>

              <div class="mt-1 text-xs text-gray-500 dark:text-gray-400"><?php echo e($r['participants_unique']); ?> unique</div>
            </td>
            <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
              <?php echo e(number_format($r['video_call_minutes'])); ?> min
              <div class="mt-1 text-xs text-gray-500 dark:text-gray-400"><?php echo e(number_format($r['video_call_coins'])); ?> coins</div>
            </td>
            <td class="px-4 py-4 text-gray-900 dark:text-white">
              <?php echo e(number_format($r['room_gift_coins'])); ?>

            </td>
            <td class="px-4 py-4 text-gray-600 dark:text-gray-300">
              <?php echo e(number_format($r['gift_coins'])); ?>

              <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">PK <?php echo e(number_format($r['pk_coins'])); ?> · <?php echo e(number_format($r['pk_events'])); ?> events</div>
            </td>
            <td class="px-4 py-4 font-semibold text-gray-900 dark:text-white"><?php echo e(number_format($r['gross_coins'])); ?></td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr class="bg-white dark:bg-gray-900"><td colspan="8" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">No host data in this range.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
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

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/reports/hosts.blade.php ENDPATH**/ ?>