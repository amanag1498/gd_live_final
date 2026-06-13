import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';

import '../../../app/brand/brand.dart';
import '../../../app/widgets/coin_lottie.dart';
import '../../../app/widgets/gd_modal_surface.dart';
import '../../../services/app_settings_service.dart';

BrandTokens _welcomeGiftTokens() {
  final settings = Get.find<AppSettingsService>();
  return getBrandTokens(settings.brandKey);
}

class BackstageWelcomeDialog extends StatefulWidget {
  const BackstageWelcomeDialog({
    super.key,
    required this.planName,
    this.endsText,
  });

  final String planName;
  final String? endsText;

  @override
  State<BackstageWelcomeDialog> createState() => _BackstageWelcomeDialogState();
}

class _BackstageWelcomeDialogState extends State<BackstageWelcomeDialog>
    with SingleTickerProviderStateMixin {
  late final AnimationController _entry;
  bool _pressing = false;

  @override
  void initState() {
    super.initState();
    HapticFeedback.lightImpact();
    _entry = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 260),
    )..forward();
  }

  @override
  void dispose() {
    _entry.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final tokens = _welcomeGiftTokens();
    final curve = CurvedAnimation(parent: _entry, curve: Curves.easeOutCubic);
    final brand = tokens.primaryButtonGradient.first;
    final accent = tokens.primaryButtonGradient.last;

    return FadeTransition(
      opacity: curve,
      child: ScaleTransition(
        scale: Tween<double>(begin: .94, end: 1).animate(curve),
        child: Dialog(
          backgroundColor: Colors.transparent,
          elevation: 0,
          insetPadding: const EdgeInsets.symmetric(horizontal: 24),
          child: GdModalSurface(
            tokens: tokens,
            radius: 30,
            padding: const EdgeInsets.fromLTRB(18, 16, 18, 18),
            scrollable: true,
            child: Stack(
              children: [
                    Positioned(
                      top: -42,
                      right: -26,
                      child: _GlowOrb(size: 160, color: brand.withOpacity(.10)),
                    ),
                    Positioned(
                      top: 120,
                      left: -30,
                      child: _GlowOrb(
                        size: 122,
                        color: accent.withOpacity(.08),
                      ),
                    ),
                    Padding(
                      padding: const EdgeInsets.fromLTRB(8, 8, 8, 6),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Row(
                            children: [
                              const SizedBox(width: 34),
                              Expanded(
                                child: Center(
                                  child: _PillTag(
                                    icon: Icons.card_giftcard_rounded,
                                    label: 'Signup Gift',
                                    tint: brand,
                                  ),
                                ),
                              ),
                              const SizedBox(width: 12),
                              GestureDetector(
                                onTap: Get.back,
                                child: Container(
                                  width: 34,
                                  height: 34,
                                  decoration: BoxDecoration(
                                    color: Colors.white,
                                    shape: BoxShape.circle,
                                    border: Border.all(
                                      color: const Color(
                                        0xFF102715,
                                      ).withOpacity(.10),
                                    ),
                                    boxShadow: [
                                      BoxShadow(
                                        color: Colors.black.withOpacity(.05),
                                        blurRadius: 10,
                                        offset: const Offset(0, 4),
                                      ),
                                    ],
                                  ),
                                  child: const Icon(
                                    Icons.close_rounded,
                                    size: 18,
                                    color: Color(0xFF12311B),
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 14),
                          Stack(
                            alignment: Alignment.center,
                            children: [
                              Container(
                                width: 124,
                                height: 124,
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  gradient: RadialGradient(
                                    colors: [
                                      brand.withOpacity(.16),
                                      accent.withOpacity(.08),
                                      Colors.transparent,
                                    ],
                                  ),
                                ),
                              ),
                              Container(
                                width: 90,
                                height: 90,
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  gradient: LinearGradient(
                                    begin: Alignment.topLeft,
                                    end: Alignment.bottomRight,
                                    colors: [
                                      Colors.white,
                                      const Color(0xFFF1FBF4),
                                    ],
                                  ),
                                  border: Border.all(
                                    color: brand.withOpacity(.15),
                                  ),
                                  boxShadow: [
                                    BoxShadow(
                                      color: brand.withOpacity(.10),
                                      blurRadius: 18,
                                      offset: const Offset(0, 10),
                                    ),
                                  ],
                                ),
                                child: const Center(
                                  child: GdLottie(
                                    asset: GdLottieAssets.gifts,
                                    width: 76,
                                    height: 76,
                                  ),
                                ),
                              ),
                              Positioned(
                                bottom: 5,
                                child: Container(
                                  padding: const EdgeInsets.symmetric(
                                    horizontal: 10,
                                    vertical: 5,
                                  ),
                                  decoration: BoxDecoration(
                                    color: const Color(0xFF14351C),
                                    borderRadius: BorderRadius.circular(999),
                                  ),
                                  child: const Text(
                                    'VIP ACCESS',
                                    style: TextStyle(
                                      color: Colors.white,
                                      fontSize: 10,
                                      fontWeight: FontWeight.w800,
                                      letterSpacing: .6,
                                    ),
                                  ),
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 16),
                          Text(
                            'Welcome to GD Live',
                            textAlign: TextAlign.center,
                            style: TextStyle(
                              color: tokens.textPrimary,
                              fontSize: 24,
                              fontWeight: FontWeight.w800,
                              letterSpacing: -.7,
                            ),
                          ),
                          const SizedBox(height: 8),
                          Text(
                            '${widget.planName} has been activated for your account.',
                            textAlign: TextAlign.center,
                            style: TextStyle(
                              color: tokens.textSecondary,
                              fontSize: 14,
                              height: 1.4,
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                          const SizedBox(height: 20),
                          Container(
                            width: double.infinity,
                            padding: const EdgeInsets.fromLTRB(18, 18, 18, 16),
                            decoration: BoxDecoration(
                              gradient: const LinearGradient(
                                begin: Alignment.topLeft,
                                end: Alignment.bottomRight,
                                colors: [Colors.white, Color(0xFFF5FDF7)],
                              ),
                              borderRadius: BorderRadius.circular(26),
                              border: Border.all(color: brand.withOpacity(.12)),
                              boxShadow: [
                                BoxShadow(
                                  color: brand.withOpacity(.06),
                                  blurRadius: 18,
                                  offset: const Offset(0, 10),
                                ),
                              ],
                            ),
                            child: Column(
                              children: [
                                _RewardSeal(tint: brand),
                                const SizedBox(height: 14),
                                Text(
                                  widget.planName,
                                  textAlign: TextAlign.center,
                                  style: TextStyle(
                                    color: tokens.textPrimary,
                                    fontSize: 20,
                                    fontWeight: FontWeight.w800,
                                  ),
                                ),
                                const SizedBox(height: 6),
                                Text(
                                  'Premium access is ready to use.',
                                  textAlign: TextAlign.center,
                                  style: TextStyle(
                                    color: tokens.textSecondary,
                                    fontSize: 13,
                                    height: 1.45,
                                    fontWeight: FontWeight.w600,
                                  ),
                                ),
                                if (widget.endsText != null) ...[
                                  const SizedBox(height: 12),
                                  Container(
                                    padding: const EdgeInsets.symmetric(
                                      horizontal: 12,
                                      vertical: 8,
                                    ),
                                    decoration: BoxDecoration(
                                      color: const Color(0xFFF3FBF5),
                                      borderRadius: BorderRadius.circular(999),
                                    ),
                                    child: Text(
                                      widget.endsText!,
                                      style: TextStyle(
                                        color: tokens.textPrimary,
                                        fontSize: 12,
                                        fontWeight: FontWeight.w700,
                                      ),
                                    ),
                                  ),
                                ],
                              ],
                            ),
                          ),
                          const SizedBox(height: 16),
                          AnimatedScale(
                            scale: _pressing ? .985 : 1,
                            duration: const Duration(milliseconds: 100),
                            child: GestureDetector(
                              onTapDown:
                                  (_) => setState(() => _pressing = true),
                              onTapCancel:
                                  () => setState(() => _pressing = false),
                              onTapUp: (_) => setState(() => _pressing = false),
                              onTap: () {
                                HapticFeedback.selectionClick();
                                Get.back();
                              },
                              child: Container(
                                width: double.infinity,
                                padding: const EdgeInsets.symmetric(
                                  vertical: 16,
                                ),
                                decoration: BoxDecoration(
                                  borderRadius: BorderRadius.circular(20),
                                  gradient: LinearGradient(
                                    colors: tokens.primaryButtonGradient,
                                  ),
                                  boxShadow: [
                                    BoxShadow(
                                      color: tokens.primaryButtonGradient.first
                                          .withOpacity(.22),
                                      blurRadius: 16,
                                      offset: const Offset(0, 10),
                                    ),
                                  ],
                                ),
                                child: const Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    Text(
                                      'Continue',
                                      style: TextStyle(
                                        color: Colors.white,
                                        fontWeight: FontWeight.w800,
                                        fontSize: 15,
                                      ),
                                    ),
                                    SizedBox(width: 8),
                                    Icon(
                                      Icons.arrow_forward_rounded,
                                      color: Colors.white,
                                      size: 18,
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ),
                        ],
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

class _GlowOrb extends StatelessWidget {
  const _GlowOrb({required this.size, required this.color});

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
            colors: [color, color.withOpacity(.32), Colors.transparent],
          ),
        ),
      ),
    );
  }
}

class _PillTag extends StatelessWidget {
  const _PillTag({required this.icon, required this.label, required this.tint});

  final IconData icon;
  final String label;
  final Color tint;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
      decoration: BoxDecoration(
        color: tint.withOpacity(.10),
        borderRadius: BorderRadius.circular(999),
        border: Border.all(color: tint.withOpacity(.14)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, size: 14, color: tint),
          const SizedBox(width: 6),
          Text(
            label,
            style: TextStyle(
              color: tint,
              fontSize: 12,
              fontWeight: FontWeight.w800,
            ),
          ),
        ],
      ),
    );
  }
}

class _RewardSeal extends StatelessWidget {
  const _RewardSeal({required this.tint});

  final Color tint;

  @override
  Widget build(BuildContext context) {
    return Container(
      width: 74,
      height: 74,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [tint.withOpacity(.14), tint.withOpacity(.06)],
        ),
        border: Border.all(color: tint.withOpacity(.16)),
      ),
      child: Center(
        child: Container(
          width: 54,
          height: 54,
          decoration: BoxDecoration(
            shape: BoxShape.circle,
            color: Colors.white,
            boxShadow: [
              BoxShadow(
                color: tint.withOpacity(.10),
                blurRadius: 12,
                offset: const Offset(0, 6),
              ),
            ],
          ),
          child: const GdLottie(
            asset: GdLottieAssets.success,
            width: 42,
            height: 42,
          ),
        ),
      ),
    );
  }
}
