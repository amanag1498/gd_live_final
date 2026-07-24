import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../app/brand/brand.dart';
import '../../../app/widgets/gd_live_logo.dart';
import '../../../services/app_settings_service.dart';
import '../controllers/auth_controller.dart';

class LoginView extends GetView<AuthController> {
  const LoginView({super.key});

  @override
  Widget build(BuildContext context) {
    return const _GdLoginView();
  }
}

class _GdLoginView extends GetView<AuthController> {
  const _GdLoginView();

  @override
  Widget build(BuildContext context) {
    final mq = MediaQuery.of(context);
    final brandKey = Get.find<AppSettingsService>().brandKey;
    final tokens = getBrandTokens(brandKey);

    return Scaffold(
      backgroundColor: tokens.backgroundGradient.first,
      body: Container(
        decoration: BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [
              const Color(0xFFFFFFFF),
              const Color(0xFFF6FCF6),
              const Color(0xFFEAF8EC),
              const Color(0xFFD6F2DB),
            ],
          ),
        ),
        child: SafeArea(
          child: LayoutBuilder(
            builder: (context, constraints) {
              final isWide = constraints.maxWidth >= 760;
              final horizontal = isWide ? 36.0 : 22.0;
              final collageHeight =
                  (isWide ? 320.0 : constraints.maxWidth * .72)
                      .clamp(240.0, 340.0)
                      .toDouble();

              return Stack(
                children: [
                  Positioned(
                    top: -70,
                    right: -30,
                    child: _SoftGlow(
                      size: 200,
                      color: tokens.primaryButtonGradient.last.withOpacity(.12),
                    ),
                  ),
                  Positioned(
                    left: -50,
                    bottom: 120,
                    child: _SoftGlow(
                      size: 160,
                      color: tokens.primaryButtonGradient.first.withOpacity(
                        .08,
                      ),
                    ),
                  ),
                  SingleChildScrollView(
                    padding: EdgeInsets.fromLTRB(
                      horizontal,
                      isWide ? 26 : 18,
                      horizontal,
                      24 + mq.viewInsets.bottom,
                    ),
                    child: ConstrainedBox(
                      constraints: BoxConstraints(
                        minHeight: constraints.maxHeight - 18,
                      ),
                      child: Center(
                        child: ConstrainedBox(
                          constraints: const BoxConstraints(maxWidth: 620),
                          child: Obx(() {
                            final loading = controller.loading.value;
                            final error = controller.error.value.trim();

                            return Column(
                              crossAxisAlignment: CrossAxisAlignment.center,
                              children: [
                                const SizedBox(height: 8),
                                const GdLiveLogo(size: 58, showWordmark: false),
                                const SizedBox(height: 18),
                                Text(
                                  'GD Live',
                                  style: TextStyle(
                                    color: tokens.textPrimary,
                                    fontSize: isWide ? 30 : 26,
                                    fontWeight: FontWeight.w700,
                                    letterSpacing: -.4,
                                  ),
                                ),
                                const SizedBox(height: 8),
                                Text(
                                  'Live, meet, and connect.',
                                  style: TextStyle(
                                    color: tokens.textSecondary,
                                    fontSize: 15,
                                    fontWeight: FontWeight.w500,
                                  ),
                                ),
                                const SizedBox(height: 28),
                                _EditorialCollage(
                                  height: collageHeight,
                                  tokens: tokens,
                                ),
                                const SizedBox(height: 26),
                                _SignInDock(
                                  tokens: tokens,
                                  loading: loading,
                                  error: error,
                                  onGooglePressed: controller.loginWithGoogle,
                                  onApplePressed:
                                      !kIsWeb &&
                                              defaultTargetPlatform ==
                                                  TargetPlatform.iOS
                                          ? controller.loginWithApple
                                          : null,
                                ),
                              ],
                            );
                          }),
                        ),
                      ),
                    ),
                  ),
                ],
              );
            },
          ),
        ),
      ),
    );
  }
}

class _SignInDock extends StatelessWidget {
  const _SignInDock({
    required this.tokens,
    required this.loading,
    required this.error,
    required this.onGooglePressed,
    this.onApplePressed,
  });

  final BrandTokens tokens;
  final bool loading;
  final String error;
  final VoidCallback onGooglePressed;
  final VoidCallback? onApplePressed;

  @override
  Widget build(BuildContext context) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: [
        if (error.isNotEmpty) ...[
          _ErrorBanner(
            message: error,
            textColor: tokens.textPrimary,
            borderColor: tokens.dangerColor.withOpacity(.22),
            backgroundColor: tokens.dangerColor.withOpacity(.08),
          ),
          const SizedBox(height: 16),
        ],
        _AuthButton(
          label: 'Sign in with Google',
          loading: loading,
          onPressed: onGooglePressed,
          tokens: tokens,
          leading: Image.asset('assets/logos/google-icon.png'),
        ),
        if (onApplePressed != null) ...[
          const SizedBox(height: 12),
          _AuthButton(
            label: 'Sign in with Apple',
            loading: loading,
            onPressed: onApplePressed!,
            tokens: tokens,
            dark: true,
            leading: const Icon(Icons.apple, color: Colors.white, size: 25),
          ),
        ],
      ],
    );
  }
}

class _AuthButton extends StatelessWidget {
  const _AuthButton({
    required this.label,
    required this.loading,
    required this.onPressed,
    required this.tokens,
    required this.leading,
    this.dark = false,
  });

  final String label;
  final bool loading;
  final VoidCallback onPressed;
  final BrandTokens tokens;
  final Widget leading;
  final bool dark;

