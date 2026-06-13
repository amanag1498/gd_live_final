<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'src' => null,
    'alt' => 'Avatar',
    'size' => 'md',
    'status' => null,
    'initials' => null,
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
    'src' => null,
    'alt' => 'Avatar',
    'size' => 'md',
    'status' => null,
    'initials' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $sizeMap = [
        'sm' => 'h-8 w-8 text-xs',
        'md' => 'h-10 w-10 text-sm',
        'lg' => 'h-12 w-12 text-base',
    ];

    $statusMap = [
        'online' => 'bg-success-500',
        'offline' => 'bg-gray-300',
        'busy' => 'bg-warning-500',
    ];
?>

<div class="relative <?php echo e($sizeMap[$size] ?? $sizeMap['md']); ?>">
    <?php if($src): ?>
        <img src="<?php echo e($src); ?>" alt="<?php echo e($alt); ?>" class="h-full w-full rounded-full object-cover" />
    <?php else: ?>
        <div class="flex h-full w-full items-center justify-center rounded-full bg-brand-50 font-semibold text-brand-600 dark:bg-brand-500/15 dark:text-brand-300">
            <?php echo e($initials ?: strtoupper(substr($alt, 0, 1))); ?>

        </div>
    <?php endif; ?>

    <?php if($status): ?>
        <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white dark:border-gray-900 <?php echo e($statusMap[$status] ?? $statusMap['offline']); ?>"></span>
    <?php endif; ?>
</div>
<?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/components/ui/avatar.blade.php ENDPATH**/ ?>