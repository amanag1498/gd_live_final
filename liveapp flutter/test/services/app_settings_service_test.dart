import 'package:flutter_test/flutter_test.dart';
import 'package:gd_live/services/app_settings_service.dart';

void main() {
  group('AppSettingsPayload parsing', () {
    test('reads maintenance and upgrade fields from app-config payload', () {
      final payload = AppSettingsPayload.fromJson({
        'maintenance_mode_enabled': true,
        'force_app_upgrade_enabled': true,
        'android_min_version_code': 61,
        'android_min_version_name': '1.0.0',
        'android_update_message': 'Update now',
        'features': const <String, dynamic>{'video_rooms_enabled': true},
      });

      expect(payload.maintenanceModeEnabled, isTrue);
      expect(payload.forceAppUpgradeEnabled, isTrue);
      expect(payload.androidMinVersionCode, 61);
      expect(payload.androidUpdateMessage, 'Update now');
      expect(payload.features.videoRoomsEnabled, isTrue);
    });

    test('falls back safely when optional config fields are missing', () {
      final payload = AppSettingsPayload.fromJson({
        'maintenance_mode_enabled': false,
        'force_app_upgrade_enabled': false,
        'features': const <String, dynamic>{},
      });

      expect(payload.maintenanceModeEnabled, isFalse);
      expect(payload.forceAppUpgradeEnabled, isFalse);
      expect(payload.androidMinVersionCode, 1);
      expect(payload.features.videoRoomsEnabled, isTrue);
    });
  });
}
