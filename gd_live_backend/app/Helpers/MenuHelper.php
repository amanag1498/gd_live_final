<?php

namespace App\Helpers;

use App\Support\PanelNavigation;
use Illuminate\Support\Facades\Route;

class MenuHelper
{
    public static function getMenuGroups(?string $context = null): array
    {
        $context ??= request()->routeIs('agency.*') ? 'agency' : 'admin';
        $groups = $context === 'agency' ? PanelNavigation::agency() : PanelNavigation::admin();

        // A newly deployed menu item can briefly precede a refreshed Laravel
        // route cache. Omit unavailable links instead of breaking the complete
        // authenticated panel with a RouteNotFoundException.
        $groups = array_map(function (array $group): array {
            $group['items'] = array_values(array_filter(
                $group['items'],
                fn (array $item): bool => Route::has($item['route'])
            ));

            return $group;
        }, $groups);
        $groups = array_values(array_filter(
            $groups,
            fn (array $group): bool => $group['items'] !== []
        ));

        return array_map(function (array $group): array {
            return [
                'title' => $group['title'],
                'items' => array_map(function (array $item): array {
                    $mapped = [
                        'name' => $item['label'],
                        'icon' => $item['icon'],
                        'path' => route($item['route']),
                    ];

                    if (! empty($item['subItems'])) {
                        $mapped['subItems'] = array_map(function (array $subItem): array {
                            return [
                                'name' => $subItem['label'],
                                'path' => route($subItem['route']),
                            ];
                        }, array_values(array_filter(
                            $item['subItems'],
                            fn (array $subItem): bool => Route::has($subItem['route'])
                        )));
                    }

                    return $mapped;
                }, $group['items']),
            ];
        }, $groups);
    }

    public static function getIconSvg(string $icon): string
    {
        return PanelNavigation::iconSvg($icon);
    }
}
