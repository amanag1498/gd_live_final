import 'package:flutter_test/flutter_test.dart';
import 'package:gd_live/modules/wallet/models/wallet_summary_dto.dart';

void main() {
  test('coin pack preserves Apple product mapping and localized price', () {
    final pack = WalletPackDto.fromJson({
      'id': 1,
      'title': 'Starter 500',
      'amount_rupees': 49,
      'coins': 450,
      'bonus_coins': 50,
      'total_coins': 500,
      'sort_order': 10,
      'apple_product_id': 'com.techybugs.gdlive.coins.500',
    }).copyWith(localizedStorePrice: '₹49.00');

    expect(pack.appleProductId, 'com.techybugs.gdlive.coins.500');
    expect(pack.localizedStorePrice, '₹49.00');
    expect(pack.totalCoins, 500);
  });
}
