import 'package:flutter/services.dart';
import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:flutter_svga/flutter_svga.dart';

import '../models/remote_media_kind.dart';

final Map<String, Future<bool>> _bundledAssetExistsCache = {};

class RemoteMediaArt extends StatelessWidget {
  const RemoteMediaArt({
    super.key,
    required this.url,
    this.explicitType,
    required this.width,
    required this.height,
    this.fit = BoxFit.contain,
    this.fallback,
    this.borderRadius,
    this.enableAudio = true,
  });

  final String? url;
  final String? explicitType;
  final double width;
  final double height;
  final BoxFit fit;
  final Widget? fallback;
  final BorderRadius? borderRadius;
  final bool enableAudio;

  @override
  Widget build(BuildContext context) {
    final value = url?.trim();
    final placeholder = fallback ?? const SizedBox.shrink();
    if (value == null || value.isEmpty) {
      return _clip(placeholder);
    }

    final kind = detectRemoteMediaKind(explicitType: explicitType, url: value);
    final isBundled = isBundledMediaPath(value);

    if (isBundled) {
      return FutureBuilder<bool>(
        future: _bundledAssetExists(value),
        builder: (context, snapshot) {
          if (snapshot.connectionState != ConnectionState.done) {
            return _clip(placeholder);
          }
          if (snapshot.data != true) {
            return _clip(placeholder);
          }
          return _buildMedia(
            context,
            value: value,
            kind: kind,
            isBundled: true,
            placeholder: placeholder,
          );
        },
      );
    }

    return _buildMedia(
      context,
      value: value,
      kind: kind,
      isBundled: false,
      placeholder: placeholder,
    );
  }

  Widget _buildMedia(
    BuildContext context, {
    required String value,
    required RemoteMediaKind kind,
    required bool isBundled,
    required Widget placeholder,
  }) {
    switch (kind) {
      case RemoteMediaKind.svga:
        return _clip(
          SizedBox(
            width: width,
            height: height,
            child: isBundled
                ? SVGAEasyPlayer(
                    assetsName: value,
                    fit: fit,
                    enableAudio: enableAudio,
                  )
                : SVGAEasyPlayer(
                    resUrl: value,
                    fit: fit,
                    enableAudio: enableAudio,
                  ),
          ),
        );
      case RemoteMediaKind.svg:
        return _clip(
          isBundled
              ? SvgPicture.asset(
                  value,
                  width: width,
                  height: height,
                  fit: fit,
                  placeholderBuilder: (_) => placeholder,
                )
              : SvgPicture.network(
                  value,
                  width: width,
                  height: height,
                  fit: fit,
                  placeholderBuilder: (_) => placeholder,
                ),
        );
      case RemoteMediaKind.gif:
      case RemoteMediaKind.image:
      case RemoteMediaKind.unknown:
        return _clip(
          isBundled
              ? Image.asset(
                  value,
                  width: width,
                  height: height,
                  fit: fit,
                  errorBuilder: (_, __, ___) => placeholder,
                )
              : Image.network(
                  value,
                  width: width,
                  height: height,
                  fit: fit,
                  errorBuilder: (_, __, ___) => placeholder,
                  loadingBuilder: (context, child, progress) {
                    if (progress == null) return child;
                    return placeholder;
                  },
                ),
        );
    }
  }

  Future<bool> _bundledAssetExists(String value) async {
    return _bundledAssetExistsCache.putIfAbsent(value, () async {
      try {
        await rootBundle.load(value);
        return true;
      } catch (_) {
        return false;
      }
    });
  }

  Widget _clip(Widget child) {
    if (borderRadius == null) return child;
    return ClipRRect(borderRadius: borderRadius!, child: child);
  }
}
