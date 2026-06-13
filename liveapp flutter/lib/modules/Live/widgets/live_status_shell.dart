import 'package:flutter/material.dart';

import '../../../app/brand/brand.dart';

class LiveStatusShell extends StatelessWidget {
  const LiveStatusShell({
    super.key,
    required this.child,
    required this.brandKey,
    required this.isHost,
    required this.isVip,
    required this.isSpeaking,
    this.isPkWinner = false,
    this.size,
    this.borderRadius = 20,
  });

  final Widget child;
  final String brandKey;
  final bool isHost;
  final bool isVip;
  final bool isSpeaking;
  final bool isPkWinner;
  final double? size;
  final double borderRadius;

  @override
  Widget build(BuildContext context) {
    final tokens = getBrandTokens(brandKey);
    final content =
        size == null
            ? child
            : SizedBox(width: size, height: size, child: child);

    return Stack(
      fit: StackFit.passthrough,
      clipBehavior: Clip.none,
      children: [
        AnimatedContainer(
          duration: const Duration(milliseconds: 180),
          curve: Curves.easeOutCubic,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(borderRadius),
            border:
                isSpeaking
                    ? Border.all(
                      color: tokens.glowColor.withOpacity(.28),
                      width: 1.5,
                    )
                    : null,
          ),
          child: content,
        ),
        if (isHost || isVip)
          Positioned(
            top: 6,
            right: 6,
            child: _StatusBadge(
              label: isHost ? 'HOST' : 'VIP',
              tokens: tokens,
              strong: isHost || isPkWinner,
            ),
          ),
      ],
    );
  }
}

class _StatusBadge extends StatelessWidget {
  const _StatusBadge({
    required this.label,
    required this.tokens,
    required this.strong,
  });

  final String label;
  final BrandTokens tokens;
  final bool strong;

  @override
  Widget build(BuildContext context) {
    final colors =
        strong
            ? tokens.primaryButtonGradient
            : [
              tokens.primaryButtonGradient.first.withOpacity(.92),
              tokens.primaryButtonGradient.last.withOpacity(.92),
            ];

    return DecoratedBox(
      decoration: BoxDecoration(
        gradient: LinearGradient(colors: colors),
        borderRadius: BorderRadius.circular(999),
        boxShadow: [
          BoxShadow(
            color: tokens.glowColor.withOpacity(strong ? .18 : .12),
            blurRadius: strong ? 10 : 6,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
        child: Text(
          label,
          style: TextStyle(
            color: tokens.textPrimary,
            fontSize: 9.5,
            fontWeight: FontWeight.w900,
            letterSpacing: .45,
          ),
        ),
      ),
    );
  }
}
