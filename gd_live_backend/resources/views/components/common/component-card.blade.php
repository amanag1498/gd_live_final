@props([
    'title' => null,
    'desc' => null,
    'padding' => 'default',
])

@php
    $bodyPadding = $padding === 'compact' ? 'p-4 sm:p-5' : 'p-5 sm:p-6';
@endphp

<section {{ $attributes->merge(['class' => 'overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900']) }}>
    @if($title || $desc || isset($header))
        <div class="border-b border-gray-100 px-5 py-4 dark:border-gray-800 sm:px-6">
            @if (isset($header))
                {{ $header }}
            @else
                @if($title)
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
                @endif
                @if($desc)
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $desc }}</p>
                @endif
            @endif
        </div>
    @endif

    <div class="{{ $bodyPadding }}">
        {{ $slot }}
    </div>

    @if (isset($footer))
        <div class="border-t border-gray-100 px-5 py-4 dark:border-gray-800 sm:px-6">
            {{ $footer }}
        </div>
    @endif
</section>
