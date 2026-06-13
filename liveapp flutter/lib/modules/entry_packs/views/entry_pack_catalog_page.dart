import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';

import '../../../app/brand/brand.dart';
import '../../../app/widgets/coin_lottie.dart';
import '../../../app/widgets/haptics.dart';
import '../../../app/widgets/remote_media_art.dart';
import '../../../services/app_settings_service.dart';
import '../../wallet/services/wallet_api.dart';
import '../../wallet/widgets/recharge_bottom_sheet.dart';
import '../models/entry_pack_dto.dart';
import '../models/user_entry_pack_dto.dart';
import '../services/entry_pack_api.dart';

enum _EntryCatalogFilter {
  all('All'),
  active('Live now'),
  owned('Owned'),
  available('Store'),
  expired('Expired');

  const _EntryCatalogFilter(this.label);
  final String label;
}

class EntryPackCatalogPage extends StatefulWidget {
  const EntryPackCatalogPage({super.key});

  @override
  State<EntryPackCatalogPage> createState() => _EntryPackCatalogPageState();
}

class _EntryPackCatalogPageState extends State<EntryPackCatalogPage> {
  late final EntryPackApi _api;
  late final WalletApi _walletApi;

  bool _loading = true;
  bool _submitting = false;
  String? _error;
  int? _walletBalanceCoins;
  List<EntryPackDto> _packs = const <EntryPackDto>[];
  EntryPackStateDto? _state;
  _EntryCatalogFilter _filter = _EntryCatalogFilter.all;

  @override
  void initState() {
    super.initState();
    _api = Get.find<EntryPackApi>();
    _walletApi = Get.find<WalletApi>();
    if (!Get.find<AppSettingsService>().entryEffectsEnabled) {
      _loading = false;
      _error = 'Entry effects are currently unavailable.';
      return;
    }
    _load();
  }

  Future<void> _load() async {
    try {
      setState(() {
        _loading = true;
        _error = null;
      });
      final results = await Future.wait<dynamic>([
        _api.fetchPacks(),
        _api.fetchMine(),
        _walletApi.fetchSummary(),
      ]);

      final packs = results[0] as List<EntryPackDto>;
      final state = results[1] as EntryPackStateDto;
      final summary = results[2];

      if (!mounted) return;
      setState(() {
        _state = state;
        _packs = _mergePackState(packs, state);
        _walletBalanceCoins = summary.balance as int;
        _loading = false;
      });
    } catch (error) {
      if (!mounted) return;
      setState(() {
        _loading = false;
        _error = error.toString().replaceFirst('Exception: ', '');
      });
    }
  }

  List<EntryPackDto> _mergePackState(
    List<EntryPackDto> packs,
    EntryPackStateDto state,
  ) {
    return packs.map((pack) {
      final latest = _latestOwnedForPackFrom(state, pack.id);
      return pack.copyWith(
        owned: latest != null && !latest.isExpired,
        active:
            state.active?.entryPackId == pack.id &&
            !(state.active?.isExpired ?? false),
      );
    }).toList();
  }

  UserEntryPackDto? _latestOwnedForPackFrom(
    EntryPackStateDto state,
    int packId,
  ) {
    final matches =
        state.owned.where((owned) => owned.entryPackId == packId).toList()
          ..sort((a, b) {
            final aTime =
                a.purchasedAt ?? DateTime.fromMillisecondsSinceEpoch(0);
            final bTime =
                b.purchasedAt ?? DateTime.fromMillisecondsSinceEpoch(0);
            return bTime.compareTo(aTime);
          });
    return matches.isEmpty ? null : matches.first;
  }

  UserEntryPackDto? _latestOwnedForPack(int packId) {
    final state = _state;
    if (state == null) return null;
    return _latestOwnedForPackFrom(state, packId);
  }

