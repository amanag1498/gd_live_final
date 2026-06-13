// lib/modules/subscriptions/widgets/choose_plan_sheet.dart
import 'dart:math' as math;
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';

import '../../../app/brand/brand.dart';
import '../../../app/widgets/coin_lottie.dart';
import '../../../app/widgets/gd_live_logo.dart';
import '../../../app/widgets/gd_modal_surface.dart';
import '../../../services/app_settings_service.dart';
import '../../wallet/services/wallet_api.dart';
import '../models/subscription_plan_dto.dart';

BrandTokens _choosePlanTokens() =>
    getBrandTokens(Get.find<AppSettingsService>().brandKey);

class ChoosePlanSheet extends StatefulWidget {
  final List<SubscriptionPlanDto> plans;

  const ChoosePlanSheet({super.key, required this.plans});

  static Future<SubscriptionPlanDto?> show(
    BuildContext context, {
    required List<SubscriptionPlanDto> plans,
  }) {
    return showModalBottomSheet<SubscriptionPlanDto>(
      context: context,
      isScrollControlled: true,
      useSafeArea: true,
      backgroundColor: Colors.transparent,
      barrierColor: Colors.black.withOpacity(.55),
      builder: (_) => ChoosePlanSheet(plans: plans),
    );
  }

  @override
  State<ChoosePlanSheet> createState() => _ChoosePlanSheetState();
}

