import 'dart:async';
import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:audioplayers/audioplayers.dart';

import '../../../../app/widgets/coin_lottie.dart';
import '../../../../app/widgets/haptics.dart';
import '../../../wallet/widgets/recharge_bottom_sheet.dart';
import '../models/fortune_wheel_models.dart';
import '../services/fortune_wheel_preload_service.dart';

const _fortuneBgAsset = 'assets/games/fortune_wheel/fortune_bg.png';
const _fortuneWheelFrameAsset = 'assets/games/fortune_wheel/wheel_frame.png';
const _fortuneSpinButtonAsset = 'assets/games/fortune_wheel/spin_button.png';
const _fortuneTitleAsset = 'assets/games/fortune_wheel/spin_and_win_title.png';
const _fortuneSpinSoundAsset = 'games/fortune_wheel/wheel_spin.mp3';

Future<void> showFortuneWheelDialog(
  BuildContext context, {
  bool freeSpinOnly = false,
}) {
  return showGeneralDialog<void>(
    context: context,
    useRootNavigator: true,
    barrierDismissible: true,
    barrierLabel: 'Close Fortune Wheel',
    barrierColor: Colors.black.withValues(alpha: .66),
    transitionDuration: const Duration(milliseconds: 360),
    pageBuilder: (dialogContext, _, __) {
      final size = MediaQuery.sizeOf(dialogContext);
      final width = math.min(size.width - 24, 480.0);
      final height = math.min(size.height - 48, 720.0);
      return SafeArea(
        minimum: const EdgeInsets.all(12),
        child: Center(
          child: SizedBox(
            width: width,
            height: height,
            child: FortuneWheelPanel(
              showCloseButton: true,
              freeSpinOnly: freeSpinOnly,
              onClose: () => Navigator.of(dialogContext).pop(),
            ),
          ),
        ),
      );
    },
    transitionBuilder: (context, animation, secondaryAnimation, child) {
      final curved = CurvedAnimation(
        parent: animation,
        curve: Curves.easeOutCubic,
        reverseCurve: Curves.easeInCubic,
      );
      return FadeTransition(
        opacity: animation,
        child: ScaleTransition(
          scale: Tween<double>(begin: .94, end: 1).animate(curved),
          child: child,
        ),
      );
    },
  );
}

class FortuneWheelPanel extends StatefulWidget {
  const FortuneWheelPanel({
    super.key,
    this.showCloseButton = false,
    this.freeSpinOnly = false,
    this.onClose,
  });

  final bool showCloseButton;
  final bool freeSpinOnly;
  final VoidCallback? onClose;

  @override
  State<FortuneWheelPanel> createState() => _FortuneWheelPanelState();
}

