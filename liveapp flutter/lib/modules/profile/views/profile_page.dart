import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';

import '../../../app/brand/brand.dart';
import '../../../app/routes/app_routes.dart';
import '../../../app/utils/avatar_url.dart';
import '../../../app/widgets/coin_lottie.dart';
import '../../../services/api_client.dart';
import '../../../services/app_settings_service.dart';
import '../../applications/controllers/applications_controller.dart';
import '../../applications/views/my_applications_page.dart';
import '../../wallet/widgets/recharge_bottom_sheet.dart';
import '../controllers/host_follow_controller.dart';
import '../controllers/profile_controller.dart';
import '../models/host_earnings_report_dto.dart';
import '../models/profile_dto.dart';

class ProfilePage extends StatefulWidget {
  const ProfilePage({super.key});

  @override
  State<ProfilePage> createState() => _ProfilePageState();
}

enum _HostReportRange {
  today('Today'),
  currentWeek('This week'),
  lastWeek('Last week');

  const _HostReportRange(this.label);
  final String label;
}

class _ProfilePageState extends State<ProfilePage> with WidgetsBindingObserver {
  _HostReportRange _hostReportRange = _HostReportRange.today;

  ProfileController get controller => Get.find<ProfileController>();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addObserver(this);
    controller.load();
  }

  @override
  void dispose() {
    WidgetsBinding.instance.removeObserver(this);
    super.dispose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      controller.load();
    }
  }

  @override
  Widget build(BuildContext context) {
    final api = Get.find<ApiClient>();
    final appSettings = Get.find<AppSettingsService>();
    final applicationsController = Get.find<ApplicationsController>();
    final follows = Get.find<HostFollowController>();

    return Obx(() {
      final tokens = getBrandTokens(appSettings.brandKey);
      final profile = controller.profile.value;

      return Scaffold(
        backgroundColor: tokens.backgroundGradient.first,
        appBar: AppBar(
          titleSpacing: 0,
          foregroundColor: tokens.textPrimary,
          iconTheme: IconThemeData(color: tokens.textPrimary),
          title: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              Text(
                'Profile',
                style: TextStyle(
                  color: tokens.textPrimary,
                  fontWeight: FontWeight.w900,
                  fontSize: 20,
                ),
              ),
              Text(
                'Manage account, wallet, and host tools',
                style: TextStyle(
                  color: tokens.textSecondary,
                  fontWeight: FontWeight.w600,
                  fontSize: 12,
                ),
              ),
            ],
          ),
          toolbarHeight: 68,
          backgroundColor: Colors.transparent,
          elevation: 0,
        ),
        body: Builder(
          builder: (context) {
            if (controller.isLoading.value && profile == null) {
              return const Center(child: CircularProgressIndicator());
            }

            if (profile == null) {
              return _ProfileMessageBoard(
                tokens: tokens,
                icon: Icons.sync_problem_rounded,
                title: 'Unable to load profile',
                message: controller.error.value ?? 'Please retry.',
                actionLabel: 'Retry',
                onTap: controller.load,
              );
            }

            final host = profile.hostProfile;
            final agency = host?.agency;
            final avatarUrl = resolveAvatarUrl(api, profile.avatarUrl);
            final roleLabels =
                profile.roles.isEmpty ? const ['user'] : profile.roles;
            final infoRows = _buildIdentityRows(profile, host, agency);

            return RefreshIndicator(
              onRefresh: controller.load,
              color: tokens.primaryButtonGradient.first,
              child: ListView(
                physics: const BouncingScrollPhysics(
                  parent: AlwaysScrollableScrollPhysics(),
                ),
                padding: const EdgeInsets.fromLTRB(18, 10, 18, 24),
                children: [
                  _ProfileHeroBoard(
                    tokens: tokens,
                    profile: profile,
                    avatarUrl: avatarUrl,
                    roleLabels: roleLabels,
                    onEdit: () => Get.toNamed(Routes.editProfile),
                  ),
                  const SizedBox(height: 14),
                  _StatsBoard(
                    tokens: tokens,
                    profile: profile,
                    onFollowingTap: () => Get.toNamed(Routes.following),
                    onFollowersTap:
                        profile.isHost
                            ? () {
                              follows.loadFollowers();
                              Get.toNamed(Routes.followers);
                            }
                            : null,
                  ),
                  const SizedBox(height: 14),
                  _IdentityLedger(tokens: tokens, rows: infoRows),
                  const SizedBox(height: 14),
                  _ActionDock(
                    tokens: tokens,
                    profile: profile,
                    walletRechargeEnabled: appSettings.walletRechargeEnabled,
                    onWallet:
                        () => Get.bottomSheet(
                          const RechargeBottomSheet(),
                          isScrollControlled: true,
                        ),
                    onApplications: showMyApplicationsSheet,
                  ),
                  if (agency != null) ...[
                    const SizedBox(height: 14),
                    _AgencyProfileBoard(tokens: tokens, agency: agency),
                  ],
                  if (profile.isHost) ...[
                    const SizedBox(height: 14),
                    _HostPerformanceBoard(
                      tokens: tokens,
                      range: _hostReportRange,
                      onRangeChanged:
                          (value) => setState(() => _hostReportRange = value),
                      report: controller.hostReport.value,
                      loading: controller.isLoadingHostReport.value,
                      error: controller.error.value,
                      onRetry: controller.loadHostReport,
                    ),
                    const SizedBox(height: 14),
                    _ModerationBoard(tokens: tokens),
                  ],
                ],
              ),
            );
          },
        ),
      );
    });
  }

  List<_IdentityRowData> _buildIdentityRows(
    ProfileDto profile,
    ProfileHostDto? host,
    ProfileAgencyDto? agency,
  ) {
    final location = [profile.location, profile.city, host?.city, host?.country]
        .where((value) => value != null && value.trim().isNotEmpty)
        .map((value) => value!.trim())
        .toSet()
        .join(', ');

    final rows = <_IdentityRowData>[
      _IdentityRowData('Email', profile.email),
      _IdentityRowData(
        'Membership',
        profile.levelTitle?.trim().isNotEmpty == true
            ? profile.levelTitle!.trim()
            : (profile.level == null ? 'Member' : 'Level ${profile.level}'),
      ),
      if (location.isNotEmpty) _IdentityRowData('Location', location),
      if (profile.joinedAt != null)
        _IdentityRowData(
          'Joined',
          DateFormat('dd MMM yyyy').format(profile.joinedAt!),
        ),
      if ((profile.bio ?? '').trim().isNotEmpty)
        _IdentityRowData('About', profile.bio!.trim()),
      if ((host?.stageName ?? '').trim().isNotEmpty)
        _IdentityRowData('Stage name', host!.stageName!.trim()),
      if ((host?.contactPhone ?? '').trim().isNotEmpty)
        _IdentityRowData('Contact', host!.contactPhone!.trim()),
      if (agency != null && (agency.name ?? '').trim().isNotEmpty)
        _IdentityRowData('Agency', agency.name!.trim()),
    ];
    return rows;
  }
}

