@props([
    'pageTitle',
    'items' => [],
])

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <div>
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $pageTitle }}</h2>
        @if ($slot->isNotEmpty())
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $slot }}</p>
        @endif
    </div>

    @if ($items)
        <nav>
            <ol class="flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
                @foreach ($items as $index => $item)
                    <li class="inline-flex items-center gap-1.5">
                        @if (!empty($item['href']))
                            <a href="{{ $item['href'] }}" class="hover:text-gray-700 dark:hover:text-gray-200">{{ $item['label'] }}</a>
                        @else
                            <span class="text-gray-900 dark:text-white">{{ $item['label'] }}</span>
                        @endif
                        @if (!$loop->last)
                            <span>/</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    @endif
</div>
