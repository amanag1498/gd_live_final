import 'dart:async';
import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';

import '../../../../app/brand/brand.dart';
import '../../../../app/widgets/haptics.dart';
import '../../../wallet/widgets/recharge_bottom_sheet.dart';
import '../models/fortune_wheel_models.dart';
import '../services/fortune_wheel_preload_service.dart';

class FortuneWheelPanel extends StatefulWidget {
  const FortuneWheelPanel({super.key});

  @override
  State<FortuneWheelPanel> createState() => _FortuneWheelPanelState();
}

class _FortuneWheelPanelState extends State<FortuneWheelPanel>
    with SingleTickerProviderStateMixin {
  late final AnimationController _spinController;
  late Animation<double> _spinAnimation;

  bool _spinning = false;
  String? _error;
  FortuneWheelSpin? _latestWin;
  double _rotation = 0;

  FortuneWheelPreloadService get _service =>
      Get.find<FortuneWheelPreloadService>();

  @override
  void initState() {
    super.initState();
    _spinController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 4200),
    );
    _spinAnimation = AlwaysStoppedAnimation<double>(_rotation);
    _spinController.addListener(() {
      setState(() => _rotation = _spinAnimation.value);
    });
    unawaited(_service.maybePreload(reason: 'panel_open'));
  }

  @override
  void dispose() {
    _spinController.dispose();
    super.dispose();
  }

  Future<void> _spin(FortuneWheelSnapshot snapshot) async {
    if (_spinning || snapshot.segments.isEmpty) return;
    if (!snapshot.canFreeSpin &&
        snapshot.settings.paidSpinsEnabled &&
        snapshot.walletBalance < snapshot.settings.paidSpinCostCoins) {
      _showRechargePrompt();
      return;
    }
    if (!snapshot.canFreeSpin && !snapshot.settings.paidSpinsEnabled) {
      setState(() => _error = 'Paid spins are disabled right now.');
      return;
    }

    setState(() {
      _spinning = true;
      _error = null;
      _latestWin = null;
    });

    try {
      HapticFeedback.mediumImpact();
      final result = await _service.spin();
      final targetIndex = _targetIndex(snapshot.segments, result.spin);
      final targetRotation = _targetRotation(
        snapshot.segments.length,
        targetIndex,
      );
      _spinAnimation = Tween<double>(
        begin: _rotation,
        end: targetRotation,
      ).animate(
        CurvedAnimation(parent: _spinController, curve: Curves.easeOutCubic),
      );
      await _spinController.forward(from: 0);
      if (!mounted) return;
      setState(() {
        _latestWin = result.spin;
        _spinning = false;
      });
      Haptics.success();
      _showReward(result.spin);
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _spinning = false;
        _error = e.toString().replaceFirst('Exception: ', '');
      });
      Haptics.warning();
    }
  }

  int _targetIndex(List<FortuneWheelSegment> segments, FortuneWheelSpin spin) {
    final segmentId = spin.segment?.id;
    final index = segments.indexWhere((segment) => segment.id == segmentId);
    return index >= 0 ? index : 0;
  }

  double _targetRotation(int count, int targetIndex) {
    final slice = (math.pi * 2) / math.max(1, count);
    final targetCenter = (targetIndex * slice) + (slice / 2);
    final pointerAngle = -math.pi / 2;
    final desired = pointerAngle - targetCenter;
    final fullTurns = 6 + math.Random().nextInt(3);
    final base = (math.pi * 2 * fullTurns) + desired;
    final currentTurns = (_rotation / (math.pi * 2)).floor();
    var target = base + (math.pi * 2 * currentTurns);
    while (target <= _rotation + (math.pi * 2 * 4)) {
      target += math.pi * 2;
    }
    return target;
  }

  void _showReward(FortuneWheelSpin spin) {
    showModalBottomSheet<void>(
      context: context,
      useRootNavigator: false,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => _FortuneRewardSheet(spin: spin),
    );
  }

  void _showRechargePrompt() {
    showModalBottomSheet<void>(
      context: context,
      useRootNavigator: false,
      isScrollControlled: true,
      backgroundColor: Colors.transparent,
      builder: (_) => const RechargeBottomSheet(),
    );
  }

  @override
  Widget build(BuildContext context) {
    final tokens = getBrandTokens('midnight');
    return Obx(() {
      final snapshot = _service.snapshot.value;
      final loading = _service.loading.value;
      final error = _error ?? _service.error.value;

      return Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            colors: [Color(0xFF140925), Color(0xFF241151), Color(0xFF071021)],
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
          ),
        ),
        child: Stack(
          children: [
            const Positioned.fill(child: _FortuneBackground()),
            SafeArea(
              top: false,
              child: ListView(
                padding: const EdgeInsets.fromLTRB(18, 2, 18, 28),
                children: [
                  _HeroHeader(snapshot: snapshot, tokens: tokens),
                  const SizedBox(height: 12),
                  if (loading && snapshot == null)
                    const _FortuneLoadingCard()
                  else if (error != null && snapshot == null)
                    _FortuneErrorCard(
                      message: error,
                      onRetry: () => unawaited(_service.refresh()),
                    )
                  else if (snapshot == null)
                    _FortuneErrorCard(
                      message: 'Fortune Wheel is not available right now.',
                      onRetry: () => unawaited(_service.refresh()),
                    )
                  else ...[
                    _WalletStrip(snapshot: snapshot),
                    const SizedBox(height: 16),
                    if (snapshot.segments.isEmpty)
                      _NoSegmentsCard(
                        onRefresh: () => unawaited(_service.refresh()),
                      )
                    else ...[
                      _WheelStage(
                        snapshot: snapshot,
                        rotation: _rotation,
                        spinning: _spinning,
                      ),
                      const SizedBox(height: 18),
                      _SpinButton(
                        snapshot: snapshot,
                        spinning: _spinning,
                        onPressed: () => unawaited(_spin(snapshot)),
                      ),
                    ],
                    if (error != null) ...[
                      const SizedBox(height: 12),
                      _InlineError(message: error),
                    ],
                    if (_latestWin != null) ...[
                      const SizedBox(height: 14),
                      _LatestWinCard(spin: _latestWin!),
                    ],
                    const SizedBox(height: 18),
                    _RecentRewards(spins: snapshot.recentSpins),
                  ],
                ],
              ),
            ),
          ],
        ),
      );
    });
  }
}

