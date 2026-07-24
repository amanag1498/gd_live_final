import 'package:flutter_test/flutter_test.dart';
import 'package:gd_live/services/auth_service.dart';

void main() {
  test('detects stale Google credentials that can be refreshed', () {
    expect(
      isStaleGoogleCredential(
        'invalid-credential',
        'ID Token issued at 1784898206 is stale to sign-in.',
      ),
      isTrue,
    );
    expect(
      isStaleGoogleCredential(
        'invalid-credential',
        'The supplied auth credential is malformed or has expired.',
      ),
      isTrue,
    );
  });

  test('does not retry unrelated Firebase authentication failures', () {
    expect(
      isStaleGoogleCredential('operation-not-allowed', 'Provider disabled'),
      isFalse,
    );
    expect(
      isStaleGoogleCredential('invalid-credential', 'Wrong OAuth audience'),
      isFalse,
    );
  });
}