  Future<void> _handlePackAction(EntryPackDto pack) async {
    if (_submitting) return;
    final owned = _latestOwnedForPack(pack.id);
    final canActivateOwned = owned != null && !owned.isExpired;

    setState(() => _submitting = true);
    try {
      if (!canActivateOwned) {
        await _api.purchase(pack.id);
      }
      await _api.activate(pack.id);
      Haptics.success();
      if (!mounted) return;
      await _load();
      Get.snackbar(
        'Entry updated',
        '${pack.name} is now active.',
        snackPosition: SnackPosition.BOTTOM,
      );
    } catch (error) {
      if (!mounted) return;
      Haptics.error();
      final message = error.toString().replaceFirst('Exception: ', '');
      if (isInsufficientCoinsErrorMessage(message)) {
        await showRechargeWalletSheet(
          reasonTitle: 'Not enough coins',
          reasonMessage:
              'You need more coins to unlock ${pack.name}. Recharge and try again.',
        );
        if (mounted) {
          await _load();
        }
      } else {
        Get.snackbar(
          'Entry pack',
          _friendlyActionMessage(message),
          snackPosition: SnackPosition.BOTTOM,
        );
      }
    } finally {
      if (mounted) setState(() => _submitting = false);
    }
  }

  String _friendlyActionMessage(String message) {
    switch (message.trim()) {
      case 'ENTRY_PACK_EXPIRED':
        return 'This entry pack expired. Purchase it again to reactivate it.';
      case 'ENTRY_PACK_NOT_OWNED':
        return 'Purchase this entry pack before activating it.';
      case 'ENTRY_PACK_INACTIVE':
        return 'This entry pack is currently unavailable.';
      default:
        return message.isEmpty
            ? 'Unable to update entry pack right now.'
            : message;
    }
  }

  List<EntryPackDto> get _filteredPacks {
    final now = DateTime.now();
    final packs = [..._packs]..sort((a, b) {
      if (a.active != b.active) return a.active ? -1 : 1;
      if (a.owned != b.owned) return a.owned ? -1 : 1;
      return a.priority.compareTo(b.priority);
    });
    return packs.where((pack) {
      final owned = _latestOwnedForPack(pack.id);
      final isExpired =
          owned?.expiresAt != null && owned!.expiresAt!.isBefore(now);
      switch (_filter) {
        case _EntryCatalogFilter.all:
          return true;
        case _EntryCatalogFilter.active:
          return pack.active;
        case _EntryCatalogFilter.owned:
          return owned != null && !isExpired;
        case _EntryCatalogFilter.available:
          return owned == null || isExpired;
        case _EntryCatalogFilter.expired:
          return owned != null && isExpired;
      }
    }).toList();
  }

