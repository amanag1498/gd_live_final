<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'as' => 'button',
    'size' => 'md',
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
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
    'as' => 'button',
    'size' => 'md',
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $base = 'inline-flex items-center justify-center gap-2 rounded-lg font-medium transition';

    $sizeMap = [
        'sm' => 'px-3 py-2 text-xs',
        'md' => 'px-4 py-2.5 text-sm',
        'lg' => 'px-5 py-3 text-sm',
    ];

    $variantMap = [
        'primary' => 'bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600',
        'secondary' => 'bg-gray-900 text-white hover:bg-black dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100',
        'outline' => 'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800',
        'light' => 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700',
        'success' => 'bg-success-500 text-white hover:bg-success-600',
        'danger' => 'bg-error-500 text-white hover:bg-error-600',
        'warning' => 'bg-warning-400 text-gray-900 hover:bg-warning-500',
    ];

    $classes = trim($base.' '.($sizeMap[$size] ?? $sizeMap['md']).' '.($variantMap[$variant] ?? $variantMap['primary']).' '.$attributes->get('class'));
    $tag = $href ? 'a' : $as;
?>

<?php if($tag === 'a'): ?>
    <a href="<?php echo e($href); ?>" <?php echo e($attributes->merge(['class' => $classes])); ?>>
        <?php echo e($slot); ?>

    </a>
<?php else: ?>
    <<?php echo e($tag); ?> <?php echo e($attributes->merge(['class' => $classes, 'type' => $type])); ?>>
        <?php echo e($slot); ?>

    </<?php echo e($tag); ?>>
<?php endif; ?>
<?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/components/ui/button.blade.php ENDPATH**/ ?>