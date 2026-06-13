import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';

import '../../../app/brand/brand.dart';
import '../../../app/widgets/coin_lottie.dart';
import '../../../services/app_settings_service.dart';
import '../controllers/notification_controller.dart';
import '../models/notification_dto.dart';

class NotificationsPage extends StatefulWidget {
  const NotificationsPage({super.key});

  @override
  State<NotificationsPage> createState() => _NotificationsPageState();
}

class _NotificationsPageState extends State<NotificationsPage> {
  late final NotificationsController _controller;
  late final ScrollController _scrollController;
  final TextEditingController _queryController = TextEditingController();
  final Set<String> _selectedIds = <String>{};
  String _activeFilter = 'all';

  @override
  void initState() {
    super.initState();
    _controller = Get.find<NotificationsController>();
    _scrollController = ScrollController()..addListener(_onScroll);
  }

  @override
  void dispose() {
    _scrollController.removeListener(_onScroll);
    _scrollController.dispose();
    _queryController.dispose();
    super.dispose();
  }

  void _onScroll() {
    if (!_scrollController.hasClients) return;
    final position = _scrollController.position;
    if (position.pixels >= position.maxScrollExtent - 220) {
      _controller.loadMore();
    }
  }

  bool get _isSelecting => _selectedIds.isNotEmpty;

  void _toggleSelection(String id) {
    setState(() {
      if (_selectedIds.contains(id)) {
        _selectedIds.remove(id);
      } else {
        _selectedIds.add(id);
      }
    });
  }

  void _clearSelection() {
    setState(_selectedIds.clear);
  }

  Future<void> _markSelectedRead() async {
    if (_selectedIds.isEmpty) return;
    final ids = _selectedIds.toList(growable: false);
    _clearSelection();
    await _controller.markManyRead(ids);
  }

  List<_NotificationSection> _sectionsFor(List<NotificationDto> items) {
    final query = _queryController.text.trim().toLowerCase();
    Iterable<NotificationDto> filtered = items;

    if (query.isNotEmpty) {
      filtered = filtered.where((notification) {
        return notification.title.toLowerCase().contains(query) ||
            notification.body.toLowerCase().contains(query) ||
            notification.type.toLowerCase().contains(query);
      });
    }

    switch (_activeFilter) {
      case 'unread':
        filtered = filtered.where((notification) => notification.isUnread);
        break;
      case 'membership':
        filtered = filtered.where((notification) {
          return notification.type.contains('subscription') ||
              notification.type.contains('gift') ||
              notification.title.toLowerCase().contains('subscription');
        });
        break;
      case 'system':
        filtered = filtered.where((notification) {
          return notification.type.contains('approved') ||
              notification.type.contains('rejected') ||
              notification.type.contains('host') ||
              notification.type.contains('agency');
        });
        break;
      default:
        break;
    }

    final now = DateTime.now();
    final today = DateTime(now.year, now.month, now.day);
    final yesterday = today.subtract(const Duration(days: 1));
    final thisWeek = today.subtract(const Duration(days: 6));

    final todayItems = <NotificationDto>[];
    final yesterdayItems = <NotificationDto>[];
    final weekItems = <NotificationDto>[];
    final earlierItems = <NotificationDto>[];

    for (final item in filtered) {
      final date = DateTime(
        item.createdAt.year,
        item.createdAt.month,
        item.createdAt.day,
      );
      if (date == today) {
        todayItems.add(item);
      } else if (date == yesterday) {
        yesterdayItems.add(item);
      } else if (date.isAfter(thisWeek) || date == thisWeek) {
        weekItems.add(item);
      } else {
        earlierItems.add(item);
      }
    }

    final sections = <_NotificationSection>[];
    if (todayItems.isNotEmpty)
      sections.add(_NotificationSection('Today', todayItems));
    if (yesterdayItems.isNotEmpty) {
      sections.add(_NotificationSection('Yesterday', yesterdayItems));
    }
    if (weekItems.isNotEmpty)
      sections.add(_NotificationSection('This week', weekItems));
    if (earlierItems.isNotEmpty)
      sections.add(_NotificationSection('Earlier', earlierItems));
    return sections;
  }

