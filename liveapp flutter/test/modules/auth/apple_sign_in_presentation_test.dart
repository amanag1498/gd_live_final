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

  test('demo logo tracker opens only after three quick taps', () {
    final tracker = DemoLogoTapTracker();
    final start = DateTime(2026, 9, 3, 10);

    expect(tracker.register(start), isFalse);
    expect(
      tracker.register(start.add(const Duration(milliseconds: 300))),
      isFalse,
    );
    expect(
      tracker.register(start.add(const Duration(milliseconds: 600))),
      isTrue,
    );
    expect(
      tracker.register(start.add(const Duration(milliseconds: 700))),
      isFalse,
    );
  });

  test('demo logo tracker resets taps outside the time window', () {
    final tracker = DemoLogoTapTracker();
    final start = DateTime(2026, 9, 3, 10);

    expect(tracker.register(start), isFalse);
    expect(
      tracker.register(start.add(const Duration(milliseconds: 1300))),
      isFalse,
    );
    expect(
      tracker.register(start.add(const Duration(milliseconds: 1500))),
      isFalse,
    );
  });
}
