// lib/modules/subscriptions/controllers/viewer_gate_controller.dart
import 'package:flutter/foundation.dart';
import 'package:get/get.dart';
import 'package:gd_live/services/api_client.dart';
import '../../wallet/widgets/recharge_bottom_sheet.dart';

import '../models/subscription_plan_dto.dart';
import '../services/subscriptions_api.dart';
import '../widgets/choose_plan_sheet.dart';

class ViewerGateController extends GetxController {
  late final SubscriptionsApi _api;
  final RxBool loading = false.obs;
  final RxList<SubscriptionPlanDto> plans = <SubscriptionPlanDto>[].obs;

  @override
  void onInit() {
    super.onInit();
    _api = SubscriptionsApi(Get.find<ApiClient>());
  }

  void _log(String msg) => debugPrint('[gate] $msg');

  Future<bool> hasActiveSubscription() async {
    try {
      final subs = await _api.mySubscriptions();
      return subs.any((s) => s.isActiveNow);
    } catch (e, st) {
      _log('hasActiveSubscription ERROR: $e');
      _log(st.toString());
      return false;
    }
  }

  Future<bool> promptSubscriptionPurchase({
    Future<void> Function()? onUnlocked,
  }) async {
    try {
      loading.value = true;

      final subs = await _api.mySubscriptions();
      final hasActive = subs.any((s) => s.isActiveNow);
      if (hasActive) {
        if (onUnlocked != null) {
          await onUnlocked();
        }
        return true;
      }

      final fetched = await _api.fetchPlans();
      final actives = fetched.where((p) => p.isActive).toList();
      if (actives.isEmpty) {
        Get.snackbar(
          'Subscriptions',
          'No active plans available right now.',
          snackPosition: SnackPosition.BOTTOM,
        );
        return false;
      }
      plans.assignAll(actives);

      final ctx = Get.context;
      if (ctx == null) {
        Get.snackbar(
          'Subscriptions',
          'Unable to open plans right now.',
          snackPosition: SnackPosition.BOTTOM,
        );
        return false;
      }

      final plan = await ChoosePlanSheet.show(
        ctx,
        plans: actives,
      );
      if (plan == null) {
        return false;
      }

      final sub = await _api.purchase(planId: plan.id);
      if (!sub.isActiveNow) {
        throw 'Subscription not active yet.';
      }

      Get.snackbar(
        'Unlocked',
        'You can now watch live streams!',
        snackPosition: SnackPosition.BOTTOM,
      );
      if (onUnlocked != null) {
        await onUnlocked();
      }
      return true;
    } catch (e, st) {
      _log('ERROR: $e');
      _log(st.toString());
      final message = e.toString().replaceFirst('Exception: ', '');
      if (isInsufficientCoinsErrorMessage(message)) {
        await showRechargeWalletSheet(
          reasonTitle: 'Not enough coins',
          reasonMessage:
              'You need more coins to buy a subscription. Recharge your wallet and try again.',
        );
      }
      Get.snackbar('Subscription', e.toString(),
          snackPosition: SnackPosition.BOTTOM);
      return false;
    } finally {
      loading.value = false;
    }
  }

  /// Call this when user taps a LIVE card.
  Future<void> ensureAccessThen({required Future<void> Function() onGranted}) async {
    _log('ensureAccessThen() start');
    await promptSubscriptionPurchase(onUnlocked: onGranted);
    _log('ensureAccessThen() end');
  }
}