  @override
  Widget build(BuildContext context) {
    final tokens = getBrandTokens(Get.find<AppSettingsService>().brandKey);
    final active = _state?.active;
    final filtered = _filteredPacks;
    final ownedCount =
        (_state?.owned ?? const <UserEntryPackDto>[])
            .where((item) => !item.isExpired)
            .length;

    return Scaffold(
      backgroundColor: tokens.backgroundGradient.first,
      appBar: AppBar(
        leading: IconButton(
          icon: Icon(
            Icons.arrow_back_ios_new_rounded,
            color: tokens.textPrimary,
            size: 20,
          ),
          onPressed: () => Get.back<void>(),
        ),
        iconTheme: IconThemeData(color: tokens.textPrimary),
        title: Text(
          'Entry',
          style: TextStyle(
            color: tokens.textPrimary,
            fontWeight: FontWeight.w800,
          ),
        ),
        backgroundColor: Colors.transparent,
        elevation: 0,
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
              padding: const EdgeInsets.fromLTRB(18, 6, 18, 0),
              sliver: SliverToBoxAdapter(
                child: _EntryHeroBoard(
                  tokens: tokens,
                  active: active,
                  walletBalanceCoins: _walletBalanceCoins,
                  ownedCount: ownedCount,
                  totalCount: _packs.length,
                  submitting: _submitting,
                ),
              ),
            ),
            if ((_state?.owned ?? const <UserEntryPackDto>[]).isNotEmpty)
              SliverPadding(
                padding: const EdgeInsets.fromLTRB(18, 12, 18, 0),
                sliver: SliverToBoxAdapter(
                  child: _OwnedEntryRail(
                    tokens: tokens,
                    items:
                        (_state?.owned ?? const <UserEntryPackDto>[])
                            .take(4)
                            .toList(),
                  ),
                ),
              ),
            SliverPadding(
              padding: const EdgeInsets.fromLTRB(18, 12, 18, 0),
              sliver: SliverToBoxAdapter(
                child: _FilterHeader(
                  tokens: tokens,
                  filter: _filter,
                  onChanged: (value) => setState(() => _filter = value),
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
                child: _EntryMessageBoard(
                  tokens: tokens,
                  icon: Icons.auto_awesome_motion_rounded,
                  title: 'Unable to load entry effects',
                  message: _error!,
                  actionLabel: 'Retry',
                  onTap: _load,
                ),
              )
            else if (filtered.isEmpty)
              SliverFillRemaining(
                hasScrollBody: false,
                child: _EntryMessageBoard(
                  tokens: tokens,
                  icon: Icons.layers_clear_rounded,
                  title: 'Nothing here',
                  message: 'Try another view.',
                ),
              )
            else
              SliverPadding(
                padding: const EdgeInsets.fromLTRB(18, 18, 18, 28),
                sliver: SliverList(
                  delegate: SliverChildBuilderDelegate((context, index) {
                    final pack = filtered[index];
                    final owned = _latestOwnedForPack(pack.id);
                    return Padding(
                      padding: EdgeInsets.only(
                        bottom: index == filtered.length - 1 ? 0 : 14,
                      ),
                      child: _EntryFeatureCard(
                        tokens: tokens,
                        pack: pack,
                        owned: owned,
                        submitting: _submitting,
                        accentIndex: index,
                        onTap:
                            pack.active ? null : () => _handlePackAction(pack),
                      ),
                    );
                  }, childCount: filtered.length),
                ),
              ),
          ],
        ),
      ),
    );
  }
}

class _EntryHeroBoard extends StatelessWidget {
  const _EntryHeroBoard({
    required this.tokens,
    required this.active,
    required this.walletBalanceCoins,
    required this.ownedCount,
    required this.totalCount,
    required this.submitting,
  });

  final BrandTokens tokens;
  final UserEntryPackDto? active;
  final int? walletBalanceCoins;
  final int ownedCount;
  final int totalCount;
  final bool submitting;

