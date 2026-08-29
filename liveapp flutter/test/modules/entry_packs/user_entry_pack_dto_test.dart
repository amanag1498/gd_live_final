import 'package:flutter_test/flutter_test.dart';
import 'package:gd_live/modules/entry_packs/models/user_entry_pack_dto.dart';

void main() {
  test('prefers a valid grant over a newer expired ownership row', () {
    final now = DateTime.utc(2026, 8, 29, 12);
    final validGrant = UserEntryPackDto(
      id: 52,
      userId: 4,
      entryPackId: 6,
      isActive: false,
      purchasedAt: now.subtract(const Duration(hours: 2)),
      expiresAt: now.add(const Duration(days: 1)),
    );
    final newerExpired = UserEntryPackDto(
      id: 55,
      userId: 4,
      entryPackId: 6,
      isActive: false,
      purchasedAt: now.subtract(const Duration(hours: 1)),
      expiresAt: now.subtract(const Duration(minutes: 1)),
    );

    final selected = preferredOwnedEntryPack(
      [newerExpired, validGrant],
      6,
      now: now,
    );

    expect(selected?.id, 52);
    expect(selected?.isExpiredAt(now), isFalse);
  });

  test('prefers the longest valid expiry for duplicate pack rows', () {
    final now = DateTime.utc(2026, 8, 29, 12);
    final oneDay = UserEntryPackDto(
      id: 52,
      userId: 4,
      entryPackId: 6,
      isActive: false,
      expiresAt: now.add(const Duration(days: 1)),
    );
    final threeDays = UserEntryPackDto(
      id: 51,
      userId: 4,
      entryPackId: 6,
      isActive: false,
      expiresAt: now.add(const Duration(days: 3)),
    );

    expect(preferredOwnedEntryPack([oneDay, threeDays], 6, now: now)?.id, 51);
  });
}