  @override
  Widget build(BuildContext context) {
    final settings = Get.find<AppSettingsService>();
    return Obx(() {
      final tokens = getBrandTokens(settings.brandKey);
      final unreadCount = _controller.unreadCount.value;
      final sections = _sectionsFor(_controller.items);

      return Scaffold(
        backgroundColor: tokens.backgroundGradient.first,
        appBar: AppBar(
          backgroundColor: Colors.transparent,
          elevation: 0,
          centerTitle: false,
          title: Text(
            _isSelecting ? '${_selectedIds.length} selected' : 'Notifications',
            style: TextStyle(
              color: tokens.textPrimary,
              fontWeight: FontWeight.w800,
            ),
          ),
          actions: [
            if (_isSelecting)
              IconButton(
                onPressed: _clearSelection,
                icon: const Icon(Icons.close_rounded),
              )
            else
              TextButton(
                onPressed:
                    _controller.items.isEmpty
                        ? null
                        : () async {
                          await _controller.markAllRead();
                        },
                child: Text(
                  'Read all',
                  style: TextStyle(
                    color: tokens.primaryButtonGradient.first,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
          ],
        ),
        body: RefreshIndicator(
          onRefresh: _controller.refreshAll,
          color: tokens.primaryButtonGradient.first,
          child: CustomScrollView(
            controller: _scrollController,
            physics: const BouncingScrollPhysics(
              parent: AlwaysScrollableScrollPhysics(),
            ),
            slivers: [
              SliverPadding(
                padding: const EdgeInsets.fromLTRB(18, 4, 18, 0),
                sliver: SliverToBoxAdapter(
                  child: _NotificationsHero(
                    tokens: tokens,
                    unreadCount: unreadCount,
                    totalCount: _controller.items.length,
                    selecting: _isSelecting,
                    onMarkSelected: _markSelectedRead,
                  ),
                ),
              ),
              SliverPadding(
                padding: const EdgeInsets.fromLTRB(18, 14, 18, 0),
                sliver: SliverToBoxAdapter(
                  child: _SearchSurface(
                    tokens: tokens,
                    controller: _queryController,
                    onChanged: (_) => setState(() {}),
                    onClear: () {
                      _queryController.clear();
                      setState(() {});
                    },
                  ),
                ),
              ),
              SliverPadding(
                padding: const EdgeInsets.fromLTRB(18, 12, 18, 0),
                sliver: SliverToBoxAdapter(
                  child: _FilterRail(
                    tokens: tokens,
                    active: _activeFilter,
                    onChanged: (value) => setState(() => _activeFilter = value),
                  ),
                ),
              ),
              if (_controller.loading.value && _controller.items.isEmpty)
                const SliverFillRemaining(
                  hasScrollBody: false,
                  child: Center(child: CircularProgressIndicator()),
                )
              else if (sections.isEmpty)
                SliverFillRemaining(
                  hasScrollBody: false,
                  child: _EmptyNotifications(tokens: tokens),
                )
              else
                SliverPadding(
                  padding: const EdgeInsets.fromLTRB(18, 16, 18, 24),
                  sliver: SliverList(
                    delegate: SliverChildListDelegate([
                      for (final section in sections) ...[
                        _SectionHeading(label: section.label, tokens: tokens),
                        const SizedBox(height: 10),
                        for (final item in section.items) ...[
                          _NotificationLedgerCard(
                            tokens: tokens,
                            notification: item,
                            selected: _selectedIds.contains(item.id),
                            selecting: _isSelecting,
                            onTap: () async {
                              if (_isSelecting) {
                                _toggleSelection(item.id);
                                return;
                              }
                              if (item.isUnread) {
                                await _controller.markRead(item.id);
                              }
                            },
                            onLongPress: () => _toggleSelection(item.id),
                          ),
                          const SizedBox(height: 12),
                        ],
                      ],
                      Center(
                        child: Padding(
                          padding: const EdgeInsets.only(top: 8, bottom: 4),
                          child:
                              _controller.hasMore
                                  ? SizedBox(
                                    width: 28,
                                    height: 28,
                                    child: CircularProgressIndicator(
                                      strokeWidth: 2.5,
                                      color: tokens.primaryButtonGradient.first,
                                    ),
                                  )
                                  : Text(
                                    'You are all caught up',
                                    style: TextStyle(
                                      color: tokens.textSecondary,
                                      fontWeight: FontWeight.w600,
                                    ),
                                  ),
                        ),
                      ),
                    ]),
                  ),
                ),
            ],
          ),
        ),
      );
    });
  }
}

class _NotificationsHero extends StatelessWidget {
  const _NotificationsHero({
    required this.tokens,
    required this.unreadCount,
    required this.totalCount,
    required this.selecting,
    required this.onMarkSelected,
  });

  final BrandTokens tokens;
  final int unreadCount;
  final int totalCount;
  final bool selecting;
  final Future<void> Function() onMarkSelected;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(.94),
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: tokens.borderColor.withOpacity(.45)),
        boxShadow: [
          BoxShadow(
            color: tokens.primaryButtonGradient.first.withOpacity(.22),
            blurRadius: 30,
            offset: const Offset(0, 18),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 52,
                height: 52,
                decoration: BoxDecoration(
                  color: tokens.primaryButtonGradient.first.withOpacity(.12),
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Icon(
                  Icons.notifications_active_rounded,
                  color: tokens.primaryButtonGradient.first,
                  size: 28,
                ),
              ),
              const Spacer(),
              if (selecting)
                FilledButton.tonal(
                  onPressed: onMarkSelected,
                  style: FilledButton.styleFrom(
                    backgroundColor: tokens.primaryButtonGradient.first,
                    foregroundColor: Colors.white,
                  ),
                  child: const Text('Mark read'),
                )
              else
                _HeroMetric(
                  label: 'Unread',
                  value: '$unreadCount',
                  tokens: tokens,
                ),
            ],
          ),
          const SizedBox(height: 14),
          Text(
            'Inbox',
            style: Theme.of(context).textTheme.headlineSmall?.copyWith(
              color: tokens.textPrimary,
              fontWeight: FontWeight.w800,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            selecting
                ? 'Batch actions for selected items.'
                : '$totalCount recent alerts across your account.',
            style: Theme.of(context).textTheme.bodyMedium?.copyWith(
              color: tokens.textSecondary,
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }
}

class _HeroMetric extends StatelessWidget {
  const _HeroMetric({
    required this.label,
    required this.value,
    required this.tokens,
  });

  final String label;
  final String value;
  final BrandTokens tokens;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        Text(
          value,
          style: Theme.of(context).textTheme.titleLarge?.copyWith(
            color: tokens.textPrimary,
            fontWeight: FontWeight.w800,
          ),
        ),
        Text(
          label,
          style: Theme.of(context).textTheme.bodySmall?.copyWith(
            color: tokens.textSecondary,
            fontWeight: FontWeight.w600,
          ),
        ),
      ],
    );
  }
}

class _SearchSurface extends StatelessWidget {
  const _SearchSurface({
    required this.tokens,
    required this.controller,
    required this.onChanged,
    required this.onClear,
  });

