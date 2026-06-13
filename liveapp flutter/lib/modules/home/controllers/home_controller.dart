// lib/modules/home/controllers/home_controller.dart
import 'dart:async'; // for Timer (optional polling)

import 'package:flutter/material.dart';
import 'package:get/get.dart';

import 'package:gd_live/app/routes/app_urls.dart';
import 'package:gd_live/modules/home/widgets/welcome_gift_dialog.dart';
import 'package:gd_live/modules/subscriptions/services/subscriptions_api.dart';
import 'package:gd_live/services/api_client.dart';
import 'package:gd_live/modules/notifications/controllers/notification_controller.dart'; // 👈 notifications

import '../../../app/widgets/approval_dialog.dart';
import '../../../app/widgets/level_up_card.dart';
import '../../../app/widgets/logout_and_blocked_dialog.dart';
import '../../../data/models/user_model.dart';
import '../../../services/auth_service.dart';
import '../../../services/app_settings_service.dart';
import '../../../services/presence_service.dart';
import '../../../services/storage_service.dart';
import '../../profile/services/profile_api.dart';

class HomeController extends SuperController {
  final AuthService auth;
  HomeController(this.auth);

  final currentUser = Rxn<UserModel>();
  late UserModel user;
  bool _presenceStarted = false;

  // Welcome tip state + API
  bool _welcomeChecked = false;
  late final SubscriptionsApi _subs;
  late final ProfileApi _profileApi;

  // (Optional) lightweight polling while Home is visible
  Timer? _notifTick;

  @override
  void onInit() {
    final u = auth.currentUser;
    if (u == null) {
      super.onInit(); // keep this; SuperController implements onInit
      return;
    }
    user = u;
    currentUser.value = u;
    _subs = SubscriptionsApi(auth.api);
    _profileApi = ProfileApi(auth.api);
    super.onInit();
  }

  @override
  void onReady() {
    if (Get.isRegistered<AppSettingsService>()) {
      unawaited(Get.find<AppSettingsService>().refresh());
    }
    _ensurePresence();
    _maybeShowWelcome(); // one-time popup if server says so
    _refreshNotifications(); // refresh notifications on first show
    unawaited(_refreshCurrentUserFromProfile());
    _startNotifPolling(); // (optional) uncomment to poll while on Home
  }

  // ─────────────────────────────────────────────────────────────────────────
  // GetX route/app lifecycle from SuperController (DO NOT call super.* here)
  // ─────────────────────────────────────────────────────────────────────────
  @override
  void onResumed() {
    if (Get.isRegistered<AppSettingsService>()) {
      unawaited(Get.find<AppSettingsService>().refresh());
    }
    // Called when Home route becomes active again (app foreground / navigated back)
    _ensurePresence();
    _refreshNotifications();
    unawaited(_refreshCurrentUserFromProfile());
    _startNotifPolling(); // (optional)
  }

  @override
  void onPaused() {
    _stopNotifPolling(); // (optional)
  }

  @override
  void onInactive() {
    _stopNotifPolling(); // (optional)

    // no-op
  }

  @override
  void onDetached() {
    _stopNotifPolling(); // (optional)

    // no-op
  }

  @override
  void onClose() {
    _stopNotifPolling(); // safe no-op if not started
    super.onClose();
  }

  // ─────────────────────────────────────────────────────────────────────────
  // Notifications refresh helper
  // ─────────────────────────────────────────────────────────────────────────
  void _refreshNotifications() {
    try {
      final NotificationsController c =
          Get.isRegistered<NotificationsController>()
              ? Get.find<NotificationsController>()
              : Get.put<NotificationsController>(
                NotificationsController(Get.find<ApiClient>()),
                permanent: true,
              );
      c.refreshBadge(); // lightweight unread badge refresh only
    } catch (e, st) {
      debugPrint('[home] _refreshNotifications skipped: $e\n$st');
    }
  }

  // OPTIONAL: lightweight periodic refresh while user idles on Home
  void _startNotifPolling() {
    _stopNotifPolling();
    _notifTick = Timer.periodic(
      const Duration(minutes: 2),
      (_) => _refreshNotifications(),
    );
  }

  void _stopNotifPolling() {
    _notifTick?.cancel();
    _notifTick = null;
  }

