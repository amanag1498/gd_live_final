import 'api_client.dart';

class CallService {
  final ApiClient api;

  CallService(this.api);

  Future<Map<String, dynamic>> requestLiveRoomCall({
    required String roomId,
    required String type,
  }) async {
    final res = await api.post<Map<String, dynamic>>(
      'live/rooms/$roomId/request-call',
      data: {'type': type},
    );
    return Map<String, dynamic>.from(
      res.data?['data'] as Map? ?? <String, dynamic>{},
    );
  }

  Future<Map<String, dynamic>> acceptCall(int callId) async {
    final res = await api.post<Map<String, dynamic>>('calls/$callId/accept');
    return Map<String, dynamic>.from(res.data?['data'] as Map? ?? <String, dynamic>{});
  }

  Future<Map<String, dynamic>> rejectCall(int callId) async {
    final res = await api.post<Map<String, dynamic>>('calls/$callId/reject');
    return Map<String, dynamic>.from(res.data?['data'] as Map? ?? <String, dynamic>{});
  }

  Future<Map<String, dynamic>> endCall(int callId, {String? reason}) async {
    final res = await api.post<Map<String, dynamic>>(
      'calls/$callId/end',
      data: {'reason': reason},
    );
    return Map<String, dynamic>.from(res.data?['data'] as Map? ?? <String, dynamic>{});
  }

  Future<Map<String, dynamic>> fetchCallToken(int callId) async {
    final res = await api.get<Map<String, dynamic>>('calls/$callId/token');
    return Map<String, dynamic>.from(res.data?['data'] as Map? ?? <String, dynamic>{});
  }

  Future<Map<String, dynamic>> fetchHistory({int page = 1}) async {
    final res = await api.get<Map<String, dynamic>>(
      'calls/history',
      query: {'page': page},
    );
    return Map<String, dynamic>.from(res.data?['data'] as Map? ?? <String, dynamic>{});
  }
}
