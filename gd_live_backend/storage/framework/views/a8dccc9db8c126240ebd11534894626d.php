<?php $__env->startSection('title', ($host->user?->name ?? $host->stage_name ?? 'Host Detail')); ?>
<?php $__env->startSection('page_intro', 'Detailed host performance across calls, live rooms, payout items, and earnings inside your agency.'); ?>

<?php
  $surfaceClass = 'rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950/60';
?>

<?php $__env->startSection('page_actions'); ?>
  <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'outline','size' => 'sm','href' => ''.e($hostsIndexRoute ?? route('agency.hosts.index')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','href' => ''.e($hostsIndexRoute ?? route('agency.hosts.index')).'']); ?>Back to Hosts <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['size' => 'sm','href' => ''.e($callsRoute ?? route('agency.calls.index', ['host_id' => $host->id])).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','href' => ''.e($callsRoute ?? route('agency.calls.index', ['host_id' => $host->id])).'']); ?>Filter Call Reports <?php echo $__env->renderComponent(); ?>
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
  <?php
    $summary = $detail['summary'];
    $availability = $host->user?->hostAvailability;
    $isOnline = in_array($availability?->socket_status, ['online'], true) || in_array($availability?->manual_status, ['online'], true);
    $avatar = $host->user?->avatar_url;
    $hostPhotos = $host->photos ?? collect();
  ?>

  <div class="space-y-6">
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <?php if (isset($component)) { $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Calls','value' => number_format($summary['call_count']),'meta' => 'Total call sessions']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Calls','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['call_count'])),'meta' => 'Total call sessions']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Minutes','value' => number_format($summary['total_minutes']),'meta' => 'Total video call time','tone' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Minutes','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['total_minutes'])),'meta' => 'Total video call time','tone' => 'dark']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Gross Total','value' => number_format($summary['gross_total']),'meta' => 'Combined earnings before payout','tone' => 'brand']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Gross Total','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['gross_total'])),'meta' => 'Combined earnings before payout','tone' => 'brand']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Followers','value' => number_format($summary['followers']),'meta' => 'Current follower base','tone' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Followers','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['followers'])),'meta' => 'Current follower base','tone' => 'warning']); ?>
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

    <section class="grid gap-6 xl:grid-cols-[minmax(0,0.85fr)_minmax(0,1.15fr)]">
      <?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Host Summary','desc' => 'Profile, rates, availability, and basic location details.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Host Summary','desc' => 'Profile, rates, availability, and basic location details.']); ?>
        <div class="space-y-4">
          <div class="flex items-start gap-4">
            <div class="h-18 w-18 overflow-hidden rounded-2xl border border-gray-200 bg-gray-100 dark:border-gray-800 dark:bg-gray-900">
              <?php if($avatar): ?>
                <img src="<?php echo e($avatar); ?>" alt="<?php echo e($host->user?->name ?? 'Host avatar'); ?>" class="h-full w-full object-cover">
              <?php else: ?>
                <div class="flex h-full w-full items-center justify-center text-lg font-semibold text-gray-500 dark:text-gray-400">
                  <?php echo e(strtoupper(substr($host->user?->name ?? $host->stage_name ?? 'H', 0, 1))); ?>

                </div>
              <?php endif; ?>
            </div>
            <div>
              <div class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo e($host->user?->name ?? '—'); ?></div>
              <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($host->stage_name ?: '—'); ?> · <?php echo e($host->user?->email ?? '—'); ?></div>
              <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">Phone: <?php echo e($host->contact_phone ?: '—'); ?></div>
            </div>
          </div>
          <div class="flex items-center gap-3">
            <span class="inline-flex h-2.5 w-2.5 rounded-full <?php echo e($isOnline ? 'bg-success-500' : 'bg-gray-400'); ?>"></span>
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo e($isOnline ? 'Online' : 'Offline'); ?></span>
          </div>
          <div class="grid gap-3 sm:grid-cols-2">
            <div class="<?php echo e($surfaceClass); ?>"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Country</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($host->country ?: '—'); ?></div></div>
            <div class="<?php echo e($surfaceClass); ?>"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">City</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($host->city ?: '—'); ?></div></div>
            <div class="<?php echo e($surfaceClass); ?>"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Video Rate</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e(number_format((int) $host->video_call_rate_per_minute)); ?></div></div>
          </div>
          <?php if($hostPhotos->isNotEmpty()): ?>
            <div class="space-y-3">
              <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Profile Photos</div>
              <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <?php $__currentLoopData = $hostPhotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <div class="aspect-square overflow-hidden rounded-2xl border border-gray-200 bg-gray-100 dark:border-gray-800 dark:bg-gray-900">
                    <img src="<?php echo e($photo->path); ?>" alt="Host photo <?php echo e($loop->iteration); ?>" class="h-full w-full object-cover">
                  </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
            </div>
          <?php endif; ?>
          <?php if($host->bio): ?>
            <div class="rounded-2xl border border-gray-200 bg-white p-4 text-sm text-gray-600 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
              <div class="mb-2 text-xs uppercase tracking-[0.18em] text-gray-400">Bio</div>
              <div><?php echo e($host->bio); ?></div>
            </div>
          <?php endif; ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Earnings Summary','desc' => 'Gross activity and payout breakdown across live rooms, PK, and calls.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Earnings Summary','desc' => 'Gross activity and payout breakdown across live rooms, PK, and calls.']); ?>
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              <tr class="bg-white dark:bg-gray-900"><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Room Minutes</th><td class="px-4 py-3"><?php echo e(number_format($summary['video_room_minutes'])); ?></td><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Gift Gross</th><td class="px-4 py-3"><?php echo e(number_format($summary['video_gift_gross'])); ?></td></tr>
              <tr class="bg-gray-50 dark:bg-gray-950/60"><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">PK Gross / Events</th><td class="px-4 py-3"><?php echo e(number_format($summary['pk_gross'])); ?> / <?php echo e(number_format($summary['pk_event_count'])); ?></td><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">PK Host / Agency</th><td class="px-4 py-3"><?php echo e(number_format($summary['pk_host_earnings'])); ?> / <?php echo e(number_format($summary['pk_agency_earnings'])); ?></td></tr>
              <tr class="bg-white dark:bg-gray-900"><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Call Min / Gross</th><td class="px-4 py-3"><?php echo e(number_format($summary['video_call_minutes'])); ?> / <?php echo e(number_format($summary['video_call_gross'])); ?></td><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Live Rooms</th><td class="px-4 py-3"><?php echo e(number_format($summary['live_rooms'])); ?> / <?php echo e(number_format($summary['live_rooms_active'])); ?> live</td></tr>
              <tr class="bg-gray-50 dark:bg-gray-950/60"><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Host Payout</th><td class="px-4 py-3"><?php echo e(number_format($summary['host_payout'])); ?></td><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Agency Payout</th><td class="px-4 py-3"><?php echo e(number_format($summary['agency_payout'])); ?></td></tr>
              <tr class="bg-white dark:bg-gray-900"><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Total Payout</th><td class="px-4 py-3"><?php echo e(number_format($summary['total_payout'])); ?></td><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Completed Calls</th><td class="px-4 py-3"><?php echo e(number_format($summary['completed_calls'])); ?></td></tr>
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
    </section>

    <section class="grid gap-6 xl:grid-cols-2">
      <?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Recent Calls','desc' => 'Latest paid calling activity for this host.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Recent Calls','desc' => 'Latest paid calling activity for this host.']); ?>
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">ID</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Caller</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Type</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Coins</th></tr></thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              <?php $__empty_1 = true; $__currentLoopData = $detail['recentCalls']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $call): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3">#<?php echo e($call->id); ?></td>
                  <td class="px-4 py-3"><?php echo e($call->caller?->name ?? '—'); ?></td>
                  <td class="px-4 py-3"><?php echo e(ucfirst($call->type)); ?></td>
                  <td class="px-4 py-3"><?php echo e(ucfirst($call->status)); ?></td>
                  <td class="px-4 py-3"><?php echo e(number_format((int) $call->total_coins_charged)); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr class="bg-white dark:bg-gray-900"><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No call records yet.</td></tr>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Weekly Payout Line Items','desc' => 'Most recent settlement windows for this host.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Weekly Payout Line Items','desc' => 'Most recent settlement windows for this host.']); ?>
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Week</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Room</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Gifts</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">PK Gifts</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Call</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Total Coins</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Total INR</th></tr></thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              <?php $__empty_1 = true; $__currentLoopData = $detail['recentPayoutItems']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3"><?php echo e(optional($item->report?->period_start)->format('d M Y') ?: '—'); ?></td>
                  <td class="px-4 py-3"><?php echo e(number_format($item->video_room_minutes)); ?> min</td>
                  <td class="px-4 py-3"><?php echo e(number_format($item->video_gift_coins)); ?></td>
                  <td class="px-4 py-3"><?php echo e(number_format($item->pk_gift_coins)); ?></td>
                  <td class="px-4 py-3"><?php echo e(number_format($item->video_call_coins)); ?> / <?php echo e(number_format($item->video_call_minutes)); ?> min</td>
                  <td class="px-4 py-3"><?php echo e(number_format($item->total_coins)); ?></td>
                  <td class="px-4 py-3"><?php echo e(number_format($item->total_inr, 2)); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr class="bg-white dark:bg-gray-900"><td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No payout items yet.</td></tr>
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
    </section>

    <?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Recent Live Rooms','desc' => 'Latest room sessions hosted by this account.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Recent Live Rooms','desc' => 'Latest room sessions hosted by this account.']); ?>
      <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Room</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Started</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Ended</th></tr></thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            <?php $__empty_1 = true; $__currentLoopData = $detail['recentLiveRooms']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr class="bg-white dark:bg-gray-900">
                <td class="px-4 py-3"><?php echo e($room->title ?: $room->room_id); ?></td>
                <td class="px-4 py-3"><?php echo e(ucfirst($room->status)); ?></td>
                <td class="px-4 py-3"><?php echo e(optional($room->started_at)->format('d M Y H:i') ?: '—'); ?></td>
                <td class="px-4 py-3"><?php echo e(optional($room->ended_at)->format('d M Y H:i') ?: '—'); ?></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr class="bg-white dark:bg-gray-900"><td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No live room activity yet.</td></tr>
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

<?php echo $__env->make('layouts.agency-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/agency/hosts/show.blade.php ENDPATH**/ ?>