class _ChoosePlanSheetState extends State<ChoosePlanSheet>
    with TickerProviderStateMixin {
  late final AnimationController _shineCtrl; // card shine sweep
  late final AnimationController _ctaPulse; // CTA micro pulse

  int? _selectedId;
  int? _walletBalanceCoins;

  @override
  void initState() {
    super.initState();
    _selectedId = widget.plans.isNotEmpty ? widget.plans.first.id : null;

    _shineCtrl = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 5),
    )..repeat();
    _ctaPulse = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1300),
    )..repeat(reverse: true);
    _loadWalletBalance();
  }

  Future<void> _loadWalletBalance() async {
    try {
      final summary = await Get.find<WalletApi>().fetchSummary();
      if (!mounted) return;
      setState(() => _walletBalanceCoins = summary.balance);
    } catch (_) {
      // Balance is contextual metadata for the sheet; keep the flow working if it fails.
    }
  }

  @override
  void dispose() {
    _shineCtrl.dispose();
    _ctaPulse.dispose();
    super.dispose();
  }

  SubscriptionPlanDto? get _selected {
    if (_selectedId == null) return null;
    return widget.plans.firstWhere(
      (p) => p.id == _selectedId,
      orElse: () => widget.plans.first,
    );
  }

  // pick “most popular” by best value (duration/price), nudge to middle if edge
  int _popularIndexFor(List<SubscriptionPlanDto> plans) {
    if (plans.isEmpty) return 0;
    double best = -1;
    int idx = 0;
    for (var i = 0; i < plans.length; i++) {
      final p = plans[i];
      final price = (p.priceCoins <= 0) ? 1 : p.priceCoins;
      final score = p.durationDays / price;
      if (score > best) {
        best = score;
        idx = i;
      }
    }
    if ((plans.length == 3 || plans.length == 4) &&
        (idx == 0 || idx == plans.length - 1)) {
      return (plans.length / 2).floor();
    }
    return idx;
  }

  @override
  Widget build(BuildContext context) {
    final tokens = _choosePlanTokens();
    final media = MediaQuery.of(context);
    final sheetMaxHeight = media.size.height * .76;
    return SafeArea(
      top: false,
      child: Padding(
        padding: const EdgeInsets.fromLTRB(12, 0, 12, 12),
        child: Align(
          alignment: Alignment.bottomCenter,
          child: GdModalSurface(
            tokens: tokens,
            radius: 30,
            padding: const EdgeInsets.fromLTRB(18, 12, 18, 12),
            child: ConstrainedBox(
              constraints: BoxConstraints(maxHeight: sheetMaxHeight),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  const GdLiveLogo(size: 52, showWordmark: false),
                  const SizedBox(height: 12),
                  const _Header(),
                  const SizedBox(height: 8),
                  Text(
                    'Choose a clean plan and unlock live access instantly.',
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      color: tokens.textSecondary.withOpacity(.88),
                      fontWeight: FontWeight.w600,
                      fontSize: 13.5,
                    ),
                  ),
                  if (_walletBalanceCoins != null) ...[
                    const SizedBox(height: 12),
                    _BalancePill(
                      label:
                          '${NumberFormat.compact().format(_walletBalanceCoins)} coins available',
                    ),
                  ],
                  const SizedBox(height: 16),
                  Flexible(
                    child: ListView.separated(
                      shrinkWrap: true,
                      padding: const EdgeInsets.only(bottom: 8),
                      itemCount: widget.plans.length,
                      separatorBuilder: (_, __) => const SizedBox(height: 12),
                      itemBuilder: (_, i) {
                        final plan = widget.plans[i];
                        final selected = plan.id == _selectedId;
                        final palette = _TierPalette.fromName(plan.name);
                        return _PlanListCard(
                          plan: plan,
                          selected: selected,
                          palette: palette,
                          shineT: _shineCtrl,
                          onTap: () {
                            HapticFeedback.selectionClick();
                            setState(() => _selectedId = plan.id);
                          },
                        );
                      },
                    ),
                  ),
                  const SizedBox(height: 8),
                  Row(
                    children: [
                      Expanded(
                        child: ScaleTransition(
                          scale: Tween(begin: .98, end: 1.0).animate(
                            CurvedAnimation(
                              parent: _ctaPulse,
                              curve: Curves.easeInOut,
                            ),
                          ),
                          child: _NeoCtaButton(
                            label:
                                _selected == null
                                    ? 'Select a plan'
                                    : 'Unlock for ${_selected!.priceCoins} coins',
                            loading: false,
                            enabled: _selected != null,
                            shineT: _shineCtrl,
                            onPressed:
                                _selected == null
                                    ? null
                                    : () async {
                                      final plan = _selected!;
                                      Navigator.of(context).pop(plan);
                                    },
                          ),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class _BtnRow extends StatelessWidget {
  final IconData icon;
  final String label;
  const _BtnRow({required this.icon, required this.label});
  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        Icon(icon),
        const SizedBox(width: 10),
        Text(label, style: const TextStyle(fontWeight: FontWeight.w900)),
      ],
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Header (gradient title + glow)
// ─────────────────────────────────────────────────────────────────────────────
class _Header extends StatelessWidget {
  const _Header();

  @override
  Widget build(BuildContext context) {
    final tokens = _choosePlanTokens();
    return Column(
      children: [
        Text(
          'Unlock live streams',
          textAlign: TextAlign.center,
          style: TextStyle(
            color: tokens.textPrimary,
            fontWeight: FontWeight.w900,
            fontSize: 22,
            letterSpacing: -.2,
          ),
        ),
      ],
    );
  }
}

class _BalancePill extends StatelessWidget {
  const _BalancePill({required this.label});

  final String label;

  @override
  Widget build(BuildContext context) {
    final tokens = _choosePlanTokens();
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(.88),
        borderRadius: BorderRadius.circular(999),
        border: Border.all(
          color: tokens.primaryButtonGradient.first.withOpacity(.14),
        ),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          const CoinLottie(size: 20),
          const SizedBox(width: 8),
          Text(
            label,
            style: TextStyle(
              color: tokens.textPrimary,
              fontWeight: FontWeight.w800,
            ),
          ),
        ],
      ),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// Tier palette mapping by plan name
// ─────────────────────────────────────────────────────────────────────────────
class _TierPalette {
  final Color ringA, ringB;
  final Color bgA, bgB;
  final Color badge;

  const _TierPalette(this.ringA, this.ringB, this.bgA, this.bgB, this.badge);

  factory _TierPalette.fromName(String name) {
    final n = name.toLowerCase();
    if (n.contains('bronze')) {
      return const _TierPalette(
        Color(0xFFD79A5C),
        Color(0xFFB56E34),
        Color(0xFFFFF5EC),
        Color(0xFFF4E1D0),
        Color(0xFFB56E34),
      );
    } else if (n.contains('silver')) {
      return const _TierPalette(
        Color(0xFFD8DFE8),
        Color(0xFFA8B5C6),
        Color(0xFFF7FAFC),
        Color(0xFFE9EEF4),
        Color(0xFF8A98AA),
      );
    } else if (n.contains('gold')) {
      return const _TierPalette(
        Color(0xFFFFDE75),
        Color(0xFFFFC738),
        Color(0xFFFFF9E8),
        Color(0xFFFBEAB7),
        Color(0xFFE1A900),
      );
    } else if (n.contains('platinum')) {
      return const _TierPalette(
        Color(0xFFE5ECF0),
        Color(0xFFB4C3CB),
        Color(0xFFF8FAFB),
        Color(0xFFE9EFF2),
        Color(0xFF95A6AF),
      );
    }
    return const _TierPalette(
      Color(0xFF7EE59E),
      Color(0xFF06B430),
      Color(0xFFF3FFF6),
      Color(0xFFE2F7E8),
      Color(0xFF06B430),
    );
  }
}

// ─────────────────────────────────────────────────────────────────────────────
// LIST CARD
// ─────────────────────────────────────────────────────────────────────────────
class _PlanListCard extends StatelessWidget {
  final SubscriptionPlanDto plan;
  final bool selected;
  final _TierPalette palette;
  final AnimationController shineT;
  final VoidCallback onTap;

  const _PlanListCard({
    required this.plan,
    required this.selected,
    required this.palette,
    required this.shineT,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    final perks = plan.perks.take(3).toList();
    final tokens = _choosePlanTokens();

    return GestureDetector(
      onTap: onTap,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 220),
        curve: Curves.easeOutCubic,
        padding: const EdgeInsets.all(2),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(22),
          gradient:
              selected
                  ? LinearGradient(
                    colors: [palette.ringA, palette.ringB],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  )
                  : null,
          color: selected ? null : Colors.transparent,
          boxShadow:
              selected
                  ? [
                    BoxShadow(
                      color: palette.ringB.withOpacity(.18),
                      blurRadius: 18,
                      offset: const Offset(0, 10),
                    ),
                  ]
                  : null,
        ),
        child: Container(
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(20),
            gradient: LinearGradient(
              colors: [palette.bgA, palette.bgB],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            border: Border.all(
              color:
                  selected
                      ? Colors.white.withOpacity(.45)
                      : tokens.borderColor.withOpacity(.36),
            ),
          ),
          clipBehavior: Clip.antiAlias,
          child: Stack(
            children: [
              Positioned(
                right: -18,
                top: -18,
                child: Container(
                  width: 88,
                  height: 88,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    color: palette.ringA.withOpacity(.12),
                  ),
                ),
              ),
              Positioned.fill(
                child: IgnorePointer(
                  child: AnimatedBuilder(
                    animation: shineT,
                    builder:
                        (_, __) =>
                            CustomPaint(painter: _ShineStripe(t: shineT.value)),
                  ),
                ),
              ),
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 16, 16, 16),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    AnimatedContainer(
                      duration: const Duration(milliseconds: 220),
                      width: 24,
                      height: 24,
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color:
                            selected
                                ? palette.ringB
                                : Colors.white.withOpacity(.84),
                        border: Border.all(
                          color:
                              selected
                                  ? palette.ringB
                                  : tokens.borderColor.withOpacity(.8),
                          width: 2,
                        ),
                      ),
                      child:
                          selected
                              ? const Icon(
                                Icons.check_rounded,
                                size: 14,
                                color: Colors.white,
                              )
                              : null,
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Expanded(
                                child: Text(
                                  plan.name,
                                  maxLines: 1,
                                  overflow: TextOverflow.ellipsis,
                                  style: TextStyle(
                                    color: tokens.textPrimary,
                                    fontWeight: FontWeight.w900,
                                    fontSize: 17,
                                  ),
                                ),
                              ),
                              const SizedBox(width: 8),
                              _PricePill(coins: plan.priceCoins, anim: shineT),
                            ],
                          ),
                          const SizedBox(height: 4),
                          Text(
                            '${plan.durationDays} days live access',
                            style: TextStyle(
                              color: tokens.textSecondary,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                          const SizedBox(height: 10),
                          ...perks.map(
                            (text) => Padding(
                              padding: const EdgeInsets.only(bottom: 6),
                              child: Row(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Icon(
                                    Icons.check_circle_rounded,
                                    size: 18,
                                    color: palette.ringB,
                                  ),
                                  const SizedBox(width: 8),
                                  Expanded(
                                    child: Text(
                                      text,
                                      style: TextStyle(
                                        color: tokens.textPrimary.withOpacity(
                                          .92,
                                        ),
                                        fontWeight: FontWeight.w700,
                                      ),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                          if (perks.isEmpty)
                            Text(
                              'Includes premium live access',
                              style: TextStyle(
                                color: tokens.textPrimary.withOpacity(.84),
                                fontWeight: FontWeight.w700,
                              ),
                            ),
                        ],
                      ),
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

// diagonal shine stripe painter (re-usable)
class _ShineStripe extends CustomPainter {
  final double t;
  const _ShineStripe({required this.t});

  @override
  void paint(Canvas canvas, Size size) {
    final dx = _lerp(-size.width * .5, size.width * 1.2, t);
    final rect = Rect.fromLTWH(
      dx,
      -size.height * .2,
      size.width * .3,
      size.height * 1.4,
    );
    final paint =
        Paint()
          ..shader = LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              Colors.white.withOpacity(0.0),
              Colors.white.withOpacity(0.12),
              Colors.white.withOpacity(0.0),
            ],
            stops: const [0.0, 0.5, 1.0],
          ).createShader(rect);
    canvas.save();
    canvas.transform(Matrix4.rotationZ(-0.6).storage);
    canvas.drawRRect(
      RRect.fromRectAndRadius(rect, const Radius.circular(24)),
      paint,
    );
    canvas.restore();
  }

  double _lerp(double a, double b, double t) => a + (b - a) * t;
  @override
  bool shouldRepaint(covariant _ShineStripe old) => old.t != t;
}

// price pill with gentle pulse
class _PricePill extends StatelessWidget {
  final int coins;
  final Animation<double> anim;
  const _PricePill({required this.coins, required this.anim});

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: anim,
      builder: (_, __) {
        final pulse = 1 + (math.sin(anim.value * 2 * math.pi) * 0.03);
        return Transform.scale(
          scale: pulse,
          child: Container(
            padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(.14),
              borderRadius: BorderRadius.circular(999),
              border: Border.all(color: Colors.white.withOpacity(.22)),
            ),
            child: Row(
              mainAxisSize: MainAxisSize.min,
              children: [
                const CoinLottie(size: 18),
                const SizedBox(width: 6),
                Text(
                  '$coins',
                  style: const TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.w900,
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }
}

class _NeoCtaButton extends StatefulWidget {
  final String label;
  final bool loading;
  final bool enabled;
  final Animation<double> shineT;
  final VoidCallback? onPressed;

  const _NeoCtaButton({
    required this.label,
    required this.loading,
    required this.enabled,
    required this.shineT,
    required this.onPressed,
  });

  @override
  State<_NeoCtaButton> createState() => _NeoCtaButtonState();
}

class _NeoCtaButtonState extends State<_NeoCtaButton>
    with SingleTickerProviderStateMixin {
  late final AnimationController _press; // press scale
  bool _down = false;

  @override
  void initState() {
    super.initState();
    _press = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 140),
      lowerBound: 0.0,
      upperBound: 1.0,
    );
  }

  @override
  void dispose() {
    _press.dispose();
    super.dispose();
  }

  void _setDown(bool v) {
    if (_down == v) return;
    setState(() => _down = v);
    if (v) {
      _press.forward(from: 0);
      HapticFeedback.selectionClick();
    } else {
      _press.reverse();
    }
  }

  @override
  Widget build(BuildContext context) {
    final tokens = _choosePlanTokens();
    final canTap = widget.enabled && !widget.loading;

    final scale = Tween<double>(
      begin: 1.0,
      end: 0.98,
    ).animate(CurvedAnimation(parent: _press, curve: Curves.easeOutCubic));

    return GestureDetector(
      onTapDown: (_) => _setDown(true),
      onTapCancel: () => _setDown(false),
      onTapUp: (_) {
        _setDown(false);
        if (canTap) widget.onPressed?.call();
      },
      child: AnimatedBuilder(
        animation: Listenable.merge([_press, widget.shineT]),
        builder: (_, __) {
          return Transform.scale(
            scale: scale.value,
            child: Container(
              padding: const EdgeInsets.all(2), // gradient ring thickness
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(16),
                gradient:
                    canTap
                        ? SweepGradient(
                          colors: [
                            tokens.primaryButtonGradient.first,
                            tokens.primaryButtonGradient.last,
                            tokens.primaryButtonGradient.first,
                          ],
                          transform: GradientRotation(
                            widget.shineT.value * 2 * math.pi,
                          ),
                        )
                        : null,
                color: canTap ? null : tokens.glassColor.withOpacity(.42),
                boxShadow:
                    canTap
                        ? [
                          BoxShadow(
                            color: tokens.glowColor.withOpacity(.35),
                            blurRadius: 20,
                            offset: const Offset(0, 8),
                          ),
                        ]
                        : null,
              ),
              child: Stack(
                children: [
                  // frosted inner plate
                  Container(
                    height: 54,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(14),
                      color: tokens.glassColor.withOpacity(.08),
                      border: Border.all(
                        color: tokens.borderColor.withOpacity(.82),
                      ),
                      gradient: LinearGradient(
                        begin: Alignment.topLeft,
                        end: Alignment.bottomRight,
                        colors:
                            canTap
                                ? [
                                  tokens.cardGradient.first.withOpacity(.9),
                                  tokens.cardGradient.last.withOpacity(.86),
                                ]
                                : [
                                  tokens.chipColor.withOpacity(.58),
                                  tokens.glassColor.withOpacity(.42),
                                ],
                      ),
                    ),
                    clipBehavior: Clip.antiAlias,
                    child: Stack(
                      children: [
                        // diagonal shimmer
                        Positioned.fill(
                          child: IgnorePointer(
                            child: CustomPaint(
                              painter: _ButtonShinePainter(
                                t: widget.shineT.value,
                              ),
                            ),
                          ),
                        ),
                        // content row
                        Center(
                          child: AnimatedSwitcher(
                            duration: const Duration(milliseconds: 220),
                            transitionBuilder:
                                (child, anim) => FadeTransition(
                                  opacity: anim,
                                  child: SlideTransition(
                                    position: Tween<Offset>(
                                      begin: const Offset(0, .12),
                                      end: Offset.zero,
                                    ).animate(anim),
                                    child: child,
                                  ),
                                ),
                            child:
                                widget.loading
                                    ? Row(
                                      key: const ValueKey('loading'),
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                        SizedBox(
                                          width: 18,
                                          height: 18,
                                          child: CircularProgressIndicator(
                                            strokeWidth: 2.4,
                                            color: tokens.textPrimary,
                                          ),
                                        ),
                                        const SizedBox(width: 10),
                                        Text(
                                          'Processing…',
                                          style: TextStyle(
                                            color: tokens.textPrimary,
                                            fontWeight: FontWeight.w900,
                                          ),
                                        ),
                                      ],
                                    )
                                    : Row(
                                      key: const ValueKey('label'),
                                      mainAxisSize: MainAxisSize.min,
                                      children: [
                                        const CoinLottie(size: 22),
                                        const SizedBox(width: 10),
                                        Text(
                                          widget.label,
                                          style: TextStyle(
                                            color: tokens.textPrimary,
                                            fontWeight: FontWeight.w900,
                                            fontSize: 16,
                                          ),
                                        ),
                                        const SizedBox(width: 10),
                                        // arrow that floats a bit
                                        _FloatIcon(
                                          icon: Icons.arrow_forward_rounded,
                                          active: canTap,
                                        ),
                                      ],
                                    ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}

class _FloatIcon extends StatefulWidget {
  final IconData icon;
  final bool active;
  const _FloatIcon({required this.icon, required this.active});

  @override
  State<_FloatIcon> createState() => _FloatIconState();
}

class _FloatIconState extends State<_FloatIcon>
    with SingleTickerProviderStateMixin {
  late final AnimationController _ctrl;

  @override
  void initState() {
    super.initState();
    _ctrl = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 900),
    )..repeat(reverse: true);
  }

  @override
  void dispose() {
    _ctrl.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final tokens = _choosePlanTokens();
    if (!widget.active) return Icon(widget.icon, color: tokens.textPrimary);
    return AnimatedBuilder(
      animation: _ctrl,
      builder: (_, __) {
        final dx = math.sin(_ctrl.value * 2 * math.pi) * 2.0;
        return Transform.translate(
          offset: Offset(dx, 0),
          child: Icon(Icons.arrow_forward_rounded, color: tokens.textPrimary),
        );
      },
    );
  }
}

class _ButtonShinePainter extends CustomPainter {
  final double t; // 0..1
  const _ButtonShinePainter({required this.t});

  @override
  void paint(Canvas canvas, Size size) {
    final dx = _lerp(-size.width * .6, size.width * 1.2, t);
    final rect = Rect.fromLTWH(
      dx,
      -size.height * .6,
      size.width * .28,
      size.height * 2.2,
    );
    final paint =
        Paint()
          ..shader = LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [
              Colors.white.withOpacity(0.00),
              Colors.white.withOpacity(0.18),
              Colors.white.withOpacity(0.00),
            ],
            stops: const [0.0, 0.5, 1.0],
          ).createShader(rect);
    canvas.save();
    canvas.transform(Matrix4.rotationZ(-0.45).storage);
    canvas.drawRRect(
      RRect.fromRectAndRadius(rect, const Radius.circular(24)),
      paint,
    );
    canvas.restore();
  }

  double _lerp(double a, double b, double t) => a + (b - a) * t;
  @override
  bool shouldRepaint(covariant _ButtonShinePainter old) => old.t != t;
}
