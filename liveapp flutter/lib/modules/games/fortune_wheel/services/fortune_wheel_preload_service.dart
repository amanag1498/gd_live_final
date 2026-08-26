import 'dart:async';

import 'package:flutter/widgets.dart';
import 'package:get/get.dart';

import '../../../../services/app_settings_service.dart';
import '../models/fortune_wheel_models.dart';
import 'fortune_wheel_api.dart';

class FortuneWheelPreloadService extends GetxService
    with WidgetsBindingObserver {
  FortuneWheelPreloadService({
    required FortuneWheelApi api,
    required AppSettingsService settings,
  }) : _api = api,
       _settings = settings;

  final FortuneWheelApi _api;
  final AppSettingsService _settings;

  final RxBool loading = false.obs;
  final RxnString error = RxnString();
  final Rxn<FortuneWheelSnapshot> snapshot = Rxn<FortuneWheelSnapshot>();

  Worker? _settingsWorker;

  @override
  void onInit() {
    super.onInit();
    WidgetsBinding.instance.addObserver(this);
    _settingsWorker = ever<AppSettingsPayload?>(_settings.payload, (_) {
      unawaited(maybePreload(reason: 'settings_changed'));
    });
    unawaited(maybePreload(reason: 'startup'));
  }

  @override
  void onClose() {
    WidgetsBinding.instance.removeObserver(this);
    _settingsWorker?.dispose();
    super.onClose();
  }

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      unawaited(maybePreload(reason: 'app_resumed'));
    }
  }

  Future<void> maybePreload({String? reason}) async {
    if (!_settings.fortuneWheelEnabled) {
      snapshot.value = null;
      error.value = null;
      return;
    }
    if (snapshot.value != null && !_snapshotLooksStale(snapshot.value!)) {
      return;
    }
    if (loading.value) {
      return;
    }
    await refresh();
  }

  Future<void> refresh() async {
    if (loading.value) return;
    if (!_settings.fortuneWheelEnabled) {
      snapshot.value = null;
      return;
    }

    loading.value = true;
    error.value = null;
    try {
      snapshot.value = await _api.fetchSnapshot();
    } catch (e) {
      error.value = e.toString().replaceFirst('Exception: ', '');
    } finally {
      loading.value = false;
    }
  }

  Future<FortuneWheelSpinResult> spin() async {
    final result = await _api.spin(
      idempotencyKey: 'fortune_wheel_${DateTime.now().microsecondsSinceEpoch}',
    );
    final current = snapshot.value;
    if (current != null) {
      snapshot.value = current.copyWith(
        walletBalance: result.walletBalance,
        freeSpinsRemaining: result.freeSpinsRemaining,
        recentSpins: [result.spin, ...current.recentSpins].take(10).toList(),
      );
    } else {
      unawaited(refresh());
    }
    return result;
  }

  bool _snapshotLooksStale(FortuneWheelSnapshot current) {
    final today = DateTime.now().toIso8601String().substring(0, 10);
    return current.spunForDate.isNotEmpty && current.spunForDate != today;
  }
}
