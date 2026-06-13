<?php

namespace App\Support;

class PanelNavigation
{
    public static function admin(): array
    {
        return [
            [
                'title' => 'Overview',
                'items' => [
                    self::item('Dashboard', 'admin.dashboard', 'home', ['admin.dashboard']),
                    self::item('Presence', 'admin.presence.index', 'pulse', ['admin.presence.*']),
                    self::item('Notifications', 'admin.notifications.index', 'bell', ['admin.notifications.*']),
                ],
            ],
            [
                'title' => 'People',
                'items' => [
                    self::item('Users', 'admin.users.index', 'users', ['admin.users.*']),
                    self::item('Hosts', 'admin.hosts.index', 'mic', ['admin.hosts.*']),
                    self::item('Agencies', 'admin.agencies.index', 'building', ['admin.agencies.*']),
                    self::item('Agency Requests', 'admin.agency-requests.index', 'clipboard', ['admin.agency-requests.*']),
                    self::item('Host Requests', 'admin.host-requests.index', 'clipboard', ['admin.host-requests.*']),
                ],
            ],
            [
                'title' => 'Live',
                'items' => [
                    self::item('Live Rooms', 'admin.live-rooms.index', 'video', ['admin.live-rooms.*']),
                    self::item('PK Battles', 'admin.pk-battles.index', 'sparkles', ['admin.pk-battles.*']),
                    self::item('Moderation', 'admin.moderation.reports', 'shield', ['admin.moderation.*']),
                    self::item('Call Reports', 'admin.calls.index', 'phone', ['admin.calls.*']),
                ],
            ],
            [
                'title' => 'Economy',
                'items' => [
                    self::item('Wallets', 'admin.wallets.index', 'wallet', ['admin.wallets.*']),
                    self::item('Recharge Plans', 'admin.recharge-plans.index', 'credit-card', ['admin.recharge-plans.*']),
                    self::item('Recharge Audit', 'admin.recharge-audit.index', 'receipt', ['admin.recharge-audit.*']),
                    self::item('Gifts', 'admin.gifts.index', 'gift', ['admin.gifts.*']),
                    self::item('Entry Packs', 'admin.entry-packs.index', 'package', ['admin.entry-packs.index', 'admin.entry-packs.create', 'admin.entry-packs.edit', 'admin.entry-packs.store', 'admin.entry-packs.update', 'admin.entry-packs.destroy']),
                    self::item('Entry Ownership', 'admin.entry-packs.reports', 'star', ['admin.entry-packs.reports', 'admin.entry-packs.purchases.*']),
                    self::item('Subscription Plans', 'admin.subscription-plans.index', 'badge', ['admin.subscription-plans.*']),
                    self::item('User Subscriptions', 'admin.user-subscriptions.index', 'user-check', ['admin.user-subscriptions.*']),
                    self::item('Payout Reports', 'admin.agency-payout-reports.index', 'coins', ['admin.agency-payout-reports.*']),
                ],
            ],
            [
                'title' => 'Reports',
                'items' => [
                    self::item('Hosts Report', 'admin.reports.hosts', 'chart', ['admin.reports.hosts*']),
                    self::item('Agencies Report', 'admin.reports.agencies', 'chart', ['admin.reports.agencies*']),
                    self::item('Agency Wallets', 'admin.reports.agency-wallets.index', 'chart', ['admin.reports.agency-wallets.*']),
                    self::item('Followers Report', 'admin.reports.host-followers', 'chart', ['admin.reports.host-followers*']),
                    self::item('Leaderboards', 'admin.reports.leaderboards', 'chart', ['admin.reports.leaderboards*']),
                    self::item('Levels Report', 'admin.reports.levels', 'chart', ['admin.reports.levels']),
                ],
            ],
            [
                'title' => 'Catalog',
                'items' => [
                    self::item('Levels', 'admin.levels.index', 'layers', ['admin.levels.*']),
                    self::item('Banners', 'admin.banners.index', 'image', ['admin.banners.*']),
                ],
            ],
            [
                'title' => 'Games',
                'items' => [
                    self::item('Teen Patti', 'admin.games.teen-patti.dashboard', 'gamepad', ['admin.games.teen-patti.*']),
                    self::item('Greedy', 'admin.games.greedy.dashboard', 'gamepad', ['admin.games.greedy.*']),
                ],
            ],
            [
                'title' => 'Settings',
                'items' => [
                    self::item('App Settings', 'admin.settings.app.edit', 'cog', ['admin.settings.app.*']),
                    self::item('Live Rooms', 'admin.settings.live-rooms.edit', 'cog', ['admin.settings.live-rooms.*']),
                    self::item('Call Settings', 'admin.settings.calls.edit', 'cog', ['admin.settings.calls.*']),
                    self::item('Game Settings', 'admin.settings.games.edit', 'cog', ['admin.settings.games.*']),
                ],
            ],
        ];
    }

    public static function agency(): array
    {
        return [
            [
                'title' => 'Agency',
                'items' => [
                    self::item('Dashboard', 'agency.dashboard', 'home', ['agency.dashboard']),
                    self::item('Profile', 'agency.profile.show', 'user', ['agency.profile.*']),
                ],
            ],
            [
                'title' => 'Operations',
                'items' => [
                    self::item('Hosts', 'agency.hosts.index', 'users', ['agency.hosts.*']),
                    self::item('Calls', 'agency.calls.index', 'phone', ['agency.calls.*']),
                    self::item('Video Rooms', 'agency.video-rooms.index', 'video', ['agency.video-rooms.*']),
                    self::item('PK Battles', 'agency.pk-battles.index', 'sparkles', ['agency.pk-battles.*']),
                ],
            ],
            [
                'title' => 'Finance',
                'items' => [
                    self::item('Wallet', 'agency.wallet.show', 'wallet', ['agency.wallet.*']),
                    self::item('Payout Reports', 'agency.payout-reports.index', 'coins', ['agency.payout-reports.*']),
                ],
            ],
        ];
    }

