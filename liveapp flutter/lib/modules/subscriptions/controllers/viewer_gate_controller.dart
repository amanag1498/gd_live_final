// lib/modules/subscriptions/controllers/viewer_gate_controller.dart
import 'package:flutter/material.dart';
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

  void _showMessage(
    BuildContext context, {
    required String title,
    required String message,
  }) {
    if (!context.mounted) {
      _log('Skipping "$title" message because its page is no longer mounted.');
      return;
    }

    final messenger = ScaffoldMessenger.maybeOf(context);
    if (messenger == null) {
      _log(
        'Skipping "$title" message because no ScaffoldMessenger is available.',
      );
      return;
    }

    messenger.showSnackBar(
      SnackBar(
        content: Text('$title\n$message'),
        behavior: SnackBarBehavior.floating,
      ),
    );
  }

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
    required BuildContext context,
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
        _showMessage(
          context,
          title: 'Subscriptions',
          message: 'No active plans available right now.',
        );
        return false;
      }
      plans.assignAll(actives);

      if (!context.mounted) {
        return false;
      }

      final plan = await ChoosePlanSheet.show(context, plans: actives);
      if (plan == null) {
        return false;
      }

      final sub = await _api.purchase(planId: plan.id);
      if (!sub.isActiveNow) {
        throw 'Subscription not active yet.';
      }

      _showMessage(
        context,
        title: 'Unlocked',
        message: 'You can now watch live streams!',
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
      _showMessage(context, title: 'Subscription', message: message);
      return false;
    } finally {
      loading.value = false;
    }
  }

  /// Call this when user taps a LIVE card.
  Future<void> ensureAccessThen({
    required BuildContext context,
    required Future<void> Function() onGranted,
  }) async {
    _log('ensureAccessThen() start');
    await promptSubscriptionPurchase(context: context, onUnlocked: onGranted);
    _log('ensureAccessThen() end');
  }
}
