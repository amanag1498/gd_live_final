<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'desc' => null,
    'padding' => 'default',
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
    'title' => null,
    'desc' => null,
    'padding' => 'default',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $bodyPadding = $padding === 'compact' ? 'p-4 sm:p-5' : 'p-5 sm:p-6';
?>

<section <?php echo e($attributes->merge(['class' => 'overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900'])); ?>>
    <?php if($title || $desc || isset($header)): ?>
        <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800 sm:px-6">
            <?php if(isset($header)): ?>
                <?php echo e($header); ?>

            <?php else: ?>
                <?php if($title): ?>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white"><?php echo e($title); ?></h3>
                <?php endif; ?>
                <?php if($desc): ?>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400"><?php echo e($desc); ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="<?php echo e($bodyPadding); ?>">
        <?php echo e($slot); ?>

    </div>

    <?php if(isset($footer)): ?>
        <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-800 sm:px-6">
            <?php echo e($footer); ?>

        </div>
    <?php endif; ?>
</section>
<?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/components/common/component-card.blade.php ENDPATH**/ ?>