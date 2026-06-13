import 'dart:async';

import 'package:flutter/widgets.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:package_info_plus/package_info_plus.dart';

import '../app/brand/brand.dart';
import 'api_client.dart';
import 'storage_service.dart';

class AppSettingsService extends GetxService with WidgetsBindingObserver {
  AppSettingsService(this._api);

  static const String androidPlatform = 'android';
  static const String brandStorageKey = 'app_brand_key';
  static String appVersionName = String.fromEnvironment(
    'APP_VERSION_NAME',
    defaultValue: '1.0.0',
  );
  static int appVersionCode = int.fromEnvironment(
    'APP_VERSION_CODE',
    defaultValue: 1,
  );

  final ApiClient _api;

  final RxBool loading = false.obs;
  final RxBool loaded = false.obs;
  final RxnString error = RxnString();
  final Rxn<AppSettingsPayload> payload = Rxn<AppSettingsPayload>();
  final RxString currentBrandKey = kDefaultBrandKey.obs;

  bool _activitySyncInFlight = false;

  Future<void> initialize() async {
    WidgetsBinding.instance.addObserver(this);
    _hydrateStoredBrandKey();
    await _hydrateRuntimeVersion();
    await refresh();
    await syncDailyActivity();
  }

  void _hydrateStoredBrandKey() {
    final box = GetStorage();
    final stored = box.read<String>(brandStorageKey);
    currentBrandKey.value = normalizeBrandVariant(stored);
  }

  Future<void> _hydrateRuntimeVersion() async {
    try {
      final info = await PackageInfo.fromPlatform();
      final runtimeName = info.version.trim();
      final runtimeCode = int.tryParse(info.buildNumber.trim());
      if (runtimeName.isNotEmpty) {
        appVersionName = runtimeName;
      }
      if (runtimeCode != null && runtimeCode > 0) {
        appVersionCode = runtimeCode;
      }
    } catch (_) {
      // Fall back to compile-time values when platform package info is unavailable.
    }
  }

  Future<void> refresh() async {
    if (loading.value) return;
    loading.value = true;
    error.value = null;
    try {
      final res = await _api.get<Map<String, dynamic>>('app-config');
      final body = res.data ?? const <String, dynamic>{};
      final data =
          body['data'] is Map<String, dynamic>
              ? body['data'] as Map<String, dynamic>
              : Map<String, dynamic>.from(body['data'] as Map? ?? const {});
      payload.value = AppSettingsPayload.fromJson(data);
      await setBrandKey(data['brand_key']?.toString());
      loaded.value = true;
    } catch (e) {
      error.value = e.toString();
      loaded.value = true;
    } finally {
      loading.value = false;
    }
  }

  Future<void> syncDailyActivity() async {
    if (_activitySyncInFlight) return;
    final storage = Get.isRegistered<StorageService>()
        ? Get.find<StorageService>()
        : null;
    final token = storage?.token;
    if (token == null || token.isEmpty) {
      return;
    }

    _activitySyncInFlight = true;
    try {
      final res = await _api.post<Map<String, dynamic>>('app/activity');
      final body = res.data ?? const <String, dynamic>{};
      final data =
          body['data'] is Map<String, dynamic>
              ? body['data'] as Map<String, dynamic>
              : Map<String, dynamic>.from(body['data'] as Map? ?? const {});
      final updated = data['streak_updated'] == true;
      if (updated) {
        await refresh();
      }
    } catch (_) {
      // Keep startup/resume lightweight; streak sync should never block the app.
    } finally {
      _activitySyncInFlight = false;
    }
  }

  bool get maintenanceModeEnabled =>
      payload.value?.maintenanceModeEnabled ?? false;

  bool get forceAppUpgradeEnabled =>
      payload.value?.forceAppUpgradeEnabled ?? false;

  bool get shouldForceUpgrade {
    final config = payload.value;
    if (config == null || !config.forceAppUpgradeEnabled) {
      return false;
    }

    return appVersionCode < config.androidMinVersionCode;
  }

  String get forceUpgradeMessage =>
      payload.value?.androidUpdateMessage ??
      'Please update GD Live to continue using the app.';

  String get brandKey => currentBrandKey.value;

  Future<void> setBrandKey(String? brandKey) async {
    final normalized = normalizeBrandVariant(brandKey);
    currentBrandKey.value = normalized;
    await GetStorage().write(brandStorageKey, normalized);
  }

  bool get videoRoomsEnabled =>
      payload.value?.features.videoRoomsEnabled ?? true;
  bool get pkBattlesEnabled => payload.value?.features.pkBattlesEnabled ?? true;
  bool get giftsEnabled => payload.value?.features.giftsEnabled ?? true;
  bool get subscriptionsEnabled =>
      payload.value?.features.subscriptionsEnabled ?? true;
  bool get entryEffectsEnabled =>
      payload.value?.features.entryEffectsEnabled ?? true;
  bool get walletRechargeEnabled =>
      payload.value?.features.walletRechargeEnabled ?? true;
  bool get hostCallingEnabled =>
      payload.value?.features.hostCallingEnabled ?? true;
  bool get teenPattiEnabled =>
      payload.value?.features.teenPattiEnabled ?? false;
  bool get greedyEnabled =>
      payload.value?.features.greedyEnabled ?? false;
  bool get videoRoomGamesEnabled =>
      payload.value?.features.videoRoomGamesEnabled ?? false;
  bool get anyLiveCreationEnabled => videoRoomsEnabled;

