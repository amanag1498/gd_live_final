<?php $__env->startSection('title','PK Battle Detail'); ?>

<?php
  $surfaceClass = 'rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950/60';
?>

<?php $__env->startSection('page_actions'); ?>
  <a href="<?php echo e(route('admin.pk-battles.index')); ?>" class="inline-flex items-center justify-center rounded-2xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">
    <i class="ti ti-arrow-left mr-2"></i>Back
  </a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="flex flex-col gap-4 px-6 py-6 lg:flex-row lg:items-start lg:justify-between lg:px-8">
      <div>
        <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white"><?php echo e($pk_battle->battle_id); ?></h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Status <?php echo e(strtoupper($pk_battle->status)); ?> · Winner <?php echo e($pk_battle->winnerRoom?->room_id ?: 'Draw / N/A'); ?></p>
      </div>
      <div class="grid gap-3 sm:grid-cols-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
          <div class="text-sm text-gray-500 dark:text-gray-400">Duration</div>
          <div class="mt-2 text-xl font-semibold text-gray-900 dark:text-white"><?php echo e($pk_battle->duration_seconds); ?>s</div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
          <div class="text-sm text-gray-500 dark:text-gray-400">Started</div>
          <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e(optional($pk_battle->started_at)->format('d M Y H:i:s') ?: '—'); ?></div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
          <div class="text-sm text-gray-500 dark:text-gray-400">Ended</div>
          <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e(optional($pk_battle->ended_at)->format('d M Y H:i:s') ?: '—'); ?></div>
        </div>
      </div>
    </div>
  </section>

  <section class="grid gap-6 md:grid-cols-2">
    <?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Room A','desc' => 'Host, room, and score details.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Room A','desc' => 'Host, room, and score details.']); ?>
      <div class="space-y-3">
        <div class="<?php echo e($surfaceClass); ?>">
          <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Room</div>
          <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white"><?php if($pk_battle->roomA): ?><a class="text-brand-600 hover:text-brand-700 dark:text-brand-300" href="<?php echo e(route('admin.live-rooms.show', $pk_battle->roomA)); ?>"><?php echo e($pk_battle->roomA->room_id); ?></a><?php else: ?>—<?php endif; ?></div>
        </div>
        <div class="<?php echo e($surfaceClass); ?>">
          <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Host</div>
          <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white"><?php if($pk_battle->hostA?->user): ?><a class="text-brand-600 hover:text-brand-700 dark:text-brand-300" href="<?php echo e(route('admin.users.show', $pk_battle->hostA->user)); ?>"><?php echo e($pk_battle->hostA?->stage_name ?: $pk_battle->hostA->user->name); ?></a><?php else: ?><?php echo e($pk_battle->hostA?->stage_name ?: $pk_battle->hostA?->user?->name ?: '—'); ?><?php endif; ?></div>
        </div>
        <div class="<?php echo e($surfaceClass); ?>">
          <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Score</div>
          <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white"><?php echo e(number_format($pk_battle->score_a)); ?></div>
        </div>
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

    <?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Room B','desc' => 'Host, room, and score details.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Room B','desc' => 'Host, room, and score details.']); ?>
      <div class="space-y-3">
        <div class="<?php echo e($surfaceClass); ?>">
          <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Room</div>
          <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white"><?php if($pk_battle->roomB): ?><a class="text-brand-600 hover:text-brand-700 dark:text-brand-300" href="<?php echo e(route('admin.live-rooms.show', $pk_battle->roomB)); ?>"><?php echo e($pk_battle->roomB->room_id); ?></a><?php else: ?>—<?php endif; ?></div>
        </div>
        <div class="<?php echo e($surfaceClass); ?>">
          <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Host</div>
          <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white"><?php if($pk_battle->hostB?->user): ?><a class="text-brand-600 hover:text-brand-700 dark:text-brand-300" href="<?php echo e(route('admin.users.show', $pk_battle->hostB->user)); ?>"><?php echo e($pk_battle->hostB?->stage_name ?: $pk_battle->hostB->user->name); ?></a><?php else: ?><?php echo e($pk_battle->hostB?->stage_name ?: $pk_battle->hostB?->user?->name ?: '—'); ?><?php endif; ?></div>
        </div>
        <div class="<?php echo e($surfaceClass); ?>">
          <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Score</div>
          <div class="mt-2 text-3xl font-semibold text-gray-900 dark:text-white"><?php echo e(number_format($pk_battle->score_b)); ?></div>
        </div>
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
  </section>

  <?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Gift Contributors','desc' => 'Top users contributing coins during the PK battle.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Gift Contributors','desc' => 'Top users contributing coins during the PK battle.']); ?>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">User</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Total Coins</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Contributions</th></tr></thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          <?php $__empty_1 = true; $__currentLoopData = $contributors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3"><?php if($row->user): ?><a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="<?php echo e(route('admin.users.show', $row->user)); ?>"><?php echo e($row->user->name); ?></a><?php else: ?><?php echo e('User #'.$row->user_id); ?><?php endif; ?></td>
              <td class="px-4 py-3"><?php echo e(number_format($row->total_coins)); ?></td>
              <td class="px-4 py-3"><?php echo e(number_format($row->contributions)); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr class="bg-white dark:bg-gray-900"><td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No contributors yet.</td></tr>
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

  <?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Event Log','desc' => 'Room-scoped PK battle event history.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Event Log','desc' => 'Room-scoped PK battle event history.']); ?>
    <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
      <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
        <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">ID</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Type</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Room</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Coins</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Wallet Tx</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Created At</th></tr></thead>
        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
          <?php $__empty_1 = true; $__currentLoopData = $pk_battle->events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="bg-white dark:bg-gray-900">
              <td class="px-4 py-3"><?php echo e($event->id); ?></td>
              <td class="px-4 py-3"><?php echo e(strtoupper($event->event_type)); ?></td>
              <td class="px-4 py-3"><?php if($event->room): ?><a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="<?php echo e(route('admin.live-rooms.show', $event->room)); ?>"><?php echo e($event->room->room_id); ?></a><?php else: ?><?php echo e($event->room_id); ?><?php endif; ?></td>
              <td class="px-4 py-3"><?php echo e(number_format($event->coins)); ?></td>
              <td class="px-4 py-3"><?php echo e($event->wallet_transaction_id ?: '—'); ?></td>
              <td class="px-4 py-3"><?php echo e(optional($event->created_at)->format('d M Y H:i:s')); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr class="bg-white dark:bg-gray-900"><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No PK events logged.</td></tr>
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

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/pk-battles/show.blade.php ENDPATH**/ ?>