<?php $__env->startSection('title', 'User 360 · '.$user->name); ?>

<?php
  $inputClass = 'block w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
  $labelClass = 'mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300';
  $surfaceClass = 'rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950/60';
  $actionButtonClass = 'inline-flex items-center justify-center rounded-2xl px-4 py-2.5 text-sm font-semibold transition';
?>

<?php $__env->startSection('page_actions'); ?>
  <a href="<?php echo e(route('admin.wallets.show', $user)); ?>" class="<?php echo e($actionButtonClass); ?> bg-brand-500 text-white hover:bg-brand-600">Wallet</a>
  <a href="<?php echo e(route('admin.users.notifications', $user)); ?>" class="<?php echo e($actionButtonClass); ?> border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Notifications</a>
  <?php if($user->is_blocked): ?>
    <form method="post" action="<?php echo e(route('admin.users.unblock', $user)); ?>">
      <?php echo csrf_field(); ?>
      <button class="<?php echo e($actionButtonClass); ?> bg-success-500 text-white hover:bg-success-600">Unblock</button>
    </form>
  <?php else: ?>
    <form method="post" action="<?php echo e(route('admin.users.block', $user)); ?>">
      <?php echo csrf_field(); ?>
      <button class="<?php echo e($actionButtonClass); ?> bg-error-500 text-white hover:bg-error-600">Block</button>
    </form>
  <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="flex flex-col gap-6 px-6 py-6 lg:flex-row lg:items-start lg:justify-between lg:px-8">
      <div class="min-w-0">
        <div class="mb-3 flex flex-wrap items-center gap-2">
          <?php if($user->is_blocked): ?>
            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['color' => 'error']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'error']); ?>Blocked <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
          <?php else: ?>
            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['color' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'success']); ?>Active <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
          <?php endif; ?>
          <?php $__currentLoopData = $user->getRoleNames(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['color' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'dark']); ?><?php echo e($role); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white"><?php echo e($user->name); ?></h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300"><?php echo e($user->email); ?></p>
        <div class="mt-3 flex flex-wrap gap-3 text-sm text-gray-500 dark:text-gray-400">
          <span>User #<?php echo e($user->id); ?></span>
          <?php if($user->device_id): ?>
            <span>Device <code><?php echo e($user->device_id); ?></code></span>
          <?php endif; ?>
          <span>Joined <?php echo e($user->created_at?->format('d M Y, H:i')); ?></span>
        </div>
      </div>
      <div class="grid gap-3 sm:grid-cols-2 xl:min-w-[320px]">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
          <div class="text-sm text-gray-500 dark:text-gray-400">Firebase UID</div>
          <div class="mt-2 break-all text-sm font-medium text-gray-900 dark:text-white"><?php echo e($user->firebase_uid ?? '—'); ?></div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-950">
          <div class="text-sm text-gray-500 dark:text-gray-400">Provider</div>
          <div class="mt-2 text-sm font-medium text-gray-900 dark:text-white"><?php echo e($user->provider ?? '—'); ?></div>
        </div>
      </div>
    </div>
  </section>

  <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
    <?php if (isset($component)) { $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Wallet','value' => number_format($walletSummary['balance']),'meta' => 'Current coin balance']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Wallet','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($walletSummary['balance'])),'meta' => 'Current coin balance']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Following','value' => number_format($followingCount),'meta' => 'Accounts followed','tone' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Following','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($followingCount)),'meta' => 'Accounts followed','tone' => 'dark']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Followers','value' => number_format($followersCount),'meta' => 'Followers on profile','tone' => 'brand']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Followers','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($followersCount)),'meta' => 'Followers on profile','tone' => 'brand']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Rooms Joined','value' => number_format($overviewStats['live_rooms_joined']),'meta' => 'Live room participation','tone' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Rooms Joined','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($overviewStats['live_rooms_joined'])),'meta' => 'Live room participation','tone' => 'warning']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Calls','value' => number_format($overviewStats['calls_total']),'meta' => 'Call history volume','tone' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Calls','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($overviewStats['calls_total'])),'meta' => 'Call history volume','tone' => 'dark']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Gift Spend','value' => number_format($overviewStats['gifts_sent']),'meta' => 'Total coins spent on gifts','tone' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Gift Spend','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($overviewStats['gifts_sent'])),'meta' => 'Total coins spent on gifts','tone' => 'danger']); ?>
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

  <section class="grid gap-6 xl:grid-cols-[minmax(320px,0.9fr)_minmax(0,1.4fr)]">
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
          <div class="flex items-center justify-between gap-3">
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Identity</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Authentication, verification, and device metadata.</p>
            </div>
          </div>
         <?php $__env->endSlot(); ?>
        <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
          <div class="<?php echo e($surfaceClass); ?>"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Firebase UID</div><div class="mt-2 break-all text-sm font-medium text-gray-900 dark:text-white"><?php echo e($user->firebase_uid ?? '—'); ?></div></div>
          <div class="<?php echo e($surfaceClass); ?>"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Provider</div><div class="mt-2 text-sm font-medium text-gray-900 dark:text-white"><?php echo e($user->provider ?? '—'); ?></div></div>
          <div class="<?php echo e($surfaceClass); ?>"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Device</div><div class="mt-2 text-sm font-medium text-gray-900 dark:text-white"><?php echo e($user->device_id ?? '—'); ?></div></div>
          <div class="<?php echo e($surfaceClass); ?>"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Email Verified</div><div class="mt-2 text-sm font-medium text-gray-900 dark:text-white"><?php echo e($user->email_verified_at?->format('d M Y, H:i') ?? 'No'); ?></div></div>
          <div class="<?php echo e($surfaceClass); ?>"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Updated</div><div class="mt-2 text-sm font-medium text-gray-900 dark:text-white"><?php echo e($user->updated_at?->format('d M Y, H:i')); ?></div></div>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
         <?php $__env->slot('header', null, []); ?> 
          <div class="flex items-center justify-between gap-3">
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Wallet Controls</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Credit or debit the wallet with an explicit audit reason.</p>
            </div>
            <a href="<?php echo e(route('admin.wallets.show', $user)); ?>" class="inline-flex items-center justify-center rounded-2xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Full Ledger</a>
          </div>
         <?php $__env->endSlot(); ?>
        <div class="grid gap-3 sm:grid-cols-2">
          <div class="<?php echo e($surfaceClass); ?>"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Credits</div><div class="mt-2 text-lg font-semibold text-gray-900 dark:text-white"><?php echo e(number_format($walletSummary['credits'])); ?></div></div>
          <div class="<?php echo e($surfaceClass); ?>"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Debits</div><div class="mt-2 text-lg font-semibold text-gray-900 dark:text-white"><?php echo e(number_format($walletSummary['debits'])); ?></div></div>
        </div>
        <div class="mt-4 space-y-4">
          <form method="post" action="<?php echo e(route('admin.wallets.credit', $user)); ?>" class="grid gap-3 sm:grid-cols-[140px_minmax(0,1fr)]">
            <?php echo csrf_field(); ?>
            <input type="number" name="amount" min="1" class="<?php echo e($inputClass); ?>" placeholder="Coins" required>
            <input type="text" name="note" class="<?php echo e($inputClass); ?>" placeholder="Credit reason">
            <div class="sm:col-span-2">
              <button class="inline-flex w-full items-center justify-center rounded-2xl bg-success-500 px-4 py-3 text-sm font-semibold text-white hover:bg-success-600">Credit Wallet</button>
            </div>
          </form>
          <form method="post" action="<?php echo e(route('admin.wallets.debit', $user)); ?>" class="grid gap-3 sm:grid-cols-[140px_minmax(0,1fr)]">
            <?php echo csrf_field(); ?>
            <input type="number" name="amount" min="1" class="<?php echo e($inputClass); ?>" placeholder="Coins" required>
            <input type="text" name="note" class="<?php echo e($inputClass); ?>" placeholder="Debit reason">
            <div class="sm:col-span-2">
              <button class="inline-flex w-full items-center justify-center rounded-2xl bg-error-500 px-4 py-3 text-sm font-semibold text-white hover:bg-error-600">Debit Wallet</button>
            </div>
          </form>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Level Controls','desc' => 'Current level placement and manual override controls.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Level Controls','desc' => 'Current level placement and manual override controls.']); ?>
        <div class="space-y-4">
          <div>
            <?php if($user->level): ?>
              <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold text-white" style="background: <?php echo e($user->level->badge_color ?: '#64748b'); ?>">L<?php echo e($user->level->level); ?> · <?php echo e($user->level->title); ?></span>
            <?php else: ?>
              <span class="text-sm text-gray-500 dark:text-gray-400">No level assigned</span>
            <?php endif; ?>
          </div>
          <div class="text-sm text-gray-500 dark:text-gray-400">
            Lifetime spend <?php echo e(number_format($levelProgress['lifetime_spend_coins'] ?? 0)); ?>

            <?php if(!empty($levelProgress['next_level'])): ?>
              · <?php echo e(number_format($levelProgress['remaining_spend_to_next_level'] ?? 0)); ?> to next
            <?php endif; ?>
          </div>
          <div class="h-2.5 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-800">
            <div class="h-full rounded-full bg-brand-500" style="width: <?php echo e((float) ($levelProgress['progress_percent'] ?? 0)); ?>%"></div>
          </div>
          <form method="post" action="<?php echo e(route('admin.users.level.set', $user)); ?>" class="space-y-3">
            <?php echo csrf_field(); ?>
            <div>
              <label class="<?php echo e($labelClass); ?>">Level</label>
              <select name="level_id" class="<?php echo e($inputClass); ?>" required>
                <?php $__currentLoopData = $availableLevels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $level): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($level->id); ?>" <?php if((int) $user->level_id === (int) $level->id): echo 'selected'; endif; ?>>L<?php echo e($level->level); ?> · <?php echo e($level->title); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
            <div>
              <label class="<?php echo e($labelClass); ?>">Reason</label>
              <input type="text" name="reason" class="<?php echo e($inputClass); ?>" placeholder="Reason">
            </div>
            <button class="inline-flex w-full items-center justify-center rounded-2xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-600">Update Level</button>
          </form>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Game Access','desc' => 'Control which game APIs this user can access.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Game Access','desc' => 'Control which game APIs this user can access.']); ?>
        <form method="post" action="<?php echo e(route('admin.users.games.update', $user)); ?>" class="space-y-4">
          <?php echo csrf_field(); ?>
          <div class="<?php echo e($surfaceClass); ?>">
            <div class="flex items-center justify-between gap-4">
              <div>
                <div class="text-sm font-semibold text-gray-900 dark:text-white">Teen Patti</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Unlock game access for user #<?php echo e($user->id); ?></div>
              </div>
              <label class="inline-flex items-center gap-3">
                <input type="hidden" name="teen_patti" value="0">
                <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="teen_patti" value="1" <?php if($gameAccessMap['teen_patti'] ?? false): echo 'checked'; endif; ?>>
              </label>
            </div>
          </div>
          <div class="<?php echo e($surfaceClass); ?>">
            <div class="flex items-center justify-between gap-4">
              <div>
                <div class="text-sm font-semibold text-gray-900 dark:text-white">Greedy</div>
                <div class="mt-1 text-sm text-gray-500 dark:text-gray-400">Unlock game access for user #<?php echo e($user->id); ?></div>
              </div>
              <label class="inline-flex items-center gap-3">
                <input type="hidden" name="greedy" value="0">
                <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="greedy" value="1" <?php if($gameAccessMap['greedy'] ?? false): echo 'checked'; endif; ?>>
              </label>
            </div>
          </div>
          <div>
            <label class="<?php echo e($labelClass); ?>">Reason</label>
            <input type="text" name="reason" class="<?php echo e($inputClass); ?>" placeholder="Reason for access change">
          </div>
          <button class="inline-flex w-full items-center justify-center rounded-2xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-600">Save Game Access</button>
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
          <div class="flex items-center justify-between gap-3">
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Subscriptions</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Grant, monitor, and cancel subscription access.</p>
            </div>
            <a href="<?php echo e(route('admin.user-subscriptions.index')); ?>" class="inline-flex items-center justify-center rounded-2xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">All Subscriptions</a>
          </div>
         <?php $__env->endSlot(); ?>
        <div class="grid gap-3 md:grid-cols-3">
          <div class="<?php echo e($surfaceClass); ?>"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Active</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($activeSubscription?->plan?->name ?? 'None'); ?></div></div>
          <div class="<?php echo e($surfaceClass); ?>"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Status</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($activeSubscription?->status ?? '—'); ?></div></div>
          <div class="<?php echo e($surfaceClass); ?>"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Ends</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($activeSubscription?->ends_at?->format('d M Y') ?? '—'); ?></div></div>
        </div>
        <form method="post" action="<?php echo e(route('admin.users.subscriptions.store', $user)); ?>" class="mt-4 grid gap-3 md:grid-cols-5">
          <?php echo csrf_field(); ?>
          <select name="plan_id" class="<?php echo e($inputClass); ?> md:col-span-2" required>
            <?php $__currentLoopData = $availablePlans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($plan->id); ?>"><?php echo e($plan->name); ?> · <?php echo e(number_format($plan->price_coins)); ?> coins / <?php echo e($plan->duration_days); ?>d</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <select name="status" class="<?php echo e($inputClass); ?>">
            <option value="active">Active</option>
            <option value="cancelled">Cancelled</option>
            <option value="expired">Expired</option>
          </select>
          <input type="datetime-local" name="starts_at" class="<?php echo e($inputClass); ?>">
          <input type="datetime-local" name="ends_at" class="<?php echo e($inputClass); ?>">
          <input type="text" name="reason" class="<?php echo e($inputClass); ?> md:col-span-4" placeholder="Reason">
          <button class="inline-flex items-center justify-center rounded-2xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-600">Grant</button>
        </form>
        <div class="mt-4 overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60">
              <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">ID</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Plan</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Starts</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Ends</th>
                <th class="px-4 py-3 text-right font-medium text-gray-500 dark:text-gray-400">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              <?php $__empty_1 = true; $__currentLoopData = $subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3"><?php echo e($subscription->id); ?></td>
                  <td class="px-4 py-3"><?php echo e($subscription->plan?->name ?? '—'); ?></td>
                  <td class="px-4 py-3"><?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['color' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'dark']); ?><?php echo e(strtoupper($subscription->status)); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?></td>
                  <td class="px-4 py-3"><?php echo e($subscription->starts_at?->format('d M Y H:i')); ?></td>
                  <td class="px-4 py-3"><?php echo e($subscription->ends_at?->format('d M Y H:i')); ?></td>
                  <td class="px-4 py-3 text-right">
                    <div class="flex justify-end gap-2">
                      <a href="<?php echo e(route('admin.user-subscriptions.edit', $subscription)); ?>" class="inline-flex items-center justify-center rounded-2xl border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Edit</a>
                      <?php if($subscription->status === 'active'): ?>
                        <form method="post" action="<?php echo e(route('admin.users.subscriptions.cancel', [$user, $subscription])); ?>">
                          <?php echo csrf_field(); ?>
                          <button class="inline-flex items-center justify-center rounded-2xl border border-warning-200 bg-warning-50 px-3 py-2 text-xs font-semibold text-warning-700 hover:bg-warning-100 dark:border-warning-500/30 dark:bg-warning-500/10 dark:text-warning-300">Cancel</button>
                        </form>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr class="bg-white dark:bg-gray-900"><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No subscriptions.</td></tr>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
         <?php $__env->slot('header', null, []); ?> 
          <div class="flex items-center justify-between gap-3">
            <div>
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Entry Packs</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Assign entrance packs and inspect ownership history.</p>
            </div>
            <a href="<?php echo e(route('admin.entry-packs.reports')); ?>" class="inline-flex items-center justify-center rounded-2xl border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Ownership Reports</a>
          </div>
         <?php $__env->endSlot(); ?>
        <div class="grid gap-3 md:grid-cols-3">
          <div class="<?php echo e($surfaceClass); ?>"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Active Pack</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($activeEntryPack?->entryPack?->name ?? 'None'); ?></div></div>
          <div class="<?php echo e($surfaceClass); ?>"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Style</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e(strtoupper($activeEntryPack?->entryPack?->animation_style ?? '—')); ?></div></div>
          <div class="<?php echo e($surfaceClass); ?>"><div class="text-xs uppercase tracking-[0.18em] text-gray-400">Expires</div><div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($activeEntryPack?->expires_at?->format('d M Y') ?? '—'); ?></div></div>
        </div>
        <form method="post" action="<?php echo e(route('admin.users.entry-packs.store', $user)); ?>" class="mt-4 grid gap-3 md:grid-cols-5">
          <?php echo csrf_field(); ?>
          <select name="entry_pack_id" class="<?php echo e($inputClass); ?> md:col-span-2" required>
            <?php $__currentLoopData = $availableEntryPacks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pack): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($pack->id); ?>"><?php echo e($pack->name); ?> · <?php echo e(number_format($pack->price_coins)); ?> coins · <?php echo e($pack->duration_days); ?>d</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <input type="datetime-local" name="purchased_at" class="<?php echo e($inputClass); ?>">
          <input type="datetime-local" name="expires_at" class="<?php echo e($inputClass); ?>">
          <label class="flex items-center gap-3 rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm text-gray-700 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200">
            <input type="checkbox" name="is_active" value="1" id="is_active_entry" checked class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900">
            Active
          </label>
          <input type="text" name="reason" class="<?php echo e($inputClass); ?> md:col-span-4" placeholder="Reason">
          <button class="inline-flex items-center justify-center rounded-2xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-600">Assign Entry Pack</button>
        </form>
        <div class="mt-4 overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60">
              <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">ID</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Pack</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Purchased</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Expires</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              <?php $__empty_1 = true; $__currentLoopData = $entryHistory; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3"><?php echo e($entry->id); ?></td>
                  <td class="px-4 py-3"><?php echo e($entry->entryPack?->name ?? '—'); ?></td>
                  <td class="px-4 py-3">
                    <?php if($entry->is_currently_usable): ?>
                      <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['color' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'success']); ?>Active <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                    <?php elseif($entry->expires_at && $entry->expires_at->isPast()): ?>
                      <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['color' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'warning']); ?>Expired <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                    <?php else: ?>
                      <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['color' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'dark']); ?>Inactive <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                    <?php endif; ?>
                  </td>
                  <td class="px-4 py-3"><?php echo e($entry->purchased_at?->format('d M Y H:i')); ?></td>
                  <td class="px-4 py-3"><?php echo e($entry->expires_at?->format('d M Y H:i') ?? '—'); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr class="bg-white dark:bg-gray-900"><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No entry packs.</td></tr>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Host / Agency Linkage','desc' => 'Current host profile and agency relationship for this user.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Host / Agency Linkage','desc' => 'Current host profile and agency relationship for this user.']); ?>
        <?php if($user->host): ?>
          <div class="grid gap-3 md:grid-cols-2">
            <div class="<?php echo e($surfaceClass); ?>">
              <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Host</div>
              <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white">#<?php echo e($user->host->id); ?> · <?php echo e($user->host->stage_name ?: $user->name); ?></div>
              <div class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?php echo e(trim(($user->host->city ?? '').' '.($user->host->country ?? '')) ?: '—'); ?></div>
            </div>
            <div class="<?php echo e($surfaceClass); ?>">
              <div class="text-xs uppercase tracking-[0.18em] text-gray-400">Agency</div>
              <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($user->host->agency?->name ?? 'No agency'); ?></div>
              <div class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?php echo e($user->host->agency?->contact_email ?? '—'); ?></div>
            </div>
          </div>
        <?php else: ?>
          <div class="rounded-2xl border border-dashed border-gray-300 px-4 py-6 text-sm text-gray-500 dark:border-gray-700 dark:text-gray-400">User is not linked to a host profile.</div>
        <?php endif; ?>
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
  </section>

  <section class="grid gap-6 xl:grid-cols-2">
    <?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Recent Activity','desc' => 'Participation across live rooms, calls, and gifts.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Recent Activity','desc' => 'Participation across live rooms, calls, and gifts.']); ?>
      <div class="space-y-4">
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Room</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Role</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Joined</th></tr></thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              <?php $__empty_1 = true; $__currentLoopData = $recentLiveParticipations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3"><?php if($row->room): ?><a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="<?php echo e(route('admin.live-rooms.show', $row->room)); ?>"><?php echo e($row->room->room_id); ?></a><?php else: ?>—<?php endif; ?></td>
                  <td class="px-4 py-3"><?php echo e($row->role); ?></td>
                  <td class="px-4 py-3"><?php echo e($row->joined_at?->format('d M Y H:i')); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr class="bg-white dark:bg-gray-900"><td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No live participation.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Call</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Type</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Coins</th></tr></thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              <?php $__empty_1 = true; $__currentLoopData = $recentCalls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $call): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3">#<?php echo e($call->id); ?></td>
                  <td class="px-4 py-3"><?php echo e(strtoupper($call->type)); ?></td>
                  <td class="px-4 py-3"><?php echo e(strtoupper($call->status)); ?></td>
                  <td class="px-4 py-3"><?php echo e(number_format($call->total_coins_charged ?? 0)); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr class="bg-white dark:bg-gray-900"><td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No calls.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Gift</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Room</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Total Coins</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">When</th></tr></thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              <?php $__empty_1 = true; $__currentLoopData = $recentGifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3"><?php echo e($gift->gift?->name ?? 'Gift'); ?></td>
                  <td class="px-4 py-3"><?php if($gift->room): ?><a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="<?php echo e(route('admin.live-rooms.show', $gift->room)); ?>"><?php echo e($gift->room->room_id); ?></a><?php else: ?>—<?php endif; ?></td>
                  <td class="px-4 py-3"><?php echo e(number_format($gift->total_coins)); ?></td>
                  <td class="px-4 py-3"><?php echo e($gift->created_at?->format('d M Y H:i')); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr class="bg-white dark:bg-gray-900"><td colspan="4" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No gifts sent.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Hosted / PK / Audit Trail','desc' => 'Hosted rooms, PK battles, and administrative actions.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Hosted / PK / Audit Trail','desc' => 'Hosted rooms, PK battles, and administrative actions.']); ?>
      <div class="space-y-4">
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Hosted Room</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Started</th></tr></thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              <?php $__empty_1 = true; $__currentLoopData = $recentHostedRooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3"><a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="<?php echo e(route('admin.live-rooms.show', $room)); ?>"><?php echo e($room->room_id); ?></a></td>
                  <td class="px-4 py-3"><?php echo e(strtoupper($room->status)); ?></td>
                  <td class="px-4 py-3"><?php echo e($room->started_at?->format('d M Y H:i') ?: '—'); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr class="bg-white dark:bg-gray-900"><td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No hosted rooms.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">PK Battle</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Score</th></tr></thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              <?php $__empty_1 = true; $__currentLoopData = $pkBattles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $battle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3"><a class="font-medium text-brand-600 hover:text-brand-700 dark:text-brand-300" href="<?php echo e(route('admin.pk-battles.show', $battle)); ?>"><?php echo e($battle->battle_id); ?></a></td>
                  <td class="px-4 py-3"><?php echo e(strtoupper($battle->status)); ?></td>
                  <td class="px-4 py-3"><?php echo e(number_format($battle->score_a)); ?> - <?php echo e(number_format($battle->score_b)); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr class="bg-white dark:bg-gray-900"><td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No PK participation.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60"><tr><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">When</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Area</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Action</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Admin</th><th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Reason</th></tr></thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              <?php $__empty_1 = true; $__currentLoopData = $auditTrail; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $audit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3"><?php echo e($audit->created_at?->format('d M Y H:i')); ?></td>
                  <td class="px-4 py-3"><?php echo e(strtoupper(str_replace('_', ' ', $audit->area))); ?></td>
                  <td class="px-4 py-3"><?php echo e(str_replace('_', ' ', $audit->action)); ?></td>
                  <td class="px-4 py-3"><?php echo e($audit->admin?->name ?? 'System'); ?></td>
                  <td class="px-4 py-3"><?php echo e($audit->reason ?: '—'); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr class="bg-white dark:bg-gray-900"><td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No admin audit entries.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
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
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/users/show.blade.php ENDPATH**/ ?>