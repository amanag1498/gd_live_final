<?php $__env->startSection('title', ($report['host']->user?->name ?? $report['host']->stage_name ?? ('Host #'.$report['host']->id)) . ' Report'); ?>

<?php
  $host = $report['host'];
  $summary = $report['summary'];
  $from = $report['from'];
  $to = $report['to'];
?>

<?php $__env->startSection('page_actions'); ?>
  <div class="flex gap-3">
    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.reports.hosts', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')])).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.reports.hosts', ['from' => $from->format('Y-m-d'), 'to' => $to->format('Y-m-d')])).'']); ?>Back to Host Reports <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['size' => 'sm','href' => ''.e(route('admin.hosts.edit', $host)).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','href' => ''.e(route('admin.hosts.edit', $host)).'']); ?>Edit Host <?php echo $__env->renderComponent(); ?>
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
        <h3 class="text-base font-semibold text-gray-900 dark:text-white"><?php echo e($host->user?->name ?? $host->stage_name ?? ('Host #'.$host->id)); ?></h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Agency: <?php echo e($host->agency?->name ?? 'Independent'); ?> · Stage name: <?php echo e($host->stage_name ?: '—'); ?> · Followers: <?php echo e(number_format($summary['followers'])); ?></p>
      </div>
     <?php $__env->endSlot(); ?>
    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
      <?php if (isset($component)) { $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Calls','value' => number_format($summary['calls']),'tone' => 'brand']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Calls','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['calls'])),'tone' => 'brand']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6)): ?>
<?php $attributes = $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6; ?>
<?php unset($__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c3cb599308b2d9971dae437d0b6bab6)): ?>
<?php $component = $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6; ?>
<?php unset($__componentOriginal3c3cb599308b2d9971dae437d0b6bab6); ?>
<?php endif; ?>
      <?php if (isset($component)) { $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Minutes','value' => number_format($summary['minutes']),'tone' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Minutes','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['minutes'])),'tone' => 'dark']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6)): ?>
<?php $attributes = $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6; ?>
<?php unset($__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c3cb599308b2d9971dae437d0b6bab6)): ?>
<?php $component = $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6; ?>
<?php unset($__componentOriginal3c3cb599308b2d9971dae437d0b6bab6); ?>
<?php endif; ?>
      <?php if (isset($component)) { $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Call Coins','value' => number_format($summary['call_coins']),'tone' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Call Coins','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['call_coins'])),'tone' => 'success']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6)): ?>
<?php $attributes = $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6; ?>
<?php unset($__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c3cb599308b2d9971dae437d0b6bab6)): ?>
<?php $component = $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6; ?>
<?php unset($__componentOriginal3c3cb599308b2d9971dae437d0b6bab6); ?>
<?php endif; ?>
      <?php if (isset($component)) { $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Host Earnings','value' => number_format($summary['host_earnings']),'tone' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Host Earnings','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['host_earnings'])),'tone' => 'warning']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6)): ?>
<?php $attributes = $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6; ?>
<?php unset($__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c3cb599308b2d9971dae437d0b6bab6)): ?>
<?php $component = $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6; ?>
<?php unset($__componentOriginal3c3cb599308b2d9971dae437d0b6bab6); ?>
<?php endif; ?>
      <?php if (isset($component)) { $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Live Rooms','value' => number_format($summary['live_rooms'])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Live Rooms','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['live_rooms']))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6)): ?>
<?php $attributes = $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6; ?>
<?php unset($__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c3cb599308b2d9971dae437d0b6bab6)): ?>
<?php $component = $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6; ?>
<?php unset($__componentOriginal3c3cb599308b2d9971dae437d0b6bab6); ?>
<?php endif; ?>
      <?php if (isset($component)) { $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Live Gift Coins','value' => number_format($summary['live_gift_coins'])]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Live Gift Coins','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['live_gift_coins']))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6)): ?>
<?php $attributes = $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6; ?>
<?php unset($__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c3cb599308b2d9971dae437d0b6bab6)): ?>
<?php $component = $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6; ?>
<?php unset($__componentOriginal3c3cb599308b2d9971dae437d0b6bab6); ?>
<?php endif; ?>
      <?php if (isset($component)) { $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'PK Coins','value' => number_format($summary['pk_gift_coins']),'meta' => number_format($summary['pk_event_count']).' events']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'PK Coins','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['pk_gift_coins'])),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['pk_event_count']).' events')]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6)): ?>