class _ProfilePageIntro extends StatelessWidget {
  const _ProfilePageIntro({
    required this.tokens,
    required this.profile,
    required this.onEdit,
  });

  final BrandTokens tokens;
  final ProfileDto profile;
  final VoidCallback onEdit;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Colors.white.withOpacity(.95),
            const Color(0xFFF2FBF4).withOpacity(.98),
          ],
        ),
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: tokens.borderColor.withOpacity(.45)),
        boxShadow: [
          BoxShadow(
            color: tokens.primaryButtonGradient.first.withOpacity(.08),
            blurRadius: 24,
            offset: const Offset(0, 12),
          ),
        ],
      ),
      child: Row(
        children: [
          Container(
            width: 58,
            height: 58,
            decoration: BoxDecoration(
              gradient: LinearGradient(
                colors: tokens.primaryButtonGradient,
              ),
              borderRadius: BorderRadius.circular(20),
            ),
            child: Icon(
              profile.isHost
                  ? Icons.workspace_premium_rounded
                  : Icons.person_rounded,
              color: Colors.white,
            ),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  profile.name,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    color: tokens.textPrimary,
                    fontSize: 18,
                    fontWeight: FontWeight.w900,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  profile.isHost
                      ? 'Host profile, earnings, and moderation tools.'
                      : 'Keep your profile sharp and ready to go live.',
                  style: TextStyle(
                    color: tokens.textSecondary,
                    fontSize: 12.5,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(width: 10),
          FilledButton(
            onPressed: onEdit,
            style: FilledButton.styleFrom(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(16),
              ),
            ),
            child: const Text(
              'Edit',
              style: TextStyle(fontWeight: FontWeight.w800),
            ),
          ),
        ],
      ),
    );
  }
}

class _ProfileHeroBoard extends StatelessWidget {
  const _ProfileHeroBoard({
    required this.tokens,
    required this.profile,
    required this.avatarUrl,
    required this.roleLabels,
    required this.onEdit,
  });

  final BrandTokens tokens;
  final ProfileDto profile;
  final String? avatarUrl;
  final List<String> roleLabels;
  final VoidCallback onEdit;

