@props([
    'src' => null,
    'alt' => 'Avatar',
    'size' => 'md',
    'status' => null,
    'initials' => null,
])

@php
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
@endphp

<div class="relative {{ $sizeMap[$size] ?? $sizeMap['md'] }}">
    @if($src)
        <img src="{{ $src }}" alt="{{ $alt }}" class="h-full w-full rounded-full object-cover" />
    @else
        <div class="flex h-full w-full items-center justify-center rounded-full bg-brand-50 font-semibold text-brand-600 dark:bg-brand-500/15 dark:text-brand-300">
            {{ $initials ?: strtoupper(substr($alt, 0, 1)) }}
        </div>
    @endif

    @if($status)
        <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white dark:border-gray-900 {{ $statusMap[$status] ?? $statusMap['offline'] }}"></span>
    @endif
</div>