  // ─────────────────────────────────────────────────────────────────────────
  // Presence & notify routing (your existing logic)
  // ─────────────────────────────────────────────────────────────────────────
  Future<void> _ensurePresence() async {
    if (_presenceStarted) {
      await PresenceService.instance.resumeOnline();
      return;
    }
    final t = auth.api.storage.token;
    if (t != null && t.isNotEmpty) {
      await PresenceService.instance.start(
        wsPresenceUrl: AppUrls.wsPresence,
        bearerToken: t,
        onForceLogout: (reason) async {
          await auth.forceLogout(reason);
          if (Get.context == null) return;
          if (reason == 'blocked') {
            await showDialog(
              context: Get.context!,
              barrierDismissible: false,
              builder: (_) => const BlockedDialog(),
            );
          } else {
            await showDialog(
              context: Get.context!,
              barrierDismissible: false,
              builder: (_) => const LoggedOutDialog(),
            );
          }
        },
        onNotify: (payload) async {
          final type = (payload['type'] ?? '').toString();
          final title = (payload['title'] ?? '').toString();
          final body = (payload['body'] ?? '').toString();
          final meta =
              payload['meta'] is Map
                  ? Map<String, dynamic>.from(payload['meta'] as Map)
                  : const <String, dynamic>{};

          switch (type) {
            case 'level_up':
              final level = _toInt(meta['level']) ?? 1;
              final levelTitle =
                  (meta['level_title'] ?? 'New tier reached').toString();
              showLevelUpCard(
                level: level,
                levelTitle: levelTitle,
                oldLevel: _toInt(meta['old_level']),
                oldLevelTitle: meta['old_level_title']?.toString(),
                badgeColor: meta['badge_color']?.toString(),
              );
              if (Get.isRegistered<StorageService>()) {
                unawaited(
                  Get.find<StorageService>().updateUserJson((json) {
                    json['level'] = level;
                    json['level_title'] = levelTitle;
                    json['badge_icon'] = meta['badge_icon'];
                    json['badge_color'] = meta['badge_color'];
                    json['lifetime_spend_coins'] = _toInt(
                      meta['lifetime_spend_coins'],
                    );
                    json['next_level'] = _toInt(meta['next_level']);
                    json['next_level_title'] =
                        meta['next_level_title']?.toString();
                    json['next_level_required_spend'] = _toInt(
                      meta['next_level_required_spend'],
                    );
                    json['remaining_spend_to_next_level'] = _toInt(
                      meta['remaining_spend_to_next_level'],
                    );
                    json['progress_percent'] = _toDouble(
                      meta['progress_percent'],
                    );
                  }),
                );
              }
              break;
            case 'host_online':
              Get.closeAllSnackbars();
              Get.snackbar(
                title.isNotEmpty ? title : 'Host update',
                body.isNotEmpty
                    ? body
                    : 'A followed host has a new status update.',
                snackPosition: SnackPosition.TOP,
              );
              break;
            case 'host_approved':
              unawaited(_refreshCurrentUserFromProfile());
              Get.dialog(
                ApprovalDialog(
                  title: title.isNotEmpty ? title : 'Host request approved 🎉',
                  message:
                      body.isNotEmpty ? body : 'You can now host live rooms.',
                  ctaText: 'Great',
                ),
              );
              break;
            case 'agency_approved':
              Get.dialog(
                ApprovalDialog(
                  title: title.isNotEmpty ? title : 'Agency approved 🎉',
                  message:
                      body.isNotEmpty ? body : 'Your agency has been approved.',
                  ctaText: 'Awesome',
                ),
              );
              break;
            case 'host_rejected':
              unawaited(_refreshCurrentUserFromProfile());
              Get.dialog(
                ApprovalDialog(
                  title: title.isNotEmpty ? title : 'Request reviewed',
                  message:
                      body.isNotEmpty ? body : 'Your request was not approved.',
                  ctaText: 'OK',
                ),
              );
              break;
            case 'agency_rejected':
              Get.dialog(
                ApprovalDialog(
                  title: title.isNotEmpty ? title : 'Request reviewed',
                  message:
                      body.isNotEmpty ? body : 'Your request was not approved.',
                  ctaText: 'OK',
                ),
              );
              break;
            default:
              Get.dialog(
                ApprovalDialog(
                  title: title.isNotEmpty ? title : 'Notification',
                  message: body.isNotEmpty ? body : 'You have a new update.',
                  ctaText: 'OK',
                ),
              );
          }

          // Keep list/badge in sync when in-app presence "notify" arrives
          _refreshNotifications();
        },
      );
      _presenceStarted = true;
    }
  }

