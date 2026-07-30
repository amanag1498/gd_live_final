import 'package:flutter_test/flutter_test.dart';
import 'package:gd_live/modules/wallet/services/recharge_payment_platform.dart';

void main() {
  test('iOS uses Apple In-App Purchase', () {
    expect(
      rechargePaymentProviderFor('ios'),
      RechargePaymentProvider.appleInAppPurchase,
    );
  });

  test('Android keeps Razorpay', () {
    expect(
      rechargePaymentProviderFor('android'),
      RechargePaymentProvider.razorpay,
    );
  });

  test('unknown non-iOS platforms never enter the Apple purchase flow', () {
    expect(rechargePaymentProviderFor('web'), RechargePaymentProvider.razorpay);
  });
}