class _FortuneWheelPanelState extends State<FortuneWheelPanel>
    with TickerProviderStateMixin {
  late final AnimationController _spinController;
  late final AnimationController _ambientController;
  late final AnimationController _entranceController;
  late Animation<double> _spinAnimation;
  late final AudioPlayer _spinAudioPlayer;

  bool _spinning = false;
  String? _error;
  double _rotation = 0;

  FortuneWheelPreloadService get _service =>
      Get.find<FortuneWheelPreloadService>();

  @override
  void initState() {
    super.initState();
    _spinAudioPlayer = AudioPlayer()..setReleaseMode(ReleaseMode.stop);
    _spinController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 3680),
    );
    _spinAnimation = AlwaysStoppedAnimation<double>(_rotation);
    _spinController.addListener(() {
      setState(() => _rotation = _spinAnimation.value);
    });
    _ambientController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 6200),
    )..repeat();
    _entranceController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 850),
    )..forward();
    unawaited(_service.maybePreload(reason: 'panel_open'));
  }

  @override
  void dispose() {
    unawaited(_spinAudioPlayer.stop());
    unawaited(_spinAudioPlayer.dispose());
    _spinController.dispose();
    _ambientController.dispose();
    _entranceController.dispose();
    super.dispose();
  }

  Future<void> _spin(FortuneWheelSnapshot snapshot) async {
    if (_spinning || snapshot.segments.isEmpty) return;
    if (widget.freeSpinOnly && !snapshot.canFreeSpin) {
      widget.onClose?.call();
      return;
    }
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
      unawaited(_playSpinSound());
      await _spinController.forward(from: 0);
      if (!mounted) return;
      setState(() => _spinning = false);
      Haptics.success();
      await _showReward(result.spin);
      if (mounted && widget.freeSpinOnly) {
        widget.onClose?.call();
      }
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _spinning = false;
        _error = e.toString().replaceFirst('Exception: ', '');
      });
      Haptics.warning();
    }
  }

  Future<void> _playSpinSound() async {
    try {
      await _spinAudioPlayer.stop();
      await _spinAudioPlayer.play(AssetSource(_fortuneSpinSoundAsset));
    } catch (_) {
      // Audio must never block or invalidate a server-authoritative spin.
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
    // The painter already starts segment zero at -pi/2 (the top pointer), so
    // only the segment's offset from that origin must be reversed here.
    final desired = -targetCenter;
    final fullTurns = 6 + math.Random().nextInt(3);
    final base = (math.pi * 2 * fullTurns) + desired;
    final currentTurns = (_rotation / (math.pi * 2)).floor();
    var target = base + (math.pi * 2 * currentTurns);
    while (target <= _rotation + (math.pi * 2 * 4)) {
      target += math.pi * 2;
    }
    return target;
  }

  Future<void> _showReward(FortuneWheelSpin spin) {
    return showGeneralDialog<void>(
      context: context,
      useRootNavigator: true,
      barrierDismissible: true,
      barrierLabel: 'Close reward',
      barrierColor: Colors.black.withValues(alpha: .72),
      transitionDuration: const Duration(milliseconds: 320),
      pageBuilder:
          (_, __, ___) => Center(
            child: Material(
              color: Colors.transparent,
              child: ConstrainedBox(
                constraints: const BoxConstraints(maxWidth: 420),
                child: _FortuneRewardSheet(spin: spin),
              ),
            ),
          ),
      transitionBuilder:
          (_, animation, __, child) => FadeTransition(
            opacity: animation,
            child: ScaleTransition(
              scale: Tween<double>(begin: .90, end: 1).animate(
                CurvedAnimation(parent: animation, curve: Curves.easeOutBack),
              ),
              child: child,
            ),
          ),
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
    return Obx(() {
      final snapshot = _service.snapshot.value;
      final loading = _service.loading.value;
      final error = _error ?? _service.error.value;

      return AnimatedBuilder(
        animation: Listenable.merge([_ambientController, _entranceController]),
        builder: (context, _) {
          final ambient = _ambientController.value;
          final entrance = Curves.easeOutCubic.transform(
            _entranceController.value,
          );

          return ColoredBox(
            color: Colors.transparent,
            child: Stack(
              children: [
                SafeArea(
                  top: false,
                  child: Opacity(
                    opacity: entrance,
                    child: Transform.translate(
                      offset: Offset(0, (1 - entrance) * 26),
                      child: ListView(
                        padding: const EdgeInsets.fromLTRB(18, 12, 18, 24),
                        children: [
                          _FortuneTopBar(
                            snapshot: snapshot,
                            showCloseButton: widget.showCloseButton,
                            onClose:
                                widget.onClose ??
                                () => Navigator.of(context).maybePop(),
                          ),
                          const SizedBox(height: 8),
                          if (loading && snapshot == null)
                            const _FortuneLoadingCard()
                          else if (error != null && snapshot == null)
                            _FortuneErrorCard(
                              message: error,
                              onRetry: () => unawaited(_service.refresh()),
                            )
                          else if (snapshot == null)
                            _FortuneErrorCard(
                              message:
                                  'Fortune Wheel is not available right now.',
                              onRetry: () => unawaited(_service.refresh()),
                            )
                          else ...[
                            if (snapshot.segments.isEmpty)
                              _NoSegmentsCard(
                                onRefresh: () => unawaited(_service.refresh()),
                              )
                            else ...[
                              _WheelStage(
                                snapshot: snapshot,
                                rotation: _rotation,
                                spinning: _spinning,
                                ambient: ambient,
                              ),
                              const SizedBox(height: 4),
                              _SpinButton(
                                snapshot: snapshot,
                                spinning: _spinning,
                                ambient: ambient,
                                onPressed: () => unawaited(_spin(snapshot)),
                              ),
                            ],
                            if (error != null) ...[
                              const SizedBox(height: 12),
                              _InlineError(message: error),
                            ],
                          ],
                        ],
                      ),
                    ),
                  ),
                ),
              ],
            ),
          );
        },
      );
    });
  }
}

class _FortuneTopBar extends StatelessWidget {
  const _FortuneTopBar({
    required this.snapshot,
    required this.showCloseButton,
    required this.onClose,
  });

  final FortuneWheelSnapshot? snapshot;
  final bool showCloseButton;
  final VoidCallback onClose;

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 70,
      child: Row(
        children: [
          SizedBox(
            width: 86,
            child:
                snapshot == null
                    ? const SizedBox.shrink()
                    : _BalanceChip(balance: snapshot!.walletBalance),
          ),
          Expanded(
            child: Image.asset(
              _fortuneTitleAsset,
              height: 64,
              fit: BoxFit.contain,
              filterQuality: FilterQuality.high,
            ),
          ),
          SizedBox(
            width: 86,
            child:
                showCloseButton
                    ? Align(
                      alignment: Alignment.centerRight,
                      child: _FortuneCloseButton(onPressed: onClose),
                    )
                    : const SizedBox.shrink(),
          ),
        ],
      ),
    );
  }
}