class _HeroHeader extends StatelessWidget {
  const _HeroHeader({required this.snapshot, required this.tokens});

  final FortuneWheelSnapshot? snapshot;
  final BrandTokens tokens;

  @override
  Widget build(BuildContext context) {
    final free = snapshot?.freeSpinsRemaining ?? 0;
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(30),
        gradient: LinearGradient(
          colors: [
            const Color(0xFFFFD76B).withValues(alpha: .18),
            const Color(0xFFFF5FD2).withValues(alpha: .14),
            Colors.white.withValues(alpha: .04),
          ],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        border: Border.all(color: Colors.white.withValues(alpha: .12)),
      ),
      child: Row(
        children: [
          Container(
            width: 62,
            height: 62,
            decoration: BoxDecoration(
              shape: BoxShape.circle,
              gradient: const SweepGradient(
                colors: [
                  Color(0xFFFFD76B),
                  Color(0xFFFF5FD2),
                  Color(0xFF67E8F9),
                  Color(0xFFFFD76B),
                ],
              ),
              boxShadow: [
                BoxShadow(
                  color: const Color(0xFFFFD76B).withValues(alpha: .28),
                  blurRadius: 28,
                  spreadRadius: 4,
                ),
              ],
            ),
            child: const Icon(
              Icons.stars_rounded,
              color: Colors.white,
              size: 34,
            ),
          ),
          const SizedBox(width: 14),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text(
                  'Fortune Wheel',
                  style: TextStyle(
                    color: Colors.white,
                    fontSize: 24,
                    fontWeight: FontWeight.w900,
                    letterSpacing: -.4,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  free > 0
                      ? '$free free spin ready today'
                      : 'Spin for coins and win live rewards',
                  style: TextStyle(
                    color: Colors.white.withValues(alpha: .72),
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _WalletStrip extends StatelessWidget {
  const _WalletStrip({required this.snapshot});

  final FortuneWheelSnapshot snapshot;

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Expanded(
          child: _MiniStat(
            label: 'Wallet',
            value: '${snapshot.walletBalance}',
            icon: Icons.account_balance_wallet_rounded,
            accent: const Color(0xFFFFD76B),
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: _MiniStat(
            label: 'Free',
            value: '${snapshot.freeSpinsRemaining}',
            icon: Icons.card_giftcard_rounded,
            accent: const Color(0xFF67E8F9),
          ),
        ),
        const SizedBox(width: 10),
        Expanded(
          child: _MiniStat(
            label: 'Paid',
            value: '${snapshot.settings.paidSpinCostCoins}',
            icon: Icons.paid_rounded,
            accent: const Color(0xFFFF5FD2),
          ),
        ),
      ],
    );
  }
}

class _MiniStat extends StatelessWidget {
  const _MiniStat({
    required this.label,
    required this.value,
    required this.icon,
    required this.accent,
  });

  final String label;
  final String value;
  final IconData icon;
  final Color accent;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(20),
        color: Colors.white.withValues(alpha: .07),
        border: Border.all(color: Colors.white.withValues(alpha: .10)),
      ),
      child: Row(
        children: [
          Icon(icon, color: accent, size: 19),
          const SizedBox(width: 8),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: TextStyle(
                    color: Colors.white.withValues(alpha: .55),
                    fontSize: 11,
                    fontWeight: FontWeight.w800,
                  ),
                ),
                Text(
                  value,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 15,
                    fontWeight: FontWeight.w900,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _WheelStage extends StatelessWidget {
  const _WheelStage({
    required this.snapshot,
    required this.rotation,
    required this.spinning,
  });

  final FortuneWheelSnapshot snapshot;
  final double rotation;
  final bool spinning;

  @override
  Widget build(BuildContext context) {
    final size = math.min(MediaQuery.sizeOf(context).width - 44, 330.0);
    return Center(
      child: SizedBox(
        width: size,
        height: size + 36,
        child: Stack(
          alignment: Alignment.center,
          children: [
            Positioned(
              top: 28,
              child: Container(
                width: size,
                height: size,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  boxShadow: [
                    BoxShadow(
                      color: const Color(0xFFFFD76B).withValues(alpha: .20),
                      blurRadius: 42,
                      spreadRadius: 4,
                    ),
                    BoxShadow(
                      color: Colors.black.withValues(alpha: .42),
                      blurRadius: 36,
                      offset: const Offset(0, 18),
                    ),
                  ],
                ),
                child: Transform.rotate(
                  angle: rotation,
                  child: CustomPaint(
                    painter: _FortuneWheelPainter(snapshot.segments),
                    child: Container(),
                  ),
                ),
              ),
            ),
            Positioned(
              top: 12,
              child: Transform.rotate(
                angle: math.pi,
                child: Icon(
                  Icons.navigation_rounded,
                  color: const Color(0xFFFFF0AA),
                  size: 42,
                  shadows: [
                    Shadow(
                      color: Colors.black.withValues(alpha: .55),
                      blurRadius: 10,
                      offset: const Offset(0, -2),
                    ),
                  ],
                ),
              ),
            ),
            Positioned(
              top: (size / 2) - 2,
              child: AnimatedScale(
                scale: spinning ? 1.08 : 1,
                duration: const Duration(milliseconds: 500),
                curve: Curves.easeOutBack,
                child: Container(
                  width: 92,
                  height: 92,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    gradient: const RadialGradient(
                      colors: [
                        Color(0xFFFFFFFF),
                        Color(0xFFFFD76B),
                        Color(0xFFFF8A00),
                      ],
                    ),
                    border: Border.all(color: Colors.white, width: 4),
                  ),
                  child: Center(
                    child: Text(
                      spinning ? 'LUCK' : 'SPIN',
                      style: const TextStyle(
                        color: Color(0xFF4A2400),
                        fontWeight: FontWeight.w900,
                        letterSpacing: .8,
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _FortuneWheelPainter extends CustomPainter {
  _FortuneWheelPainter(this.segments);

  final List<FortuneWheelSegment> segments;

  static const _fallbackColors = [
    Color(0xFFFFD76B),
    Color(0xFFFF5FD2),
    Color(0xFF67E8F9),
    Color(0xFF8B5CF6),
    Color(0xFF34D399),
    Color(0xFFFF7A7A),
  ];

  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2);
    final radius = math.min(size.width, size.height) / 2;
    final slice = math.pi * 2 / math.max(1, segments.length);
    final rect = Rect.fromCircle(center: center, radius: radius);
    final textPainter = TextPainter(
      textDirection: TextDirection.ltr,
      textAlign: TextAlign.center,
      maxLines: 2,
    );

    for (var i = 0; i < segments.length; i++) {
      final start = -math.pi / 2 + (i * slice);
      final color = _segmentColor(segments[i], i);
      final paint =
          Paint()
            ..shader = RadialGradient(
              colors: [
                color.withValues(alpha: .96),
                color.withValues(alpha: .58),
              ],
            ).createShader(rect);
      canvas.drawArc(rect, start, slice, true, paint);

      final line =
          Paint()
            ..color = Colors.white.withValues(alpha: .35)
            ..strokeWidth = 2;
      canvas.drawLine(
        center,
        center + Offset(math.cos(start), math.sin(start)) * radius,
        line,
      );

      final textAngle = start + (slice / 2);
      final labelOffset =
          center +
          Offset(math.cos(textAngle), math.sin(textAngle)) * (radius * .62);
      canvas.save();
      canvas.translate(labelOffset.dx, labelOffset.dy);
      canvas.rotate(textAngle + math.pi / 2);
      textPainter.text = TextSpan(
        text: _compactLabel(segments[i]),
        style: const TextStyle(
          color: Colors.white,
          fontSize: 12,
          fontWeight: FontWeight.w900,
          shadows: [Shadow(color: Colors.black54, blurRadius: 5)],
        ),
      );
      textPainter.layout(maxWidth: radius * .52);
      textPainter.paint(
        canvas,
        Offset(-textPainter.width / 2, -textPainter.height / 2),
      );
      canvas.restore();
    }

    canvas.drawCircle(
      center,
      radius,
      Paint()
        ..style = PaintingStyle.stroke
        ..strokeWidth = 9
        ..color = Colors.white.withValues(alpha: .24),
    );
    canvas.drawCircle(
      center,
      radius - 7,
      Paint()
        ..style = PaintingStyle.stroke
        ..strokeWidth = 2
        ..color = const Color(0xFFFFD76B).withValues(alpha: .72),
    );
  }

  Color _segmentColor(FortuneWheelSegment segment, int index) {
    final raw = segment.colorHex?.replaceAll('#', '').trim();
    if (raw != null && (raw.length == 6 || raw.length == 8)) {
      final value = int.tryParse(raw.length == 6 ? 'FF$raw' : raw, radix: 16);
      if (value != null) return Color(value);
    }
    return _fallbackColors[index % _fallbackColors.length];
  }

  String _compactLabel(FortuneWheelSegment segment) {
    if (segment.rewardType == 'coins') {
      return '${segment.rewardValueCoins}\nCoins';
    }
    if (segment.rewardType == 'entry_pack') {
      return 'Entry\nPack';
    }
    if (segment.rewardType == 'subscription') {
      return 'VIP\nPass';
    }
    return segment.label;
  }

  @override
  bool shouldRepaint(covariant _FortuneWheelPainter oldDelegate) {
    return oldDelegate.segments != segments;
  }
}

class _SpinButton extends StatelessWidget {
  const _SpinButton({
    required this.snapshot,
    required this.spinning,
    required this.onPressed,
  });

  final FortuneWheelSnapshot snapshot;
  final bool spinning;
  final VoidCallback onPressed;

  @override
  Widget build(BuildContext context) {
    final isFree = snapshot.canFreeSpin;
    final label =
        spinning
            ? 'Spinning...'
            : isFree
            ? 'Use Free Spin'
            : 'Spin for ${snapshot.settings.paidSpinCostCoins} Coins';
    return DecoratedBox(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(24),
        gradient: const LinearGradient(
          colors: [Color(0xFFFFD76B), Color(0xFFFF7A1A), Color(0xFFFF5FD2)],
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        boxShadow: [
          BoxShadow(
            color: const Color(0xFFFF9A32).withValues(alpha: .34),
            blurRadius: 22,
            offset: const Offset(0, 12),
          ),
        ],
      ),
      child: Material(
        color: Colors.transparent,
        child: InkWell(
          onTap: spinning || snapshot.segments.isEmpty ? null : onPressed,
          borderRadius: BorderRadius.circular(24),
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 18, vertical: 16),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                if (spinning)
                  const SizedBox(
                    width: 18,
                    height: 18,
                    child: CircularProgressIndicator(
                      strokeWidth: 2.4,
                      color: Colors.white,
                    ),
                  )
                else
                  const Icon(Icons.auto_awesome_rounded, color: Colors.white),
                const SizedBox(width: 10),
                Text(
                  label,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 17,
                    fontWeight: FontWeight.w900,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class _NoSegmentsCard extends StatelessWidget {
  const _NoSegmentsCard({required this.onRefresh});

  final VoidCallback onRefresh;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(26),
        color: Colors.white.withValues(alpha: .06),
        border: Border.all(color: Colors.white.withValues(alpha: .10)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Row(
            children: [
              Icon(Icons.tune_rounded, color: Color(0xFFFFD76B)),
              SizedBox(width: 10),
              Expanded(
                child: Text(
                  'Rewards are being configured',
                  style: TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.w900,
                    fontSize: 17,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 8),
          Text(
            'Admin needs to add at least one active Fortune Wheel segment before users can spin.',
            style: TextStyle(
              color: Colors.white.withValues(alpha: .68),
              fontWeight: FontWeight.w700,
              height: 1.35,
            ),
          ),
          const SizedBox(height: 14),
          OutlinedButton.icon(
            onPressed: onRefresh,
            icon: const Icon(Icons.refresh_rounded),
            label: const Text('Refresh'),
          ),
        ],
      ),
    );
  }
}

class _RecentRewards extends StatelessWidget {
  const _RecentRewards({required this.spins});

  final List<FortuneWheelSpin> spins;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(26),
        color: Colors.white.withValues(alpha: .06),
        border: Border.all(color: Colors.white.withValues(alpha: .10)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Recent rewards',
            style: TextStyle(
              color: Colors.white,
              fontWeight: FontWeight.w900,
              fontSize: 16,
            ),
          ),
          const SizedBox(height: 12),
          if (spins.isEmpty)
            Text(
              'Your Fortune Wheel rewards will appear here.',
              style: TextStyle(
                color: Colors.white.withValues(alpha: .62),
                fontWeight: FontWeight.w700,
              ),
            )
          else
            ...spins
                .take(5)
                .map(
                  (spin) => Padding(
                    padding: const EdgeInsets.only(bottom: 10),
                    child: Row(
                      children: [
                        Icon(
                          _rewardIcon(spin.rewardType),
                          color: const Color(0xFFFFD76B),
                          size: 18,
                        ),
                        const SizedBox(width: 10),
                        Expanded(
                          child: Text(
                            _rewardText(spin),
                            style: const TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                        ),
                        Text(
                          spin.spinType == 'free'
                              ? 'Free'
                              : '-${spin.spinCostCoins}',
                          style: TextStyle(
                            color: Colors.white.withValues(alpha: .55),
                            fontWeight: FontWeight.w800,
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

class _LatestWinCard extends StatelessWidget {
  const _LatestWinCard({required this.spin});

  final FortuneWheelSpin spin;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(15),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(22),
        gradient: LinearGradient(
          colors: [
            const Color(0xFFFFD76B).withValues(alpha: .18),
            Colors.white.withValues(alpha: .06),
          ],
        ),
        border: Border.all(
          color: const Color(0xFFFFD76B).withValues(alpha: .25),
        ),
      ),
      child: Row(
        children: [
          const Icon(Icons.emoji_events_rounded, color: Color(0xFFFFD76B)),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              _rewardText(spin),
              style: const TextStyle(
                color: Colors.white,
                fontWeight: FontWeight.w900,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _FortuneRewardSheet extends StatelessWidget {
  const _FortuneRewardSheet({required this.spin});

  final FortuneWheelSpin spin;

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      top: false,
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Container(
          padding: const EdgeInsets.all(22),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(32),
            gradient: const LinearGradient(
              colors: [Color(0xFF2E145F), Color(0xFF11091F)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            border: Border.all(color: Colors.white.withValues(alpha: .14)),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                width: 86,
                height: 86,
                decoration: const BoxDecoration(
                  shape: BoxShape.circle,
                  gradient: LinearGradient(
                    colors: [Color(0xFFFFD76B), Color(0xFFFF5FD2)],
                  ),
                ),
                child: Icon(
                  _rewardIcon(spin.rewardType),
                  color: Colors.white,
                  size: 42,
                ),
              ),
              const SizedBox(height: 18),
              const Text(
                'You Won',
                style: TextStyle(
                  color: Colors.white70,
                  fontWeight: FontWeight.w900,
                  letterSpacing: 1.2,
                ),
              ),
              const SizedBox(height: 8),
              Text(
                _rewardText(spin),
                textAlign: TextAlign.center,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 24,
                  fontWeight: FontWeight.w900,
                ),
              ),
              const SizedBox(height: 18),
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () => Navigator.of(context).maybePop(),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFFFFD76B),
                    foregroundColor: const Color(0xFF321400),
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(18),
                    ),
                  ),
                  child: const Text(
                    'Collect',
                    style: TextStyle(fontWeight: FontWeight.w900),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _FortuneLoadingCard extends StatelessWidget {
  const _FortuneLoadingCard();

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(22),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(26),
        color: Colors.white.withValues(alpha: .06),
        border: Border.all(color: Colors.white.withValues(alpha: .10)),
      ),
      child: const Row(
        children: [
          CircularProgressIndicator(color: Color(0xFFFFD76B)),
          SizedBox(width: 16),
          Expanded(
            child: Text(
              'Loading your daily Fortune Wheel...',
              style: TextStyle(
                color: Colors.white,
                fontWeight: FontWeight.w800,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _FortuneErrorCard extends StatelessWidget {
  const _FortuneErrorCard({required this.message, required this.onRetry});

  final String message;
  final VoidCallback onRetry;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(26),
        color: Colors.white.withValues(alpha: .06),
        border: Border.all(color: Colors.white.withValues(alpha: .10)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Fortune Wheel unavailable',
            style: TextStyle(
              color: Colors.white,
              fontWeight: FontWeight.w900,
              fontSize: 17,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            message,
            style: TextStyle(
              color: Colors.white.withValues(alpha: .68),
              fontWeight: FontWeight.w700,
            ),
          ),
          const SizedBox(height: 14),
          OutlinedButton.icon(
            onPressed: onRetry,
            icon: const Icon(Icons.refresh_rounded),
            label: const Text('Retry'),
          ),
        ],
      ),
    );
  }
}

class _InlineError extends StatelessWidget {
  const _InlineError({required this.message});

  final String message;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(13),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(18),
        color: Colors.redAccent.withValues(alpha: .14),
        border: Border.all(color: Colors.redAccent.withValues(alpha: .24)),
      ),
      child: Row(
        children: [
          const Icon(Icons.info_rounded, color: Colors.redAccent, size: 18),
          const SizedBox(width: 9),
          Expanded(
            child: Text(
              message,
              style: const TextStyle(
                color: Colors.white,
                fontWeight: FontWeight.w800,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _FortuneBackground extends StatelessWidget {
  const _FortuneBackground();

  @override
  Widget build(BuildContext context) {
    return CustomPaint(painter: _FortuneBackgroundPainter());
  }
}

class _FortuneBackgroundPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final paint = Paint()..style = PaintingStyle.fill;
    final points = [
      Offset(size.width * .12, 80),
      Offset(size.width * .82, 130),
      Offset(size.width * .24, size.height * .56),
      Offset(size.width * .74, size.height * .72),
    ];
    for (final point in points) {
      paint.shader = RadialGradient(
        colors: [
          const Color(0xFFFFD76B).withValues(alpha: .16),
          Colors.transparent,
        ],
      ).createShader(Rect.fromCircle(center: point, radius: 120));
      canvas.drawCircle(point, 120, paint);
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}

String _rewardText(FortuneWheelSpin spin) {
  if (spin.rewardType == 'coins') {
    return '${spin.rewardValueCoins} Coins';
  }
  if (spin.rewardType == 'entry_pack') {
    final name = spin.entryPackName ?? 'Entry Pack';
    return '$name for ${_durationText(spin.rewardDurationHours)}';
  }
  if (spin.rewardType == 'subscription') {
    final name = spin.subscriptionPlanName ?? 'Subscription';
    return '$name for ${_durationText(spin.rewardDurationHours)}';
  }
  return spin.segment?.label ?? 'Reward';
}

String _durationText(int? hours) {
  final value = hours ?? 24;
  if (value < 24) return '$value hours';
  final days = (value / 24).round();
  return days == 1 ? '1 day' : '$days days';
}

IconData _rewardIcon(String rewardType) {
  return switch (rewardType) {
    'entry_pack' => Icons.rocket_launch_rounded,
    'subscription' => Icons.workspace_premium_rounded,
    _ => Icons.monetization_on_rounded,
  };
}
