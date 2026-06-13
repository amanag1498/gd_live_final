import 'dart:async';
import 'dart:math' as math;
import 'dart:ui';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../app/routes/app_urls.dart';
import '../../../app/routes/app_routes.dart';
import '../../../app/widgets/coin_lottie.dart';
import '../../../app/widgets/gd_modal_surface.dart';
import '../../../app/widgets/haptics.dart';
import '../../../services/auth_service.dart';
import '../../../services/app_settings_service.dart';
import '../../../app/brand/brand.dart';
import '../../../services/api_client.dart';
import '../../../app/utils/avatar_url.dart';
import '../../profile/controllers/profile_controller.dart';
import '../../entry_packs/models/user_entry_pack_dto.dart';
import '../../entry_packs/services/entry_pack_api.dart';
import '../../subscriptions/models/user_subscription_dto.dart';
import '../../subscriptions/services/subscriptions_api.dart';
import '../../entry_packs/widgets/entry_pack_bottom_sheet.dart';
import '../../wallet/models/wallet_summary_dto.dart';
import '../../wallet/services/wallet_api.dart';
import '../../wallet/widgets/recharge_bottom_sheet.dart';

BrandTokens _settingsTokens() {
  final settings = Get.find<AppSettingsService>();
  return getBrandTokens(settings.brandKey);
}

class SettingsPage extends StatefulWidget {
  final double bottomPadding;
  const SettingsPage({super.key, required this.bottomPadding});

  @override
  State<SettingsPage> createState() => _SettingsPageState();
}

