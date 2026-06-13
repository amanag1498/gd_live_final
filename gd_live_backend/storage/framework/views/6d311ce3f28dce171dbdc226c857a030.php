<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'pageTitle',
    'items' => [],
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'pageTitle',
    'items' => [],
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white"><?php echo e($pageTitle); ?></h2>
        <?php if($slot->isNotEmpty()): ?>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?php echo e($slot); ?></p>
        <?php endif; ?>
    </div>

    <?php if($items): ?>
        <nav>
            <ol class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="inline-flex items-center gap-1.5">
                        <?php if(!empty($item['href'])): ?>
                            <a href="<?php echo e($item['href']); ?>" class="hover:text-gray-700 dark:hover:text-gray-200"><?php echo e($item['label']); ?></a>
                        <?php else: ?>
                            <span class="text-gray-900 dark:text-white"><?php echo e($item['label']); ?></span>
                        <?php endif; ?>
                        <?php if(!$loop->last): ?>
                            <span>/</span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ol>
        </nav>
    <?php endif; ?>
</div>
<?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/components/common/page-breadcrumb.blade.php ENDPATH**/ ?>