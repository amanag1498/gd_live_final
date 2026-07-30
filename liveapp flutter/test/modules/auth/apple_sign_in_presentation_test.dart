import 'package:flutter/foundation.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:gd_live/modules/auth/controllers/auth_controller.dart';
import 'package:gd_live/modules/auth/views/login_view.dart';

void main() {
  test('offers Apple sign-in only for native iOS', () {
    expect(
      shouldOfferAppleSignIn(isWeb: false, platform: TargetPlatform.iOS),
      isTrue,
    );
    expect(
      shouldOfferAppleSignIn(isWeb: false, platform: TargetPlatform.android),
      isFalse,
    );
    expect(
      shouldOfferAppleSignIn(isWeb: true, platform: TargetPlatform.iOS),
      isFalse,
    );
  });

  test('removes implementation prefixes from authentication errors', () {
    expect(
      friendlyAuthError(Exception('Apple sign-in is not configured.')),
      'Apple sign-in is not configured.',
    );
  });
}
