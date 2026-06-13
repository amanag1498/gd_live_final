@props([
    'variant' => 'light',
    'color' => 'primary',
    'size' => 'md',
])

@php
    $base = 'inline-flex items-center justify-center gap-1 rounded-full font-medium capitalize';
    $sizeMap = [
        'sm' => 'px-2 py-0.5 text-[11px]',
        'md' => 'px-2.5 py-1 text-xs',
    ];

    $variants = [
        'light' => [
            'primary' => 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-300',
            'success' => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-300',
            'error' => 'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-300',
            'warning' => 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-300',
            'info' => 'bg-blue-light-50 text-blue-light-700 dark:bg-blue-light-500/15 dark:text-blue-light-300',
            'dark' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-200',
        ],
        'solid' => [
            'primary' => 'bg-brand-500 text-white',
            'success' => 'bg-success-500 text-white',
            'error' => 'bg-error-500 text-white',
            'warning' => 'bg-warning-400 text-gray-900',
            'info' => 'bg-blue-light-500 text-white',
            'dark' => 'bg-gray-900 text-white dark:bg-gray-700',
        ],
    ];
@endphp

<span {{ $attributes->merge(['class' => trim($base.' '.($sizeMap[$size] ?? $sizeMap['md']).' '.($variants[$variant][$color] ?? $variants['light']['primary']).' '.$attributes->get('class'))]) }}>
    {{ $slot }}
</span>
