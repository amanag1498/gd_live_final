@props([
    'label',
    'value',
    'meta' => null,
    'tone' => 'default',
    'icon' => null,
])

@php
    $tones = [
        'default' => 'border-gray-200 bg-white text-gray-900 dark:border-gray-800 dark:bg-gray-900 dark:text-white',
        'brand' => 'border-brand-500/20 bg-brand-500 text-white',
        'dark' => 'border-gray-900 bg-gray-900 text-white dark:border-gray-700 dark:bg-gray-800',
        'success' => 'border-success-200 bg-success-50 text-gray-900 dark:border-success-500/30 dark:bg-success-500/10 dark:text-white',
        'warning' => 'border-warning-200 bg-warning-50 text-gray-900 dark:border-warning-500/30 dark:bg-warning-500/10 dark:text-white',
        'danger' => 'border-error-200 bg-error-50 text-gray-900 dark:border-error-500/30 dark:bg-error-500/10 dark:text-white',
    ];

    $chip = match ($tone) {
        'brand' => 'bg-white/15 text-white',
        'dark' => 'bg-white/10 text-white',
        'success' => 'bg-success-100 text-success-700 dark:bg-success-500/20 dark:text-success-300',
        'warning' => 'bg-warning-100 text-warning-700 dark:bg-warning-500/20 dark:text-warning-300',
        'danger' => 'bg-error-100 text-error-700 dark:bg-error-500/20 dark:text-error-300',
        default => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300',
    };

    $metaColor = in_array($tone, ['brand', 'dark']) ? 'text-white/75' : 'text-gray-500 dark:text-gray-400';
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border p-5 '.$tones[$tone]]) }}>
    <div class="flex items-start justify-between gap-4">
        <div>
            <div class="text-sm font-medium {{ $metaColor }}">{{ $label }}</div>
            <div class="mt-3 text-3xl font-semibold tracking-tight">{{ $value }}</div>
            @if($meta)
                <div class="mt-3 text-sm {{ $metaColor }}">{{ $meta }}</div>
            @endif
        </div>
        @if($icon)
            <div class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $chip }}">
                {!! $icon !!}
            </div>
        @endif
    </div>
</div>
