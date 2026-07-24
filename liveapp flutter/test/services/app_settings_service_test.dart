import 'package:flutter/foundation.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:gd_live/services/app_settings_service.dart';

void main() {
  tearDown(() {
    debugDefaultTargetPlatformOverride = null;
  });

  group('AppSettingsPayload parsing', () {
    test('reads selected-platform fields from app-config payload', () {
      final payload = AppSettingsPayload.fromJson({
        'maintenance_mode_enabled': true,
        'force_app_upgrade_enabled': true,
        'minimum_version_code': 63,
        'minimum_version_name': '1.1.0',
        'update_message': 'Update from the store',
        'features': const <String, dynamic>{'video_rooms_enabled': true},
      });

      expect(payload.maintenanceModeEnabled, isTrue);
      expect(payload.forceAppUpgradeEnabled, isTrue);
      expect(payload.minimumVersionCode, 63);
      expect(payload.minimumVersionName, '1.1.0');
      expect(payload.updateMessage, 'Update from the store');
      expect(payload.features.videoRoomsEnabled, isTrue);
    });

    test('keeps compatibility with the legacy Android payload', () {
      final payload = AppSettingsPayload.fromJson({
        'android_min_version_code': 61,
        'android_min_version_name': '1.0.0',
        'android_update_message': 'Update now',
        'features': const <String, dynamic>{},
      });

      expect(payload.minimumVersionCode, 61);
      expect(payload.minimumVersionName, '1.0.0');
      expect(payload.updateMessage, 'Update now');
    });

    test('falls back safely when optional config fields are missing', () {
      final payload = AppSettingsPayload.fromJson({
        'maintenance_mode_enabled': false,
        'force_app_upgrade_enabled': false,
        'features': const <String, dynamic>{},
      });

      expect(payload.maintenanceModeEnabled, isFalse);
      expect(payload.forceAppUpgradeEnabled, isFalse);
      expect(payload.minimumVersionCode, 1);
      expect(payload.features.videoRoomsEnabled, isTrue);
    });

    test('allows iOS recharge when the flag is missing', () {
      debugDefaultTargetPlatformOverride = TargetPlatform.iOS;

      final payload = AppSettingsPayload.fromJson({
        'features': const <String, dynamic>{},
      });

      expect(payload.features.walletRechargeEnabled, isTrue);
    });

    test('preserves the Android recharge fallback', () {
      debugDefaultTargetPlatformOverride = TargetPlatform.android;

      final payload = AppSettingsPayload.fromJson({
        'features': const <String, dynamic>{},
      });

      expect(payload.features.walletRechargeEnabled, isTrue);
    });
  });
}
