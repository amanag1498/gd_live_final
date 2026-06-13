<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'variant' => 'info',
    'title' => null,
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
    'variant' => 'info',
    'title' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $map = [
        'success' => ['wrap' => 'border-success-200 bg-success-50 dark:border-success-500/30 dark:bg-success-500/10', 'icon' => 'text-success-500', 'symbol' => 'check'],
        'error' => ['wrap' => 'border-error-200 bg-error-50 dark:border-error-500/30 dark:bg-error-500/10', 'icon' => 'text-error-500', 'symbol' => 'x'],
        'warning' => ['wrap' => 'border-warning-200 bg-warning-50 dark:border-warning-500/30 dark:bg-warning-500/10', 'icon' => 'text-warning-500', 'symbol' => '!'],
        'info' => ['wrap' => 'border-blue-light-200 bg-blue-light-50 dark:border-blue-light-500/30 dark:bg-blue-light-500/10', 'icon' => 'text-blue-light-500', 'symbol' => 'i'],
    ][$variant] ?? ['wrap' => 'border-blue-light-200 bg-blue-light-50', 'icon' => 'text-blue-light-500', 'symbol' => 'i'];
?>

<div <?php echo e($attributes->merge(['class' => 'rounded-2xl border p-4 '.$map['wrap']])); ?>>
    <div class="flex items-start gap-3">
        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/80 text-sm font-bold <?php echo e($map['icon']); ?>">
            <?php echo e($map['symbol']); ?>

        </div>
        <div class="flex-1">
            <?php if($title): ?>
                <h4 class="mb-1 text-sm font-semibold text-gray-900 dark:text-white"><?php echo e($title); ?></h4>
            <?php endif; ?>
            <div class="text-sm text-gray-600 dark:text-gray-300">
                <?php echo e($slot); ?>

            </div>
        </div>
    </div>
</div>
<?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/components/ui/alert.blade.php ENDPATH**/ ?>