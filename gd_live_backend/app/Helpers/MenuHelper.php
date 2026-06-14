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
                    $mapped = [
                        'name' => $item['label'],
                        'icon' => $item['icon'],
                        'path' => route($item['route']),
                    ];

                    if (!empty($item['subItems'])) {
                        $mapped['subItems'] = array_map(function (array $subItem): array {
                            return [
                                'name' => $subItem['label'],
                                'path' => route($subItem['route']),
                            ];
                        }, $item['subItems']);
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
