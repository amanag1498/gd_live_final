<?php $__env->startSection('title', 'Agency Wallet'); ?>
<?php $__env->startSection('page_intro', 'Separate treasury balance for agency loads and user coin credits with linked wallet ledger visibility.'); ?>

<?php
  $inputClass = 'block w-full rounded-2xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white';
?>

<?php $__env->startSection('page_actions'); ?>
  <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'outline','size' => 'sm','href' => ''.e($walletRoute ?? (request()->routeIs('admin.*') ? route('admin.agencies.wallet.show', $agency) : route('agency.wallet.show'))).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','href' => ''.e($walletRoute ?? (request()->routeIs('admin.*') ? route('admin.agencies.wallet.show', $agency) : route('agency.wallet.show'))).'']); ?>Refresh <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
  <?php if(request()->routeIs('admin.*')): ?>
    <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['size' => 'sm','href' => ''.e(route('admin.reports.agency-wallets.index', ['agency_id' => $agency->id])).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm','href' => ''.e(route('admin.reports.agency-wallets.index', ['agency_id' => $agency->id])).'']); ?>Global Report <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $attributes = $__attributesOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__attributesOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala8bb031a483a05f647cb99ed3a469847)): ?>
<?php $component = $__componentOriginala8bb031a483a05f647cb99ed3a469847; ?>
<?php unset($__componentOriginala8bb031a483a05f647cb99ed3a469847); ?>
<?php endif; ?>
  <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
  <div class="space-y-6">
    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
      <?php if (isset($component)) { $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Treasury Balance','value' => number_format($walletSummary['balance'] ?? 0),'meta' => 'Current agency coin balance']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Treasury Balance','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($walletSummary['balance'] ?? 0)),'meta' => 'Current agency coin balance']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Admin Loaded','value' => number_format($walletSummary['total_loaded'] ?? 0),'meta' => number_format($walletSummary['loads_recorded'] ?? 0).' load events','tone' => 'brand']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Admin Loaded','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($walletSummary['total_loaded'] ?? 0)),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($walletSummary['loads_recorded'] ?? 0).' load events'),'tone' => 'brand']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Distributed','value' => number_format($walletSummary['total_distributed'] ?? 0),'meta' => 'Coins moved to users','tone' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Distributed','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($walletSummary['total_distributed'] ?? 0)),'meta' => 'Coins moved to users','tone' => 'warning']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'User Credits','value' => number_format($walletSummary['credits_issued'] ?? 0),'meta' => 'Completed transfer records','tone' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'User Credits','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($walletSummary['credits_issued'] ?? 0)),'meta' => 'Completed transfer records','tone' => 'dark']); ?>
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

    <section class="grid gap-6 xl:grid-cols-[minmax(320px,0.75fr)_minmax(0,1.25fr)]">
      <div class="space-y-6">
        <?php if($canLoadWallet ?? false): ?>
          <?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Admin Load Agency Wallet','desc' => 'Load treasury coins into the agency balance with a traceable reference.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Admin Load Agency Wallet','desc' => 'Load treasury coins into the agency balance with a traceable reference.']); ?>
            <form method="post" action="<?php echo e(route('admin.agencies.wallet.load', $agency)); ?>" class="space-y-4">
              <?php echo csrf_field(); ?>
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Coins</label>
                <input type="number" name="coins" min="1" class="<?php echo e($inputClass); ?>" required>
              </div>
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Reference</label>
                <input type="text" name="reference" class="<?php echo e($inputClass); ?>" placeholder="Invoice / batch / remark">
              </div>
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Note</label>
                <textarea name="note" rows="4" class="<?php echo e($inputClass); ?>" placeholder="Why this load was made"></textarea>
              </div>
              <button class="inline-flex w-full items-center justify-center rounded-2xl bg-brand-500 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-600">Load Wallet</button>
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
        <?php endif; ?>

        <?php if($canCreditUsers ?? false): ?>
          <?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Credit User From Agency Wallet','desc' => 'Move treasury balance into a user wallet and keep the transfer linked.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Credit User From Agency Wallet','desc' => 'Move treasury balance into a user wallet and keep the transfer linked.']); ?>
            <form method="post" action="<?php echo e(request()->routeIs('admin.*') ? route('admin.agencies.wallet.credit-user', $agency) : route('agency.wallet.credit-user')); ?>" class="space-y-4">
              <?php echo csrf_field(); ?>
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Target User ID</label>
                <input type="number" name="target_user_id" min="1" class="<?php echo e($inputClass); ?>" required>
              </div>
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Coins</label>
                <input type="number" name="coins" min="1" class="<?php echo e($inputClass); ?>" required>
              </div>
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Reference</label>
                <input type="text" name="reference" class="<?php echo e($inputClass); ?>" placeholder="Campaign / support / recharge ref">
              </div>
              <div>
                <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Note</label>
                <textarea name="note" rows="4" class="<?php echo e($inputClass); ?>" placeholder="Why this user is being credited"></textarea>
              </div>
              <button class="inline-flex w-full items-center justify-center rounded-2xl bg-success-500 px-4 py-3 text-sm font-semibold text-white hover:bg-success-600">Credit User</button>
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
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Agency Wallet Ledger</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Separate agency treasury transactions.</p>
              </div>
            </div>
           <?php $__env->endSlot(); ?>
          <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
              <thead class="bg-gray-50 dark:bg-gray-950/60">
                <tr>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Type</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Category</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Coins</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Balance</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Target User</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Actor</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Time</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                <?php $__empty_1 = true; $__currentLoopData = $walletTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <tr class="bg-white dark:bg-gray-900">
                    <td class="px-4 py-3"><?php echo e($tx->id); ?></td>
                    <td class="px-4 py-3"><?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['color' => $tx->type === 'credit' ? 'success' : 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($tx->type === 'credit' ? 'success' : 'warning')]); ?><?php echo e(strtoupper($tx->type)); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?></td>
                    <td class="px-4 py-3"><?php echo e(str_replace('_', ' ', ucfirst($tx->category))); ?></td>
                    <td class="px-4 py-3"><?php echo e(number_format($tx->coins)); ?></td>
                    <td class="px-4 py-3"><?php echo e(number_format($tx->balance_before)); ?> → <?php echo e(number_format($tx->balance_after)); ?></td>
                    <td class="px-4 py-3">
                      <?php if($tx->targetUser): ?>
                        <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($tx->targetUser->name); ?></div>
                        <div class="text-gray-500 dark:text-gray-400">#<?php echo e($tx->targetUser->id); ?></div>
                      <?php else: ?>
                        <span class="text-gray-500 dark:text-gray-400">—</span>
                      <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                      <?php if($tx->admin): ?>
                        <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($tx->admin->name); ?></div>
                        <div class="text-gray-500 dark:text-gray-400">Admin</div>
                      <?php elseif($tx->agencyUser): ?>
                        <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($tx->agencyUser->name); ?></div>
                        <div class="text-gray-500 dark:text-gray-400">Agency</div>
                      <?php else: ?>
                        <span class="text-gray-500 dark:text-gray-400">System</span>
                      <?php endif; ?>
                    </td>
                    <td class="px-4 py-3"><?php echo e(optional($tx->created_at)->format('d M Y, h:i A')); ?></td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr class="bg-white dark:bg-gray-900"><td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No agency wallet transactions yet.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <div class="mt-4 flex justify-end"><?php echo e($walletTransactions->withQueryString()->links()); ?></div>
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
                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Agency Coin Transfers</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Linked agency treasury and user wallet flow.</p>
              </div>
            </div>
           <?php $__env->endSlot(); ?>
          <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
              <thead class="bg-gray-50 dark:bg-gray-950/60">
                <tr>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Direction</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Coins</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">User</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Actor</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Linked Wallet Tx</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Time</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                <?php $__empty_1 = true; $__currentLoopData = $walletTransfers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transfer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <tr class="bg-white dark:bg-gray-900">
                    <td class="px-4 py-3"><?php echo e($transfer->id); ?></td>
                    <td class="px-4 py-3"><?php echo e(str_replace('_', ' ', ucfirst($transfer->direction))); ?></td>
                    <td class="px-4 py-3"><?php echo e(number_format($transfer->coins)); ?></td>
                    <td class="px-4 py-3">
                      <?php if($transfer->targetUser): ?>
                        <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($transfer->targetUser->name); ?></div>
                        <div class="text-gray-500 dark:text-gray-400">#<?php echo e($transfer->targetUser->id); ?></div>
                      <?php else: ?>
                        <span class="text-gray-500 dark:text-gray-400">Agency treasury load</span>
                      <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                      <?php if($transfer->admin): ?>
                        <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($transfer->admin->name); ?></div>
                        <div class="text-gray-500 dark:text-gray-400">Admin</div>
                      <?php elseif($transfer->agencyUser): ?>
                        <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($transfer->agencyUser->name); ?></div>
                        <div class="text-gray-500 dark:text-gray-400">Agency</div>
                      <?php else: ?>
                        <span class="text-gray-500 dark:text-gray-400">—</span>
                      <?php endif; ?>
                    </td>
                    <td class="px-4 py-3">
                      <div class="text-gray-500 dark:text-gray-400">Agency tx #<?php echo e($transfer->agency_wallet_transaction_id); ?></div>
                      <?php if($transfer->user_wallet_transaction_id): ?>
                        <div class="text-gray-500 dark:text-gray-400">User tx #<?php echo e($transfer->user_wallet_transaction_id); ?></div>
                      <?php endif; ?>
                    </td>
                    <td class="px-4 py-3"><?php echo e(optional($transfer->created_at)->format('d M Y, h:i A')); ?></td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr class="bg-white dark:bg-gray-900"><td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No agency wallet transfers yet.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <div class="mt-4 flex justify-end"><?php echo e($walletTransfers->withQueryString()->links()); ?></div>
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

    <?php if(request()->routeIs('admin.*')): ?>
      <?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Recent Admin Audit','desc' => 'Admin-visible audit trail for treasury operations.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Recent Admin Audit','desc' => 'Admin-visible audit trail for treasury operations.']); ?>
        <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
            <thead class="bg-gray-50 dark:bg-gray-950/60">
              <tr>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">#</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Action</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Admin</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Target User</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Reason</th>
                <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Time</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
              <?php $__empty_1 = true; $__currentLoopData = $walletAudits; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $audit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="bg-white dark:bg-gray-900">
                  <td class="px-4 py-3"><?php echo e($audit->id); ?></td>
                  <td class="px-4 py-3"><?php echo e(str_replace('_', ' ', ucfirst($audit->action))); ?></td>
                  <td class="px-4 py-3"><?php echo e($audit->admin?->name ?? '—'); ?></td>
                  <td class="px-4 py-3"><?php echo e($audit->targetUser?->name ? $audit->targetUser->name.' (#'.$audit->targetUser->id.')' : '—'); ?></td>
                  <td class="px-4 py-3"><?php echo e($audit->reason ?: '—'); ?></td>
                  <td class="px-4 py-3"><?php echo e(optional($audit->created_at)->format('d M Y, h:i A')); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr class="bg-white dark:bg-gray-900"><td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No audit records yet.</td></tr>
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
    <?php endif; ?>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.agency-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/agency/wallets/dashboard.blade.php ENDPATH**/ ?>