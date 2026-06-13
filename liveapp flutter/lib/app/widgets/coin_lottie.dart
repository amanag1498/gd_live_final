import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:lottie/lottie.dart';

final Map<String, Future<bool>> _bundledLottieExistsCache = {};

class GdLottieAssets {
  const GdLottieAssets._();

  static const String coin = 'assets/images/animations/coin.json';
  static const String rupeeBox =
      'assets/images/animations/animation_rupee_box.json';
  static const String gifts = 'assets/images/animations/gifts.json';
  static const String chat = 'assets/images/animations/chat.json';
  static const String cancelChat = 'assets/images/animations/cancel_chat.json';
  static const String addUser = 'assets/images/animations/add_user.json';
  static const String connect = 'assets/images/animations/connect.json';
  static const String connectInteract =
      'assets/images/animations/connect-interact.json';
  static const String startStream =
      'assets/images/animations/start-stream.json';
  static const String live = 'assets/images/animations/live.json';
  static const String publicRequest =
      'assets/images/animations/public_request.json';
  static const String privateRequest =
      'assets/images/animations/private_request.json';
  static const String success =
      'assets/images/animations/72462-check-register.json';
  static const String docer =
      'assets/images/animations/141594-animation-of-docer.json';
  static const String emptyFile =
      'assets/images/animations/53207-empty-file.json';
  static const String invite = 'assets/images/animations/invite.json';
  static const String heartBurst = 'assets/images/animations/heart_burst.json';
}

class GdLottie extends StatelessWidget {
  const GdLottie({
    super.key,
    required this.asset,
    this.width,
    this.height,
    this.fit = BoxFit.contain,
    this.repeat = true,
  });

  final String asset;
  final double? width;
  final double? height;
  final BoxFit fit;
  final bool repeat;

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<bool>(
      future: _bundledLottieExists(asset),
      builder: (context, snapshot) {
        if (snapshot.connectionState != ConnectionState.done) {
          return SizedBox(width: width, height: height);
        }

        if (snapshot.data != true) {
          if (kDebugMode) {
            return _MissingLottiePlaceholder(
              width: width,
              height: height,
            );
          }
          return SizedBox(width: width, height: height);
        }

        return Lottie.asset(
          asset,
          width: width,
          height: height,
          fit: fit,
          repeat: repeat,
          frameRate: FrameRate.max,
        );
      },
    );
  }
}

class _MissingLottiePlaceholder extends StatelessWidget {
  const _MissingLottiePlaceholder({
    this.width,
    this.height,
  });

  final double? width;
  final double? height;

  @override
  Widget build(BuildContext context) {
    final resolvedWidth = width ?? 48;
    final resolvedHeight = height ?? 48;
    return Container(
      width: resolvedWidth,
      height: resolvedHeight,
      decoration: BoxDecoration(
        color: const Color(0x14000000),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: const Color(0x26FF6B6B)),
      ),
      child: const Icon(
        Icons.animation_rounded,
        size: 22,
        color: Color(0xFFFF6B6B),
      ),
    );
  }
}

class CoinLottie extends StatelessWidget {
  const CoinLottie({super.key, this.size = 24, this.fit = BoxFit.contain});

  final double size;
  final BoxFit fit;

  static const String asset = 'assets/images/animations/coin.json';

  @override
  Widget build(BuildContext context) {
    return SizedBox(
      width: size,
      height: size,
      child: GdLottie(asset: asset, width: size, height: size, fit: fit),
    );
  }
}

Future<bool> _bundledLottieExists(String asset) {
  return _bundledLottieExistsCache.putIfAbsent(asset, () async {
    try {
      await rootBundle.load(asset);
      return true;
    } catch (error, stackTrace) {
      if (kDebugMode) {
        debugPrint('Missing Lottie asset: $asset');
        debugPrintStack(
          label: 'Lottie asset load failed',
          stackTrace: stackTrace,
        );
      }
      return false;
    }
  });
}