  @override
  Widget build(BuildContext context) {
    final progress = ((profile.progressPercent ?? 0).clamp(0.0, 100.0)) / 100;
    final accent =
        _parseProfileColor(profile.badgeColor) ?? const Color(0xFFFFCC00);

    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(30),
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            const Color(0xFF0F2F18),
            tokens.primaryButtonGradient.first.withOpacity(.98),
            tokens.primaryButtonGradient.last.withOpacity(.92),
          ],
        ),
        border: Border.all(color: Colors.white.withOpacity(.16)),
        boxShadow: [
          BoxShadow(
            color: tokens.primaryButtonGradient.first.withOpacity(.24),
            blurRadius: 28,
            offset: const Offset(0, 16),
          ),
        ],
      ),
      child: Stack(
        children: [
          Positioned(
            top: -48,
            right: -34,
            child: _ProfileGlowBubble(
              size: 160,
              color: Colors.white.withOpacity(.08),
            ),
          ),
          Positioned(
            bottom: -38,
            left: -18,
            child: _ProfileGlowBubble(
              size: 120,
              color: accent.withOpacity(.12),
            ),
          ),
          Column(
            crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              _HeroAvatar(avatarUrl: avatarUrl, fallbackText: profile.name),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      profile.name,
                      style: Theme.of(
                        context,
                      ).textTheme.headlineSmall?.copyWith(
                        color: Colors.white,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'User ID #${profile.id}',
                      style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                        color: Colors.white.withOpacity(.82),
                        fontWeight: FontWeight.w600,
                      ),
                    ),
                    const SizedBox(height: 10),
                    Wrap(
                      spacing: 8,
                      runSpacing: 8,
                      children: [
                        for (final label in roleLabels)
                          _HeroChip(label: label.toUpperCase(), filled: false),
                        if (profile.canGoLive)
                          const _HeroChip(label: 'LIVE READY'),
                        if (profile.status.agencyAttached)
                          const _HeroChip(label: 'AGENCY'),
                      ],
                    ),
                  ],
                ),
              ),
              IconButton(
                onPressed: onEdit,
                style: IconButton.styleFrom(
                  backgroundColor: Colors.white.withOpacity(.14),
                  foregroundColor: Colors.white,
                ),
                icon: const Icon(Icons.edit_rounded),
              ),
            ],
          ),
          const SizedBox(height: 14),
          Row(
            children: [
              Expanded(
                child: Text(
                  profile.nextLevelRequiredSpend != null
                      ? '${NumberFormat.compact().format(profile.remainingSpendToNextLevel ?? 0)} coins to ${profile.nextLevelTitle ?? 'next level'}'
                      : 'Keep spending to rank up.',
                  style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                    color: Colors.white.withOpacity(.86),
                    height: 1.35,
                  ),
                ),
              ),
              const SizedBox(width: 12),
              Container(
                width: 70,
                height: 70,
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(.12),
                  shape: BoxShape.circle,
                  border: Border.all(color: accent.withOpacity(.85), width: 2.5),
                ),
                child: Center(
                  child: Text(
                    profile.level?.toString() ?? '1',
                    style: Theme.of(context).textTheme.headlineSmall?.copyWith(
                      color: Colors.white,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 12),
          ClipRRect(
            borderRadius: BorderRadius.circular(999),
            child: LinearProgressIndicator(
              value: progress,
              minHeight: 9,
              backgroundColor: Colors.white.withOpacity(.18),
              valueColor: AlwaysStoppedAnimation<Color>(accent),
            ),
          ),
        ],
      ),
        ],
      ),
    );
  }
}

class _HeroAvatar extends StatelessWidget {
  const _HeroAvatar({required this.avatarUrl, required this.fallbackText});

  final String? avatarUrl;
  final String fallbackText;

  @override
  Widget build(BuildContext context) {
    final initial =
        fallbackText.trim().isEmpty
            ? 'U'
            : fallbackText.trim().substring(0, 1).toUpperCase();
    return Container(
      width: 86,
      height: 86,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        border: Border.all(color: Colors.white.withOpacity(.24), width: 2),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(.10),
            blurRadius: 18,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: ClipOval(
        child:
            avatarUrl != null && avatarUrl!.isNotEmpty
                ? Image.network(
                  avatarUrl!,
                  fit: BoxFit.cover,
                  errorBuilder:
                      (_, __, ___) => _HeroAvatarFallback(initial: initial),
                )
                : _HeroAvatarFallback(initial: initial),
      ),
    );
  }
}

class _HeroAvatarFallback extends StatelessWidget {
  const _HeroAvatarFallback({required this.initial});

  final String initial;

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [
            Colors.white,
            const Color(0xFFF0FBF2),
          ],
        ),
      ),
      alignment: Alignment.center,
      child: Text(
        initial,
        style: Theme.of(context).textTheme.headlineSmall?.copyWith(
          color: const Color(0xFF1A7A35),
          fontWeight: FontWeight.w900,
        ),
      ),
    );
  }
}

class _HeroChip extends StatelessWidget {
  const _HeroChip({required this.label, this.filled = true});

