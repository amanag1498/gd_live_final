import 'dart:math' as math;
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:gd_live/app/widgets/gd_live_logo.dart';
import 'package:gd_live/app/brand/brand.dart';
import 'package:gd_live/app/widgets/gd_modal_surface.dart';
// ⬇️ adjust this path if needed

class LoggedOutDialog extends StatefulWidget {
  const LoggedOutDialog({super.key});

  @override
  State<LoggedOutDialog> createState() => _LoggedOutDialogState();
}

class _LoggedOutDialogState extends State<LoggedOutDialog>
    with TickerProviderStateMixin {
  late final AnimationController _pop;
  late final AnimationController _glow; // logo halo
  late final AnimationController _btn;  // button pulse

  @override
  void initState() {
    super.initState();
    HapticFeedback.lightImpact();
    _pop  = AnimationController(vsync: this, duration: const Duration(milliseconds: 220))..forward();
    _glow = AnimationController(vsync: this, duration: const Duration(milliseconds: 2200))..repeat(reverse: true);
    _btn  = AnimationController(vsync: this, duration: const Duration(milliseconds: 1900))..repeat(reverse: true);
  }

  @override
  void dispose() {
    _pop.dispose();
    _glow.dispose();
    _btn.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final curve = CurvedAnimation(parent: _pop, curve: Curves.easeOutBack);
    final tokens = getBrandTokens(
      'midnight',
    );

    return FadeTransition(
      opacity: curve,
      child: ScaleTransition(
        scale: Tween(begin: .94, end: 1.0).animate(curve),
        child: Dialog(
          elevation: 0,
          backgroundColor: Colors.transparent,
          insetPadding: const EdgeInsets.symmetric(horizontal: 22),
          child: GdModalSurface(
            tokens: tokens,
            scrollable: true,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                AnimatedBuilder(
                  animation: _glow,
                  builder: (_, __) {
                    final t = _glow.value;
                    return Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: tokens.primaryButtonGradient.first.withOpacity(.08),
                        border: Border.all(
                          color: tokens.primaryButtonGradient.first.withOpacity(.12),
                        ),
                        boxShadow: [
                          BoxShadow(
                            color: tokens.glowColor.withOpacity(
                              .12 + .06 * math.sin(t * math.pi),
                            ),
                            blurRadius: 20,
                            spreadRadius: 1,
                          ),
                        ],
                      ),
                      child: const GdLiveLogo(size: 54, showWordmark: false),
                    );
                  },
                ),
                const SizedBox(height: 12),
                Text(
                  'Signed out',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: tokens.textPrimary,
                    fontSize: 20,
                    fontWeight: FontWeight.w900,
                    letterSpacing: .1,
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  'Your session ended. Sign in again to continue.',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: tokens.textSecondary,
                    fontWeight: FontWeight.w600,
                    height: 1.35,
                  ),
                ),
                const SizedBox(height: 18),
                AnimatedBuilder(
                  animation: _btn,
                  builder: (_, __) {
                    final lift =
                        1 + (math.sin(_btn.value * 2 * math.pi) * 0.01);
                    return Transform.scale(
                      scale: lift,
                      child: SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: tokens.primaryButtonGradient.first,
                            foregroundColor: Colors.white,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(16),
                            ),
                            padding: const EdgeInsets.symmetric(vertical: 14),
                            shadowColor: Colors.transparent,
                            elevation: 0,
                          ),
                          onPressed: () => Navigator.of(context).pop(),
                          child: const Text(
                            'OK',
                            style: TextStyle(fontWeight: FontWeight.w900),
                          ),
                        ),
                      ),
                    );
                  },
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

class BlockedDialog extends StatefulWidget {
  const BlockedDialog({super.key});

  @override
  State<BlockedDialog> createState() => _BlockedDialogState();
}

class _BlockedDialogState extends State<BlockedDialog>
    with TickerProviderStateMixin {
  late final AnimationController _pop;
  late final AnimationController _glow;
  late final AnimationController _btn;

  @override
  void initState() {
    super.initState();
    HapticFeedback.lightImpact();
    _pop  = AnimationController(vsync: this, duration: const Duration(milliseconds: 220))..forward();
    _glow = AnimationController(vsync: this, duration: const Duration(milliseconds: 2200))..repeat(reverse: true);
    _btn  = AnimationController(vsync: this, duration: const Duration(milliseconds: 1900))..repeat(reverse: true);
  }

  @override
  void dispose() {
    _pop.dispose();
    _glow.dispose();
    _btn.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final curve = CurvedAnimation(parent: _pop, curve: Curves.easeOutBack);
    final tokens = getBrandTokens('midnight');

    return FadeTransition(
      opacity: curve,
      child: ScaleTransition(
        scale: Tween(begin: .94, end: 1.0).animate(curve),
        child: Dialog(
          elevation: 0,
          backgroundColor: Colors.transparent,
          insetPadding: const EdgeInsets.symmetric(horizontal: 22),
          child: GdModalSurface(
            tokens: tokens,
            scrollable: true,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                AnimatedBuilder(
                  animation: _glow,
                  builder: (_, __) {
                    final t = _glow.value;
                    return Container(
                      padding: const EdgeInsets.all(10),
                      decoration: BoxDecoration(
                        shape: BoxShape.circle,
                        color: const Color(0xFFF1FBF4),
                        border: Border.all(
                          color: tokens.dangerColor.withOpacity(.14),
                        ),
                        boxShadow: [
                          BoxShadow(
                            color: tokens.dangerColor.withOpacity(
                              .10 + .05 * math.sin(t * math.pi),
                            ),
                            blurRadius: 20,
                            spreadRadius: 1,
                          ),
                        ],
                      ),
                      child: const GdLiveLogo(size: 54, showWordmark: false),
                    );
                  },
                ),
                const SizedBox(height: 12),
                Text(
                  'Account blocked',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: tokens.textPrimary,
                    fontSize: 20,
                    fontWeight: FontWeight.w900,
                    letterSpacing: .1,
                  ),
                ),
                const SizedBox(height: 8),
                Text(
                  'Access is restricted for this account.\nIf this looks wrong, contact support.',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: tokens.textSecondary,
                    fontWeight: FontWeight.w600,
                    height: 1.35,
                  ),
                ),
                const SizedBox(height: 18),
                AnimatedBuilder(
                  animation: _btn,
                  builder: (_, __) {
                    final lift =
                        1 + (math.sin(_btn.value * 2 * math.pi) * 0.01);
                    return Transform.scale(
                      scale: lift,
                      child: SizedBox(
                        width: double.infinity,
                        child: ElevatedButton(
                          style: ElevatedButton.styleFrom(
                            backgroundColor: tokens.primaryButtonGradient.first,
                            foregroundColor: Colors.white,
                            shape: RoundedRectangleBorder(
                              borderRadius: BorderRadius.circular(16),
                            ),
                            padding: const EdgeInsets.symmetric(vertical: 14),
                            shadowColor: Colors.transparent,
                            elevation: 0,
                          ),
                          onPressed: () => Navigator.of(context).pop(),
                          child: const Text(
                            'OK',
                            style: TextStyle(fontWeight: FontWeight.w900),
                          ),
                        ),
                      ),
                    );
                  },
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
