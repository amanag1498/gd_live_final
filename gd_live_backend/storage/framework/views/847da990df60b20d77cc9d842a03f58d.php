<?php $__env->startSection('title', 'Game Settings'); ?>

<?php
  $selectedGame = request('game', 'teen_patti');
  if (!in_array($selectedGame, ['teen_patti', 'greedy'], true)) {
    $selectedGame = 'teen_patti';
  }

  $gameMeta = [
    'teen_patti' => [
      'label' => 'Teen Patti',
      'subtitle' => 'Cards, pot flow, fake bets, payout rule, and room-strip visibility.',
      'dashboard_route' => 'admin.games.teen-patti.dashboard',
      'settings_route' => route('admin.settings.games.edit', ['game' => 'teen_patti']),
    ],
    'greedy' => [
      'label' => 'Greedy',
      'subtitle' => 'Spinner timing, fake bets, weighted pots, multipliers, and sector distribution.',
      'dashboard_route' => 'admin.games.greedy.dashboard',
      'settings_route' => route('admin.settings.games.edit', ['game' => 'greedy']),
    ],
  ];

  $selectedMeta = $gameMeta[$selectedGame];
  $prefix = "games.{$selectedGame}.";
  $filteredDefinitions = collect($definitions)->filter(fn ($definition, $key) => str_starts_with($key, $prefix));
  $groupedDefinitions = [];
  foreach ($groups as $groupKey => $groupLabel) {
    $items = $filteredDefinitions->filter(fn ($definition) => ($definition['group'] ?? 'general') === $groupKey);
    if ($items->isNotEmpty()) {
      $groupedDefinitions[$groupKey] = ['label' => $groupLabel, 'items' => $items];
    }
  }
  $enabledKey = "games.{$selectedGame}.enabled";
  $visibleKey = "games.{$selectedGame}.visible_in_video_room_strip";
  $fakeKey = "games.{$selectedGame}.fake_bets_enabled";
  $minKey = "games.{$selectedGame}.min_bet";
  $maxKey = "games.{$selectedGame}.max_bet";
  $durationKey = "games.{$selectedGame}.round_duration_seconds";
  $lockKey = "games.{$selectedGame}.betting_lock_seconds";
  $displayKey = "games.{$selectedGame}.result_display_seconds";
  $inputClass = 'h-10 w-full rounded-xl border border-gray-300 bg-white px-3 text-sm text-gray-900 shadow-theme-xs outline-hidden placeholder:text-gray-400 focus:border-brand-300 focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white dark:placeholder:text-gray-500';
