import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';

import '../../../app/brand/brand.dart';
import '../../../app/widgets/coin_lottie.dart';
import '../../../services/api_client.dart';
import '../../../services/app_settings_service.dart';
import '../../wallet/services/wallet_api.dart';
import '../../wallet/widgets/recharge_bottom_sheet.dart';
import '../models/subscription_plan_dto.dart';
import '../models/user_subscription_dto.dart';
import '../services/subscriptions_api.dart';
import '../widgets/choose_plan_sheet.dart';

class SubscriptionsPage extends StatefulWidget {
  const SubscriptionsPage({super.key});

  @override
  State<SubscriptionsPage> createState() => _SubscriptionsPageState();
}

class _SubscriptionsPageState extends State<SubscriptionsPage> {
  late final SubscriptionsApi _api;
  late final WalletApi _walletApi;

  bool _loading = true;
  bool _buying = false;
  String? _error;
  int? _walletBalanceCoins;
  List<SubscriptionPlanDto> _plans = const <SubscriptionPlanDto>[];
  List<UserSubscriptionDto> _subscriptions = const <UserSubscriptionDto>[];

  @override
  void initState() {
    super.initState();
    _api = SubscriptionsApi(Get.find<ApiClient>());
    _walletApi = Get.find<WalletApi>();
    if (!Get.find<AppSettingsService>().subscriptionsEnabled) {
      _loading = false;
      _error = 'Subscriptions are currently unavailable.';
      return;
    }
    _load();
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });

    try {
      final results = await Future.wait([
        _api.fetchPlans(),
        _api.mySubscriptions(),
        _walletApi.fetchSummary(),
      ]);

      if (!mounted) return;
      setState(() {
        _plans = results[0] as List<SubscriptionPlanDto>;
        _subscriptions = results[1] as List<UserSubscriptionDto>;
        _walletBalanceCoins = (results[2] as dynamic).balance as int;
      });
    } catch (error) {
      if (!mounted) return;
      setState(() {
        _error = error.toString().replaceFirst('Exception: ', '');
      });
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  Future<void> _purchasePlan(SubscriptionPlanDto plan) async {
    if (_buying) return;
    setState(() {
      _buying = true;
      _error = null;
    });

    try {
      await _api.purchase(planId: plan.id);
      if (!mounted) return;
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('${plan.name} unlocked successfully.')),
      );
      await _load();
    } catch (error) {
      if (!mounted) return;
      final message = error.toString().replaceFirst('Exception: ', '');
      setState(() => _error = message);
      if (isInsufficientCoinsErrorMessage(message)) {
        await showRechargeWalletSheet(
          reasonTitle: 'Not enough coins',
          reasonMessage:
              'You need more coins to unlock ${plan.name}. Recharge and try again.',
        );
        if (mounted) {
          await _load();
        }
      } else {
        ScaffoldMessenger.of(
          context,
        ).showSnackBar(SnackBar(content: Text(message)));
      }
    } finally {
      if (mounted) setState(() => _buying = false);
    }
  }

  Future<void> _openPlanSheet() async {
    final plans = _plans.where((plan) => plan.isActive).toList();
    if (plans.isEmpty || _buying) return;

    final plan = await ChoosePlanSheet.show(context, plans: plans);
    if (plan == null || !mounted) return;
    await _purchasePlan(plan);
  }

  UserSubscriptionDto? get _activeSubscription {
    final active = _subscriptions.where((item) => item.isActiveNow).toList();
    if (active.isEmpty) return null;
    active.sort((a, b) {
      final aEnds = a.endsAt ?? DateTime.fromMillisecondsSinceEpoch(0);
      final bEnds = b.endsAt ?? DateTime.fromMillisecondsSinceEpoch(0);
      return bEnds.compareTo(aEnds);
    });
    return active.first;
  }

  List<UserSubscriptionDto> get _historySubscriptions {
    final active = _activeSubscription;
    return _subscriptions.where((item) => item != active).toList();
  }

  @override
  Widget build(BuildContext context) {
    final settings = Get.find<AppSettingsService>();
    final tokens = getBrandTokens(settings.brandKey);
    final active = _activeSubscription;
    final activePlans = _plans.where((plan) => plan.isActive).toList();

    return Scaffold(
      backgroundColor: tokens.backgroundGradient.first,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: IconThemeData(color: tokens.textPrimary),
        actionsIconTheme: IconThemeData(color: tokens.textPrimary),
        title: Text(
          'Subscriptions',
          style: TextStyle(
            color: tokens.textPrimary,
            fontWeight: FontWeight.w800,
          ),
        ),
      ),
      body: RefreshIndicator(
        onRefresh: _load,
        color: tokens.primaryButtonGradient.first,
        child: CustomScrollView(
          physics: const BouncingScrollPhysics(
            parent: AlwaysScrollableScrollPhysics(),
          ),
          slivers: [
            SliverPadding(
              padding: const EdgeInsets.fromLTRB(18, 8, 18, 0),
              sliver: SliverToBoxAdapter(
                child: _MembershipBoard(
                  tokens: tokens,
                  walletBalanceCoins: _walletBalanceCoins,
                  activeSubscription: active,
                  hasPlans: activePlans.isNotEmpty,
                  buying: _buying,
                  onChoosePlan: _openPlanSheet,
                ),
              ),
            ),
            if (_loading)
              const SliverFillRemaining(
                hasScrollBody: false,
                child: Center(child: CircularProgressIndicator()),
              )
            else if (_error != null)
              SliverFillRemaining(
                hasScrollBody: false,
                child: _SubscriptionsMessage(
                  tokens: tokens,
                  icon: Icons.sync_problem_rounded,
                  title: 'Unable to load subscriptions',
                  subtitle: _error!,
                  actionLabel: 'Retry',
                  onAction: _load,
                ),
              )
            else ...[
              SliverPadding(
                padding: const EdgeInsets.fromLTRB(18, 16, 18, 0),
                sliver: SliverToBoxAdapter(
                  child: _SectionLabel(
                    tokens: tokens,
                    title: 'Plans',
                    trailing: '${activePlans.length} tiers',
                  ),
                ),
              ),
              SliverPadding(
                padding: const EdgeInsets.fromLTRB(18, 12, 18, 0),
                sliver: SliverList(
                  delegate: SliverChildBuilderDelegate((context, index) {
                    final plan = activePlans[index];
                    final isCurrent =
                        active?.planId == plan.id &&
                        (active?.isActiveNow ?? false);
                    final isUpgrade =
                        (active?.durationDays ?? 0) < plan.durationDays;
                    return Padding(
                      padding: EdgeInsets.only(
                        bottom: index == activePlans.length - 1 ? 0 : 14,
                      ),
                      child: _PlanFeatureBoard(
                        tokens: tokens,
                        plan: plan,
                        current: isCurrent,
                        buying: _buying,
                        accentIndex: index,
                        actionLabel:
                            isCurrent
                                ? 'Current plan'
                                : isUpgrade
                                ? 'Upgrade now'
                                : 'Activate',
                        onAction: isCurrent ? null : () => _purchasePlan(plan),
                      ),
                    );
                  }, childCount: activePlans.length),
                ),
              ),
              SliverPadding(
                padding: const EdgeInsets.fromLTRB(18, 20, 18, 0),
                sliver: SliverToBoxAdapter(
                  child: _SectionLabel(
                    tokens: tokens,
                    title: 'Access',
                    trailing: active == null ? 'Inactive' : 'Active',
                  ),
                ),
              ),
              SliverPadding(
                padding: const EdgeInsets.fromLTRB(18, 12, 18, 0),
                sliver: SliverToBoxAdapter(
                  child:
                      active == null
                          ? _SubscriptionsMessage(
                            tokens: tokens,
                            icon: Icons.lock_outline_rounded,
                            title: 'No active plan',
                            subtitle: 'Choose a tier to unlock access.',
                          )
                          : _CurrentAccessBoard(
                            tokens: tokens,
                            subscription: active,
                          ),
                ),
              ),
              if (_historySubscriptions.isNotEmpty)
                SliverPadding(
                  padding: const EdgeInsets.fromLTRB(18, 20, 18, 24),
                  sliver: SliverToBoxAdapter(
                    child: _HistoryColumn(
                      tokens: tokens,
                      history: _historySubscriptions,
                    ),
                  ),
                )
              else
                const SliverToBoxAdapter(child: SizedBox(height: 28)),
            ],
          ],
        ),
      ),
    );
  }
}

