import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'dart:math' as math;

import '../../services/app_settings_service.dart';
import '../brand/brand.dart'; // kGdLivePrimary, kGdLiveBg

const String _kGdLiveLogoAsset = 'assets/logos/gd-live-logo.png';

/// GdLiveLogo: adaptive & overflow-safe.
/// - Scales the badge to available constraints
/// - Wraps the whole mark+wordmark in a FittedBox to avoid RenderFlex overflows
class GdLiveLogo extends StatefulWidget {
  final double size;               // desired max size of the badge
  final bool showWordmark;
  final bool wordmarkBelow;
  final TextStyle? wordmarkStyle;

  const GdLiveLogo({
    super.key,
    this.size = 104,
    this.showWordmark = false,
    this.wordmarkBelow = true,
    this.wordmarkStyle,
  });

  @override
  State<GdLiveLogo> createState() => _GdLiveLogoState();
}

class GdLiveLauncherIcon extends StatelessWidget {
  final double size;
  final double padding;
  final String brandKey;

  const GdLiveLauncherIcon({
    super.key,
    this.size = 1024,
    this.padding = 176,
    this.brandKey = 'midnight',
  });

  @override
  Widget build(BuildContext context) {
    return SizedBox.square(
      dimension: size,
      child: CustomPaint(
        painter: GdLiveLauncherIconPainter(
          brandKey: brandKey,
          padding: padding,
        ),
        isComplex: true,
      ),
    );
  }
}

class GdLiveLauncherIconPainter extends CustomPainter {
  final String brandKey;
  final double padding;

  const GdLiveLauncherIconPainter({
    this.brandKey = 'midnight',
    this.padding = 176,
  });

  @override
  void paint(Canvas canvas, Size size) {
    final tokens = getBrandTokens(brandKey);
    final rect = Offset.zero & size;
    final gradientColors = <Color>[
      tokens.primaryButtonGradient.first,
      tokens.primaryButtonGradient.last,
    ];

    final backgroundPaint = Paint()
      ..isAntiAlias = true
      ..shader = LinearGradient(
        begin: Alignment.topLeft,
        end: Alignment.bottomRight,
        colors: [
          const Color(0xFF060814),
          tokens.backgroundGradient.first.withOpacity(.94),
          tokens.backgroundGradient.last.withOpacity(.98),
        ],
      ).createShader(rect);
    canvas.drawRect(rect, backgroundPaint);

    final radialPaint = Paint()
      ..isAntiAlias = true
      ..shader = RadialGradient(
        center: const Alignment(-.48, -.52),
        radius: 1.08,
        colors: [
          gradientColors.first.withOpacity(.34),
          Colors.transparent,
        ],
      ).createShader(rect);
    canvas.drawRect(rect, radialPaint);

    final washPaint = Paint()
      ..isAntiAlias = true
      ..shader = LinearGradient(
        begin: Alignment.topCenter,
        end: Alignment.bottomCenter,
        colors: [
          Colors.white.withOpacity(.08),
          Colors.transparent,
          Colors.black.withOpacity(.16),
        ],
      ).createShader(rect);
    canvas.drawRect(rect, washPaint);

    final badgeSize = math.max(0.0, size.shortestSide - (padding * 2));
    final left = (size.width - badgeSize) / 2;
    final top = (size.height - badgeSize) / 2;
    canvas.save();
    canvas.translate(left, top);
    GdLivePainter(
      pulseT: .82,
      angle: -math.pi / 6,
      sweepT: .34,
      neonT: .58,
      gradientColors: gradientColors,
    ).paint(canvas, Size.square(badgeSize));
    canvas.restore();
  }

  @override
  bool shouldRepaint(covariant GdLiveLauncherIconPainter old) =>
      old.brandKey != brandKey || old.padding != padding;
}

class _GdLiveLogoState extends State<GdLiveLogo> with TickerProviderStateMixin {
  late final AnimationController _pulse;   // live dot
  late final AnimationController _rotate;  // satellites
  late final AnimationController _sweep;   // badge gradient rotation
  late final AnimationController _breath;  // aura breathing

  double _tiltX = 0.0; // Y rotation
  double _tiltY = 0.0; // X rotation

  @override
  void initState() {
    super.initState();
    _pulse  = AnimationController(vsync: this, duration: const Duration(milliseconds: 1200))..repeat(reverse: true);
    _rotate = AnimationController(vsync: this, duration: const Duration(seconds: 10))..repeat();
    _sweep  = AnimationController(vsync: this, duration: const Duration(seconds: 6))..repeat();
    _breath = AnimationController(vsync: this, duration: const Duration(milliseconds: 2600))..repeat(reverse: true);
  }

  @override
  void dispose() {
    _pulse.dispose();
    _rotate.dispose();
    _sweep.dispose();
    _breath.dispose();
    super.dispose();
  }