  final String label;
  final bool filled;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 7),
      decoration: BoxDecoration(
        color: filled ? Colors.white.withOpacity(.16) : Colors.transparent,
        borderRadius: BorderRadius.circular(999),
        border: Border.all(color: Colors.white.withOpacity(.28)),
      ),
      child: Text(
        label,
        style: Theme.of(context).textTheme.bodySmall?.copyWith(
          color: Colors.white,
          fontWeight: FontWeight.w800,
        ),
      ),
    );
  }
}

class _ProfileGlowBubble extends StatelessWidget {
  const _ProfileGlowBubble({required this.size, required this.color});

  final double size;
  final Color color;

  @override
  Widget build(BuildContext context) {
    return IgnorePointer(
      child: Container(
        width: size,
        height: size,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          gradient: RadialGradient(
            colors: [color, color.withOpacity(.18), Colors.transparent],
          ),
        ),
      ),
    );
  }
}

class _SectionLabel extends StatelessWidget {
  const _SectionLabel({required this.title, required this.subtitle});

  final String title;
  final String subtitle;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          title,
          style: Theme.of(context).textTheme.titleMedium?.copyWith(
            color: const Color(0xFF15351C),
            fontWeight: FontWeight.w900,
          ),
        ),
        const SizedBox(height: 4),
        Text(
          subtitle,
          style: Theme.of(context).textTheme.bodySmall?.copyWith(
            color: const Color(0xFF6C7E72),
            fontWeight: FontWeight.w600,
          ),
        ),
      ],
    );
  }
}

class _StatsBoard extends StatelessWidget {
  const _StatsBoard({
    required this.tokens,
    required this.profile,
    this.onFollowingTap,
    this.onFollowersTap,
  });

  final BrandTokens tokens;
  final ProfileDto profile;
  final VoidCallback? onFollowingTap;
  final VoidCallback? onFollowersTap;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        SizedBox(
          height: 136,
          child: ListView(
            scrollDirection: Axis.horizontal,
            children: [
              _StatTile(
                width: 170,
                icon: Icons.account_balance_wallet_rounded,
                iconWidget: const CoinLottie(size: 28),
                label: 'Balance',
                value:
                    '${NumberFormat.compact().format(profile.walletBalance)} coins',
                accent: const Color(0xFF06B430),
              ),
              const SizedBox(width: 12),
              _StatTile(
                width: 158,
                icon: Icons.bolt_rounded,
                label: 'Level',
                value:
                    profile.levelTitle?.trim().isNotEmpty == true
                        ? profile.levelTitle!.trim()
                        : (profile.level == null
                            ? 'Member'
                            : 'Level ${profile.level}'),
                accent: const Color(0xFF5B3FC5),
              ),
              const SizedBox(width: 12),
              _StatTile(
                width: 178,
                icon: Icons.trending_up_rounded,
                iconWidget: const CoinLottie(size: 28),
                label: 'Spend',
                value:
                    '${NumberFormat.compact().format(profile.lifetimeSpendCoins ?? 0)} coins',
                accent: const Color(0xFFFFB400),
              ),
              const SizedBox(width: 12),
              _StatTile(
                width: 162,
                icon: Icons.people_alt_rounded,
                label: profile.isHost ? 'Followers' : 'Following',
                value:
                    profile.isHost
                        ? '${profile.followersCount ?? 0}'
                        : '${profile.followingCount ?? 0}',
                accent: const Color(0xFF1E88E5),
                onTap: profile.isHost ? onFollowersTap : onFollowingTap,
              ),
            ],
          ),
        ),
      ],
    );
  }
}

class _StatTile extends StatelessWidget {
  const _StatTile({
    required this.width,
    required this.icon,
    required this.label,
    required this.value,
    required this.accent,
    this.iconWidget,
    this.onTap,
  });

  final double width;
  final IconData icon;
  final String label;
  final String value;
  final Color accent;
  final Widget? iconWidget;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    final card = Container(
      width: width,
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Colors.white.withOpacity(.98),
            const Color(0xFFF4FBF5).withOpacity(.96),
          ],
        ),
        borderRadius: BorderRadius.circular(24),
        border: Border.all(color: accent.withOpacity(.12)),
        boxShadow: [
          BoxShadow(
            color: accent.withOpacity(.06),
            blurRadius: 18,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Container(
            width: 38,
            height: 38,
            decoration: BoxDecoration(
              gradient: LinearGradient(colors: [accent.withOpacity(.16), accent.withOpacity(.08)]),
              borderRadius: BorderRadius.circular(12),
            ),
            alignment: Alignment.center,
            child: iconWidget ?? Icon(icon, color: accent, size: 22),
          ),
          const SizedBox(height: 8),
          Text(
            label,
            style: Theme.of(context).textTheme.bodySmall?.copyWith(
              color: const Color(0xFF6C7E72),
              fontWeight: FontWeight.w700,
            ),
          ),
          Text(
            value,
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
            style: Theme.of(context).textTheme.titleMedium?.copyWith(
              color: const Color(0xFF15351C),
              fontWeight: FontWeight.w800,
            ),
          ),
        ],
      ),
    );
    if (onTap == null) return card;
    return InkWell(
      borderRadius: BorderRadius.circular(24),
      onTap: onTap,
      child: card,
    );
  }
}