extension on UserSubscriptionDto? {
  int get durationDays {
    final start = this?.startsAt;
    final end = this?.endsAt;
    if (start == null || end == null) return 0;
    return end.difference(start).inDays;
  }
}

class _MembershipBoard extends StatelessWidget {
  const _MembershipBoard({
    required this.tokens,
    required this.walletBalanceCoins,
    required this.activeSubscription,
    required this.hasPlans,
    required this.buying,
    required this.onChoosePlan,
  });

  final BrandTokens tokens;
  final int? walletBalanceCoins;
  final UserSubscriptionDto? activeSubscription;
  final bool hasPlans;
  final bool buying;
  final VoidCallback onChoosePlan;

  @override
  Widget build(BuildContext context) {
    final active = activeSubscription;
    final title = active?.planName ?? 'Membership';
    final subtitle =
        active == null
            ? 'Choose a tier to unlock member access.'
            : active.remaining == null
            ? 'Your access is active.'
            : '${active.remaining!.inDays} days left.';

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(30),
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            Colors.white,
            tokens.cardGradient.first,
            tokens.cardGradient.last,
          ],
        ),
        boxShadow: [
          BoxShadow(
            color: tokens.primaryButtonGradient.first.withOpacity(.24),
            blurRadius: 36,
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
                width: 54,
                height: 54,
                decoration: BoxDecoration(
                  color: tokens.primaryButtonGradient.first.withOpacity(.12),
                  borderRadius: BorderRadius.circular(18),
                ),
                child: Icon(
                  Icons.workspace_premium_rounded,
                  color: tokens.primaryButtonGradient.first,
                  size: 28,
                ),
              ),
              const Spacer(),
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 14,
                  vertical: 8,
                ),
                decoration: BoxDecoration(
                  color: tokens.chipColor,
                  borderRadius: BorderRadius.circular(999),
                ),
                child: Text(
                  active == null ? 'Ready' : 'Live',
                  style: TextStyle(
                    color: tokens.primaryButtonGradient.first,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 18),
          Text(
            title,
            style: Theme.of(context).textTheme.headlineSmall?.copyWith(
              color: tokens.textPrimary,
              fontWeight: FontWeight.w800,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            subtitle,
            style: Theme.of(context).textTheme.bodyMedium?.copyWith(
              color: tokens.textSecondary,
              height: 1.38,
            ),
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: _HeroInfoPill(
                  label: 'Wallet',
                  value:
                      '${NumberFormat.compact().format(walletBalanceCoins ?? 0)} coins',
                  showCoin: true,
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _HeroInfoPill(
                  label: 'Plans',
                  value: hasPlans ? 'Available' : 'Offline',
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _HeroInfoPill(
                  label: 'Status',
                  value:
                      buying
                          ? 'Processing'
                          : (active == null ? 'Idle' : 'Active'),
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          SizedBox(
            width: double.infinity,
            child: FilledButton(
              onPressed: hasPlans && !buying ? onChoosePlan : null,
              style: FilledButton.styleFrom(
                backgroundColor: Colors.white,
                foregroundColor: tokens.primaryButtonGradient.first,
                padding: const EdgeInsets.symmetric(vertical: 15),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(18),
                ),
              ),
              child: Text(active == null ? 'Choose plan' : 'Change plan'),
            ),
          ),
        ],
      ),
    );
  }
}

