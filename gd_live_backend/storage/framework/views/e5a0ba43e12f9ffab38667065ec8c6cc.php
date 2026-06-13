<?php $__env->startSection('title', 'Agency Dashboard'); ?>
<?php $__env->startSection('page_intro', 'Agency-side operations view for host roster, live performance, call earnings, and weekly payout readiness.'); ?>

<?php
  $isAgencyPanel = request()->routeIs('agency.*');
  $agencyHostsRoute = $hostsIndexRoute ?? route('agency.hosts.index');
  $agencyCallsRoute = $callsRoute ?? route('agency.calls.index');
  $agencyWalletRoute = $walletRoute ?? route('agency.wallet.show');
  $agencyPayoutRoute = $payoutReportsRoute ?? route('agency.payout-reports.index');
?>

<?php $__env->startSection('page_actions'); ?>
  <a href="<?php echo e($agencyHostsRoute); ?>" class="<?php echo e($isAgencyPanel ? 'inline-flex' : 'hidden'); ?> items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Hosts</a>
  <a href="<?php echo e($agencyCallsRoute); ?>" class="<?php echo e($isAgencyPanel ? 'inline-flex' : 'hidden'); ?> items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Call Reports</a>
  <a href="<?php echo e($agencyWalletRoute); ?>" class="<?php echo e($isAgencyPanel && $agency ? 'inline-flex' : 'hidden'); ?> items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Wallet</a>
  <a href="<?php echo e($agencyPayoutRoute); ?>" class="<?php echo e($isAgencyPanel ? 'inline-flex' : 'hidden'); ?> items-center justify-center gap-2 rounded-lg bg-brand-500 px-3 py-2 text-xs font-medium text-white transition hover:bg-brand-600">Weekly Payout Reports</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
  <?php if(!$agency): ?>
    <?php if (isset($component)) { $__componentOriginal746de018ded8594083eb43be3f1332e1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal746de018ded8594083eb43be3f1332e1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.alert','data' => ['variant' => 'warning','title' => 'Agency not ready']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.alert'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'warning','title' => 'Agency not ready']); ?>
      Your agency is not created yet. If you recently applied, wait for admin approval.
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
  <?php else: ?>
    <?php
      $summary = $dashboard['summary'] ?? [];
      $hosts = $dashboard['hosts'] ?? collect();
      $recentPayoutReports = $dashboard['recentPayoutReports'] ?? collect();
      $recentLiveRooms = $dashboard['recentLiveRooms'] ?? collect();
      $topHosts = $dashboard['topHosts'] ?? collect();
    ?>

    <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
      <?php if (isset($component)) { $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Total Hosts','value' => number_format($summary['host_count'] ?? 0),'meta' => 'Blocked: '.number_format($summary['blocked_host_count'] ?? 0)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Total Hosts','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['host_count'] ?? 0)),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Blocked: '.number_format($summary['blocked_host_count'] ?? 0))]); ?>
        <?php $__env->slot('icon'); ?><i class="ti ti-users text-lg"></i><?php $__env->endSlot(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Active Hosts','value' => number_format($summary['active_host_count'] ?? 0),'meta' => 'Live now: '.number_format($summary['live_host_count'] ?? 0),'tone' => 'brand']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Active Hosts','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['active_host_count'] ?? 0)),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Live now: '.number_format($summary['live_host_count'] ?? 0)),'tone' => 'brand']); ?>
        <?php $__env->slot('icon'); ?><i class="ti ti-broadcast text-lg"></i><?php $__env->endSlot(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Gross Total','value' => number_format($summary['gross_total'] ?? 0),'meta' => 'Host payout '.number_format($summary['host_payout_total'] ?? 0).' · Agency payout '.number_format($summary['agency_payout_total'] ?? 0)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Gross Total','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['gross_total'] ?? 0)),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Host payout '.number_format($summary['host_payout_total'] ?? 0).' · Agency payout '.number_format($summary['agency_payout_total'] ?? 0))]); ?>
        <?php $__env->slot('icon'); ?><i class="ti ti-chart-bar text-lg"></i><?php $__env->endSlot(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Combined Payout','value' => number_format($summary['combined_payout_total'] ?? 0),'meta' => 'Host plus agency earned totals','tone' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Combined Payout','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['combined_payout_total'] ?? 0)),'meta' => 'Host plus agency earned totals','tone' => 'dark']); ?>
        <?php $__env->slot('icon'); ?><i class="ti ti-cash text-lg"></i><?php $__env->endSlot(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Video Activity','value' => number_format($summary['video_room_minutes'] ?? 0).' min','meta' => 'Calls '.number_format($summary['video_call_minutes'] ?? 0).' min · Gifts '.number_format($summary['video_gift_gross'] ?? 0)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Video Activity','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['video_room_minutes'] ?? 0).' min'),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Calls '.number_format($summary['video_call_minutes'] ?? 0).' min · Gifts '.number_format($summary['video_gift_gross'] ?? 0))]); ?>
        <?php $__env->slot('icon'); ?><i class="ti ti-video text-lg"></i><?php $__env->endSlot(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'PK Earnings','value' => number_format($summary['pk_gross'] ?? 0),'meta' => 'Events '.number_format($summary['pk_event_count'] ?? 0).' · Agency '.number_format($summary['pk_agency_earnings'] ?? 0),'tone' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'PK Earnings','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['pk_gross'] ?? 0)),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Events '.number_format($summary['pk_event_count'] ?? 0).' · Agency '.number_format($summary['pk_agency_earnings'] ?? 0)),'tone' => 'warning']); ?>
        <?php $__env->slot('icon'); ?><i class="ti ti-swords text-lg"></i><?php $__env->endSlot(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Payout Reports','value' => number_format($summary['payout_reports'] ?? 0),'meta' => 'Approved unpaid: '.number_format($summary['approved_unpaid_reports'] ?? 0)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Payout Reports','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['payout_reports'] ?? 0)),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Approved unpaid: '.number_format($summary['approved_unpaid_reports'] ?? 0))]); ?>
        <?php $__env->slot('icon'); ?><i class="ti ti-file-invoice text-lg"></i><?php $__env->endSlot(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Approved Unpaid Amount','value' => number_format($summary['approved_unpaid_amount'] ?? 0),'meta' => 'Offline payout pending review/payment','tone' => 'danger']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Approved Unpaid Amount','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($summary['approved_unpaid_amount'] ?? 0)),'meta' => 'Offline payout pending review/payment','tone' => 'danger']); ?>
        <?php $__env->slot('icon'); ?><i class="ti ti-hourglass text-lg"></i><?php $__env->endSlot(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Agency Wallet','value' => number_format($walletSummary['balance'] ?? 0),'meta' => 'Loaded '.number_format($walletSummary['total_loaded'] ?? 0).' · Sent '.number_format($walletSummary['total_distributed'] ?? 0)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Agency Wallet','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format($walletSummary['balance'] ?? 0)),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Loaded '.number_format($walletSummary['total_loaded'] ?? 0).' · Sent '.number_format($walletSummary['total_distributed'] ?? 0))]); ?>
        <?php $__env->slot('icon'); ?><i class="ti ti-wallet text-lg"></i><?php $__env->endSlot(); ?>
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

    <section class="grid gap-6 xl:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)]">
      <?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Agency Summary']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Agency Summary']); ?>
        <div class="space-y-4">
            <div class="mb-3">
              <div class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo e($agency->name); ?></div>
              <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($agency->legal_name ?: 'No legal name on file'); ?></div>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
              <div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Owner</div>
                <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($agency->owner?->name ?? '—'); ?></div>
                <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($agency->owner?->email ?? '—'); ?></div>
              </div>
              <div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Contact</div>
                <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($agency->contact_phone ?: '—'); ?></div>
                <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($agency->contact_email ?: '—'); ?></div>
              </div>
            </div>
            <?php if($agency->notes): ?>
              <div class="border-t border-gray-200 pt-4 text-sm text-gray-600 dark:border-gray-800 dark:text-gray-300">
                <div class="mb-1 text-sm text-gray-500 dark:text-gray-400">Notes</div>
                <div><?php echo e($agency->notes); ?></div>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Top Hosts By Gross Activity']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Top Hosts By Gross Activity']); ?>
        <div class="space-y-4">
              <?php $__empty_1 = true; $__currentLoopData = $topHosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-start justify-between gap-4 border-b border-gray-200 pb-4 last:border-0 last:pb-0 dark:border-gray-800">
                  <div>
                    <div class="font-semibold text-gray-900 dark:text-white">
                      <?php
                        $hostProfileRoute = $isAgencyPanel
                          ? route('agency.hosts.show', $row['host'])
                          : route('admin.agencies.hosts.show', ['agency' => $agency->id, 'host' => $row['host']->id]);
                      ?>
                      <a href="<?php echo e($hostProfileRoute); ?>" class="hover:text-brand-500">
                        <?php echo e($row['host']->user?->name ?? $row['host']->stage_name); ?>

                      </a>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($row['host']->stage_name ?: '—'); ?> · Calls: <?php echo e(number_format($row['call_count'])); ?> · PK <?php echo e(number_format($row['pk_event_count'])); ?></div>
                  </div>
                  <div class="text-right">
                    <div class="font-semibold text-gray-900 dark:text-white"><?php echo e(number_format($row['gross'])); ?></div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Agency payout <?php echo e(number_format($row['agency_earnings'])); ?> · PK <?php echo e(number_format($row['pk_gross'])); ?></div>
                  </div>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-sm text-gray-500 dark:text-gray-400">No host activity yet.</div>
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
    </section>

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
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Host Roster</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Agency-scoped host performance and contribution breakdown.</p>
          </div>
        </div>
       <?php $__env->endSlot(); ?>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
          <thead class="bg-gray-50 dark:bg-gray-950/60">
            <tr>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Host</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Room Min</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Gifts</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">PK Gross / Events</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Video Call Min / Earn</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Gross</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Host Payout</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Agency Payout</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Total Payout</th>
              <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Joined</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
            <?php $__empty_1 = true; $__currentLoopData = $hosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $host): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <?php
                $availability = $host->user?->hostAvailability;
                $isOnline = in_array($availability?->socket_status, ['online'], true) || in_array($availability?->manual_status, ['online'], true);
              ?>
              <tr class="bg-white dark:bg-gray-900">
                <td class="px-4 py-3">
                  <div class="font-semibold text-gray-900 dark:text-white">
                    <a href="<?php echo e($isAgencyPanel ? route('agency.hosts.show', $host) : route('admin.agencies.hosts.show', ['agency' => $agency->id, 'host' => $host->id])); ?>" class="hover:text-brand-500">
                      <?php echo e($host->user?->name ?? '—'); ?>

                    </a>
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400"><?php echo e($host->stage_name ?: '—'); ?> · <?php echo e($host->user?->email ?? ''); ?></div>
                </td>
                <td class="px-4 py-3">
                  <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['color' => $isOnline ? 'success' : 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isOnline ? 'success' : 'dark')]); ?><?php echo e($isOnline ? 'Online' : 'Offline'); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
                </td>
                <td class="px-4 py-3"><?php echo e(number_format((int) $host->dashboard_video_room_minutes)); ?></td>
                <td class="px-4 py-3"><?php echo e(number_format((int) $host->dashboard_video_gift_gross)); ?></td>
                <td class="px-4 py-3"><?php echo e(number_format((int) $host->dashboard_pk_gross)); ?> / <?php echo e(number_format((int) $host->dashboard_pk_event_count)); ?></td>
                <td class="px-4 py-3"><?php echo e(number_format((int) $host->dashboard_video_call_minutes)); ?> / <?php echo e(number_format((int) $host->dashboard_video_call_gross)); ?></td>
                <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white"><?php echo e(number_format((int) $host->dashboard_total_gross)); ?></td>
                <td class="px-4 py-3"><?php echo e(number_format((int) $host->dashboard_host_payout)); ?></td>
                <td class="px-4 py-3"><?php echo e(number_format((int) $host->dashboard_agency_payout)); ?></td>
                <td class="px-4 py-3"><?php echo e(number_format((int) $host->dashboard_total_payout)); ?></td>
                <td class="px-4 py-3"><?php echo e(optional($host->created_at)->format('d M Y') ?: '—'); ?></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr class="bg-white dark:bg-gray-900"><td colspan="12" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No hosts attached to this agency.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
        <?php if(method_exists($hosts, 'links')): ?>
          <div class="mt-4 flex justify-end"><?php echo e($hosts->links()); ?></div>
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

    <section class="grid gap-6 xl:grid-cols-2">
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
              <h3 class="text-base font-semibold text-gray-900 dark:text-white">Recent Weekly Payout Reports</h3>
              <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Latest agency payout reporting windows and review state.</p>
            </div>
            <a href="<?php echo e($agencyPayoutRoute); ?>" class="<?php echo e($isAgencyPanel ? 'inline-flex' : 'hidden'); ?> items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">View All</a>
          </div>
         <?php $__env->endSlot(); ?>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
              <thead class="bg-gray-50 dark:bg-gray-950/60">
                <tr>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Week</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Final Payable</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                <?php $__empty_1 = true; $__currentLoopData = $recentPayoutReports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <tr class="bg-white dark:bg-gray-900">
                    <td class="px-4 py-3"><?php echo e(optional($report->period_start)->format('d M Y')); ?> - <?php echo e(optional($report->period_end)->format('d M Y')); ?></td>
                    <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white"><?php echo e(number_format($report->final_payable)); ?></td>
                    <td class="px-4 py-3"><?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['color' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'dark']); ?><?php echo e(ucwords(str_replace('_', ' ', $report->status))); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?></td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr class="bg-white dark:bg-gray-900"><td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No payout reports yet.</td></tr>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => 'Recent Live Rooms','desc' => 'Latest room sessions and current room-state history.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Recent Live Rooms','desc' => 'Latest room sessions and current room-state history.']); ?>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-800">
              <thead class="bg-gray-50 dark:bg-gray-950/60">
                <tr>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Room</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Host</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Status</th>
                  <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Started</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                <?php $__empty_1 = true; $__currentLoopData = $recentLiveRooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <tr class="bg-white dark:bg-gray-900">
                    <td class="px-4 py-3"><?php echo e($room->title ?: $room->room_id); ?></td>
                    <td class="px-4 py-3"><?php echo e($room->host?->user?->name ?? '—'); ?></td>
                    <td class="px-4 py-3"><?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['color' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'dark']); ?><?php echo e(ucfirst($room->status)); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?></td>
                    <td class="px-4 py-3"><?php echo e(optional($room->started_at)->format('d M Y H:i') ?: '—'); ?></td>
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
    </section>
  <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.agency-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/agency/dashboard.blade.php ENDPATH**/ ?>