  @override
  Widget build(BuildContext context) {
    final activeName = active?.entryPack?.name ?? 'No active effect';
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(30),
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF1B1F3B), Color(0xFF5B3FC5)],
        ),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFF5B3FC5).withOpacity(.28),
            blurRadius: 34,
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
                width: 56,
                height: 56,
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(.12),
                  borderRadius: BorderRadius.circular(18),
                ),
                child: const Icon(
                  Icons.auto_awesome_rounded,
                  color: Colors.white,
                  size: 30,
                ),
              ),
              const Spacer(),
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 12,
                  vertical: 8,
                ),
                decoration: BoxDecoration(
                  color: Colors.white.withOpacity(.12),
                  borderRadius: BorderRadius.circular(999),
                ),
                child: Text(
                  submitting ? 'Applying' : 'Studio',
                  style: const TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          Text(
            activeName,
            style: Theme.of(context).textTheme.headlineSmall?.copyWith(
              color: Colors.white,
              fontWeight: FontWeight.w800,
            ),
          ),
          const SizedBox(height: 6),
          Text(
            active == null
                ? 'Unlock an entry effect for your live-room arrival.'
                : 'Your current entry effect is ready.',
            style: Theme.of(context).textTheme.bodyMedium?.copyWith(
              color: Colors.white.withOpacity(.86),
              height: 1.38,
            ),
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: _HeroCounter(
                  label: 'Wallet',
                  value: NumberFormat.compact().format(walletBalanceCoins ?? 0),
                  showCoin: true,
                ),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _HeroCounter(label: 'Owned', value: '$ownedCount'),
              ),
              const SizedBox(width: 10),
              Expanded(
                child: _HeroCounter(label: 'Catalog', value: '$totalCount'),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class _HeroCounter extends StatelessWidget {
  const _HeroCounter({
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
      padding: const EdgeInsets.all(14),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(.1),
        borderRadius: BorderRadius.circular(18),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            label,
            style: Theme.of(context).textTheme.bodySmall?.copyWith(
              color: Colors.white.withOpacity(.72),
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
                  style: Theme.of(context).textTheme.titleLarge?.copyWith(
                    color: Colors.white,
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

class _FilterHeader extends StatelessWidget {
  const _FilterHeader({
    required this.tokens,
    required this.filter,
    required this.onChanged,
  });

  final BrandTokens tokens;
  final _EntryCatalogFilter filter;
  final ValueChanged<_EntryCatalogFilter> onChanged;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 42,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        itemBuilder: (context, index) {
          final item = _EntryCatalogFilter.values[index];
          final selected = item == filter;
          return GestureDetector(
            onTap: () => onChanged(item),
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 220),
              curve: Curves.easeOutCubic,
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
              decoration: BoxDecoration(
                color:
                    selected
                        ? const Color(0xFF5B3FC5)
                        : Colors.white.withOpacity(.84),
                borderRadius: BorderRadius.circular(999),
                border: Border.all(
                  color:
                      selected
                          ? const Color(0xFF5B3FC5)
                          : tokens.borderColor.withOpacity(.5),
                ),
              ),
              child: Text(
                item.label,
                style: TextStyle(
                  color: selected ? Colors.white : tokens.textSecondary,
                  fontWeight: FontWeight.w700,
                ),
              ),
            ),
          );
        },
        separatorBuilder: (_, __) => const SizedBox(width: 10),
        itemCount: _EntryCatalogFilter.values.length,
      ),
    );
  }
}

class _OwnedEntryRail extends StatelessWidget {
  const _OwnedEntryRail({required this.tokens, required this.items});

  final BrandTokens tokens;
  final List<UserEntryPackDto> items;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 44,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        itemBuilder: (context, index) {
          final item = items[index];
          final label = item.entryPack?.name ?? 'Entry';
          final accent =
              item.isExpired
                  ? const Color(0xFFE35D6A)
                  : const Color(0xFF5B3FC5);
          return Container(
            padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 10),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(.86),
              borderRadius: BorderRadius.circular(999),
              border: Border.all(color: accent.withOpacity(.22)),
            ),
            child: Row(
              children: [
                Container(
                  width: 8,
                  height: 8,
                  decoration: BoxDecoration(
                    color: accent,
                    shape: BoxShape.circle,
                  ),
                ),
                const SizedBox(width: 8),
                Text(
                  label,
                  style: TextStyle(
                    color: tokens.textPrimary,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ],
            ),
          );
        },
        separatorBuilder: (_, __) => const SizedBox(width: 8),
        itemCount: items.length,
      ),
    );
  }
}

class _EntryFeatureCard extends StatelessWidget {
  const _EntryFeatureCard({
    required this.tokens,
    required this.pack,
    required this.owned,
    required this.submitting,
    required this.accentIndex,
    required this.onTap,
  });

  final BrandTokens tokens;
  final EntryPackDto pack;
  final UserEntryPackDto? owned;
  final bool submitting;
  final int accentIndex;
  final VoidCallback? onTap;