?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <section class="overflow-hidden rounded-3xl border border-gray-200 bg-linear-to-br from-white via-gray-50 to-brand-50 dark:border-gray-800 dark:from-gray-900 dark:via-gray-900 dark:to-brand-500/10">
    <div class="px-6 py-6 lg:px-8">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div class="max-w-3xl">
          <div class="mb-3 flex flex-wrap gap-2">
            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['color' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'dark']); ?>Config <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
            <?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['color' => 'brand']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'brand']); ?>Games <?php echo $__env->renderComponent(); ?>
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
          <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Game Settings</h2>
          <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">Separate control surfaces for Teen Patti and Greedy. Keep round engines, fake bets, timing, and payout controls isolated.</p>
        </div>
        <div class="flex flex-wrap gap-2">
          <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.games.teen-patti.dashboard')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.games.teen-patti.dashboard')).'']); ?>Teen Patti Dashboard <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.games.greedy.dashboard')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','size' => 'sm','href' => ''.e(route('admin.games.greedy.dashboard')).'']); ?>Greedy Dashboard <?php echo $__env->renderComponent(); ?>
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
      </div>
    </div>
  </section>

  <div class="flex flex-wrap gap-2">
    <?php $__currentLoopData = $gameMeta; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gameKey => $meta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <a href="<?php echo e($meta['settings_route']); ?>" class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-medium <?php echo e($selectedGame === $gameKey ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900' : 'border border-gray-300 bg-white text-gray-700 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200'); ?>">
        <?php echo e($meta['label']); ?>

      </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>

  <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
    <?php if (isset($component)) { $__componentOriginal3c3cb599308b2d9971dae437d0b6bab6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3c3cb599308b2d9971dae437d0b6bab6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Current Game','value' => $selectedMeta['label'],'meta' => $selectedMeta['subtitle']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Current Game','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedMeta['label']),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedMeta['subtitle'])]); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Status','value' => !empty($values[$enabledKey]) ? 'Enabled' : 'Disabled','meta' => !empty($values[$visibleKey]) ? 'Visible in video room strip' : 'Hidden from room strip','tone' => 'success']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Status','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!empty($values[$enabledKey]) ? 'Enabled' : 'Disabled'),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!empty($values[$visibleKey]) ? 'Visible in video room strip' : 'Hidden from room strip'),'tone' => 'success']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Bet Window','value' => ($values[$durationKey] ?? '—') . 's','meta' => 'Lock ' . ($values[$lockKey] ?? '—') . 's, display ' . ($values[$displayKey] ?? '—') . 's','tone' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Bet Window','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(($values[$durationKey] ?? '—') . 's'),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute('Lock ' . ($values[$lockKey] ?? '—') . 's, display ' . ($values[$displayKey] ?? '—') . 's'),'tone' => 'warning']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.admin.stat-card','data' => ['label' => 'Bet Range','value' => number_format((int) ($values[$minKey] ?? 0)) . ' - ' . number_format((int) ($values[$maxKey] ?? 0)),'meta' => !empty($values[$fakeKey]) ? 'Fake bets enabled' : 'Fake bets disabled','tone' => 'dark']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('admin.stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Bet Range','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(number_format((int) ($values[$minKey] ?? 0)) . ' - ' . number_format((int) ($values[$maxKey] ?? 0))),'meta' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(!empty($values[$fakeKey]) ? 'Fake bets enabled' : 'Fake bets disabled'),'tone' => 'dark']); ?>
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

  <form method="post" action="<?php echo e(route('admin.settings.games.update', ['game' => $selectedGame])); ?>" class="space-y-6">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <?php $__currentLoopData = $groupedDefinitions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupKey => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php if (isset($component)) { $__componentOriginalb8dfe58016103e374219da4cf072c7cf = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb8dfe58016103e374219da4cf072c7cf = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.common.component-card','data' => ['title' => $group['label'],'desc' => $selectedMeta['subtitle']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('common.component-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($group['label']),'desc' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedMeta['subtitle'])]); ?>
        <div class="grid gap-4 xl:grid-cols-2">
          <?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $definition): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php ($field = str_replace('games.', '', $key)); ?>
            <?php ($fieldName = str_replace('.', '][', $field)); ?>
            <?php ($inputName = "games[{$fieldName}]"); ?>
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950/60">
              <div class="mb-3">
                <div class="font-semibold text-gray-900 dark:text-white"><?php echo e($definition['label']); ?></div>
                <?php if(!empty($definition['hint'])): ?>
                  <div class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?php echo e($definition['hint']); ?></div>
                <?php endif; ?>
              </div>
              <?php if(($definition['type'] ?? 'boolean') === 'string' && !empty($definition['options'])): ?>
                <select class="<?php echo e($inputClass); ?>" name="<?php echo e($inputName); ?>">
                  <?php $__currentLoopData = ($definition['options'] ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($option); ?>" <?php if(old($key, $values[$key] ?? $definition['default'] ?? null) === $option): echo 'selected'; endif; ?>><?php echo e(ucfirst(str_replace('_', ' ', $option))); ?></option>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
              <?php elseif(($definition['type'] ?? 'boolean') === 'boolean'): ?>
                <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 dark:border-gray-700 dark:bg-gray-900">
                  <input type="hidden" name="<?php echo e($inputName); ?>" value="0">
                  <input class="h-5 w-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500/30 dark:border-gray-700 dark:bg-gray-900" type="checkbox" name="<?php echo e($inputName); ?>" value="1" <?php if(old($key, $values[$key] ?? false)): echo 'checked'; endif; ?>>
                  <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Enabled</span>
                </label>
              <?php else: ?>
                <input
                  type="number"
                  step="<?php echo e($definition['step'] ?? 1); ?>"
                  min="<?php echo e($definition['min'] ?? 0); ?>"
                  max="<?php echo e($definition['max'] ?? ''); ?>"
                  class="<?php echo e($inputClass); ?>"
                  name="<?php echo e($inputName); ?>"
                  value="<?php echo e(old($key, $values[$key] ?? $definition['default'] ?? '')); ?>"
                >
              <?php endif; ?>
              <?php $__errorArgs = [$key];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="mt-2 text-sm text-error-600 dark:text-error-300"><?php echo e($message); ?></div>
              <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <div class="flex items-center justify-between gap-4 rounded-2xl border border-gray-200 bg-white px-5 py-4 dark:border-gray-800 dark:bg-gray-900">
      <div class="text-sm text-gray-500 dark:text-gray-400">These values are stored in <code>app_settings</code> and used by Laravel, the websocket service, and the Android client.</div>
      <?php if (isset($component)) { $__componentOriginala8bb031a483a05f647cb99ed3a469847 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala8bb031a483a05f647cb99ed3a469847 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.button','data' => ['type' => 'submit','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','size' => 'sm']); ?>Save <?php echo e($selectedMeta['label']); ?> Settings <?php echo $__env->renderComponent(); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/settings/games.blade.php ENDPATH**/ ?>