class _HeroInfoPill extends StatelessWidget {
  const _HeroInfoPill({
    required this.label,
    required this.value,
    this.showCoin = false,
  });

  final String label;
  final String value;
  final bool showCoin;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(.72),
        borderRadius: BorderRadius.circular(18),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            label,
            style: Theme.of(context).textTheme.bodySmall?.copyWith(
              color: const Color(0xFF5F7666),
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 6),
          Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              if (showCoin) ...[
                const CoinLottie(size: 18),
                const SizedBox(width: 6),
              ],
              Flexible(
                child: Text(
                  value,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: Theme.of(context).textTheme.titleMedium?.copyWith(
                    color: const Color(0xFF15351C),
                    fontWeight: FontWeight.w800,
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class _SectionLabel extends StatelessWidget {
  const _SectionLabel({
    required this.tokens,
    required this.title,
    required this.trailing,
  });

  final BrandTokens tokens;
  final String title;
  final String trailing;

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Text(
          title,
          style: Theme.of(context).textTheme.titleLarge?.copyWith(
            color: tokens.textPrimary,
            fontWeight: FontWeight.w800,
          ),
        ),
        const Spacer(),
        Text(
          trailing,
          style: Theme.of(context).textTheme.bodySmall?.copyWith(
            color: tokens.textSecondary,
            fontWeight: FontWeight.w700,
          ),
        ),
      ],
    );
  }
}

class _PlanFeatureBoard extends StatelessWidget {
  const _PlanFeatureBoard({
    required this.tokens,
    required this.plan,
    required this.current,
    required this.buying,
    required this.accentIndex,
    required this.actionLabel,
    required this.onAction,
  });