  final BrandTokens tokens;
  final TextEditingController controller;
  final ValueChanged<String> onChanged;
  final VoidCallback onClear;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 2),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(.86),
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: tokens.borderColor.withOpacity(.6)),
      ),
      child: TextField(
        controller: controller,
        onChanged: onChanged,
        decoration: InputDecoration(
          border: InputBorder.none,
          prefixIcon: Icon(Icons.search_rounded, color: tokens.textSecondary),
          hintText: 'Search alerts',
          suffixIcon:
              controller.text.isEmpty
                  ? null
                  : IconButton(
                    onPressed: onClear,
                    icon: Icon(
                      Icons.close_rounded,
                      color: tokens.textSecondary,
                    ),
                  ),
        ),
      ),
    );
  }
}

class _FilterRail extends StatelessWidget {
  const _FilterRail({
    required this.tokens,
    required this.active,
    required this.onChanged,
  });

  final BrandTokens tokens;
  final String active;
  final ValueChanged<String> onChanged;

  @override
  Widget build(BuildContext context) {
    final items = <(String, String)>[
      ('all', 'All'),
      ('unread', 'Unread'),
      ('membership', 'Membership'),
      ('system', 'Approvals'),
    ];

    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      child: Row(
        children: [
          for (final item in items) ...[
            Padding(
              padding: const EdgeInsets.only(right: 10),
              child: GestureDetector(
                onTap: () => onChanged(item.$1),
                child: AnimatedContainer(
                  duration: const Duration(milliseconds: 220),
                  curve: Curves.easeOutCubic,
                  padding: const EdgeInsets.symmetric(
                    horizontal: 16,
                    vertical: 12,
                  ),
                  decoration: BoxDecoration(
                    color:
                        active == item.$1
                            ? tokens.primaryButtonGradient.first
                            : Colors.white.withOpacity(.82),
                    borderRadius: BorderRadius.circular(999),
                    border: Border.all(
                      color:
                          active == item.$1
                              ? tokens.primaryButtonGradient.first
                              : tokens.borderColor.withOpacity(.6),
                    ),
                  ),
                  child: Text(
                    item.$2,
                    style: TextStyle(
                      color:
                          active == item.$1
                              ? Colors.white
                              : tokens.textSecondary,
                      fontWeight: FontWeight.w700,
                    ),
                  ),
                ),
              ),
            ),
          ],
        ],
      ),
    );
  }
}

