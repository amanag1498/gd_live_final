// This is a basic Flutter widget test.
//
// To perform an interaction with a widget in your test, use the WidgetTester
// utility in the flutter_test package. For example, you can send tap and scroll
// gestures. You can also use WidgetTester to find child widgets in the widget
// tree, read text, and verify that the values of widget properties are correct.

import 'package:flutter/foundation.dart';
import 'package:flutter_test/flutter_test.dart';

import 'package:gd_live/main.dart';

void main() {
  tearDown(() {
    debugDefaultTargetPlatformOverride = null;
  });

  testWidgets('App boots to login route', (WidgetTester tester) async {
    await tester.pumpWidget(
      const MyApp(apiBase: 'http://localhost:8000/api', initialRoute: '/login'),
    );

    await tester.pump(const Duration(milliseconds: 300));
    expect(find.text('GD Live'), findsOneWidget);
    expect(find.text('Sign in with Google'), findsOneWidget);
  });

  testWidgets('iOS login offers Apple and Google sign-in', (
    WidgetTester tester,
  ) async {
    debugDefaultTargetPlatformOverride = TargetPlatform.iOS;

    await tester.pumpWidget(
      const MyApp(apiBase: 'http://localhost:8000/api', initialRoute: '/login'),
    );

    await tester.pump(const Duration(milliseconds: 300));
    expect(find.text('Sign in with Apple'), findsOneWidget);
    expect(find.text('Sign in with Google'), findsOneWidget);
    debugDefaultTargetPlatformOverride = null;
  });
}