class _IdentityLedger extends StatelessWidget {
  const _IdentityLedger({required this.tokens, required this.rows});

  final BrandTokens tokens;
  final List<_IdentityRowData> rows;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Colors.white.withOpacity(.96),
            const Color(0xFFF4FBF5).withOpacity(.98),
          ],
        ),
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: tokens.borderColor.withOpacity(.35)),
        boxShadow: [
          BoxShadow(
            color: tokens.primaryButtonGradient.first.withOpacity(.05),
            blurRadius: 18,
            offset: const Offset(0, 10),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const _SectionLabel(
            title: 'Identity',
            subtitle: 'Key profile information and account links.',
          ),
          const SizedBox(height: 12),
          Wrap(
            spacing: 12,
            runSpacing: 12,
            children: [
              for (final row in rows)
                _IdentityTile(label: row.label, value: row.value),
            ],
          ),
        ],
      ),
    );
  }
}

class _IdentityTile extends StatelessWidget {
  const _IdentityTile({required this.label, required this.value});

  final String label;
  final String value;

  @override
  Widget build(BuildContext context) {
    return ConstrainedBox(
      constraints: const BoxConstraints(minWidth: 140, maxWidth: 220),
      child: Container(
        padding: const EdgeInsets.all(14),
        decoration: BoxDecoration(
          color: const Color(0xFFF7FBF7),
          borderRadius: BorderRadius.circular(18),
          border: Border.all(color: const Color(0xFFD9EBDD)),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              label,
              style: Theme.of(context).textTheme.bodySmall?.copyWith(
                color: const Color(0xFF6C7E72),
                fontWeight: FontWeight.w700,
              ),
            ),
            const SizedBox(height: 6),
            Text(
              value,
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                color: const Color(0xFF15351C),
                fontWeight: FontWeight.w700,
                height: 1.3,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _ActionDock extends StatelessWidget {
  const _ActionDock({
    required this.tokens,
    required this.profile,
    required this.walletRechargeEnabled,
    required this.onWallet,
    required this.onApplications,
  });

  final BrandTokens tokens;
  final ProfileDto profile;
  final bool walletRechargeEnabled;
  final VoidCallback onWallet;
  final VoidCallback onApplications;

  @override
  Widget build(BuildContext context) {
    final items = <_ActionTileData>[
      if (walletRechargeEnabled)
        _ActionTileData(
          icon: Icons.account_balance_wallet_rounded,
          iconWidget: const CoinLottie(size: 24),
          title: 'Wallet',
          accent: const Color(0xFF06B430),
          onTap: onWallet,
        ),
      _ActionTileData(
        icon: Icons.assignment_rounded,
        title: 'Applications',
        accent: const Color(0xFF5B3FC5),
        onTap: onApplications,
      ),
      if (profile.isNormalUser)
        _ActionTileData(
          icon: Icons.mic_external_on_rounded,
          title: 'Apply host',
          accent: const Color(0xFFFFB400),
          onTap: () => Get.toNamed(Routes.applyHost),
        ),
      if (profile.isNormalUser)
        _ActionTileData(
          icon: Icons.apartment_rounded,
          title: 'Apply agency',
          accent: const Color(0xFF1E88E5),
          onTap: () => Get.toNamed(Routes.applyAgency),
        ),
    ];

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const _SectionLabel(
          title: 'Quick actions',
          subtitle: 'Common profile and account tasks.',
        ),
        const SizedBox(height: 12),
        GridView.count(
          shrinkWrap: true,
          physics: const NeverScrollableScrollPhysics(),
          crossAxisCount: 2,
          crossAxisSpacing: 12,
          mainAxisSpacing: 12,
          childAspectRatio: 1.55,
          children: [
            for (final item in items)
              InkWell(
                borderRadius: BorderRadius.circular(24),
                onTap: item.onTap,
                child: Container(
                  padding: const EdgeInsets.all(18),
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                      colors: [
                        Colors.white.withOpacity(.98),
                        const Color(0xFFF4FBF5).withOpacity(.96),
                      ],
                    ),
                    borderRadius: BorderRadius.circular(24),
                    border: Border.all(color: item.accent.withOpacity(.12)),
                    boxShadow: [
                      BoxShadow(
                        color: item.accent.withOpacity(.06),
                        blurRadius: 16,
                        offset: const Offset(0, 10),
                      ),
                    ],
                  ),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Container(
                        width: 42,
                        height: 42,
                        decoration: BoxDecoration(
                          color: item.accent.withOpacity(.12),
                          borderRadius: BorderRadius.circular(13),
                        ),
                        alignment: Alignment.center,
                        child:
                            item.iconWidget ??
                            Icon(item.icon, color: item.accent),
                      ),
                      Text(
                        item.title,
                        style: Theme.of(
                          context,
                        ).textTheme.titleMedium?.copyWith(
                          color: const Color(0xFF15351C),
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                    ],
                  ),
                ),
              ),
          ],
        ),
      ],
    );
  }
}

class _AgencyProfileBoard extends StatelessWidget {
  const _AgencyProfileBoard({required this.tokens, required this.agency});

