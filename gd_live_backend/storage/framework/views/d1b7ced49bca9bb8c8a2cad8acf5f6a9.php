<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'compact' => false,
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
    'compact' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div <?php echo e($attributes->class(['flex items-center gap-3'])); ?>>
    <div class="<?php echo e($compact ? 'h-10 w-10 rounded-2xl' : 'h-14 w-14 rounded-3xl'); ?> flex shrink-0 items-center justify-center bg-linear-to-br from-brand-500 via-brand-600 to-gray-950 text-white shadow-lg shadow-brand-500/20">
        <span class="<?php echo e($compact ? 'text-base' : 'text-xl'); ?> font-bold tracking-[0.2em]">GD</span>
    </div>
    <div x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen || <?php echo e($compact ? 'true' : 'false'); ?>">
        <div class="text-[11px] font-semibold uppercase tracking-[0.28em] text-gray-400">Control</div>
        <div class="<?php echo e($compact ? 'text-base' : 'text-lg'); ?> font-semibold text-gray-900 dark:text-white">GD Live</div>
    </div>
</div>
<?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/components/layout/brand-mark.blade.php ENDPATH**/ ?>