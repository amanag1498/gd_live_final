<?php

namespace App\Helpers;

use App\Support\PanelNavigation;

class MenuHelper
{
    public static function getMenuGroups(?string $context = null): array
    {
        $context ??= request()->routeIs('agency.*') ? 'agency' : 'admin';
        $groups = $context === 'agency' ? PanelNavigation::agency() : PanelNavigation::admin();

        return array_map(function (array $group): array {
            return [
                'title' => $group['title'],
                'items' => array_map(function (array $item): array {
                    return [
                        'name' => $item['label'],
                        'icon' => $item['icon'],
                        'path' => route($item['route']),
                    ];
                }, $group['items']),
            ];
        }, $groups);
    }

    public static function getIconSvg(string $icon): string
    {
        return PanelNavigation::iconSvg($icon);
    }
}