  void _updateTilt(Offset localPos, Size box) {
    final nx = (localPos.dx / (box.width  == 0 ? 1 : box.width)) * 2 - 1;  // -1..1
    final ny = (localPos.dy / (box.height == 0 ? 1 : box.height)) * 2 - 1; // -1..1
    setState(() {
      _tiltX = (nx * 10) * math.pi / 180;
      _tiltY = (-ny * 10) * math.pi / 180;
    });
  }

  void _resetTilt() => setState(() { _tiltX = 0; _tiltY = 0; });

  @override
  Widget build(BuildContext context) {
      final settingsBrandKey =
          Get.isRegistered<AppSettingsService>()
              ? Get.find<AppSettingsService>().brandKey
              : kGdLiveBrandKey;
      final tokens = getBrandTokens(settingsBrandKey);
      final gradientColors = <Color>[
        tokens.primaryButtonGradient.first,
        tokens.primaryButtonGradient.last,
      ];

      final wordmark = ShaderMask(
        shaderCallback:
            (r) => LinearGradient(
              colors: gradientColors,
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ).createShader(r),
        child: Text(
          'GD Live',
          style:
              (widget.wordmarkStyle ?? Theme.of(context).textTheme.headlineSmall)
                  ?.copyWith(
                    fontWeight: FontWeight.w800,
                    color: tokens.textPrimary,
                    letterSpacing: .5,
                  ),
        ),
      );

      final mark = LayoutBuilder(
        builder: (context, constraints) {
          double s;
          if (constraints.hasBoundedWidth && constraints.hasBoundedHeight) {
            final shortest = constraints.biggest.shortestSide;
            s =
                shortest.isFinite && shortest > 0
                    ? math.min(widget.size, shortest)
                    : widget.size;
          } else {
            s = widget.size;
          }
          s = s.clamp(12.0, widget.size);

          final child = AnimatedBuilder(
            animation: Listenable.merge([_pulse, _breath]),
            builder: (_, __) {
              final bob = math.sin(_breath.value * 2 * math.pi) * 0.02;
              final m =
                  Matrix4.identity()
                    ..setEntry(3, 2, 0.0015)
                    ..rotateX(_tiltY)
                    ..rotateY(_tiltX)
                    ..scale(1.0 + bob);

              return Transform(
                alignment: Alignment.center,
                transform: m,
                child: Container(
                  width: s,
                  height: s,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(s * .26),
                    boxShadow: [
                      BoxShadow(
                        color: gradientColors.first.withOpacity(.16),
                        blurRadius: s * .18,
                        offset: Offset(0, s * .08),
                      ),
                    ],
                  ),
                  child: ClipRRect(
                    borderRadius: BorderRadius.circular(s * .26),
                    child: Image.asset(
                      _kGdLiveLogoAsset,
                      fit: BoxFit.cover,
                    ),
                  ),
                ),
              );
            },
          );

          return MouseRegion(
            onExit: (_) => _resetTilt(),
            onHover: (e) {
              final box = context.findRenderObject() as RenderBox?;
              if (box != null) {
                _updateTilt(box.globalToLocal(e.position), box.size);
              }
            },
            child: GestureDetector(
              behavior: HitTestBehavior.translucent,
              onPanUpdate: (d) {
                final box = context.findRenderObject() as RenderBox?;
                if (box != null) _updateTilt(d.localPosition, box.size);
              },
              onPanEnd: (_) => _resetTilt(),
              onPanCancel: _resetTilt,
              child: SizedBox.square(dimension: s, child: child),
            ),
          );
        },
      );

      if (!widget.showWordmark) {
        return FittedBox(
          fit: BoxFit.scaleDown,
          alignment: Alignment.center,
          child: mark,
        );
      }

      final combined =
          widget.wordmarkBelow
              ? Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  mark,
                  const SizedBox(height: 8),
                  FittedBox(fit: BoxFit.scaleDown, child: wordmark),
                ],
              )
              : Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  mark,
                  const SizedBox(width: 12),
                  FittedBox(fit: BoxFit.scaleDown, child: wordmark),
                ],
              );

      return FittedBox(
        fit: BoxFit.scaleDown,
        alignment: Alignment.center,
        child: combined,
      );
  }
}

class GdLivePainter extends CustomPainter {
  final double pulseT; // 0..1
  final double angle;  // radians
  final double sweepT; // 0..1
  final double neonT;  // 0..1
  final List<Color> gradientColors;

  const GdLivePainter({
    required this.pulseT,
    required this.angle,
    required this.sweepT,
    required this.neonT,
    required this.gradientColors,
  });