  @override
  Widget build(BuildContext context) {
    final isExpired = owned?.isExpired == true;
    final actionLabel =
        pack.active
            ? 'Active now'
            : owned == null
            ? 'Unlock'
            : isExpired
            ? 'Renew'
            : 'Activate';
    final accent =
        [
          const Color(0xFF5B3FC5),
          const Color(0xFF06B430),
          const Color(0xFFEF7D57),
          const Color(0xFF1F8EFA),
        ][accentIndex % 4];

    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(.92),
        borderRadius: BorderRadius.circular(28),
        border: Border.all(
          color:
              pack.active
                  ? accent.withOpacity(.42)
                  : tokens.borderColor.withOpacity(.35),
          width: pack.active ? 1.4 : 1,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: double.infinity,
            height: 154,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(22),
              gradient: LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [accent.withOpacity(.18), accent.withOpacity(.05)],
              ),
            ),
            child: Stack(
              children: [
                Positioned.fill(
                  child: RemoteMediaArt(
                    url: pack.svgUrl,
                    explicitType: pack.assetType,
                    width: double.infinity,
                    height: 154,
                    fit: BoxFit.contain,
                    borderRadius: BorderRadius.circular(22),
                    fallback: Icon(
                      Icons.auto_awesome_motion_rounded,
                      color: accent,
                      size: 44,
                    ),
                  ),
                ),
                Positioned(
                  top: 12,
                  left: 12,
                  child: _TagPill(
                    label: pack.active ? 'Active' : pack.animationStyle,
                    accent: accent,
                  ),
                ),
                if (pack.owned)
                  const Positioned(
                    top: 12,
                    right: 12,
                    child: _TagPill(label: 'Owned', accent: Color(0xFF06B430)),
                  ),
                if (isExpired)
                  const Positioned(
                    top: 48,
                    right: 12,
                    child: _TagPill(
                      label: 'Expired',
                      accent: Color(0xFFE35D6A),
                    ),
                  ),
              ],
            ),
          ),
          const SizedBox(height: 14),
          Text(
            pack.name,
            style: Theme.of(context).textTheme.titleLarge?.copyWith(
              color: tokens.textPrimary,
              fontWeight: FontWeight.w800,
            ),
          ),
          const SizedBox(height: 6),
          Text(
            '${pack.durationDays} day access • ${pack.durationMs ~/ 1000}s scene',
            style: Theme.of(context).textTheme.bodyMedium?.copyWith(
              color: tokens.textSecondary,
              fontWeight: FontWeight.w600,
            ),
          ),
          const SizedBox(height: 10),
          Text(
            owned?.expiresAt == null
                ? 'Premium arrival effect for your room entry.'
                : 'Purchased ${DateFormat('dd MMM').format(owned!.purchasedAt ?? DateTime.now())} • expires ${DateFormat('dd MMM yyyy').format(owned!.expiresAt!)}',
            style: Theme.of(context).textTheme.bodySmall?.copyWith(
              color: tokens.textSecondary,
              height: 1.35,
            ),
          ),
          const SizedBox(height: 14),
          Row(
            children: [
              Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  const CoinLottie(size: 18),
                  const SizedBox(width: 6),
                  Text(
                    '${NumberFormat.compact().format(pack.priceCoins)} coins',
                    style: Theme.of(context).textTheme.titleMedium?.copyWith(
                      color: accent,
                      fontWeight: FontWeight.w800,
                    ),
                  ),
                ],
              ),
              const Spacer(),
              FilledButton(
                onPressed: submitting ? null : onTap,
                style: FilledButton.styleFrom(
                  backgroundColor:
                      pack.active ? accent.withOpacity(.18) : accent,
                  foregroundColor: pack.active ? accent : Colors.white,
                  padding: const EdgeInsets.symmetric(
                    horizontal: 16,
                    vertical: 14,
                  ),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(16),
                  ),
                ),
                child: Text(actionLabel),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

class _TagPill extends StatelessWidget {
  const _TagPill({required this.label, required this.accent});

  final String label;
  final Color accent;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: accent.withOpacity(.12),
        borderRadius: BorderRadius.circular(999),
      ),
      child: Text(
        label,
        style: TextStyle(
          color: accent,
          fontWeight: FontWeight.w700,
          fontSize: 12,
        ),
      ),
    );
  }
}

class _EntryMessageBoard extends StatelessWidget {
  const _EntryMessageBoard({
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
          color: Colors.white.withOpacity(.9),
          borderRadius: BorderRadius.circular(28),
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Container(
              width: 74,
              height: 74,
              decoration: BoxDecoration(
                color: const Color(0xFF5B3FC5).withOpacity(.12),
                borderRadius: BorderRadius.circular(24),
              ),
              child: Icon(icon, color: const Color(0xFF5B3FC5), size: 34),
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
              FilledButton(
                onPressed: onTap,
                style: FilledButton.styleFrom(
                  backgroundColor: const Color(0xFF5B3FC5),
                  foregroundColor: Colors.white,
                ),
                child: Text(actionLabel!),
              ),
            ],
          ],
        ),
      ),
    );
  }
}
