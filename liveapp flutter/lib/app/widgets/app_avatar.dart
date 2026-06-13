import 'package:flutter/material.dart';

class AppAvatar extends StatelessWidget {
  const AppAvatar({
    super.key,
    required this.size,
    required this.label,
    this.avatarUrl,
    this.textColor = Colors.white,
    this.backgroundColor = Colors.transparent,
    this.avatarInset = 0,
    this.borderRadius,
  });

  final double size;
  final String label;
  final String? avatarUrl;
  final Color textColor;
  final Color backgroundColor;
  final double avatarInset;
  final double? borderRadius;

  @override
  Widget build(BuildContext context) {
    final trimmedAvatar = avatarUrl?.trim();
    final hasAvatar = trimmedAvatar != null && trimmedAvatar.isNotEmpty;
    final initial = label.trim().isNotEmpty ? label.trim().characters.first.toUpperCase() : '?';

    return SizedBox(
      width: size,
      height: size,
      child: Padding(
        padding: EdgeInsets.all(size * avatarInset),
        child: DecoratedBox(
          decoration: BoxDecoration(
            color: backgroundColor,
            shape: BoxShape.circle,
            borderRadius: borderRadius == null ? null : BorderRadius.circular(borderRadius!),
          ),
          child: ClipOval(
            child: hasAvatar
                ? Image.network(
                    trimmedAvatar,
                    fit: BoxFit.cover,
                    errorBuilder: (_, __, ___) => _FallbackInitial(
                      initial: initial,
                      textColor: textColor,
                    ),
                  )
                : _FallbackInitial(
                    initial: initial,
                    textColor: textColor,
                  ),
          ),
        ),
      ),
    );
  }
}

class _FallbackInitial extends StatelessWidget {
  const _FallbackInitial({
    required this.initial,
    required this.textColor,
  });

  final String initial;
  final Color textColor;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Text(
        initial,
        style: TextStyle(
          color: textColor,
          fontWeight: FontWeight.w900,
          fontSize: 22,
        ),
      ),
    );
  }
}