  @override
  void paint(Canvas canvas, Size size) {
    final s = size.shortestSide;
    final rect = Offset.zero & Size.square(s);
    final c = rect.center;
    final r = s / 2;

    final badge = RRect.fromRectAndRadius(rect.deflate(s * 0.06), Radius.circular(r * 0.34));
    final sweepAngle = sweepT * 2 * math.pi;
    final badgePaint = Paint()
      ..isAntiAlias = true
      ..shader = LinearGradient(
        colors: gradientColors,
        begin: Alignment(-1, -1),
        end: Alignment(1, 1),
        transform: GradientRotation(sweepAngle),
      ).createShader(badge.outerRect);
    canvas.drawRRect(badge, badgePaint);

    final sheenPaint = Paint()
      ..isAntiAlias = true
      ..blendMode = BlendMode.srcOver
      ..shader = LinearGradient(
        colors: [Colors.white.withOpacity(.18), Colors.white.withOpacity(0)],
        begin: Alignment.topLeft,
        end: Alignment.centerRight,
        transform: GradientRotation(sweepAngle + math.pi / 6),
      ).createShader(badge.outerRect);
    canvas.drawRRect(badge, sheenPaint);

    final white = Paint()..color = Colors.white..isAntiAlias = true;
    final stemW = s * 0.16, stemH = s * 0.48;
    final barW  = s * 0.52, barH  = s * 0.16;
    final stem = RRect.fromRectAndRadius(
      Rect.fromCenter(center: c.translate(0, s * 0.03), width: stemW, height: stemH),
      Radius.circular(stemW / 2),
    );
    final bar = RRect.fromRectAndRadius(
      Rect.fromCenter(center: c.translate(0, -s * 0.16), width: barW, height: barH),
      Radius.circular(barH / 2),
    );
    canvas.save();
    canvas.translate(0, s * 0.012);
    canvas.drawRRect(bar, Paint()..color = Colors.black.withOpacity(.10)..isAntiAlias = true);
    canvas.drawRRect(stem, Paint()..color = Colors.black.withOpacity(.10)..isAntiAlias = true);
    canvas.restore();
    canvas.drawRRect(bar, white);
    canvas.drawRRect(stem, white);

    final dotCenter = Offset(badge.outerRect.right - s * 0.18, badge.outerRect.top + s * 0.18);
    const liveColor = Color(0xFFFF4D67);
    final dotR = s * 0.045;
    canvas.drawCircle(dotCenter, dotR, Paint()..color = liveColor..isAntiAlias = true);
    final ringR = dotR + (s * 0.06 * pulseT);
    final ringAlpha = (1.0 - pulseT).clamp(0.0, 1.0);
    canvas.drawCircle(
      dotCenter,
      ringR,
      Paint()
        ..isAntiAlias = true
        ..style = PaintingStyle.stroke
        ..strokeWidth = s * 0.014 * (1.0 - pulseT * 0.6)
        ..color = liveColor.withOpacity(0.45 * ringAlpha),
    );

    final auraBase = (0.35 + 0.25 * math.sin(neonT * 2 * math.pi));
    for (int i = 0; i < 3; i++) {
      final grow = 8.0 + i * 10.0;
      canvas.drawRRect(
          badge.inflate(grow),
          Paint()
            ..isAntiAlias = true
            ..style = PaintingStyle.stroke
            ..strokeWidth = 2 + i.toDouble()
          ..color = gradientColors.first.withOpacity(0.10 * (i + 1) * auraBase)
          ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 10),
      );
    }

    final orbitR = r * 0.88;
    void satellite(double phase, double scale) {
      final a = angle + phase;
      final p = Offset(c.dx + orbitR * math.cos(a), c.dy + orbitR * math.sin(a));
      for (int t = 1; t <= 4; t++) {
        final ta = a - t * 0.18;
        final tp = Offset(c.dx + orbitR * math.cos(ta), c.dy + orbitR * math.sin(ta));
        canvas.drawCircle(tp, s * 0.010 * scale, Paint()
          ..color = Colors.white.withOpacity(0.12 * (1 - t / 5))
          ..isAntiAlias = true);
      }
      canvas.drawCircle(p, s * 0.018 * scale, Paint()..color = Colors.white.withOpacity(0.85)..isAntiAlias = true);
      canvas.drawCircle(p, s * 0.036 * scale, Paint()
        ..isAntiAlias = true
        ..color = gradientColors.first.withOpacity(.18)
        ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 6));
    }
    satellite(0.0, 1.0);
    satellite(math.pi, 0.85);

    canvas.drawCircle(c, r - s * 0.035, Paint()
      ..isAntiAlias = true
      ..style = PaintingStyle.stroke
      ..strokeWidth = s * 0.010
      ..color = Colors.white.withOpacity(0.08));
  }

  @override
  bool shouldRepaint(covariant GdLivePainter old) =>
      old.pulseT != pulseT ||
      old.angle != angle ||
      old.sweepT != sweepT ||
      old.neonT != neonT ||
      old.gradientColors.first != gradientColors.first ||
      old.gradientColors.last != gradientColors.last;
}