  @override
  Widget build(BuildContext context) {
    return Container(
      height: 58,
      decoration: BoxDecoration(
        color: dark ? Colors.black : Colors.white.withOpacity(.86),
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: tokens.borderColor.withOpacity(.34)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(.03),
            blurRadius: 16,
            offset: const Offset(0, 8),
          ),
        ],
      ),
      child: TextButton(
        onPressed: loading ? null : onPressed,
        style: TextButton.styleFrom(
          backgroundColor: Colors.transparent,
          foregroundColor: dark ? Colors.white : tokens.textPrimary,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(18),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 18),
        ),
        child:
            loading
                ? SizedBox(
                  width: 24,
                  height: 24,
                  child: CircularProgressIndicator(
                    strokeWidth: 2.4,
                    valueColor: AlwaysStoppedAnimation<Color>(
                      tokens.primaryButtonGradient.first,
                    ),
                  ),
                )
                : Row(
                  children: [
                    SizedBox(
                      width: 32,
                      height: 32,
                      child: Center(child: leading),
                    ),
                    const SizedBox(width: 14),
                    Expanded(
                      child: Text(
                        label,
                        style: TextStyle(
                          color: dark ? Colors.white : tokens.textPrimary,
                          fontSize: 15,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                    Icon(
                      Icons.chevron_right_rounded,
                      size: 20,
                      color:
                          dark
                              ? Colors.white.withOpacity(.7)
                              : tokens.textSecondary,
                    ),
                  ],
                ),
      ),
    );
  }
}

class _ErrorBanner extends StatelessWidget {
  const _ErrorBanner({
    required this.message,
    required this.textColor,
    required this.borderColor,
    required this.backgroundColor,
  });

  final String message;
  final Color textColor;
  final Color borderColor;
  final Color backgroundColor;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        color: backgroundColor,
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: borderColor),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(Icons.error_outline, size: 18, color: textColor),
          const SizedBox(width: 10),
          Expanded(
            child: Text(
              message,
              style: TextStyle(
                color: textColor,
                fontSize: 13.5,
                fontWeight: FontWeight.w600,
                height: 1.4,
              ),
            ),
          ),
        ],
      ),
    );
  }
}

class _EditorialCollage extends StatelessWidget {
  const _EditorialCollage({required this.height, required this.tokens});

  final double height;
  final BrandTokens tokens;

  @override
  Widget build(BuildContext context) {
    final imageWidth = height * .46;
    final imageHeight = height * .72;
    final stripHeight = height * .72;
    final sideStripWidth = (imageWidth * .075).clamp(8.0, 14.0).toDouble();

    return SizedBox(
      height: height,
      child: Stack(
        alignment: Alignment.bottomCenter,
        children: [
          _HeroCard(
            assetPath: 'assets/images/img_rectangle_3799.png',
            width: imageWidth,
            height: imageHeight,
            alignment: Alignment.topLeft,
            margin: const EdgeInsets.only(left: 8, top: 8),
          ),
          _HeroCard(
            assetPath: 'assets/images/img_rectangle_3800.png',
            width: imageWidth,
            height: imageHeight,
            alignment: Alignment.bottomCenter,
            margin: const EdgeInsets.only(bottom: 8),
          ),
          _HeroCard(
            assetPath: 'assets/images/img_rectangle_3803.png',
            width: imageWidth,
            height: height * .27,
            alignment: Alignment.topCenter,
          ),
          _HeroCard(
            assetPath: 'assets/images/img_rectangle_3802.png',
            width: imageWidth,
            height: imageHeight,
            alignment: Alignment.topRight,
            margin: const EdgeInsets.only(top: 22, right: 8),
          ),
          _HeroCard(
            assetPath: 'assets/images/img_rectangle_3801.png',
            width: sideStripWidth,
            height: stripHeight,
            alignment: Alignment.bottomRight,
          ),
          _HeroCard(
            assetPath: 'assets/images/img_rectangle_3799_158x8.png',
            width: sideStripWidth,
            height: stripHeight,
            alignment: Alignment.topLeft,
            margin: const EdgeInsets.only(top: 20),
          ),
          IgnorePointer(
            child: DecoratedBox(
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(22),
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [
                    Colors.white.withOpacity(0),
                    tokens.primaryButtonGradient.first.withOpacity(.04),
                    tokens.primaryButtonGradient.last.withOpacity(.18),
                  ],
                ),
              ),
              child: const SizedBox.expand(),
            ),
          ),
        ],
      ),
    );
  }
}

class _HeroCard extends StatelessWidget {
  const _HeroCard({
    required this.assetPath,
    required this.width,
    required this.height,
    required this.alignment,
    this.margin = EdgeInsets.zero,
  });

  final String assetPath;
  final double width;
  final double height;
  final Alignment alignment;
  final EdgeInsets margin;

  @override
  Widget build(BuildContext context) {
    return Align(
      alignment: alignment,
      child: Container(
        width: width,
        height: height,
        margin: margin,
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(width <= 14 ? 6 : 16),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(.08),
              blurRadius: 14,
              offset: const Offset(0, 8),
            ),
          ],
          image: DecorationImage(
            image: AssetImage(assetPath),
            fit: BoxFit.cover,
          ),
        ),
      ),
    );
  }
}

class _SoftGlow extends StatelessWidget {
  const _SoftGlow({required this.size, required this.color});

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
            colors: [color, color.withOpacity(.16), Colors.transparent],
          ),
        ),
      ),
    );
  }
}