    public static function iconSvg(string $icon): string
    {
        return match ($icon) {
            'home' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5.5v-6h-5v6H4a1 1 0 0 1-1-1v-9.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'pulse' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M3 12h4l2.5-5 4 10 2.5-5H21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'bell' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M6 9a6 6 0 1 1 12 0v4l1.5 2.5H4.5L6 13V9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M10 19a2 2 0 0 0 4 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'users' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M16 19a4 4 0 0 0-8 0M12 12a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM18.5 18a3.5 3.5 0 0 0-2.2-3.25M17 11.5a2.5 2.5 0 1 0 0-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'mic' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M12 15a3 3 0 0 0 3-3V7a3 3 0 1 0-6 0v5a3 3 0 0 0 3 3Z" stroke="currentColor" stroke-width="1.8"/><path d="M6.5 11.5a5.5 5.5 0 1 0 11 0M12 17v4M8.5 21h7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'building' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M4 21V5a1 1 0 0 1 1-1h8a1 1 0 0 1 1 1v16M14 8h5a1 1 0 0 1 1 1v12M8 8h2M8 12h2M8 16h2M16 12h2M16 16h2M9 21v-3h2v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'clipboard' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M9 4h6l1 2h2a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h2l1-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 11h6M9 15h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'video' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M3 7a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7Z" stroke="currentColor" stroke-width="1.8"/><path d="m16 10 5-3v10l-5-3v-4Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'sparkles' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="m12 3 1.8 4.7L18.5 9.5l-4.7 1.8L12 16l-1.8-4.7L5.5 9.5l4.7-1.8L12 3ZM19 15l.9 2.1L22 18l-2.1.9L19 21l-.9-2.1L16 18l2.1-.9L19 15Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>',
            'shield' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M12 3 5 6v5c0 5 3.4 8 7 10 3.6-2 7-5 7-10V6l-7-3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'phone' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M6 4h4l1 5-2.5 1.5a16 16 0 0 0 5 5L15 13l5 1v4a2 2 0 0 1-2 2h-1C9.8 20 4 14.2 4 7V6a2 2 0 0 1 2-2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'wallet' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M4 7a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v2H6a2 2 0 1 0 0 4h13v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7Z" stroke="currentColor" stroke-width="1.8"/><path d="M19 9h1a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1h-1V9ZM6 11h1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'credit-card' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M3 9h18M7 15h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'receipt' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M7 3h10v18l-2-1.5L12 21l-3-1.5L7 21V3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M9 8h6M9 12h6M9 16h4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'gift' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M4 9h16v4H4zM6 13h12v7H6zM12 9v11M12 9H8.5a2.5 2.5 0 1 1 0-5C11 4 12 9 12 9ZM12 9h3.5a2.5 2.5 0 1 0 0-5C13 4 12 9 12 9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'package' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="m12 3 8 4.5v9L12 21 4 16.5v-9L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m4 7.5 8 4.5 8-4.5M12 12v9" stroke="currentColor" stroke-width="1.8"/></svg>',
            'badge' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M12 3 9.5 5.5 6 6l.5 3.5L5 13l3 1.5L9 18l3-1.5 3 1.5 1-3.5L19 13l-1.5-3.5L18 6l-3.5-.5L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'coins' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><ellipse cx="12" cy="7" rx="6" ry="3" stroke="currentColor" stroke-width="1.8"/><path d="M6 7v5c0 1.7 2.7 3 6 3s6-1.3 6-3V7M6 12v5c0 1.7 2.7 3 6 3s6-1.3 6-3v-5" stroke="currentColor" stroke-width="1.8"/></svg>',
            'chart' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M4 19h16M7 16V9M12 16V5M17 16v-3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'layers' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="m12 4 8 4-8 4-8-4 8-4ZM4 12l8 4 8-4M4 16l8 4 8-4" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>',
            'image' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="m7 15 3-3 2 2 3-4 4 5M9 9.5h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>',
            'gamepad' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M7 10h10a4 4 0 0 1 3.8 5.2l-1.3 4a2 2 0 0 1-3.3.8L13.5 17h-3L7.8 20a2 2 0 0 1-3.3-.8l-1.3-4A4 4 0 0 1 7 10Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M8 13h3M9.5 11.5v3M15.5 13h.01M17.5 11.5h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            'cog' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="m12 3 1.3 2.6 2.9.4.4 2.9L19 10l-1.4 2.1.4 2.9-2.9.4L12 18l-2.1-1.4-2.9.4-.4-2.9L4 12l1.4-2.1-.4-2.9 2.9-.4L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>',
            'user' => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM5 20a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>',
            default => '<svg viewBox="0 0 24 24" fill="none" class="h-5 w-5"><circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/></svg>',
        };
    }

    private static function item(string $label, string $route, string $icon, array $match): array
    {
        return [
            'label' => $label,
            'route' => $route,
            'icon' => $icon,
            'match' => $match,
        ];
    }
}