class _SectionHeading extends StatelessWidget {
  const _SectionHeading({required this.label, required this.tokens});

  final String label;
  final BrandTokens tokens;

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Text(
          label,
          style: Theme.of(context).textTheme.bodySmall?.copyWith(
            color: tokens.textSecondary,
            fontWeight: FontWeight.w800,
            letterSpacing: .4,
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: Container(
            height: 1,
            color: tokens.borderColor.withOpacity(.35),
          ),
        ),
      ],
    );
  }
}

class _NotificationLedgerCard extends StatelessWidget {
  const _NotificationLedgerCard({
    required this.tokens,
    required this.notification,
    required this.selected,
    required this.selecting,
    required this.onTap,
    required this.onLongPress,
  });

  final BrandTokens tokens;
  final NotificationDto notification;
  final bool selected;
  final bool selecting;
  final VoidCallback onTap;
  final VoidCallback onLongPress;

  @override
  Widget build(BuildContext context) {
    final visual = _notificationVisual(notification);
    final time = DateFormat('hh:mm a').format(notification.createdAt.toLocal());

    return Material(
      color: Colors.transparent,
      child: InkWell(
        borderRadius: BorderRadius.circular(24),
        onTap: onTap,
        onLongPress: onLongPress,
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 200),
          curve: Curves.easeOutCubic,
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color:
                selected
                    ? visual.accent.withOpacity(.12)
                    : Colors.white.withOpacity(
                      notification.isUnread ? .95 : .82,
                    ),
            borderRadius: BorderRadius.circular(24),
            border: Border.all(
              color:
                  selected
                      ? visual.accent.withOpacity(.45)
                      : tokens.borderColor.withOpacity(
                        notification.isUnread ? .6 : .3,
                      ),
              width: selected ? 1.4 : 1,
            ),
            boxShadow:
                notification.isUnread
                    ? [
                      BoxShadow(
                        color: visual.accent.withOpacity(.12),
                        blurRadius: 22,
                        offset: const Offset(0, 12),
                      ),
                    ]
                    : null,
          ),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 4,
                height: 96,
                margin: const EdgeInsets.only(right: 12, top: 2),
                decoration: BoxDecoration(
                  color: visual.accent.withOpacity(
                    notification.isUnread ? .72 : .28,
                  ),
                  borderRadius: BorderRadius.circular(999),
                ),
              ),
              Container(
                width: 46,
                height: 46,
                decoration: BoxDecoration(
                  color: visual.accent.withOpacity(.12),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: Icon(visual.icon, color: visual.accent, size: 24),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Expanded(
                          child: Text(
                            notification.title,
                            style: Theme.of(
                              context,
                            ).textTheme.titleMedium?.copyWith(
                              color: tokens.textPrimary,
                              fontWeight:
                                  notification.isUnread
                                      ? FontWeight.w800
                                      : FontWeight.w700,
                            ),
                          ),
                        ),
                        const SizedBox(width: 10),
                        Text(
                          time,
                          style: Theme.of(
                            context,
                          ).textTheme.bodySmall?.copyWith(
                            color: tokens.textSecondary,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 6),
                    Text(
                      notification.body,
                      style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                        color: tokens.textSecondary,
                        height: 1.35,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                    const SizedBox(height: 10),
                    Row(
                      children: [
                        Container(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 10,
                            vertical: 6,
                          ),
                          decoration: BoxDecoration(
                            color: visual.accent.withOpacity(.12),
                            borderRadius: BorderRadius.circular(999),
                          ),
                          child: Text(
                            visual.label,
                            style: TextStyle(
                              color: visual.accent,
                              fontWeight: FontWeight.w700,
                              fontSize: 12,
                            ),
                          ),
                        ),
                        const Spacer(),
                        if (selecting)
                          Icon(
                            selected
                                ? Icons.check_circle_rounded
                                : Icons.radio_button_unchecked_rounded,
                            color:
                                selected ? visual.accent : tokens.textSecondary,
                          )
                        else if (notification.isUnread)
                          Container(
                            width: 10,
                            height: 10,
                            decoration: BoxDecoration(
                              color: visual.accent,
                              shape: BoxShape.circle,
                            ),
                          ),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _EmptyNotifications extends StatelessWidget {
  const _EmptyNotifications({required this.tokens});

  final BrandTokens tokens;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 28),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Container(
              width: 116,
              height: 116,
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(32),
                boxShadow: [
                  BoxShadow(
                    color: tokens.primaryButtonGradient.first.withOpacity(.12),
                    blurRadius: 30,
                    offset: const Offset(0, 16),
                  ),
                ],
              ),
              child: const Center(
                child: GdLottie(
                  asset: GdLottieAssets.chat,
                  width: 86,
                  height: 86,
                ),
              ),
            ),
            const SizedBox(height: 18),
            Text(
              'Nothing yet',
              style: Theme.of(context).textTheme.titleLarge?.copyWith(
                color: tokens.textPrimary,
                fontWeight: FontWeight.w800,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Approvals, gifts, and system alerts will appear here.',
              textAlign: TextAlign.center,
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                color: tokens.textSecondary,
                height: 1.4,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _NotificationSection {
  const _NotificationSection(this.label, this.items);

  final String label;
  final List<NotificationDto> items;
}

class _NotificationVisual {
  const _NotificationVisual({
    required this.icon,
    required this.accent,
    required this.label,
  });

  final IconData icon;
  final Color accent;
  final String label;
}

_NotificationVisual _notificationVisual(NotificationDto notification) {
  final type = notification.type.toLowerCase();
  if (type.contains('subscription') || type.contains('gift')) {
    return const _NotificationVisual(
      icon: Icons.workspace_premium_rounded,
      accent: Color(0xFF7B4DFF),
      label: 'Membership',
    );
  }
  if (type.contains('approved') ||
      type.contains('host') ||
      type.contains('agency')) {
    return const _NotificationVisual(
      icon: Icons.verified_rounded,
      accent: Color(0xFF06B430),
      label: 'Approval',
    );
  }
  if (type.contains('rejected') || type.contains('blocked')) {
    return const _NotificationVisual(
      icon: Icons.gpp_bad_rounded,
      accent: Color(0xFFE35D6A),
      label: 'Alert',
    );
  }
  return const _NotificationVisual(
    icon: Icons.campaign_rounded,
    accent: Color(0xFF0F9D58),
    label: 'Update',
  );
}