class _SettingsPageState extends State<SettingsPage>
    with SingleTickerProviderStateMixin, WidgetsBindingObserver {
  late final AnimationController _bgMotion;
  late final ProfileController _profileController;
  late final SubscriptionsApi _subscriptionsApi;
  late final EntryPackApi _entryPackApi;
  late final WalletApi _walletApi;
  String? _subscriptionMeta;
  String? _entryMeta;
  String? _walletMeta;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    _profileController = Get.find<ProfileController>();
    _subscriptionsApi = SubscriptionsApi(Get.find<ApiClient>());
    _entryPackApi = Get.find<EntryPackApi>();
    _walletApi = Get.find<WalletApi>();
    _bgMotion = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 18),
    )..repeat();
    unawaited(_profileController.load());
    unawaited(_loadInlineMeta());
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    _bgMotion.dispose();
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      unawaited(_profileController.load());
      unawaited(_loadInlineMeta());
    }
  }

  Future<void> _loadInlineMeta() async {
    try {
      final results = await Future.wait([
        _subscriptionsApi.mySubscriptions(),
        _entryPackApi.fetchMine(),
        _walletApi.fetchSummary(),
        _walletApi.fetchRechargeOrders(),
      ]);
      if (!mounted) return;

      final subscriptions = results[0] as List<UserSubscriptionDto>;
      final entryState = results[1] as EntryPackStateDto;
      final walletSummary = results[2] as WalletSummaryDto;
      final orders = results[3] as List<dynamic>;

      final activeSubscription =
          subscriptions.where((s) => s.isActiveNow).toList()..sort(
            (a, b) => (b.endsAt ?? DateTime.fromMillisecondsSinceEpoch(0))
                .compareTo(a.endsAt ?? DateTime.fromMillisecondsSinceEpoch(0)),
          );

      final activePlan =
          activeSubscription.isNotEmpty ? activeSubscription.first : null;
      final subscriptionMeta =
          activePlan != null
              ? '${activePlan.planName ?? 'Plan'} · ${_formatShortDate(activePlan.endsAt)}'
              : subscriptions.isNotEmpty
              ? '${subscriptions.length} in history'
              : 'No active plan';

      final entryMeta =
          entryState.active != null
              ? entryState.active!.entryPack?.name ?? 'Active effect'
              : entryState.owned.isNotEmpty
              ? '${entryState.owned.length} owned'
              : 'None owned';

      final walletMeta = '${_formatCompactNumber(walletSummary.balance)} coins';
      setState(() {
        _subscriptionMeta = subscriptionMeta;
        _entryMeta = entryMeta;
        _walletMeta = walletMeta;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() {
        _subscriptionMeta ??= 'View plans';
        _entryMeta ??= 'Manage effects';
        _walletMeta ??= 'Wallet';
      });
    }
  }

  Future<void> _refresh() async {
    await Future.wait([_profileController.load(), _loadInlineMeta()]);
  }

  @override
  Widget build(BuildContext context) {
    final auth = Get.find<AuthService>();
    final api = Get.find<ApiClient>();
    final appSettings = Get.find<AppSettingsService>();
    final user = auth.currentUser;
    return Obx(() {
      final tokens = _settingsTokens();
      final primaryActions = <_DeckAction>[
        if (appSettings.walletRechargeEnabled)
          _DeckAction(
            icon: Icons.account_balance_wallet_rounded,
            iconWidget: const CoinLottie(size: 24),
            title: 'Recharge Wallet',
            meta: _walletMeta,
            onTap:
                () => Get.bottomSheet(
                  const RechargeBottomSheet(),
                  isScrollControlled: true,
                ),
            tint: const [Color(0xFF38B46A), Color(0xFF79E89A)],
          ),
        if (appSettings.subscriptionsEnabled)
          _DeckAction(
            icon: Icons.workspace_premium_rounded,
            title: 'Subscriptions',
            meta: _subscriptionMeta,
            onTap: () => Get.toNamed(Routes.subscriptions),
            tint: const [Color(0xFF4D7CFF), Color(0xFF78A6FF)],
          ),
        if (appSettings.entryEffectsEnabled)
          _DeckAction(
            icon: Icons.auto_awesome_rounded,
            title: 'Entry Effects',
            meta: _entryMeta,
            onTap:
                () => Get.bottomSheet(
                  const EntryPackBottomSheet(),
                  isScrollControlled: true,
                ),
            tint: const [Color(0xFFFF8D62), Color(0xFFFFC073)],
          ),
        _DeckAction(
          icon: Icons.notifications_active_rounded,
          title: 'Notifications',
          meta: null,
          onTap: () => Get.toNamed(Routes.notifications),
          tint: const [Color(0xFF8056F2), Color(0xFFB391FF)],
        ),
      ];

      final supportActions = <_DeckAction>[
        _DeckAction(
          icon: Icons.privacy_tip_rounded,
          title: 'Privacy Policy',
          meta: null,
          onTap: () => _openExternal(AppUrls.privacyPolicyUrl),
          tint: const [Color(0xFF1FA4A0), Color(0xFF6BD7C8)],
        ),
        _DeckAction(
          icon: Icons.article_rounded,
          title: 'Terms & Conditions',
          meta: null,
          onTap: () => _openExternal(AppUrls.termsOfServiceUrl),
          tint: const [Color(0xFF4470FF), Color(0xFF85A6FF)],
        ),
        _DeckAction(
          icon: Icons.support_agent_rounded,
          title: 'Help / Support',
          meta: null,
          onTap: () => _openExternal(AppUrls.supportUrl),
          tint: const [Color(0xFF22B573), Color(0xFF77E3AA)],
        ),
        _DeckAction(
          icon: Icons.delete_forever_rounded,
          title: 'Account Deletion',
          meta: null,
          onTap: () => _openExternal(AppUrls.accountDeletionUrl),
          tint: const [Color(0xFFE45C30), Color(0xFFFF9A69)],
        ),
      ];

      final profile = _profileController.profile.value;
      final effectiveName = profile?.name ?? user?.name ?? 'GD Live user';
      final effectiveRoles = profile?.roles ?? user?.roles ?? const <String>[];
      final avatarUrl = resolveAvatarUrl(
        api,
        profile?.avatarUrl ?? user?.avatarUrl,
      );
      final roleLabel =
          effectiveRoles.isEmpty
              ? 'USER'
              : effectiveRoles.join(' • ').toUpperCase();

      return Scaffold(
        backgroundColor: tokens.backgroundGradient.first,
        body: Stack(
          children: [
            Positioned.fill(child: _SettingsBackdrop(t: _bgMotion)),
            Positioned.fill(
              child: DecoratedBox(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [
                      tokens.cardGradient.first.withOpacity(.16),
                      Colors.transparent,
                      tokens.glassColor.withOpacity(.20),
                    ],
                  ),
                ),
              ),
            ),
            RefreshIndicator(
              onRefresh: _refresh,
              color: tokens.primaryButtonGradient.first,
              backgroundColor: Colors.white,
              child: ListView(
                physics: const AlwaysScrollableScrollPhysics(
                  parent: BouncingScrollPhysics(),
                ),
                padding: EdgeInsets.fromLTRB(16, 16, 16, widget.bottomPadding),
                children: [
                  _AnimatedEntrance(
                    index: 0,
                    child: _AccountCard(
                      userId: profile?.id ?? user?.id,
                      name: effectiveName,
                      roleLabel: roleLabel,
                      avatarUrl: avatarUrl,
                      initials:
                          (effectiveName.isNotEmpty
                                  ? effectiveName.substring(0, 1)
                                  : 'U')
                              .toUpperCase(),
                      level: profile?.level ?? user?.level,
                      levelTitle: profile?.levelTitle ?? user?.levelTitle,
                      badgeColor: profile?.badgeColor ?? user?.badgeColor,
                      lifetimeSpendCoins:
                          profile?.lifetimeSpendCoins ??
                          user?.lifetimeSpendCoins,
                      nextLevelTitle:
                          profile?.nextLevelTitle ?? user?.nextLevelTitle,
                      nextLevelRequiredSpend:
                          profile?.nextLevelRequiredSpend ??
                          user?.nextLevelRequiredSpend,
                      remainingSpendToNextLevel:
                          profile?.remainingSpendToNextLevel ??
                          user?.remainingSpendToNextLevel,
                      progressPercent:
                          profile?.progressPercent ?? user?.progressPercent,
                    ),
                  ),
                  const SizedBox(height: 16),
                  _AnimatedEntrance(
                    index: 1,
                    child:
                        primaryActions.isEmpty
                            ? const _PremiumEmptyState(
                              title: 'Wallet and premium tools are unavailable',
                              message:
                                  'This section is currently disabled by the platform configuration.',
                            )
                            : _ControlDeck(actions: primaryActions),
                  ),
                  const SizedBox(height: 18),
                  _AnimatedEntrance(
                    index: 2,
                    child: _ActionRail(
                      title: 'Support',
                      actions: supportActions,
                    ),
                  ),
                  const SizedBox(height: 18),
                  _AnimatedEntrance(
                    index: 3,
                    child: _SessionDock(
                      onDeactivate: _confirmDeactivateAccount,
                      onLogout: () => _confirmLogout(auth),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      );
    });
  }

  static Future<void> _openExternal(String raw) async {
    final uri = Uri.parse(raw);
    await launchUrl(uri, mode: LaunchMode.externalApplication);
  }

  Future<void> _confirmDeactivateAccount() async {
    final tokens = _settingsTokens();
    final confirmed = await showDialog<bool>(
      context: context,
      barrierColor: Colors.black.withOpacity(.55),
      builder: (dialogContext) {
        return Dialog(
          backgroundColor: Colors.transparent,
          insetPadding: const EdgeInsets.symmetric(horizontal: 24),
          child: GdModalSurface(
            tokens: tokens,
            scrollable: true,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Container(
                      width: 42,
                      height: 42,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: const Color(0xFFFF8A3D).withOpacity(.12),
                        border: Border.all(
                          color: const Color(0xFFFF8A3D).withOpacity(.18),
                        ),
                      ),
                      alignment: Alignment.center,
                      child: const Icon(
                        Icons.person_off_rounded,
                        color: Color(0xFFE45C30),
                        size: 20,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Text(
                        'Deactivate account?',
                        style: TextStyle(
                          color: tokens.textPrimary,
                          fontWeight: FontWeight.w900,
                          fontSize: 20,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 14),
                Text(
                  'This opens support so you can request account deactivation.',
                  style: TextStyle(
                    color: tokens.textSecondary,
                    fontWeight: FontWeight.w600,
                    height: 1.4,
                  ),
                ),
                const SizedBox(height: 10),
                Text(
                  'Support email: ${AppUrls.supportEmail}',
                  style: TextStyle(
                    color: tokens.textSecondary.withOpacity(.9),
                    fontWeight: FontWeight.w700,
                  ),
                ),
                const SizedBox(height: 18),
                Row(
                  children: [
                    Expanded(
                      child: OutlinedButton(
                        style: OutlinedButton.styleFrom(
                          foregroundColor: tokens.textSecondary,
                          side: BorderSide(color: tokens.borderColor),
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                        ),
                        onPressed: () => Navigator.of(dialogContext).pop(false),
                        child: const Text('Cancel'),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: FilledButton(
                        style: FilledButton.styleFrom(
                          backgroundColor: tokens.primaryButtonGradient.first,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                        ),
                        onPressed: () => Navigator.of(dialogContext).pop(true),
                        child: const Text('Continue'),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        );
      },
    );

    if (confirmed == true) {
      await _openExternal(AppUrls.deactivateAccountMailto);
    }
  }

  Future<void> _confirmLogout(AuthService auth) async {
    final tokens = _settingsTokens();
    final confirmed = await showDialog<bool>(
      context: context,
      barrierColor: Colors.black.withOpacity(.55),
      builder: (dialogContext) {
        return Dialog(
          backgroundColor: Colors.transparent,
          insetPadding: const EdgeInsets.symmetric(horizontal: 24),
          child: GdModalSurface(
            tokens: tokens,
            scrollable: true,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Container(
                      width: 42,
                      height: 42,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: tokens.dangerColor.withOpacity(.12),
                        border: Border.all(
                          color: tokens.dangerColor.withOpacity(.18),
                        ),
                      ),
                      alignment: Alignment.center,
                      child: Icon(
                        Icons.logout_rounded,
                        color: tokens.dangerColor,
                        size: 20,
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Text(
                        'Logout?',
                        style: TextStyle(
                          color: tokens.textPrimary,
                          fontWeight: FontWeight.w900,
                          fontSize: 20,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 14),
                Text(
                  'You will be signed out from this device.',
                  style: TextStyle(
                    color: tokens.textSecondary,
                    fontWeight: FontWeight.w600,
                    height: 1.4,
                  ),
                ),
                const SizedBox(height: 18),
                Row(
                  children: [
                    Expanded(
                      child: OutlinedButton(
                        style: OutlinedButton.styleFrom(
                          foregroundColor: tokens.textSecondary,
                          side: BorderSide(color: tokens.borderColor),
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                        ),
                        onPressed: () => Navigator.of(dialogContext).pop(false),
                        child: const Text('Cancel'),
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: FilledButton(
                        style: FilledButton.styleFrom(
                          backgroundColor: tokens.dangerColor,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 14),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                        ),
                        onPressed: () => Navigator.of(dialogContext).pop(true),
                        child: const Text('Logout'),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        );
      },
    );

    if (confirmed == true) {
      await auth.logout();
    }
  }

  static String _formatShortDate(DateTime? value) {
    if (value == null) return 'active';
    const months = <String>[
      'Jan',
      'Feb',
      'Mar',
      'Apr',
      'May',
      'Jun',
      'Jul',
      'Aug',
      'Sep',
      'Oct',
      'Nov',
      'Dec',
    ];
    final local = value.toLocal();
    return '${local.day} ${months[local.month - 1]}';
  }

  static String _formatCompactNumber(int value) {
    if (value >= 1000000) {
      return '${(value / 1000000).toStringAsFixed(value % 1000000 == 0 ? 0 : 1)}M';
    }
    if (value >= 1000) {
      return '${(value / 1000).toStringAsFixed(value % 1000 == 0 ? 0 : 1)}K';
    }
    return '$value';
  }
}

class _AccountCard extends StatelessWidget {
  final int? userId;
  final String name;
  final String roleLabel;
  final String? avatarUrl;
  final String initials;
  final int? level;
  final String? levelTitle;
  final String? badgeColor;
  final int? lifetimeSpendCoins;
  final String? nextLevelTitle;
  final int? nextLevelRequiredSpend;
  final int? remainingSpendToNextLevel;
  final double? progressPercent;

  const _AccountCard({
    this.userId,
    required this.name,
    required this.roleLabel,
    required this.avatarUrl,
    required this.initials,
    this.level,
    this.levelTitle,
    this.badgeColor,
    this.lifetimeSpendCoins,
    this.nextLevelTitle,
    this.nextLevelRequiredSpend,
    this.remainingSpendToNextLevel,
    this.progressPercent,
  });

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          padding: const EdgeInsets.fromLTRB(18, 18, 18, 12),
          decoration: BoxDecoration(
            gradient: LinearGradient(
              colors: [
                const Color(0xFF667EEA).withOpacity(.96),
                const Color(0xFF764BA2).withOpacity(.92),
              ],
            ),
            borderRadius: BorderRadius.circular(30),
            boxShadow: const [
              BoxShadow(
                color: Color(0x33667EEA),
                blurRadius: 18,
                offset: Offset(0, 10),
              ),
            ],
          ),
          child: Material(
            color: Colors.transparent,
            child: InkWell(
              borderRadius: BorderRadius.circular(28),
              onTap: () {
                Haptics.light();
                Get.toNamed(Routes.profile);
              },
              child: Column(
                children: [
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              name,
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                              style: const TextStyle(
                                color: Colors.white,
                                fontWeight: FontWeight.w900,
                                fontSize: 24,
                                letterSpacing: -.45,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              userId != null ? 'ID $userId' : roleLabel,
                              style: TextStyle(
                                color: Colors.white.withOpacity(.78),
                                fontWeight: FontWeight.w700,
                                fontSize: 12.5,
                              ),
                            ),
                            const SizedBox(height: 14),
                            Wrap(
                              spacing: 8,
                              runSpacing: 8,
                              children: [
                                _HeaderCapsule(
                                  label: roleLabel,
                                  icon: Icons.verified_user_rounded,
                                ),
                                if (level != null ||
                                    (levelTitle?.trim().isNotEmpty ?? false))
                                  _HeaderCapsule(
                                    label:
                                        levelTitle?.trim().isNotEmpty == true
                                            ? 'L$level'
                                            : 'LEVEL ${level ?? 1}',
                                    icon: Icons.workspace_premium_rounded,
                                  ),
                              ],
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(width: 14),
                      Container(
                        width: 72,
                        height: 72,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: Colors.white.withOpacity(.12),
                          border: Border.all(
                            color: Colors.white.withOpacity(.18),
                          ),
                        ),
                        padding: const EdgeInsets.all(5),
                        child: _ProfileHeroAvatar(
                          size: 62,
                          initials: initials,
                          avatarUrl: avatarUrl,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 14),
                  Row(
                    children: [
                      const Spacer(),
                      Container(
                        width: 34,
                        height: 34,
                        decoration: BoxDecoration(
                          shape: BoxShape.circle,
                          color: Colors.white.withOpacity(.14),
                          border: Border.all(
                            color: Colors.white.withOpacity(.18),
                          ),
                        ),
                        alignment: Alignment.center,
                        child: const Icon(
                          Icons.arrow_forward_rounded,
                          color: Colors.white,
                          size: 18,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ),
        if ((lifetimeSpendCoins ?? 0) > 0 || level != null) ...[
          const SizedBox(height: 14),
          _LevelProgressCard(
            level: level,
            levelTitle: levelTitle,
            lifetimeSpendCoins: lifetimeSpendCoins ?? 0,
            nextLevelTitle: nextLevelTitle,
            nextLevelRequiredSpend: nextLevelRequiredSpend,
            remainingSpendToNextLevel: remainingSpendToNextLevel,
            progressPercent: progressPercent ?? 0,
            badgeColor: _parseColor(badgeColor) ?? const Color(0xFF7B50C5),
          ),
        ],
      ],
    );
  }
}

class _HeaderCapsule extends StatelessWidget {
  final String label;
  final IconData icon;

  const _HeaderCapsule({required this.label, required this.icon});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 7),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(.14),
        borderRadius: BorderRadius.circular(999),
        border: Border.all(color: Colors.white.withOpacity(.16)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, color: Colors.white, size: 13),
          const SizedBox(width: 6),
          Text(
            label,
            style: const TextStyle(
              color: Colors.white,
              fontSize: 11,
              fontWeight: FontWeight.w800,
              letterSpacing: .2,
            ),
          ),
        ],
      ),
    );
  }
}

class _ProfileHeroAvatar extends StatelessWidget {
  final double size;
  final String initials;
  final String? avatarUrl;

  const _ProfileHeroAvatar({
    required this.size,
    required this.initials,
    required this.avatarUrl,
  });

  @override
  Widget build(BuildContext context) {
    final hasAvatar = avatarUrl != null && avatarUrl!.trim().isNotEmpty;
    return ClipOval(
      child: Container(
        width: size,
        height: size,
        color: Colors.white,
        child:
            hasAvatar
                ? Image.network(
                  avatarUrl!,
                  fit: BoxFit.cover,
                  errorBuilder:
                      (_, __, ___) => _HeroAvatarFallback(initials: initials),
                )
                : _HeroAvatarFallback(initials: initials),
      ),
    );
  }
}

class _HeroAvatarFallback extends StatelessWidget {
  final String initials;

  const _HeroAvatarFallback({required this.initials});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Text(
        initials,
        style: const TextStyle(
          color: Color(0xFF6E4DB2),
          fontWeight: FontWeight.w900,
          fontSize: 24,
        ),
      ),
    );
  }
}

class _DeckAction {
  final IconData icon;
  final Widget? iconWidget;
  final String title;
  final String? meta;
  final VoidCallback onTap;
  final List<Color> tint;

  const _DeckAction({
    required this.icon,
    this.iconWidget,
    required this.title,
    required this.meta,
    required this.onTap,
    required this.tint,
  });
}

class _ControlDeck extends StatelessWidget {
  final List<_DeckAction> actions;

  const _ControlDeck({required this.actions});

  @override
  Widget build(BuildContext context) {
    return LayoutBuilder(
      builder: (context, constraints) {
        final width = (constraints.maxWidth - 12) / 2;
        return Wrap(
          spacing: 12,
          runSpacing: 12,
          children: actions
              .map((action) {
                return SizedBox(width: width, child: _DeckTile(action: action));
              })
              .toList(growable: false),
        );
      },
    );
  }
}

class _DeckTile extends StatelessWidget {
  final _DeckAction action;

  const _DeckTile({required this.action});

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        borderRadius: BorderRadius.circular(26),
        onTap: () {
          Haptics.light();
          action.onTap();
        },
        child: Ink(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(26),
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: action.tint,
            ),
            boxShadow: [
              BoxShadow(
                color: action.tint.first.withOpacity(.22),
                blurRadius: 20,
                offset: const Offset(0, 12),
              ),
            ],
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Row(
                children: [
                  Container(
                    width: 42,
                    height: 42,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: Colors.white.withOpacity(.18),
                      border: Border.all(color: Colors.white.withOpacity(.18)),
                    ),
                    alignment: Alignment.center,
                    child:
                        action.iconWidget ??
                        Icon(action.icon, color: Colors.white, size: 20),
                  ),
                  const Spacer(),
                  const Icon(
                    Icons.arrow_forward_rounded,
                    color: Colors.white,
                    size: 18,
                  ),
                ],
              ),
              const SizedBox(height: 22),
              Text(
                action.title,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 16,
                  fontWeight: FontWeight.w800,
                  height: 1.05,
                ),
              ),
              const SizedBox(height: 8),
              Text(
                action.meta ?? '',
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
                style: TextStyle(
                  color: Colors.white.withOpacity(.84),
                  fontSize: 11.5,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _ActionRail extends StatelessWidget {
  final String title;
  final List<_DeckAction> actions;

  const _ActionRail({required this.title, required this.actions});

  @override
  Widget build(BuildContext context) {
    final tokens = _settingsTokens();
    return _GlassShell(
      borderRadius: 28,
      padding: const EdgeInsets.fromLTRB(16, 16, 16, 10),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: Theme.of(context).textTheme.titleLarge?.copyWith(
              fontWeight: FontWeight.w900,
              color: tokens.textPrimary,
            ),
          ),
          const SizedBox(height: 12),
          ...List.generate(actions.length, (index) {
            return Padding(
              padding: EdgeInsets.only(
                bottom: index == actions.length - 1 ? 0 : 10,
              ),
              child: _RailTile(action: actions[index]),
            );
          }),
        ],
      ),
    );
  }
}

class _RailTile extends StatelessWidget {
  final _DeckAction action;

  const _RailTile({required this.action});

  @override
  Widget build(BuildContext context) {
    final tokens = _settingsTokens();
    return Material(
      color: Colors.transparent,
      child: InkWell(
        borderRadius: BorderRadius.circular(20),
        onTap: () {
          Haptics.light();
          action.onTap();
        },
        child: Ink(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(20),
            color: Colors.white.withOpacity(.56),
            border: Border.all(color: tokens.borderColor.withOpacity(.88)),
          ),
          child: Row(
            children: [
              Container(
                width: 38,
                height: 38,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: LinearGradient(colors: action.tint),
                ),
                alignment: Alignment.center,
                child: Icon(action.icon, color: Colors.white, size: 18),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Text(
                  action.title,
                  style: TextStyle(
                    color: tokens.textPrimary,
                    fontSize: 14.5,
                    fontWeight: FontWeight.w800,
                  ),
                ),
              ),
              Icon(
                Icons.arrow_forward_ios_rounded,
                color: tokens.textSecondary.withOpacity(.8),
                size: 15,
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _SessionDock extends StatelessWidget {
  final VoidCallback onDeactivate;
  final VoidCallback onLogout;

  const _SessionDock({required this.onDeactivate, required this.onLogout});

  @override
  Widget build(BuildContext context) {
    final tokens = _settingsTokens();
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(28),
        color: Colors.white.withOpacity(.7),
        border: Border.all(color: tokens.borderColor),
      ),
      child: Row(
        children: [
          Expanded(
            child: _SessionButton(
              label: 'Deactivate',
              icon: Icons.person_off_rounded,
              tint: const [Color(0xFFFF9B54), Color(0xFFFFC76C)],
              onTap: onDeactivate,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: _SessionButton(
              label: 'Logout',
              icon: Icons.logout_rounded,
              tint: [
                tokens.dangerColor,
                Color.lerp(tokens.dangerColor, Colors.white, .18)!,
              ],
              onTap: onLogout,
            ),
          ),
        ],
      ),
    );
  }
}

class _SessionButton extends StatelessWidget {
  final String label;
  final IconData icon;
  final List<Color> tint;
  final VoidCallback onTap;

  const _SessionButton({
    required this.label,
    required this.icon,
    required this.tint,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        borderRadius: BorderRadius.circular(22),
        onTap: () {
          Haptics.light();
          onTap();
        },
        child: Ink(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 16),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(22),
            gradient: LinearGradient(colors: tint),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(icon, color: Colors.white, size: 20),
              const SizedBox(height: 8),
              Text(
                label,
                style: const TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.w800,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _LevelProgressCard extends StatelessWidget {
  final int? level;
  final String? levelTitle;
  final int lifetimeSpendCoins;
  final String? nextLevelTitle;
  final int? nextLevelRequiredSpend;
  final int? remainingSpendToNextLevel;
  final double progressPercent;
  final Color badgeColor;

  const _LevelProgressCard({
    required this.level,
    required this.levelTitle,
    required this.lifetimeSpendCoins,
    required this.nextLevelTitle,
    required this.nextLevelRequiredSpend,
    required this.remainingSpendToNextLevel,
    required this.progressPercent,
    required this.badgeColor,
  });

  @override
  Widget build(BuildContext context) {
    final accent =
        badgeColor == const Color(0xFF7B50C5)
            ? const Color(0xFFC9B6FF)
            : badgeColor;
    final currentLevelLabel =
        levelTitle?.trim().isNotEmpty == true
            ? 'Level ${level ?? 1} · ${levelTitle!.trim()}'
            : 'Level ${level ?? 1}';
    final summary =
        nextLevelRequiredSpend == null
            ? '${_compact(lifetimeSpendCoins)} spent · highest active level'
            : '${_compact(lifetimeSpendCoins)} spent · ${_compact(remainingSpendToNextLevel ?? 0)} to ${nextLevelTitle ?? 'next'}';
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            const Color(0xFF6E4DB2).withOpacity(.96),
            const Color(0xFF8A63D2).withOpacity(.94),
            const Color(0xFF9D7CE0).withOpacity(.90),
          ],
        ),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: Colors.white.withOpacity(.14)),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF764BA2).withOpacity(.22),
            blurRadius: 20,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 34,
                height: 34,
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(.14),
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: Colors.white.withOpacity(.12)),
                ),
                child: Icon(
                  Icons.workspace_premium_rounded,
                  color: accent,
                  size: 18,
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      currentLevelLabel,
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 15,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    const SizedBox(height: 2),
                    Text(
                      summary,
                      style: TextStyle(
                        color: Colors.white.withOpacity(.76),
                        fontWeight: FontWeight.w600,
                        fontSize: 12.5,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 10),
          ClipRRect(
            borderRadius: BorderRadius.circular(999),
            child: Stack(
              children: [
                Container(height: 8, color: Colors.white.withOpacity(.14)),
                FractionallySizedBox(
                  widthFactor: (progressPercent.clamp(0, 100)) / 100,
                  child: Container(
                    height: 8,
                    decoration: BoxDecoration(
                      gradient: LinearGradient(
                        colors: [accent, Colors.white.withOpacity(.92)],
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  static String _compact(int value) {
    if (value >= 1000000) {
      return '${(value / 1000000).toStringAsFixed(value % 1000000 == 0 ? 0 : 1)}M';
    }
    if (value >= 1000) {
      return '${(value / 1000).toStringAsFixed(value % 1000 == 0 ? 0 : 1)}K';
    }
    return '$value';
  }
}

class _SettingsSection extends StatelessWidget {
  final String eyebrow;
  final String title;
  final String? subtitle;
  final List<Widget> children;
  const _SettingsSection({
    required this.eyebrow,
    required this.title,
    this.subtitle,
    required this.children,
  });

  @override
  Widget build(BuildContext context) {
    final tokens = _settingsTokens();
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Padding(
          padding: const EdgeInsets.only(left: 4, bottom: 12),
          child: Row(
            children: [
              Container(
                width: 10,
                height: 10,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: tokens.primaryButtonGradient.first,
                ),
              ),
              const SizedBox(width: 8),
              Text(
                title,
                style: Theme.of(context).textTheme.titleLarge?.copyWith(
                  fontWeight: FontWeight.w900,
                  color: tokens.textPrimary,
                  letterSpacing: -.2,
                ),
              ),
            ],
          ),
        ),
        ...List.generate(children.length, (index) {
          return Padding(
            padding: EdgeInsets.only(
              bottom: index == children.length - 1 ? 0 : 12,
            ),
            child: children[index],
          );
        }),
      ],
    );
  }
}

class _PremiumEmptyState extends StatelessWidget {
  final String title;
  final String message;

  const _PremiumEmptyState({required this.title, required this.message});

  @override
  Widget build(BuildContext context) {
    final tokens = _settingsTokens();
    return Padding(
      padding: const EdgeInsets.all(18),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const GdLottie(
            asset: GdLottieAssets.connectInteract,
            width: 92,
            height: 92,
          ),
          const SizedBox(height: 10),
          Text(
            title,
            style: TextStyle(
              color: tokens.textPrimary,
              fontSize: 16,
              fontWeight: FontWeight.w800,
            ),
          ),
          const SizedBox(height: 6),
          Text(
            message,
            style: TextStyle(
              color: tokens.textSecondary.withOpacity(.92),
              height: 1.45,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      ),
    );
  }
}

class _PremiumSettingTile extends StatelessWidget {
  final IconData icon;
  final String title;
  final String? subtitle;
  final String? meta;
  final VoidCallback onTap;
  final Color? tint;

  const _PremiumSettingTile({
    required this.icon,
    required this.title,
    this.subtitle,
    this.meta,
    required this.onTap,
    this.tint,
  });

  @override
  Widget build(BuildContext context) {
    final tokens = _settingsTokens();
    final color = tint ?? tokens.primaryButtonGradient.first;
    final metaVisible = meta != null && meta!.trim().isNotEmpty;
    final gradient = [Color.lerp(color, Colors.white, .22)!, color];
    return InkWell(
      borderRadius: BorderRadius.circular(24),
      onTap: () {
        Haptics.light();
        onTap();
      },
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 240),
        curve: Curves.easeOutCubic,
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: gradient,
          ),
          borderRadius: BorderRadius.circular(22),
          boxShadow: [
            BoxShadow(
              color: color.withOpacity(.22),
              blurRadius: 18,
              offset: const Offset(0, 10),
            ),
          ],
        ),
        child: Row(
          children: [
            Container(
              width: 42,
              height: 42,
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(.18),
                shape: BoxShape.circle,
                border: Border.all(color: Colors.white.withOpacity(.24)),
              ),
              alignment: Alignment.center,
              child: Icon(icon, color: Colors.white, size: 20),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Text(
                title,
                style: const TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.w800,
                  fontSize: 15.5,
                ),
              ),
            ),
            if (metaVisible) ...[
              const SizedBox(width: 10),
              Container(
                constraints: const BoxConstraints(maxWidth: 110),
                padding: const EdgeInsets.symmetric(
                  horizontal: 10,
                  vertical: 6,
                ),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(.16),
                  borderRadius: BorderRadius.circular(999),
                  border: Border.all(color: Colors.white.withOpacity(.18)),
                ),
                child: Text(
                  meta!,
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 10.2,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ],
            const SizedBox(width: 10),
            const Icon(
              Icons.arrow_forward_ios_rounded,
              color: Colors.white,
              size: 16,
            ),
          ],
        ),
      ),
    );
  }
}

class _AnimatedEntrance extends StatelessWidget {
  final int index;
  final Widget child;

  const _AnimatedEntrance({required this.index, required this.child});

  @override
  Widget build(BuildContext context) {
    final begin = 80 * index;
    final total = begin + 520;
    return TweenAnimationBuilder<double>(
      tween: Tween(begin: 0, end: 1),
      duration: Duration(milliseconds: total),
      curve: Curves.easeOutCubic,
      builder: (context, value, _) {
        final raw = ((value * total) - begin) / 520;
        final clamped = raw.clamp(0.0, 1.0);
        final eased = Curves.easeOutCubic.transform(clamped);
        return Opacity(
          opacity: eased,
          child: Transform.translate(
            offset: Offset(0, (1 - eased) * 28),
            child: child,
          ),
        );
      },
      child: child,
    );
  }
}

class _SettingsBackdrop extends StatelessWidget {
  final Animation<double> t;

  const _SettingsBackdrop({required this.t});

  @override
  Widget build(BuildContext context) {
    final tokens = _settingsTokens();
    return AnimatedBuilder(
      animation: t,
      builder: (context, _) {
        return CustomPaint(
          painter: _SettingsBackdropPainter(t.value),
          child: Container(
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: tokens.backgroundGradient,
                begin: Alignment.topCenter,
                end: Alignment.bottomCenter,
              ),
            ),
          ),
        );
      },
    );
  }
}

class _SettingsBackdropPainter extends CustomPainter {
  final double t;

  const _SettingsBackdropPainter(this.t);

  @override
  void paint(Canvas canvas, Size size) {
    final tokens = _settingsTokens();
    final paint1 =
        Paint()
          ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 64)
          ..color = tokens.glowColor.withOpacity(.24);
    final paint2 =
        Paint()
          ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 72)
          ..color = tokens.primaryButtonGradient.last.withOpacity(.28);

    final wobble1 = math.sin(t * math.pi * 2) * 18;
    final wobble2 = math.cos(t * math.pi * 2) * 24;
    final wobble3 = math.sin((t * math.pi * 2) + 1.2) * 20;

    canvas.drawCircle(
      Offset(size.width * .18, size.height * .18 + wobble1),
      size.height * .12,
      paint1,
    );
    canvas.drawCircle(
      Offset(size.width * .88, size.height * .32 + wobble2),
      size.height * .15,
      paint2,
    );
    canvas.drawCircle(
      Offset(size.width * .55, size.height * .78 + wobble3),
      size.height * .18,
      paint1,
    );
  }

  @override
  bool shouldRepaint(covariant _SettingsBackdropPainter oldDelegate) =>
      oldDelegate.t != t;
}

class _GlassShell extends StatelessWidget {
  final Widget child;
  final EdgeInsetsGeometry padding;
  final double borderRadius;

  const _GlassShell({
    required this.child,
    this.padding = const EdgeInsets.all(16),
    this.borderRadius = 28,
  });

  @override
  Widget build(BuildContext context) {
    final tokens = _settingsTokens();
    return ClipRRect(
      borderRadius: BorderRadius.circular(borderRadius),
      child: BackdropFilter(
        filter: ImageFilter.blur(sigmaX: 18, sigmaY: 18),
        child: Container(
          padding: padding,
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
              colors: tokens.cardGradient,
            ),
            borderRadius: BorderRadius.circular(borderRadius),
            border: Border.all(color: tokens.borderColor),
            boxShadow: [
              BoxShadow(
                color: tokens.glowColor.withOpacity(.22),
                blurRadius: 24,
                offset: const Offset(0, 10),
              ),
            ],
          ),
          child: Stack(
            children: [
              Positioned(
                top: -24,
                right: -18,
                child: Container(
                  width: 120,
                  height: 120,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    gradient: RadialGradient(
                      colors: [
                        Colors.white.withOpacity(.30),
                        Colors.white.withOpacity(0),
                      ],
                    ),
                  ),
                ),
              ),
              child,
            ],
          ),
        ),
      ),
    );
  }
}

class _RoleChip extends StatelessWidget {
  final String label;
  final Color? color;

  const _RoleChip({required this.label, this.color});

  @override
  Widget build(BuildContext context) {
    final tokens = _settingsTokens();
    final chipColor = color ?? Colors.white;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
      decoration: BoxDecoration(
        color:
            chipColor == Colors.white
                ? tokens.chipColor.withOpacity(.78)
                : chipColor.withOpacity(.12),
        borderRadius: BorderRadius.circular(999),
        border: Border.all(
          color:
              chipColor == Colors.white
                  ? tokens.borderColor
                  : chipColor.withOpacity(.18),
        ),
      ),
      child: Text(
        label,
        style: TextStyle(
          color:
              chipColor == Colors.white
                  ? tokens.textPrimary
                  : chipColor.withOpacity(.92),
          fontSize: 11,
          fontWeight: FontWeight.w700,
          letterSpacing: .4,
        ),
      ),
    );
  }
}

Color? _parseColor(String? raw) {
  if (raw == null || raw.trim().isEmpty) {
    return null;
  }
  final hex = raw.trim().replaceFirst('#', '');
  if (hex.length != 6 && hex.length != 8) {
    return null;
  }
  final normalized = hex.length == 6 ? 'FF$hex' : hex;
  final value = int.tryParse(normalized, radix: 16);
  if (value == null) {
    return null;
  }
  return Color(value);
}
