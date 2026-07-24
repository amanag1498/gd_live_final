import 'dart:async';
import 'package:get/get.dart';

import '../models/leaderboard_dto.dart';
import '../services/dashboard_api.dart';

class DashboardController extends GetxController {
  DashboardController(this._api);

  static const Duration _autoRefreshInterval = Duration(seconds: 60);
  static const Duration _resumeRefreshCooldown = Duration(seconds: 20);

  final DashboardApi _api;

  final isLoading = false.obs;
  final isRefreshing = false.obs;
  final error = RxnString();
  final leaderboards = Rxn<DashboardLeaderboardsDto>();

  Timer? _refreshTimer;
  Future<void>? _inFlightLoad;
  DateTime? _lastLoadedAt;

  Future<void> load({bool silent = false}) async {
    if (_inFlightLoad != null) {
      return _inFlightLoad!;
    }

    if (!silent) {
      isLoading.value = true;
    } else {
      isRefreshing.value = true;
    }
    error.value = null;

    final future = _performLoad();
    _inFlightLoad = future;
    await future;
  }

  Future<void> _performLoad() async {
    try {
      final payload = await _api.fetchLeaderboards();
      leaderboards.value = payload;
      _lastLoadedAt = DateTime.now();
    } catch (e) {
      error.value = e.toString();
    } finally {
      isLoading.value = false;
      isRefreshing.value = false;
      _inFlightLoad = null;
    }
  }

  Future<void> ensureLoaded() async {
    if (leaderboards.value != null) {
      return;
    }
    await load();
  }

  Future<void> refreshIfStale({
    bool silent = true,
    Duration minAge = _resumeRefreshCooldown,
  }) async {
    if (leaderboards.value == null) {
      await load(silent: silent);
      return;
    }

    final lastLoadedAt = _lastLoadedAt;
    if (lastLoadedAt == null) {
      await load(silent: silent);
      return;
    }

    final age = DateTime.now().difference(lastLoadedAt);
    if (age >= minAge) {
      await load(silent: silent);
    }
  }

  void startAutoRefresh() {
    _refreshTimer?.cancel();
    _refreshTimer = Timer.periodic(_autoRefreshInterval, (_) {
      unawaited(
        refreshIfStale(
          silent: true,
          minAge: _autoRefreshInterval - const Duration(seconds: 5),
        ),
      );
    });
  }

  void stopAutoRefresh() {
    _refreshTimer?.cancel();
    _refreshTimer = null;
  }

  @override
  void onClose() {
    stopAutoRefresh();
    super.onClose();
  }
}