<?php $attributes = $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6; ?>
<?php unset($__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c3cb599308b2d9971dae437d0b6bab6)): ?>
<?php $component = $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6; ?>
<?php unset($__componentOriginal3c3cb599308b2d9971dae437d0b6bab6); ?>
<?php endif; ?>
      <?php if (isset($component)) { $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Agency Earnings','value' => number_format($summary['agency_earnings']),'tone' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Agency Earnings','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['agency_earnings'])),'tone' => 'danger']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6)): ?>
<?php $attributes = $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6; ?>
<?php unset($__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3c3cb599308b2d9971dae437d0b6bab6)): ?>
<?php $component = $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6; ?>
<?php unset($__componentOriginal3c3cb599308b2d9971dae437d0b6bab6); ?>
<?php endif; ?>
    </section>
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

  <div class="grid gap-6 xl:grid-cols-2">
    <?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Weekly Breakdown','padding' => 'compact']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Weekly Breakdown','padding' => 'compact']); ?>
      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left text-gray-500">Week</th><th class="px-4 py-3 text-left text-gray-500">Calls</th><th class="px-4 py-3 text-left text-gray-500">Minutes</th><th class="px-4 py-3 text-left text-gray-500">Call Coins</th><th class="px-4 py-3 text-left text-gray-500">Live Gifts</th><th class="px-4 py-3 text-left text-gray-500">PK</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800"><?php $__empty_1 = true; $__currentLoopData = $report['weekly_breakdown']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $week): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><tr class="bg-white dark:bg-gray-900"><td class="px-4 py-3"><?php echo e(\Carbon\Carbon::parse($week['week_start'])->format('d M Y')); ?></td><td class="px-4 py-3"><?php echo e(number_format($week['calls'])); ?></td><td class="px-4 py-3"><?php echo e(number_format($week['minutes'])); ?></td><td class="px-4 py-3"><?php echo e(number_format($week['call_coins'])); ?></td><td class="px-4 py-3"><?php echo e(number_format($week['live_gift_coins'])); ?></td><td class="px-4 py-3"><?php echo e(number_format($week['pk_gift_coins'])); ?> / <?php echo e(number_format($week['pk_event_count'])); ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr class="bg-white dark:bg-gray-900"><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No weekly data.</td></tr><?php endif; ?></tbody>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Recent Followers','padding' => 'compact']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Recent Followers','padding' => 'compact']); ?>
      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left text-gray-500">User</th><th class="px-4 py-3 text-left text-gray-500">Online Alerts</th><th class="px-4 py-3 text-left text-gray-500">Followed</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800"><?php $__empty_1 = true; $__currentLoopData = $report['followers']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $follow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><tr class="bg-white dark:bg-gray-900"><td class="px-4 py-3"><?php echo e($follow->user?->name ?? ('User #'.$follow->user_id)); ?></td><td class="px-4 py-3"><?php echo e($follow->notify_when_online ? 'Yes' : 'No'); ?></td><td class="px-4 py-3"><?php echo e(optional($follow->created_at)?->format('d M Y H:i') ?: '—'); ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr class="bg-white dark:bg-gray-900"><td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No followers yet.</td></tr><?php endif; ?></tbody>
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

  <div class="grid gap-6 xl:grid-cols-2">
    <?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Recent Calls','padding' => 'compact']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Recent Calls','padding' => 'compact']); ?>
      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left text-gray-500">ID</th><th class="px-4 py-3 text-left text-gray-500">Caller</th><th class="px-4 py-3 text-left text-gray-500">Type</th><th class="px-4 py-3 text-left text-gray-500">Status</th><th class="px-4 py-3 text-left text-gray-500">Minutes</th><th class="px-4 py-3 text-left text-gray-500">Coins</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800"><?php $__empty_1 = true; $__currentLoopData = $report['recent_calls']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $call): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><tr class="bg-white dark:bg-gray-900"><td class="px-4 py-3">#<?php echo e($call->id); ?></td><td class="px-4 py-3"><?php echo e($call->caller?->name ?? ('User #'.$call->caller_id)); ?></td><td class="px-4 py-3"><?php echo e(ucfirst($call->type)); ?></td><td class="px-4 py-3"><?php echo e(ucfirst($call->status)); ?></td><td class="px-4 py-3"><?php echo e(number_format((int) $call->billable_minutes)); ?></td><td class="px-4 py-3"><?php echo e(number_format((int) $call->total_coins_charged)); ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr class="bg-white dark:bg-gray-900"><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No recent calls.</td></tr><?php endif; ?></tbody>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Recent Live Rooms','padding' => 'compact']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Recent Live Rooms','padding' => 'compact']); ?>
      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left text-gray-500">Room</th><th class="px-4 py-3 text-left text-gray-500">Status</th><th class="px-4 py-3 text-left text-gray-500">Started</th><th class="px-4 py-3 text-left text-gray-500">Ended</th><th class="px-4 py-3 text-left text-gray-500">Duration</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800"><?php $__empty_1 = true; $__currentLoopData = $report['recent_live_rooms']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><tr class="bg-white dark:bg-gray-900"><td class="px-4 py-3"><?php echo e($room->title ?: $room->room_id); ?></td><td class="px-4 py-3"><?php echo e(ucfirst($room->status)); ?></td><td class="px-4 py-3"><?php echo e(optional($room->started_at)?->format('d M Y H:i') ?: '—'); ?></td><td class="px-4 py-3"><?php echo e(optional($room->ended_at)?->format('d M Y H:i') ?: '—'); ?></td><td class="px-4 py-3"><?php echo e($room->duration_minutes !== null ? number_format($room->duration_minutes) . ' min' : '—'); ?></td></tr><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><tr class="bg-white dark:bg-gray-900"><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No live rooms in this range.</td></tr><?php endif; ?></tbody>
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
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/reports/host-show.blade.php ENDPATH**/ ?>