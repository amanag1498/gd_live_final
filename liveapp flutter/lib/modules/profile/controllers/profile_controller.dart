import 'package:flutter/foundation.dart';
import 'package:get/get.dart';

import '../../../data/models/user_model.dart';
import '../../../services/auth_service.dart';
import '../../../services/storage_service.dart';
import '../../profile/models/host_earnings_report_dto.dart';
import '../../profile/models/profile_dto.dart';
import '../services/profile_api.dart';

class ProfileController extends GetxController {
  final ProfileApi api;
  final AuthService auth;
  final StorageService storage;

  ProfileController({
    required this.api,
    required this.auth,
    required this.storage,
  });

  final isLoading = false.obs;
  final isSaving = false.obs;
  final isUploadingAvatar = false.obs;
  final isLoadingHostReport = false.obs;
  final error = RxnString();
  final profile = Rxn<ProfileDto>();
  final hostReport = Rxn<HostEarningsReportDto>();

  @override
  void onInit() {
    super.onInit();
    load();
  }

  UserModel? get currentUser => auth.currentUser;

  Future<void> load() async {
    isLoading.value = true;
    error.value = null;
    try {
      final data = await api.fetchProfile();
      if (isClosed) return;
      profile.value = data;
      await _syncUserCache(data);
      if (data.isHost) {
        await loadHostReport();
      } else {
        hostReport.value = null;
      }
    } catch (e) {
      if (isClosed) return;
      error.value = _message(e);
    } finally {
      if (!isClosed) isLoading.value = false;
    }
  }

  Future<void> loadHostReport() async {
    if (isLoadingHostReport.value) return;
    isLoadingHostReport.value = true;
    try {
      final data = await api.fetchHostEarningsReport();
      if (isClosed) return;
      _debugHostReport(data);
      hostReport.value = data;
    } catch (e) {
      if (!isClosed) {
        error.value ??= _message(e);
      }
    } finally {
      if (!isClosed) isLoadingHostReport.value = false;
    }
  }

  Future<bool> saveProfile({
    required String name,
    String? stageName,
    String? contactPhone,
    String? country,
    String? city,
    String? bio,
  }) async {
    if (isSaving.value) return false;
    isSaving.value = true;
    error.value = null;
    try {
      final data = await api.updateProfile(
        name: name,
        stageName: stageName,
        contactPhone: contactPhone,
        country: country,
        city: city,
        bio: bio,
      );
      if (isClosed) return false;
      profile.value = data;
      await _syncUserCache(data);
      return true;
    } catch (e) {
      if (!isClosed) error.value = _message(e);
      return false;
    } finally {
      if (!isClosed) isSaving.value = false;
    }
  }

  Future<bool> uploadAvatar(String path) async {
    if (isUploadingAvatar.value) return false;
    isUploadingAvatar.value = true;
    error.value = null;
    try {
      final data = await api.uploadAvatar(path);
      if (isClosed) return false;
      profile.value = data;
      await _syncUserCache(data);
      return true;
    } catch (e) {
      if (!isClosed) error.value = _message(e);
      return false;
    } finally {
      if (!isClosed) isUploadingAvatar.value = false;
    }
  }

  Future<void> _syncUserCache(ProfileDto data) async {
    final current = auth.currentUser;
    if (current == null) return;
    await storage.saveUserJson(
      current.copyWith(
        name: data.name,
        avatarUrl: data.avatarUrl,
        roles: data.roles,
        canGoLive: data.canGoLive,
        level: data.level,
        levelTitle: data.levelTitle,
        badgeIcon: data.badgeIcon,
        badgeColor: data.badgeColor,
        lifetimeSpendCoins: data.lifetimeSpendCoins,
        nextLevel: data.nextLevel,
        nextLevelTitle: data.nextLevelTitle,
        nextLevelRequiredSpend: data.nextLevelRequiredSpend,
        remainingSpendToNextLevel: data.remainingSpendToNextLevel,
        progressPercent: data.progressPercent,
        hostProfile: data.hostProfile == null
            ? null
            : HostProfile(
                stageName: data.hostProfile?.stageName,
                country: data.hostProfile?.country,
                city: data.hostProfile?.city,
                bio: data.hostProfile?.bio,
                contactPhone: data.hostProfile?.contactPhone,
              ),
      ).toJson(),
    );
  }

  String _message(Object error) {
    final text = error.toString().replaceFirst('Exception: ', '');
    debugPrint('[profile] $text');
    return text;
  }

  void _debugHostReport(HostEarningsReportDto data) {
    final today = data.today.summary;
    final currentWeek = data.currentWeek.summary;
    final lastWeek = data.lastWeek.summary;
    debugPrint(
      '[profile][host-report] '
      'today={video_call_minutes:${today.videoCallMinutes}, '
      'video_call_coins:${today.videoCallEarnings}, '
      'video_room_total_coins:${today.videoRoomGiftEarnings}, '
      'video_room_gift_coins:${today.videoRoomGiftsCoins}, '
      'pk_gift_coins:${today.pkGiftCoins}} '
      'current_week={video_call_minutes:${currentWeek.videoCallMinutes}, '
      'video_call_coins:${currentWeek.videoCallEarnings}, '
      'video_room_total_coins:${currentWeek.videoRoomGiftEarnings}, '
      'video_room_gift_coins:${currentWeek.videoRoomGiftsCoins}, '
      'pk_gift_coins:${currentWeek.pkGiftCoins}} '
      'last_week={video_call_minutes:${lastWeek.videoCallMinutes}, '
      'video_call_coins:${lastWeek.videoCallEarnings}, '
      'video_room_total_coins:${lastWeek.videoRoomGiftEarnings}, '
      'video_room_gift_coins:${lastWeek.videoRoomGiftsCoins}, '
      'pk_gift_coins:${lastWeek.pkGiftCoins}}',
    );
  }
}
