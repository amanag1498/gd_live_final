import 'package:flutter/foundation.dart';
import 'package:flutter_test/flutter_test.dart';
import 'package:gd_live/app/routes/app_urls.dart';

void main() {
  tearDown(() {
    debugDefaultTargetPlatformOverride = null;
  });

  test('uses secure production sockets on iOS', () {
    debugDefaultTargetPlatformOverride = TargetPlatform.iOS;

    expect(AppUrls.socketOrigin, 'wss://ws.gdlive.in:443');
    expect(AppUrls.wsCalls, 'wss://ws.gdlive.in:443/calls');
  });

  test('preserves the existing Android socket endpoint', () {
    debugDefaultTargetPlatformOverride = TargetPlatform.android;

    expect(AppUrls.socketOrigin, 'ws://31.97.233.109:4001');
    expect(AppUrls.wsRooms, 'ws://31.97.233.109:4001/rooms');
  });
}
