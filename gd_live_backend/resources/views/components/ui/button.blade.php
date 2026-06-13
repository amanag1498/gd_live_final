@props([
    'as' => 'button',
    'size' => 'md',
    'variant' => 'primary',
    'href' => null,
    'type' => 'button',
])

@php
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
@endphp

@if ($tag === 'a')
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <{{ $tag }} {{ $attributes->merge(['class' => $classes, 'type' => $type]) }}>
        {{ $slot }}
    </{{ $tag }}>
@endif