  @override
  void didChangeAppLifecycleState(AppLifecycleState state) {
    if (state == AppLifecycleState.resumed) {
      unawaited(syncDailyActivity());
    }
  }
}

class AppSettingsPayload {
  const AppSettingsPayload({
    required this.maintenanceModeEnabled,
    required this.forceAppUpgradeEnabled,
    required this.androidMinVersionCode,
    required this.androidMinVersionName,
    required this.androidUpdateMessage,
    required this.features,
  });

  final bool maintenanceModeEnabled;
  final bool forceAppUpgradeEnabled;
  final int androidMinVersionCode;
  final String androidMinVersionName;
  final String androidUpdateMessage;
  final AppPlatformFeatureFlags features;

  factory AppSettingsPayload.fromJson(Map<String, dynamic> json) {
    return AppSettingsPayload(
      maintenanceModeEnabled: _toBool(json['maintenance_mode_enabled']),
      forceAppUpgradeEnabled: _toBool(json['force_app_upgrade_enabled']),
      androidMinVersionCode: _toInt(json['android_min_version_code'], 1),
      androidMinVersionName:
          (json['android_min_version_name']?.toString().trim().isNotEmpty ??
                  false)
              ? json['android_min_version_name'].toString().trim()
              : '1.0.0',
      androidUpdateMessage:
          (json['android_update_message']?.toString().trim().isNotEmpty ??
                  false)
              ? json['android_update_message'].toString().trim()
              : 'Please update GD Live to continue using the app.',
      features: AppPlatformFeatureFlags.fromJson(
        Map<String, dynamic>.from(json['features'] as Map? ?? const {}),
      ),
    );
  }

  static bool _toBool(dynamic value) {
    if (value is bool) return value;
    final normalized = value?.toString().toLowerCase().trim();
    return normalized == '1' || normalized == 'true' || normalized == 'yes';
  }

  static int _toInt(dynamic value, int fallback) {
    if (value is int) return value;
    return int.tryParse(value?.toString() ?? '') ?? fallback;
  }
}

class AppPlatformFeatureFlags {
  const AppPlatformFeatureFlags({
    required this.videoRoomsEnabled,
    required this.pkBattlesEnabled,
    required this.giftsEnabled,
    required this.subscriptionsEnabled,
    required this.entryEffectsEnabled,
    required this.walletRechargeEnabled,
    required this.hostCallingEnabled,
    required this.teenPattiEnabled,
    required this.greedyEnabled,
    required this.videoRoomGamesEnabled,
  });

  const AppPlatformFeatureFlags.enabled()
    : videoRoomsEnabled = true,
      pkBattlesEnabled = true,
      giftsEnabled = true,
      subscriptionsEnabled = true,
      entryEffectsEnabled = true,
      walletRechargeEnabled = true,
      hostCallingEnabled = true,
      teenPattiEnabled = false,
      greedyEnabled = false,
      videoRoomGamesEnabled = false;

  final bool videoRoomsEnabled;
  final bool pkBattlesEnabled;
  final bool giftsEnabled;
  final bool subscriptionsEnabled;
  final bool entryEffectsEnabled;
  final bool walletRechargeEnabled;
  final bool hostCallingEnabled;
  final bool teenPattiEnabled;
  final bool greedyEnabled;
  final bool videoRoomGamesEnabled;

  factory AppPlatformFeatureFlags.fromJson(Map<String, dynamic> json) {
    bool toBool(dynamic value, {required bool fallback}) {
      if (value is bool) return value;
      if (value == null) return fallback;
      final normalized = value.toString().toLowerCase().trim();
      return normalized == '1' || normalized == 'true' || normalized == 'yes';
    }

    return AppPlatformFeatureFlags(
      videoRoomsEnabled: toBool(
        json['video_rooms_enabled'],
        fallback: true,
      ),
      pkBattlesEnabled: toBool(json['pk_battles_enabled'], fallback: true),
      giftsEnabled: toBool(json['gifts_enabled'], fallback: true),
      subscriptionsEnabled: toBool(
        json['subscriptions_enabled'],
        fallback: true,
      ),
      entryEffectsEnabled: toBool(
        json['entry_effects_enabled'],
        fallback: true,
      ),
      walletRechargeEnabled: toBool(
        json['wallet_recharge_enabled'],
        fallback: true,
      ),
      hostCallingEnabled: toBool(
        json['host_calling_enabled'],
        fallback: true,
      ),
      teenPattiEnabled: toBool(json['teen_patti_enabled'], fallback: false),
      greedyEnabled: toBool(json['greedy_enabled'], fallback: false),
      videoRoomGamesEnabled: toBool(
        json['video_room_games_enabled'],
        fallback: false,
      ),
    );
  }
}