  final BrandTokens tokens;
  final ProfileAgencyDto agency;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Colors.white.withOpacity(.97),
            const Color(0xFFF4FBF5).withOpacity(.98),
          ],
        ),
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: tokens.borderColor.withOpacity(.35)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const _SectionLabel(
            title: 'Agency',
            subtitle: 'Connection details for the attached agency.',
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Container(
                width: 46,
                height: 46,
                decoration: BoxDecoration(
                  color: const Color(0xFF1E88E5).withOpacity(.12),
                  borderRadius: BorderRadius.circular(14),
                ),
                child: const Icon(
                  Icons.apartment_rounded,
                  color: Color(0xFF1E88E5),
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Text(
                  agency.name ?? 'Agency attached',
                  style: Theme.of(context).textTheme.titleLarge?.copyWith(
                    color: tokens.textPrimary,
                    fontWeight: FontWeight.w800,
                  ),
                ),
              ),
              Text(
                agency.isBlocked ? 'Blocked' : 'Active',
                style: Theme.of(context).textTheme.bodySmall?.copyWith(
                  color:
                      agency.isBlocked
                          ? tokens.dangerColor
                          : tokens.successColor,
                  fontWeight: FontWeight.w800,
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          _IdentityTile(
            label: 'Owner',
            value: agency.ownerName ?? 'Unavailable',
          ),
          const SizedBox(height: 10),
          if ((agency.contactEmail ?? '').isNotEmpty)
            _IdentityTile(label: 'Email', value: agency.contactEmail!)
          else
            const SizedBox.shrink(),
          if ((agency.contactEmail ?? '').isNotEmpty)
            const SizedBox(height: 10),
          if ((agency.contactPhone ?? '').isNotEmpty)
            _IdentityTile(label: 'Phone', value: agency.contactPhone!),
        ],
      ),
    );
  }
}

class _HostPerformanceBoard extends StatelessWidget {
  const _HostPerformanceBoard({
    required this.tokens,
    required this.range,
    required this.onRangeChanged,
    required this.report,
    required this.loading,
    required this.error,
    required this.onRetry,
  });

  final BrandTokens tokens;
  final _HostReportRange range;
  final ValueChanged<_HostReportRange> onRangeChanged;
  final HostEarningsReportDto? report;
  final bool loading;
  final String? error;
  final VoidCallback onRetry;

