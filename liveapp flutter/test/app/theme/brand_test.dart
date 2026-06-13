import 'package:flutter_test/flutter_test.dart';
import 'package:gd_live/app/brand/brand.dart';

void main() {
  group('brand tokens', () {
    test('returns the GD Live token set', () {
      final tokens = getBrandTokens('midnight');
      expect(tokens.backgroundGradient, isNotEmpty);
      expect(tokens.cardGradient, isNotEmpty);
      expect(tokens.primaryButtonGradient, isNotEmpty);
    });

    test('normalizes any input to the single GD Live key', () {
      expect(normalizeBrandVariant('neon'), 'midnight');
      expect(normalizeBrandVariant(''), 'midnight');
    });

    test('falls back to the GD Live token set for any unknown key', () {
      final invalid = getBrandTokens('neon');
      final midnight = getBrandTokens('midnight');

      expect(invalid.backgroundGradient, midnight.backgroundGradient);
      expect(invalid.cardGradient, midnight.cardGradient);
      expect(invalid.primaryButtonGradient, midnight.primaryButtonGradient);
    });
  });
}
