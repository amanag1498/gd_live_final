import 'package:flutter/services.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:gd_live/services/device_id_service.dart';

void main() {
  TestWidgetsFlutterBinding.ensureInitialized();

  const channel = MethodChannel('com.gdlive/device');

  tearDown(() {
    TestDefaultBinaryMessengerBinding.instance.defaultBinaryMessenger
        .setMockMethodCallHandler(channel, null);
  });

  test('reads and trims the native persistent device identifier', () async {
    TestDefaultBinaryMessengerBinding.instance.defaultBinaryMessenger
        .setMockMethodCallHandler(channel, (call) async {
          expect(call.method, 'getDeviceId');
          return ' ios:1234 ';
        });

    expect(await DeviceIdService.getDeviceId(), 'ios:1234');
  });

  test('returns an empty identifier when the native bridge fails', () async {
    TestDefaultBinaryMessengerBinding.instance.defaultBinaryMessenger
        .setMockMethodCallHandler(channel, (_) async {
          throw PlatformException(code: 'DEVICE_ID_ERROR');
        });

    expect(await DeviceIdService.getDeviceId(), isEmpty);
  });
}