  @override
  Widget build(BuildContext context) {
    final period = switch (range) {
      _HostReportRange.today => report?.today,
      _HostReportRange.currentWeek => report?.currentWeek,
      _HostReportRange.lastWeek => report?.lastWeek,
    };
    final summary = period?.summary;
    final total =
        summary == null
            ? 0
            : summary.totalGiftedCoins + summary.videoCallEarnings;

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Colors.white.withOpacity(.97),
            const Color(0xFFF4FBF5).withOpacity(.98),
          ],
        ),
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: tokens.borderColor.withOpacity(.35)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const _SectionLabel(
            title: 'Host performance',
            subtitle: 'Today and weekly earnings at a glance.',
          ),
          const SizedBox(height: 12),
          Row(
            children: [
              Expanded(
                child: Text(
                  'Performance',
                  style: Theme.of(context).textTheme.titleLarge?.copyWith(
                    color: tokens.textPrimary,
                    fontWeight: FontWeight.w800,
                  ),
                ),
              ),
              if ((period?.label ?? '').isNotEmpty)
                Text(
                  period!.label,
                  style: Theme.of(context).textTheme.bodySmall?.copyWith(
                    color: tokens.textSecondary,
                    fontWeight: FontWeight.w700,
                  ),
                ),
            ],
          ),
          const SizedBox(height: 14),
          Row(
            children: [
              for (final item in _HostReportRange.values) ...[
                Expanded(
                  child: GestureDetector(
                    onTap: () => onRangeChanged(item),
                    child: AnimatedContainer(
                      duration: const Duration(milliseconds: 220),
                      curve: Curves.easeOutCubic,
                      margin: EdgeInsets.only(
                        right: item == _HostReportRange.values.last ? 0 : 8,
                      ),
                      padding: const EdgeInsets.symmetric(vertical: 12),
                      decoration: BoxDecoration(
                        color:
                            item == range
                                ? const Color(0xFF12371B)
                                : const Color(0xFFF2F7F2),
                        borderRadius: BorderRadius.circular(16),
                      ),
                      child: Text(
                        item.label,
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          color:
                              item == range
                                  ? Colors.white
                                  : tokens.textSecondary,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                    ),
                  ),
                ),
              ],
            ],
          ),
          const SizedBox(height: 18),
          if (loading && report == null)
            const Center(child: CircularProgressIndicator())
          else if (summary == null)
            _ProfileMessageInline(
              tokens: tokens,
              title: 'Report unavailable',
              message: error ?? 'No host earnings data is available yet.',
              actionLabel: 'Retry',
              onTap: onRetry,
            )
          else ...[
            GridView.count(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              crossAxisCount: 2,
              crossAxisSpacing: 10,
              mainAxisSpacing: 10,
              childAspectRatio: 1.5,
              children: [
                _ReportStat(
                  label: 'Video room minutes',
                  value: '${summary.totalVideoRoomMinutes}',
                  accent: const Color(0xFF06B430),
                ),
                _ReportStat(
                  label: 'Gifted coins',
                  value: NumberFormat.compact().format(
                    summary.totalGiftedCoins,
                  ),
                  accent: const Color(0xFFFFB400),
                ),
                _ReportStat(
                  label: 'Video call',
                  value:
                      '${summary.videoCallMinutes}m / ${NumberFormat.compact().format(summary.videoCallEarnings)}',
                  accent: const Color(0xFF1E88E5),
                ),
                _ReportStat(
                  label: 'PK rooms',
                  value:
                      '${summary.pkRoomCount} / ${NumberFormat.compact().format(summary.pkEarnings)}',
                  accent: const Color(0xFFEF7D57),
                ),
              ],
            ),
            const SizedBox(height: 14),
            _MiniLedgerRow(
              label: 'Video room gifts',
              value:
                  '${NumberFormat.compact().format(summary.videoRoomGiftsCoins)} coins',
            ),
            _MiniLedgerRow(
              label: 'PK gift coins',
              value:
                  '${NumberFormat.compact().format(summary.pkGiftCoins)} coins',
            ),
            _MiniLedgerRow(
              label: 'Grand total',
              value: '${NumberFormat.compact().format(total)} coins',
            ),
          ],
        ],
      ),
    );
  }
}

class _ReportStat extends StatelessWidget {
  const _ReportStat({
    required this.label,
    required this.value,
    required this.accent,
  });

  final String label;
  final String value;
  final Color accent;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: accent.withOpacity(.09),
        borderRadius: BorderRadius.circular(18),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            label,
            style: Theme.of(context).textTheme.bodySmall?.copyWith(
              color: accent,
              fontWeight: FontWeight.w700,
            ),
          ),
          const Spacer(),
          Text(
            value,
            style: Theme.of(context).textTheme.titleMedium?.copyWith(
              color: const Color(0xFF15351C),
              fontWeight: FontWeight.w800,
            ),
          ),
        ],
      ),
    );
  }
}

class _MiniLedgerRow extends StatelessWidget {
  const _MiniLedgerRow({required this.label, required this.value});

  final String label;
  final String value;

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.only(top: 10),
      child: Row(
        children: [
          Text(
            label,
            style: Theme.of(context).textTheme.bodyMedium?.copyWith(
              color: const Color(0xFF6C7E72),
              fontWeight: FontWeight.w600,
            ),
          ),
          const Spacer(),
          Text(
            value,
            style: Theme.of(context).textTheme.bodyMedium?.copyWith(
              color: const Color(0xFF15351C),
              fontWeight: FontWeight.w800,
            ),
          ),
        ],
      ),
    );
  }
}

class _ModerationBoard extends StatelessWidget {
  const _ModerationBoard({required this.tokens});

  final BrandTokens tokens;

