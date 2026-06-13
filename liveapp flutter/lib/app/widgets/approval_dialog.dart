import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import '../../../app/widgets/gd_live_logo.dart';
import '../../../app/widgets/gd_modal_surface.dart';
import '../../../services/app_settings_service.dart';
import '../../../app/brand/brand.dart';

class ApprovalDialog extends StatefulWidget {
  const ApprovalDialog({
    super.key,
    required this.title,
    required this.message,
    this.ctaText = 'OK',
  });

  final String title;
  final String message;
  final String ctaText;

  @override
  State<ApprovalDialog> createState() => _ApprovalDialogState();
}

class _ApprovalDialogState extends State<ApprovalDialog>
    with TickerProviderStateMixin {
  late final AnimationController _pop;

  @override
  void initState() {
    super.initState();
    HapticFeedback.lightImpact();
    _pop = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 220),
    )..forward();
  }

  @override
  void dispose() {
    _pop.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final curve = CurvedAnimation(parent: _pop, curve: Curves.easeOutBack);
    final tokens = getBrandTokens(Get.find<AppSettingsService>().brandKey);

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
                const GdLiveLogo(size: 54, showWordmark: false),
                const SizedBox(height: 12),
                Text(
                  widget.title,
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
                  widget.message,
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: tokens.textSecondary,
                    fontWeight: FontWeight.w600,
                    height: 1.35,
                  ),
                ),
                const SizedBox(height: 18),
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    style: ElevatedButton.styleFrom(
                      backgroundColor: tokens.primaryButtonGradient.first,
                      foregroundColor: Colors.white,
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(16),
                      ),
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      elevation: 0,
                    ),
                    onPressed: () => Get.back(),
                    child: Text(
                      widget.ctaText,
                      style: const TextStyle(fontWeight: FontWeight.w900),
                    ),
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