class _BalanceChip extends StatelessWidget {
  const _BalanceChip({required this.balance});

  final int balance;

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 38,
      padding: const EdgeInsets.fromLTRB(5, 3, 9, 3),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(999),
        gradient: LinearGradient(
          colors: [
            const Color(0xFF3C1A62).withValues(alpha: .94),
            const Color(0xFF160A25).withValues(alpha: .96),
          ],
        ),
        border: Border.all(
          color: const Color(0xFFFFD76B).withValues(alpha: .68),
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: .30),
            blurRadius: 14,
            offset: const Offset(0, 7),
          ),
        ],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          const CoinLottie(size: 24),
          const SizedBox(width: 3),
          Flexible(
            child: FittedBox(
              fit: BoxFit.scaleDown,
              child: Text(
                '$balance',
                maxLines: 1,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 12,
                  fontWeight: FontWeight.w900,
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _FortuneCloseButton extends StatelessWidget {
  const _FortuneCloseButton({required this.onPressed});

  final VoidCallback onPressed;

  @override
  Widget build(BuildContext context) {
    return Material(
      color: const Color(0xFF090512).withValues(alpha: .78),
      shape: const CircleBorder(),
      elevation: 8,
      shadowColor: Colors.black,
      child: IconButton(
        tooltip: 'Close',
        onPressed: onPressed,
        icon: const Icon(Icons.close_rounded, size: 20),
        color: Colors.white,
        padding: EdgeInsets.zero,
        constraints: const BoxConstraints.tightFor(width: 38, height: 38),
      ),
    );
  }
}

class _WalletStrip extends StatelessWidget {
  const _WalletStrip({required this.snapshot});

  final FortuneWheelSnapshot snapshot;

  @override
  Widget build(BuildContext context) {
    final free = snapshot.canFreeSpin;
    return Wrap(
      alignment: WrapAlignment.center,
      spacing: 10,
      runSpacing: 8,
      children: [
        _MiniStat(
          label: '${snapshot.walletBalance}',
          icon: Icons.monetization_on_rounded,
          accent: const Color(0xFFFFD76B),
        ),
        _MiniStat(
          label:
              free
                  ? '${snapshot.freeSpinsRemaining} FREE'
                  : '${snapshot.settings.paidSpinCostCoins} COINS',
          icon: free ? Icons.card_giftcard_rounded : Icons.bolt_rounded,
          accent: free ? const Color(0xFF67E8F9) : const Color(0xFFFF5FD2),
        ),
      ],
    );
  }
}

class _MiniStat extends StatelessWidget {
  const _MiniStat({
    required this.label,
    required this.icon,
    required this.accent,
  });

  final String label;
  final IconData icon;
  final Color accent;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 13, vertical: 8),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(999),
        color: Colors.white.withValues(alpha: .06),
        border: Border.all(color: Colors.white.withValues(alpha: .10)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, color: accent, size: 17),
          const SizedBox(width: 7),
          Text(
            label,
            style: const TextStyle(
              color: Colors.white,
              fontSize: 12,
              fontWeight: FontWeight.w900,
              letterSpacing: .2,
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
    required this.ambient,
  });

  final FortuneWheelSnapshot snapshot;
  final double rotation;
  final bool spinning;
  final double ambient;

  @override
  Widget build(BuildContext context) {
    final size = math.min(MediaQuery.sizeOf(context).width - 44, 330.0);
    final wheelSize = size * .82;
    final hubSize = size * .27;
    final wheelCenterY = 44 + (wheelSize / 2);
    final glowPulse = .5 + (.5 * math.sin(ambient * math.pi * 2));
    final frameScale = 1 + (glowPulse * .018);
    return Center(
      child: SizedBox(
        width: size,
        height: size + 48,
        child: Stack(
          alignment: Alignment.center,
          children: [
            Positioned(
              top: 44,
              child: Container(
                width: wheelSize,
                height: wheelSize,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  boxShadow: [
                    BoxShadow(
                      color: const Color(
                        0xFFFFD76B,
                      ).withValues(alpha: .18 + glowPulse * .12),
                      blurRadius: 42 + glowPulse * 18,
                      spreadRadius: 4 + glowPulse * 4,
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
                  child: Stack(
                    children: [
                      CustomPaint(
                        painter: _FortuneWheelPainter(snapshot.segments),
                        child: Container(),
                      ),
                      Positioned.fill(
                        child: CustomPaint(
                          painter: _WheelSweepPainter(ambient, spinning),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
            Positioned(
              top: 18,
              child: IgnorePointer(
                child: Transform.scale(
                  scale: frameScale,
                  child: Image.asset(
                    _fortuneWheelFrameAsset,
                    width: size,
                    height: size,
                    fit: BoxFit.contain,
                    filterQuality: FilterQuality.high,
                  ),
                ),
              ),
            ),
            Positioned(
              top: 42,
              child: IgnorePointer(
                child: AnimatedScale(
                  scale: spinning ? 1.08 : 1,
                  duration: const Duration(milliseconds: 300),
                  curve: Curves.easeOutBack,
                  child: const SizedBox(
                    width: 34,
                    height: 38,
                    child: CustomPaint(painter: _FortunePointerPainter()),
                  ),
                ),
              ),
            ),
            Positioned(
              top: wheelCenterY - (hubSize / 2),
              child: AnimatedScale(
                scale: spinning ? 1.12 : 1 + glowPulse * .025,
                duration: const Duration(milliseconds: 500),
                curve: Curves.easeOutBack,
                child: SizedBox(
                  width: hubSize,
                  height: hubSize,
                  child: Stack(
                    alignment: Alignment.center,
                    children: [
                      Image.asset(
                        _fortuneSpinButtonAsset,
                        fit: BoxFit.contain,
                        filterQuality: FilterQuality.high,
                      ),
                      Icon(
                        spinning
                            ? Icons.auto_awesome_rounded
                            : Icons.stars_rounded,
                        color: Colors.white,
                        size: size * .075,
                        shadows: const [
                          Shadow(color: Color(0xFF4A0045), blurRadius: 7),
                        ],
                      ),
                    ],
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

class _FortunePointerPainter extends CustomPainter {
  const _FortunePointerPainter();

  @override
  void paint(Canvas canvas, Size size) {
    final path =
        Path()
          ..moveTo(2, 3)
          ..quadraticBezierTo(size.width / 2, -1, size.width - 2, 3)
          ..lineTo(size.width / 2, size.height - 2)
          ..close();
    canvas.drawShadow(path, Colors.black, 8, true);
    final paint =
        Paint()
          ..shader = const LinearGradient(
            colors: [Color(0xFFFFF2A8), Color(0xFFFFB21C), Color(0xFFB75A00)],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ).createShader(Offset.zero & size);
    canvas.drawPath(path, paint);
    canvas.drawPath(
      path,
      Paint()
        ..style = PaintingStyle.stroke
        ..strokeWidth = 1.5
        ..color = Colors.white.withValues(alpha: .72),
    );
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}

class _FortuneWheelPainter extends CustomPainter {
  _FortuneWheelPainter(this.segments);

  final List<FortuneWheelSegment> segments;

  static const _fallbackColors = [
    Color(0xFF7C3AED),
    Color(0xFFDB2777),
    Color(0xFF0891B2),
    Color(0xFFEA580C),
    Color(0xFF059669),
    Color(0xFF9333EA),
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
                Color.lerp(color, Colors.white, .16)!,
                Color.lerp(color, Colors.black, .20)!,
              ],
            ).createShader(rect);
      canvas.drawArc(rect, start, slice, true, paint);

      final line =
          Paint()
            ..color = const Color(0xFFFFE7A3).withValues(alpha: .34)
            ..strokeWidth = 1.2;
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
          fontSize: 10.5,
          fontWeight: FontWeight.w900,
          letterSpacing: .25,
          height: 1.05,
          shadows: [Shadow(color: Colors.black87, blurRadius: 6)],
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
      return '${segment.rewardValueCoins}\nCOINS';
    }
    if (segment.rewardType == 'entry_pack') {
      return 'ENTRY\n1 DAY';
    }
    if (segment.rewardType == 'subscription') {
      return 'VIP\n1 DAY';
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
    required this.ambient,
    required this.onPressed,
  });

  final FortuneWheelSnapshot snapshot;
  final bool spinning;
  final double ambient;
  final VoidCallback onPressed;

  @override
  Widget build(BuildContext context) {
    final isFree = snapshot.canFreeSpin;
    final pulse = .5 + (.5 * math.sin(ambient * math.pi * 2));
    final label =
        spinning
            ? 'SPINNING...'
            : isFree
            ? 'FREE SPIN'
            : 'SPIN';
    return Center(
      child: Transform.scale(
        scale: spinning ? .98 : .98 + (pulse * .035),
        child: SizedBox(
          width: 180,
          height: 60,
          child: Stack(
            children: [
              Positioned(
                left: 3,
                right: 3,
                top: 9,
                bottom: 0,
                child: DecoratedBox(
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(20),
                    color: const Color(0xFF64134E),
                    border: Border.all(color: const Color(0xFF3D092E)),
                    boxShadow: [
                      BoxShadow(
                        color: const Color(
                          0xFFFF4DCB,
                        ).withValues(alpha: .18 + pulse * .16),
                        blurRadius: 20 + pulse * 8,
                        offset: const Offset(0, 8),
                      ),
                    ],
                  ),
                ),
              ),
              Positioned(
                left: 0,
                right: 0,
                top: 0,
                bottom: 8,
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(20),
                  child: Material(
                    color: Colors.transparent,
                    child: Ink(
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(20),
                        gradient: const LinearGradient(
                          colors: [Color(0xFFFF7BE4), Color(0xFFC61E9B)],
                          begin: Alignment.topCenter,
                          end: Alignment.bottomCenter,
                        ),
                        border: Border.all(
                          color: const Color(0xFFFFE9A0),
                          width: 1.4,
                        ),
                      ),
                      child: InkWell(
                        onTap:
                            spinning || snapshot.segments.isEmpty
                                ? null
                                : onPressed,
                        borderRadius: BorderRadius.circular(20),
                        child: Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            if (spinning)
                              const SizedBox(
                                width: 17,
                                height: 17,
                                child: CircularProgressIndicator(
                                  strokeWidth: 2.3,
                                  color: Colors.white,
                                ),
                              )
                            else
                              const Icon(
                                Icons.auto_awesome_rounded,
                                color: Color(0xFFFFF0A8),
                                size: 19,
                              ),
                            const SizedBox(width: 8),
                            Text(
                              label,
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 15,
                                fontWeight: FontWeight.w900,
                                letterSpacing: .5,
                                shadows: [
                                  Shadow(
                                    color: Color(0xFF5B0A43),
                                    blurRadius: 5,
                                  ),
                                ],
                              ),
                            ),
                            if (!spinning && !isFree) ...[
                              const SizedBox(width: 6),
                              const CoinLottie(size: 18),
                              const SizedBox(width: 2),
                              Text(
                                '${snapshot.settings.paidSpinCostCoins}',
                                style: const TextStyle(
                                  color: Colors.white,
                                  fontSize: 15,
                                  fontWeight: FontWeight.w900,
                                ),
                              ),
                            ],
                          ],
                        ),
                      ),
                    ),
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

class _FortuneRewardSheet extends StatefulWidget {
  const _FortuneRewardSheet({required this.spin});

  final FortuneWheelSpin spin;

  @override
  State<_FortuneRewardSheet> createState() => _FortuneRewardSheetState();
}

class _FortuneRewardSheetState extends State<_FortuneRewardSheet>
    with SingleTickerProviderStateMixin {
  late final AnimationController _controller;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1600),
    )..repeat();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: _controller,
      builder: (context, _) {
        final pulse = .5 + (.5 * math.sin(_controller.value * math.pi * 2));
        return SafeArea(
          top: false,
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: TweenAnimationBuilder<double>(
              tween: Tween(begin: .92, end: 1),
              duration: const Duration(milliseconds: 520),
              curve: Curves.easeOutBack,
              builder:
                  (context, scale, child) =>
                      Transform.scale(scale: scale, child: child),
              child: Container(
                padding: const EdgeInsets.fromLTRB(24, 22, 24, 24),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(34),
                  gradient: const LinearGradient(
                    colors: [
                      Color(0xFF3B1764),
                      Color(0xFF180925),
                      Color(0xFF09050F),
                    ],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  border: Border.all(
                    color: const Color(0xFFFFD76B).withValues(alpha: .48),
                    width: 1.4,
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: const Color(
                        0xFFFFD76B,
                      ).withValues(alpha: .12 + pulse * .12),
                      blurRadius: 42 + pulse * 22,
                      spreadRadius: pulse * 2,
                      offset: const Offset(0, 18),
                    ),
                  ],
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(30),
                  child: Stack(
                    children: [
                      Positioned.fill(
                        child: CustomPaint(
                          painter: _RewardBurstPainter(_controller.value),
                        ),
                      ),
                      Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          const Text(
                            'LUCKY DROP',
                            style: TextStyle(
                              color: Color(0xFFFFD76B),
                              fontSize: 14,
                              fontWeight: FontWeight.w900,
                              letterSpacing: 2.2,
                            ),
                          ),
                          const SizedBox(height: 10),
                          Transform.scale(
                            scale: 1 + pulse * .045,
                            child: SizedBox(
                              width: 108,
                              height: 108,
                              child: Stack(
                                alignment: Alignment.center,
                                children: [
                                  Image.asset(
                                    _fortuneSpinButtonAsset,
                                    fit: BoxFit.contain,
                                    filterQuality: FilterQuality.high,
                                  ),
                                  Icon(
                                    _rewardIcon(widget.spin.rewardType),
                                    color: Colors.white,
                                    size: 42,
                                    shadows: const [
                                      Shadow(
                                        color: Color(0xFF4A0045),
                                        blurRadius: 8,
                                      ),
                                    ],
                                  ),
                                ],
                              ),
                            ),
                          ),
                          const SizedBox(height: 10),
                          const Text(
                            'YOUR REWARD',
                            style: TextStyle(
                              color: Colors.white60,
                              fontSize: 12,
                              fontWeight: FontWeight.w900,
                              letterSpacing: 1.8,
                            ),
                          ),
                          const SizedBox(height: 6),
                          Text(
                            _rewardText(widget.spin),
                            textAlign: TextAlign.center,
                            style: const TextStyle(
                              color: Colors.white,
                              fontSize: 27,
                              fontWeight: FontWeight.w900,
                              letterSpacing: -.3,
                              shadows: [
                                Shadow(
                                  color: Color(0xFFFF5FD2),
                                  blurRadius: 14,
                                ),
                              ],
                            ),
                          ),
                          const SizedBox(height: 22),
                          _PremiumCollectButton(
                            pulse: pulse,
                            onPressed: () => Navigator.of(context).maybePop(),
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
      },
    );
  }
}

class _PremiumCollectButton extends StatelessWidget {
  const _PremiumCollectButton({required this.pulse, required this.onPressed});

  final double pulse;
  final VoidCallback onPressed;

  @override
  Widget build(BuildContext context) {
    return Transform.scale(
      scale: .98 + pulse * .025,
      child: SizedBox(
        width: 176,
        height: 58,
        child: Stack(
          children: [
            Positioned(
              left: 3,
              right: 3,
              top: 8,
              bottom: 0,
              child: DecoratedBox(
                decoration: BoxDecoration(
                  color: const Color(0xFF6B174F),
                  borderRadius: BorderRadius.circular(19),
                  border: Border.all(color: const Color(0xFF3E082C)),
                ),
              ),
            ),
            Positioned(
              left: 0,
              right: 0,
              top: 0,
              bottom: 7,
              child: ClipRRect(
                borderRadius: BorderRadius.circular(19),
                child: Material(
                  color: Colors.transparent,
                  child: Ink(
                    decoration: BoxDecoration(
                      gradient: const LinearGradient(
                        colors: [Color(0xFFFF86E6), Color(0xFFD829A9)],
                        begin: Alignment.topCenter,
                        end: Alignment.bottomCenter,
                      ),
                      borderRadius: BorderRadius.circular(19),
                      border: Border.all(
                        color: const Color(0xFFFFEBA5),
                        width: 1.3,
                      ),
                    ),
                    child: InkWell(
                      onTap: onPressed,
                      borderRadius: BorderRadius.circular(19),
                      child: const Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Icon(
                            Icons.card_giftcard_rounded,
                            color: Colors.white,
                            size: 19,
                          ),
                          SizedBox(width: 8),
                          Text(
                            'COLLECT',
                            style: TextStyle(
                              color: Colors.white,
                              fontSize: 15,
                              fontWeight: FontWeight.w900,
                              letterSpacing: .8,
                              shadows: [
                                Shadow(color: Color(0xFF6B174F), blurRadius: 5),
                              ],
                            ),
                          ),
                        ],
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

class _FortuneSparkleOverlay extends StatelessWidget {
  const _FortuneSparkleOverlay({required this.progress});

  final double progress;

  @override
  Widget build(BuildContext context) {
    return IgnorePointer(
      child: CustomPaint(painter: _FortuneSparklePainter(progress)),
    );
  }
}

class _FortuneSparklePainter extends CustomPainter {
  _FortuneSparklePainter(this.progress);

  final double progress;

  static const _stars = <Offset>[
    Offset(.14, .17),
    Offset(.78, .15),
    Offset(.28, .34),
    Offset(.90, .42),
    Offset(.11, .62),
    Offset(.62, .71),
    Offset(.35, .86),
    Offset(.82, .88),
  ];

  @override
  void paint(Canvas canvas, Size size) {
    for (var i = 0; i < _stars.length; i++) {
      final phase = (progress + i * .13) % 1;
      final sparkle = math.sin(phase * math.pi).abs();
      final point = Offset(
        _stars[i].dx * size.width,
        ((_stars[i].dy + phase * .035) % 1) * size.height,
      );
      final radius = 1.8 + sparkle * 3.2;
      final color = (i.isEven
              ? const Color(0xFFFFD76B)
              : const Color(0xFF67E8F9))
          .withValues(alpha: .12 + sparkle * .38);
      final fill = Paint()..color = color;
      final stroke =
          Paint()
            ..color = color.withValues(alpha: .82)
            ..strokeWidth = 1.1
            ..strokeCap = StrokeCap.round;

      canvas.drawCircle(point, radius, fill);
      canvas.drawLine(
        point + Offset(-radius * 2.4, 0),
        point + Offset(radius * 2.4, 0),
        stroke,
      );
      canvas.drawLine(
        point + Offset(0, -radius * 2.4),
        point + Offset(0, radius * 2.4),
        stroke,
      );
    }
  }

  @override
  bool shouldRepaint(covariant _FortuneSparklePainter oldDelegate) {
    return oldDelegate.progress != progress;
  }
}

class _ShimmerSweepPainter extends CustomPainter {
  _ShimmerSweepPainter(this.progress);

  final double progress;

  @override
  void paint(Canvas canvas, Size size) {
    final bandWidth = size.width * .22;
    final travel = size.width + bandWidth * 2;
    final x = (travel * progress) - bandWidth;
    final rect = Rect.fromLTWH(
      x,
      -size.height * .45,
      bandWidth,
      size.height * 1.9,
    );
    final paint =
        Paint()
          ..shader = LinearGradient(
            colors: [
              Colors.transparent,
              Colors.white.withValues(alpha: .18),
              Colors.transparent,
            ],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ).createShader(rect);

    canvas.save();
    canvas.translate(size.width / 2, size.height / 2);
    canvas.rotate(-.34);
    canvas.translate(-size.width / 2, -size.height / 2);
    canvas.drawRect(rect, paint);
    canvas.restore();
  }

  @override
  bool shouldRepaint(covariant _ShimmerSweepPainter oldDelegate) {
    return oldDelegate.progress != progress;
  }
}

class _WheelSweepPainter extends CustomPainter {
  _WheelSweepPainter(this.progress, this.spinning);

  final double progress;
  final bool spinning;

  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2);
    final radius = math.min(size.width, size.height) / 2;
    final rect = Rect.fromCircle(center: center, radius: radius);
    final start = (progress * math.pi * 2) - math.pi / 8;
    final paint =
        Paint()
          ..style = PaintingStyle.stroke
          ..strokeWidth = spinning ? 16 : 10
          ..strokeCap = StrokeCap.round
          ..shader = SweepGradient(
            startAngle: start,
            endAngle: start + math.pi / 2.7,
            colors: [
              Colors.transparent,
              const Color(0xFFFFF3B0).withValues(alpha: spinning ? .68 : .36),
              Colors.transparent,
            ],
          ).createShader(rect);

    canvas.drawArc(
      Rect.fromCircle(center: center, radius: radius * .82),
      start,
      math.pi / 2.7,
      false,
      paint,
    );
  }

  @override
  bool shouldRepaint(covariant _WheelSweepPainter oldDelegate) {
    return oldDelegate.progress != progress || oldDelegate.spinning != spinning;
  }
}

class _ButtonShimmerPainter extends CustomPainter {
  _ButtonShimmerPainter(this.progress);

  final double progress;

  @override
  void paint(Canvas canvas, Size size) {
    final width = size.width * .24;
    final x = (size.width + width * 2) * progress - width;
    final rect = Rect.fromLTWH(x, -size.height, width, size.height * 3);
    final paint =
        Paint()
          ..shader = LinearGradient(
            colors: [
              Colors.transparent,
              Colors.white.withValues(alpha: .30),
              Colors.transparent,
            ],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ).createShader(rect);

    canvas.save();
    canvas.translate(size.width / 2, size.height / 2);
    canvas.rotate(-.46);
    canvas.translate(-size.width / 2, -size.height / 2);
    canvas.drawRect(rect, paint);
    canvas.restore();
  }

  @override
  bool shouldRepaint(covariant _ButtonShimmerPainter oldDelegate) {
    return oldDelegate.progress != progress;
  }
}

class _RewardBurstPainter extends CustomPainter {
  _RewardBurstPainter(this.progress);

  final double progress;

  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height * .22);
    final radius = math.min(size.width, size.height) * .34;
    final ring =
        Paint()
          ..style = PaintingStyle.stroke
          ..strokeWidth = 2
          ..color = const Color(0xFFFFD76B).withValues(alpha: .16);
    canvas.drawCircle(center, radius * (.72 + progress * .18), ring);

    final ray =
        Paint()
          ..strokeWidth = 2
          ..strokeCap = StrokeCap.round
          ..color = Colors.white.withValues(alpha: .14);
    for (var i = 0; i < 18; i++) {
      final angle = (i / 18 * math.pi * 2) + progress * math.pi * 2;
      final start =
          center + Offset(math.cos(angle), math.sin(angle)) * (radius * .38);
      final end =
          center + Offset(math.cos(angle), math.sin(angle)) * (radius * .82);
      canvas.drawLine(start, end, ray);
    }
  }

  @override
  bool shouldRepaint(covariant _RewardBurstPainter oldDelegate) {
    return oldDelegate.progress != progress;
  }
}

class _FortuneBackground extends StatelessWidget {
  const _FortuneBackground();

  @override
  Widget build(BuildContext context) {
    return Stack(
      fit: StackFit.expand,
      children: [
        Opacity(
          opacity: .34,
          child: Image.asset(
            _fortuneBgAsset,
            fit: BoxFit.cover,
            alignment: Alignment.topCenter,
            filterQuality: FilterQuality.high,
          ),
        ),
        DecoratedBox(
          decoration: BoxDecoration(
            gradient: LinearGradient(
              colors: [
                const Color(0xFF160B2C).withValues(alpha: .30),
                const Color(0xFF07040E).withValues(alpha: .70),
              ],
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
            ),
          ),
        ),
      ],
    );
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
