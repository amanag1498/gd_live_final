import 'package:flutter/material.dart';

import '../brand/brand.dart';

class GdModalSurface extends StatelessWidget {
  const GdModalSurface({
    super.key,
    required this.tokens,
    required this.child,
    this.maxWidth = 460,
    this.maxHeightFactor = 0.92,
    this.radius = 28,
    this.padding = const EdgeInsets.fromLTRB(20, 16, 20, 18),
    this.showHandle = true,
    this.scrollable = false,
  });

  final BrandTokens tokens;
  final Widget child;
  final double maxWidth;
  final double maxHeightFactor;
  final double radius;
  final EdgeInsetsGeometry padding;
  final bool showHandle;
  final bool scrollable;

  @override
  Widget build(BuildContext context) {
    final maxHeight = MediaQuery.sizeOf(context).height * maxHeightFactor;
    return ConstrainedBox(
      constraints: BoxConstraints(maxWidth: maxWidth, maxHeight: maxHeight),
      child:
          scrollable
              ? ClipRRect(
                borderRadius: BorderRadius.circular(radius),
                child: DecoratedBox(
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.topCenter,
                      end: Alignment.bottomCenter,
                      colors: [
                        Colors.white.withOpacity(.98),
                        const Color(0xFFF3FBF4).withOpacity(.98),
                      ],
                    ),
                    border: Border.all(color: tokens.borderColor.withOpacity(.55)),
                    boxShadow: [
                      BoxShadow(
                        color: tokens.primaryButtonGradient.first.withOpacity(.08),
                        blurRadius: 28,
                        offset: const Offset(0, 14),
                      ),
                    ],
                  ),
                  child: Padding(
                    padding: padding,
                    child: SingleChildScrollView(
                      physics: const BouncingScrollPhysics(),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          if (showHandle) ...[
                            Container(
                              width: 42,
                              height: 4,
                              decoration: BoxDecoration(
                                color: tokens.borderColor.withOpacity(.62),
                                borderRadius: BorderRadius.circular(999),
                              ),
                            ),
                            const SizedBox(height: 14),
                          ],
                          child,
                        ],
                      ),
                    ),
                  ),
                ),
              )
              : Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                    colors: [
                      Colors.white.withOpacity(.98),
                      const Color(0xFFF3FBF4).withOpacity(.98),
                    ],
                  ),
                  borderRadius: BorderRadius.circular(radius),
                  border: Border.all(color: tokens.borderColor.withOpacity(.55)),
                  boxShadow: [
                    BoxShadow(
                      color: tokens.primaryButtonGradient.first.withOpacity(.08),
                      blurRadius: 28,
                      offset: const Offset(0, 14),
                    ),
                  ],
                ),
                child: Padding(
                  padding: padding,
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      if (showHandle) ...[
                        Container(
                          width: 42,
                          height: 4,
                          decoration: BoxDecoration(
                            color: tokens.borderColor.withOpacity(.62),
                            borderRadius: BorderRadius.circular(999),
                          ),
                        ),
                        const SizedBox(height: 14),
                      ],
                      child,
                    ],
                  ),
                ),
              ),
    );
  }
}