  @override
  Widget build(BuildContext context) {
    final items = <_ActionTileData>[
      _ActionTileData(
        icon: Icons.block_rounded,
        title: 'Blocked users',
        accent: const Color(0xFFE35D6A),
        onTap: () => Get.toNamed(Routes.profileBlockedUsers),
      ),
      _ActionTileData(
        icon: Icons.mark_email_unread_rounded,
        title: 'Unblock requests',
        accent: const Color(0xFF1E88E5),
        onTap: () => Get.toNamed(Routes.profileUnblockRequests),
      ),
      _ActionTileData(
        icon: Icons.history_rounded,
        title: 'Moderation history',
        accent: const Color(0xFF5B3FC5),
        onTap: () => Get.toNamed(Routes.profileModerationHistory),
      ),
    ];

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        const _SectionLabel(
          title: 'Moderation',
          subtitle: 'Fast access to host safety tools.',
        ),
        const SizedBox(height: 12),
        for (int i = 0; i < items.length; i++) ...[
          InkWell(
            borderRadius: BorderRadius.circular(24),
            onTap: items[i].onTap,
            child: Container(
              padding: const EdgeInsets.all(18),
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [
                    Colors.white.withOpacity(.98),
                    const Color(0xFFF4FBF5).withOpacity(.96),
                  ],
                ),
                borderRadius: BorderRadius.circular(24),
                border: Border.all(color: items[i].accent.withOpacity(.12)),
                boxShadow: [
                  BoxShadow(
                    color: items[i].accent.withOpacity(.06),
                    blurRadius: 16,
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
                      color: items[i].accent.withOpacity(.12),
                      borderRadius: BorderRadius.circular(13),
                    ),
                    child: Icon(items[i].icon, color: items[i].accent),
                  ),
                  const SizedBox(width: 14),
                  Expanded(
                    child: Text(
                      items[i].title,
                      style: Theme.of(context).textTheme.titleMedium?.copyWith(
                        color: const Color(0xFF15351C),
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                  ),
                  const Icon(Icons.chevron_right_rounded),
                ],
              ),
            ),
          ),
          if (i != items.length - 1) const SizedBox(height: 12),
        ],
      ],
    );
  }
}

class _ProfileMessageBoard extends StatelessWidget {
  const _ProfileMessageBoard({
    required this.tokens,
    required this.icon,
    required this.title,
    required this.message,
    this.actionLabel,
    this.onTap,
  });

  final BrandTokens tokens;
  final IconData icon;
  final String title;
  final String message;
  final String? actionLabel;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 18),
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Colors.white.withOpacity(.97),
              const Color(0xFFF4FBF5).withOpacity(.98),
            ],
          ),
          borderRadius: BorderRadius.circular(28),
          border: Border.all(color: tokens.borderColor.withOpacity(.35)),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 72,
              height: 72,
              decoration: BoxDecoration(
                color: tokens.chipColor,
                borderRadius: BorderRadius.circular(22),
              ),
              child: Icon(
                icon,
                color: tokens.primaryButtonGradient.first,
                size: 34,
              ),
            ),
            const SizedBox(height: 16),
            Text(
              title,
              textAlign: TextAlign.center,
              style: Theme.of(context).textTheme.titleLarge?.copyWith(
                color: tokens.textPrimary,
                fontWeight: FontWeight.w800,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              message,
              textAlign: TextAlign.center,
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                color: tokens.textSecondary,
                height: 1.4,
              ),
            ),
            if (actionLabel != null && onTap != null) ...[
              const SizedBox(height: 18),
              FilledButton(onPressed: onTap, child: Text(actionLabel!)),
            ],
          ],
        ),
      ),
    );
  }
}

class _ProfileMessageInline extends StatelessWidget {
  const _ProfileMessageInline({
    required this.tokens,
    required this.title,
    required this.message,
    required this.actionLabel,
    required this.onTap,
  });

  final BrandTokens tokens;
  final String title;
  final String message;
  final String actionLabel;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        color: const Color(0xFFF7FBF7),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: const Color(0xFFD9EBDD)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            title,
            style: Theme.of(context).textTheme.titleMedium?.copyWith(
              color: tokens.textPrimary,
              fontWeight: FontWeight.w800,
            ),
          ),
          const SizedBox(height: 6),
          Text(
            message,
            style: Theme.of(
              context,
            ).textTheme.bodyMedium?.copyWith(color: tokens.textSecondary),
          ),
          const SizedBox(height: 12),
          FilledButton(onPressed: onTap, child: Text(actionLabel)),
        ],
      ),
    );
  }
}

class _IdentityRowData {
  const _IdentityRowData(this.label, this.value);

  final String label;
  final String value;
}

class _ActionTileData {
  const _ActionTileData({
    required this.icon,
    this.iconWidget,
    required this.title,
    required this.accent,
    required this.onTap,
  });

  final IconData icon;
  final Widget? iconWidget;
  final String title;
  final Color accent;
  final VoidCallback onTap;
}

Color? _parseProfileColor(String? raw) {
  if (raw == null) return null;
  var value = raw.trim();
  if (value.isEmpty) return null;
  if (value.startsWith('#')) value = value.substring(1);
  if (value.length == 6) value = 'FF$value';
  if (value.length != 8) return null;
  final parsed = int.tryParse(value, radix: 16);
  if (parsed == null) return null;
  return Color(parsed);
}
