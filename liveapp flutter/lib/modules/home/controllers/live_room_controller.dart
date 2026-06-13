import 'package:flutter/foundation.dart';
import 'package:get/get.dart';
import 'package:gd_live/app/routes/app_urls.dart';

import '../../../services/auth_service.dart';
import '../../../services/live_rooms_ws_service.dart';
import '../../Live/services/live_service.dart';
import '../../home/models/live_room_dto.dart';

class LiveRoomsController extends GetxController {
  final RoomsSocketService socket;
  final AuthService auth;
  final LiveService live;

  LiveRoomsController(this.auth, this.socket, this.live);

  final RxList<LiveRoomModel> liveRooms = <LiveRoomModel>[].obs;
  final RxBool loading = false.obs;
  final RxnString error = RxnString();
  final Map<String, DateTime> _lastRoomUpdateAt = <String, DateTime>{};

  @override
  void onInit() {
    super.onInit();
    refreshForCurrentAuth();
  }

  Future<void> refreshForCurrentAuth() async {
    final token = auth.api.storage.token;
    if (token == null || token.isEmpty) {
      debugPrint('[rooms][CTRL] no token, skipping rooms bootstrap');
      liveRooms.clear();
      loading.value = false;
      error.value = null;
      _lastRoomUpdateAt.clear();
      await socket.stop();
      return;
    }

    await _loadInitialRooms();
    await _startSocket();
  }

  Future<void> _loadInitialRooms() async {
    loading.value = true;
    error.value = null;
    try {
      final rooms = await live.listLiveRooms();
      final filtered = rooms.where((room) => room.status == 'live').toList();
      _sortLiveRooms(filtered);
      for (final room in filtered) {
        final updatedAt =
            room.updatedAt ??
            room.startedAt ??
            DateTime.fromMillisecondsSinceEpoch(0);
        _lastRoomUpdateAt[room.id] = updatedAt;
      }
      liveRooms.assignAll(filtered);
    } catch (e) {
      debugPrint('[rooms][CTRL] initial load failed: $e');
      error.value = e.toString().replaceFirst('Exception: ', '');
    } finally {
      loading.value = false;
    }
  }

  Future<void> refreshRooms() async {
    await _loadInitialRooms();
  }

  Future<void> _startSocket() async {
    final t = auth.api.storage.token;
    if (t == null || t.isEmpty) {
      debugPrint('[rooms][CTRL] no token, skipping socket start');
      return;
    }

    await socket.start(
      wsRoomsUrl: AppUrls.wsRooms,
      bearerToken: t,
      onSnapshot: (list) {
        final mapped = list
            .map(LiveRoomModel.fromJson)
            .where((r) => r.status == 'live')
            .toList();
        _lastRoomUpdateAt
          ..clear()
          ..addEntries(
            mapped.map(
              (room) => MapEntry(
                room.id,
                room.updatedAt ??
                    room.startedAt ??
                    DateTime.fromMillisecondsSinceEpoch(0),
              ),
            ),
          );
        debugPrint('[rooms][CTRL] snapshot -> ${mapped.length} rooms');
        _sortLiveRooms(mapped);
        liveRooms.assignAll(mapped);
      },
      onUpsert: (row) {
        final r = LiveRoomModel.fromJson(row);
        final incomingUpdatedAt =
            r.updatedAt ?? r.startedAt ?? DateTime.fromMillisecondsSinceEpoch(0);
        final knownUpdatedAt = _lastRoomUpdateAt[r.id];
        if (knownUpdatedAt != null && incomingUpdatedAt.isBefore(knownUpdatedAt)) {
          debugPrint('[rooms][CTRL] stale upsert ignored ${r.id}');
          return;
        }
        _lastRoomUpdateAt[r.id] = incomingUpdatedAt;
        debugPrint('[rooms][CTRL] upsert ${r.id} status=${r.status}');
        if (r.status == 'live') {
          final i = liveRooms.indexWhere((e) => e.id == r.id);
          if (i >= 0) {
            liveRooms[i] = r;
          } else {
            liveRooms.add(r);
          }
          _sortLiveRooms(liveRooms);
          liveRooms.refresh();
        } else {
          liveRooms.removeWhere((e) => e.id == r.id);
        }
      },
      onRemove: (roomId) {
        debugPrint('[rooms][CTRL] remove $roomId');
        _lastRoomUpdateAt.remove(roomId);
        liveRooms.removeWhere((e) => e.id == roomId);
      },
    );
  }

  @override
  void onClose() {
    socket.stop();
    super.onClose();
  }

  void _sortLiveRooms(List<LiveRoomModel> rooms) {
    rooms.sort((a, b) {
      final followedCompare = _boolSort(a.isFollowingHost, b.isFollowingHost);
      if (followedCompare != 0) return followedCompare;

      final audienceCompare = b.liveAudience.compareTo(a.liveAudience);
      if (audienceCompare != 0) return audienceCompare;

      final peakCompare = b.peakViewers.compareTo(a.peakViewers);
      if (peakCompare != 0) return peakCompare;

      final startedA = a.startedAt ?? DateTime.fromMillisecondsSinceEpoch(0);
      final startedB = b.startedAt ?? DateTime.fromMillisecondsSinceEpoch(0);
      final startedCompare = startedB.compareTo(startedA);
      if (startedCompare != 0) return startedCompare;

      return (a.hostName ?? a.title).toLowerCase().compareTo(
        (b.hostName ?? b.title).toLowerCase(),
      );
    });
  }

  int _boolSort(bool a, bool b) {
    if (a == b) return 0;
    return a ? -1 : 1;
  }
}