  final BrandTokens tokens;
  final SubscriptionPlanDto plan;
  final bool current;
  final bool buying;
  final int accentIndex;
  final String actionLabel;
  final VoidCallback? onAction;

  @override
  Widget build(BuildContext context) {
    final accent =
        [
          const Color(0xFF06B430),
          const Color(0xFF1F8F45),
          const Color(0xFFFFB400),
          const Color(0xFF6A9E3F),
        ][accentIndex % 4];

    return Container(
      padding: const EdgeInsets.all(0),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(.9),
        borderRadius: BorderRadius.circular(28),
        border: Border.all(
          color:
              current
                  ? accent.withOpacity(.45)
                  : tokens.borderColor.withOpacity(.35),
          width: current ? 1.4 : 1,
        ),
      ),
      child: Row(
        children: [
          Container(
            width: 10,
            decoration: BoxDecoration(
              color: accent,
              borderRadius: const BorderRadius.horizontal(
                left: Radius.circular(28),
              ),
            ),
          ),
          Expanded(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(16, 16, 16, 16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              plan.name,
                              style: Theme.of(
                                context,
                              ).textTheme.headlineSmall?.copyWith(
                                color: tokens.textPrimary,
                                fontWeight: FontWeight.w800,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              '${plan.durationDays} day access',
                              style: Theme.of(
                                context,
                              ).textTheme.bodyMedium?.copyWith(
                                color: tokens.textSecondary,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(width: 12),
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 12,
                          vertical: 8,
                        ),
                        decoration: BoxDecoration(
                          color: accent.withOpacity(.12),
                          borderRadius: BorderRadius.circular(999),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            const CoinLottie(size: 18),
                            const SizedBox(width: 8),
                            Text(
                              '${NumberFormat.compact().format(plan.priceCoins)} coins',
                              style: TextStyle(
                                color: accent,
                                fontWeight: FontWeight.w800,
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),
                  Column(
                    children: [
                      for (final perk in plan.perks.take(4))
                        Padding(
                          padding: const EdgeInsets.only(bottom: 8),
                          child: Row(
                            children: [
                              Icon(
                                Icons.check_circle_rounded,
                                size: 16,
                                color: accent,
                              ),
                              const SizedBox(width: 8),
                              Expanded(
                                child: Text(
                                  perk,
                                  style: TextStyle(
                                    color: tokens.textPrimary,
                                    fontWeight: FontWeight.w600,
                                    fontSize: 13,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  SizedBox(
                    width: double.infinity,
                    child: FilledButton(
                      onPressed: buying ? null : onAction,
                      style: FilledButton.styleFrom(
                        backgroundColor:
                            current
                                ? accent.withOpacity(.14)
                                : tokens.primaryButtonGradient.first,
                        foregroundColor: current ? accent : Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(18),
                        ),
                      ),
                      child: Text(actionLabel),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _CurrentAccessBoard extends StatelessWidget {
  const _CurrentAccessBoard({required this.tokens, required this.subscription});

  final BrandTokens tokens;
  final UserSubscriptionDto subscription;

  @override
  Widget build(BuildContext context) {
    final endsAt = subscription.endsAt?.toLocal();
    final startsAt = subscription.startsAt?.toLocal();

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(28),
        boxShadow: [
          BoxShadow(
            color: tokens.primaryButtonGradient.first.withOpacity(.08),
            blurRadius: 24,
            offset: const Offset(0, 14),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  color: tokens.chipColor,
                  borderRadius: BorderRadius.circular(16),
                ),
                child: Icon(
                  Icons.verified_rounded,
                  color: tokens.primaryButtonGradient.first,
                ),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      subscription.planName ?? 'Subscription',
                      style: Theme.of(context).textTheme.titleLarge?.copyWith(
                        color: tokens.textPrimary,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      subscription.status.toUpperCase(),
                      style: Theme.of(context).textTheme.bodySmall?.copyWith(
                        color: tokens.successColor,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
          const SizedBox(height: 18),
          Row(
            children: [
              Expanded(
                child: _AccessStat(
                  label: 'Starts',
                  value:
                      startsAt == null
                          ? 'Immediate'
                          : DateFormat('dd MMM yyyy').format(startsAt),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _AccessStat(
                  label: 'Ends',
                  value:
                      endsAt == null
                          ? 'Open ended'
                          : DateFormat('dd MMM yyyy').format(endsAt),
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _AccessStat(
                  label: 'Remaining',
                  value:
                      subscription.remaining == null
                          ? '--'
                          : '${subscription.remaining!.inDays} days',
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class _AccessStat extends StatelessWidget {
  const _AccessStat({required this.label, required this.value});

  final String label;
  final String value;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: const Color(0xFFF7FBF7),
        borderRadius: BorderRadius.circular(18),
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
  }
}

class _HistoryColumn extends StatelessWidget {
  const _HistoryColumn({required this.tokens, required this.history});

  final BrandTokens tokens;
  final List<UserSubscriptionDto> history;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _SectionLabel(
          tokens: tokens,
          title: 'History',
          trailing: '${history.length} items',
        ),
        const SizedBox(height: 12),
        Container(
          padding: const EdgeInsets.all(20),
          decoration: BoxDecoration(
            color: Colors.white.withOpacity(.88),
            borderRadius: BorderRadius.circular(28),
          ),
          child: Column(
            children: [
              for (int i = 0; i < history.length; i++) ...[
                _HistoryRow(item: history[i], tokens: tokens),
                if (i != history.length - 1)
                  Divider(
                    height: 26,
                    color: tokens.borderColor.withOpacity(.35),
                  ),
              ],
            ],
          ),
        ),
      ],
    );
  }
}

class _HistoryRow extends StatelessWidget {
  const _HistoryRow({required this.item, required this.tokens});

  final UserSubscriptionDto item;
  final BrandTokens tokens;

  @override
  Widget build(BuildContext context) {
    final endsAt = item.endsAt?.toLocal();
    final startsAt = item.startsAt?.toLocal();
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          width: 12,
          height: 12,
          margin: const EdgeInsets.only(top: 6),
          decoration: BoxDecoration(
            color:
                item.isExpired
                    ? tokens.textSecondary.withOpacity(.45)
                    : tokens.primaryButtonGradient.first.withOpacity(.8),
            shape: BoxShape.circle,
          ),
        ),
        const SizedBox(width: 14),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                item.planName ?? 'Subscription',
                style: Theme.of(context).textTheme.titleMedium?.copyWith(
                  color: tokens.textPrimary,
                  fontWeight: FontWeight.w800,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                [
                  if (startsAt != null)
                    DateFormat('dd MMM yyyy').format(startsAt),
                  if (endsAt != null) DateFormat('dd MMM yyyy').format(endsAt),
                ].join('  •  '),
                style: Theme.of(context).textTheme.bodySmall?.copyWith(
                  color: tokens.textSecondary,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ],
          ),
        ),
        const SizedBox(width: 12),
        Text(
          item.status.toUpperCase(),
          style: Theme.of(context).textTheme.bodySmall?.copyWith(
            color: item.isExpired ? tokens.textSecondary : tokens.successColor,
            fontWeight: FontWeight.w800,
          ),
        ),
      ],
    );
  }
}

class _SubscriptionsMessage extends StatelessWidget {
  const _SubscriptionsMessage({
    required this.tokens,
    required this.icon,
    required this.title,
    required this.subtitle,
    this.actionLabel,
    this.onAction,
  });

  final BrandTokens tokens;
  final IconData icon;
  final String title;
  final String subtitle;
  final String? actionLabel;
  final VoidCallback? onAction;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Container(
        margin: const EdgeInsets.symmetric(horizontal: 18),
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          color: Colors.white.withOpacity(.9),
          borderRadius: BorderRadius.circular(28),
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
              subtitle,
              textAlign: TextAlign.center,
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                color: tokens.textSecondary,
                height: 1.4,
              ),
            ),
            if (actionLabel != null && onAction != null) ...[
              const SizedBox(height: 18),
              FilledButton(onPressed: onAction, child: Text(actionLabel!)),
            ],
          ],
        ),
      ),
    );
  }
}
