@props([
    'variant' => 'info',
    'title' => null,
])

@php
    $map = [
        'success' => ['wrap' => 'border-success-200 bg-success-50 dark:border-success-500/30 dark:bg-success-500/10', 'icon' => 'text-success-500', 'symbol' => 'check'],
        'error' => ['wrap' => 'border-error-200 bg-error-50 dark:border-error-500/30 dark:bg-error-500/10', 'icon' => 'text-error-500', 'symbol' => 'x'],
        'warning' => ['wrap' => 'border-warning-200 bg-warning-50 dark:border-warning-500/30 dark:bg-warning-500/10', 'icon' => 'text-warning-500', 'symbol' => '!'],
        'info' => ['wrap' => 'border-blue-light-200 bg-blue-light-50 dark:border-blue-light-500/30 dark:bg-blue-light-500/10', 'icon' => 'text-blue-light-500', 'symbol' => 'i'],
    ][$variant] ?? ['wrap' => 'border-blue-light-200 bg-blue-light-50', 'icon' => 'text-blue-light-500', 'symbol' => 'i'];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-2xl border p-4 '.$map['wrap']]) }}>
    <div class="flex items-start gap-3">
        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/80 text-sm font-bold {{ $map['icon'] }}">
            {{ $map['symbol'] }}
        </div>
        <div class="flex-1">
            @if($title)
                <h4 class="mb-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $title }}</h4>
            @endif
            <div class="text-sm text-gray-600 dark:text-gray-300">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