  // ─────────────────────────────────────────────────────────────────────────
  // WELCOME GIFT POPUP (one-time)
  // ─────────────────────────────────────────────────────────────────────────
  Future<void> _maybeShowWelcome() async {
    if (_welcomeChecked) return;
    _welcomeChecked = true;

    final userId = auth.currentUser?.id;
    if (userId != null &&
        Get.isRegistered<StorageService>() &&
        Get.find<StorageService>().hasWelcomeTipAck(userId)) {
      return;
    }

    try {
      // GET /subscriptions/welcome-tip (server returns {show, plan, sub_id...})
      final tip = await _subs.welcomeTip();
      if (tip['show'] == true) {
        final plan =
            (tip['plan'] is Map)
                ? Map<String, dynamic>.from(tip['plan'])
                : <String, dynamic>{};
        final planName = (plan['name'] ?? 'Premium').toString();
        final endsAtIso = plan['ends_at']?.toString();

        await _showWelcomeGiftDialog(planName: planName, endsAtIso: endsAtIso);

        // POST /subscriptions/welcome-tip/ack so it never shows again
        final subId =
            tip['sub_id'] is int
                ? tip['sub_id'] as int
                : int.tryParse('${tip['sub_id']}') ?? 0;
        if (subId > 0) {
          await _subs.ackWelcomeTip(subId: subId);
          if (userId != null && Get.isRegistered<StorageService>()) {
            await Get.find<StorageService>().markWelcomeTipAck(userId);
          }
        }
      }
    } catch (e, st) {
      debugPrint('[home] welcomeTip error: $e\n$st');
    }
  }

  Future<void> _showWelcomeGiftDialog({
    required String planName,
    String? endsAtIso,
  }) {
    final ends = endsAtIso != null ? DateTime.tryParse(endsAtIso) : null;
    final endsText = (ends != null) ? 'Valid till ${_fmtShort(ends)}' : null;

    return Get.dialog(
      BackstageWelcomeDialog(planName: planName, endsText: endsText),
      barrierDismissible: true,
    );
  }

  String _fmtShort(DateTime dt) {
    final y = dt.year.toString().padLeft(4, '0');
    final m = dt.month.toString().padLeft(2, '0');
    final d = dt.day.toString().padLeft(2, '0');
    final hh = dt.hour.toString().padLeft(2, '0');
    final mm = dt.minute.toString().padLeft(2, '0');
    return '$y-$m-$d $hh:$mm';
  }

  // Existing convenience
  bool get canGoLive {
    final activeUser = currentUser.value;
    if (activeUser == null) return false;
    try {
      final v = (activeUser as dynamic).canGoLive;
      if (v is bool) return v;
    } catch (_) {}
    final roles = activeUser.roles;
    return roles.contains('host');
  }

  Future<void> _refreshCurrentUserFromProfile() async {
    final seed = auth.currentUser;
    if (seed != null && currentUser.value == null) {
      user = seed;
      currentUser.value = seed;
    }
    if (!auth.isLoggedIn) return;
    try {
      final profile = await _profileApi.fetchProfile();
      final latest =
          (auth.currentUser ?? currentUser.value)?.copyWith(
            name: profile.name,
            avatarUrl: profile.avatarUrl,
            roles: profile.roles,
            canGoLive: profile.canGoLive,
            level: profile.level,
            levelTitle: profile.levelTitle,
            badgeIcon: profile.badgeIcon,
            badgeColor: profile.badgeColor,
            lifetimeSpendCoins: profile.lifetimeSpendCoins,
            nextLevel: profile.nextLevel,
            nextLevelTitle: profile.nextLevelTitle,
            nextLevelRequiredSpend: profile.nextLevelRequiredSpend,
            remainingSpendToNextLevel: profile.remainingSpendToNextLevel,
            progressPercent: profile.progressPercent,
            hostProfile: profile.hostProfile == null
                ? null
                : HostProfile(
                    stageName: profile.hostProfile?.stageName,
                    country: profile.hostProfile?.country,
                    city: profile.hostProfile?.city,
                    bio: profile.hostProfile?.bio,
                    contactPhone: profile.hostProfile?.contactPhone,
                  ),
          );
      if (latest == null || isClosed) return;
      user = latest;
      currentUser.value = latest;
      if (Get.isRegistered<StorageService>()) {
        await Get.find<StorageService>().saveUserJson(latest.toJson());
      }
    } catch (e, st) {
      debugPrint('[home] current user refresh skipped: $e\n$st');
    }
  }

  Future<void> logout() => auth.logout();

  @override
  void onHidden() {
    _stopNotifPolling(); // (optional)

    // TODO: implement onHidden
  }

  int? _toInt(dynamic value) {
    if (value is int) return value;
    if (value is num) return value.toInt();
    return int.tryParse(value?.toString() ?? '');
  }

  double? _toDouble(dynamic value) {
    if (value is double) return value;
    if (value is num) return value.toDouble();
    return double.tryParse(value?.toString() ?? '');
  }
}
