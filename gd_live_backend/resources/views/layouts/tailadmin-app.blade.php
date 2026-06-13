<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $defaultTitle ?? 'GD Live Panel')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('images/logo/logo-icon.svg') }}" type="image/svg+xml">
    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/admin.js'])
</head>
<body
    x-data="{ loaded: true }"
    x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280;
    const checkMobile = () => {
        if (window.innerWidth < 1280) {
            $store.sidebar.setMobileOpen(false);
            $store.sidebar.isExpanded = false;
        } else {
            $store.sidebar.isMobileOpen = false;
            $store.sidebar.isExpanded = true;
        }
    };
    window.addEventListener('resize', checkMobile);"
>
    <div class="min-h-screen xl:flex">
        @include('layouts.backdrop')
        @include('layouts.sidebar', ['menuContext' => $menuContext ?? 'admin', 'homeRoute' => $homeRoute ?? url('/')])

        <div class="flex-1 transition-all duration-300 ease-in-out"
            :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'xl:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
                'ml-0': $store.sidebar.isMobileOpen
            }">
            @include('layouts.app-header', [
                'panelLabel' => $panelLabel ?? 'Panel',
                'defaultTitle' => $defaultTitle ?? 'GD Live',
                'roleLabel' => $roleLabel ?? 'User',
            ])

            <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                @if (session('ok'))
                    <x-ui.alert variant="success" title="Success">{{ session('ok') }}</x-ui.alert>
                @endif
                @if (session('err'))
                    <x-ui.alert variant="error" title="Error">{{ session('err') }}</x-ui.alert>
                @endif
                @if ($errors->any())
                    <x-ui.alert variant="error" title="Please fix the following">
                        <ul class="list-disc pl-4">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-ui.alert>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
</body>
@stack('scripts')
</html>
