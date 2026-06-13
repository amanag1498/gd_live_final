import '../../../app/routes/app_urls.dart';

class LiveRoomModel {
  final String id;
  final String title;
  final String roomType;
  final String status;
  final int? hostId;
  final int? hostProfileId;
  final String? hostName;
  final int capacity;
  final int maxSpeakers;
  final int maxParticipants;
  final String? thumbnail;
  final int participantCount;
  final int viewerCount;
  final int audienceCount;
  final int speakerCount;
  final int pendingSeatRequestCount;
  final int peakViewers;
  final int followerCount;
  final String? topic;
  final String? language;
  final bool isLocked;
  final bool isFollowingHost;
  final DateTime? startedAt;
  final DateTime? updatedAt;

  const LiveRoomModel({
    required this.id,
    required this.title,
    this.roomType = 'video',
    required this.status,
    this.hostId,
    this.hostProfileId,
    this.hostName,
    this.capacity = 0,
    this.maxSpeakers = 4,
    this.maxParticipants = 50,
    this.thumbnail,
    this.participantCount = 0,
    this.viewerCount = 0,
    this.audienceCount = 0,
    this.speakerCount = 0,
    this.pendingSeatRequestCount = 0,
    this.peakViewers = 0,
    this.followerCount = 0,
    this.topic,
    this.language,
    this.isLocked = false,
    this.isFollowingHost = false,
    this.startedAt,
    this.updatedAt,
  });

  bool get isVideoRoom => roomType == 'video';

  int get liveAudience {
    if (viewerCount > 0) return viewerCount;
    if (audienceCount > 0) return audienceCount;
    if (participantCount > 0) return participantCount;
    return maxParticipants > 0 ? maxParticipants : capacity;
  }

  factory LiveRoomModel.fromJson(Map<String, dynamic> json) {
    final id = (json['room_id'] ?? json['id'] ?? '').toString().trim();
    final title = (json['title'] ?? '').toString();
    final roomType = _normalizeRoomType(
      json['room_type'] ?? json['type'] ?? json['media_type'],
    );
    final status = (json['status'] ?? 'live').toString();

    final hostId =
        json['host_id'] == null ? null : int.tryParse(json['host_id'].toString());
    final hostProfileId = json['host_profile_id'] == null
        ? null
        : int.tryParse(json['host_profile_id'].toString());
    final hostName = json['host_name']?.toString();
    final capacity =
        json['capacity'] == null ? 0 : (int.tryParse(json['capacity'].toString()) ?? 0);
    final maxSpeakers = json['max_speakers'] == null
        ? 4
        : (int.tryParse(json['max_speakers'].toString()) ?? 4);
    final maxParticipants = json['max_participants'] == null
        ? 50
        : (int.tryParse(json['max_participants'].toString()) ?? 50);
    final participantCount = json['participant_count'] == null
        ? 0
        : (int.tryParse(json['participant_count'].toString()) ?? 0);
    final viewerCount = json['viewer_count'] == null
        ? 0
        : (int.tryParse(json['viewer_count'].toString()) ?? 0);
    final audienceCount = json['audience_count'] == null
        ? 0
        : (int.tryParse(json['audience_count'].toString()) ?? 0);
    final speakerCount = json['speaker_count'] == null
        ? 0
        : (int.tryParse(json['speaker_count'].toString()) ?? 0);
    final pendingSeatRequestCount = json['pending_seat_request_count'] == null
        ? 0
        : (int.tryParse(json['pending_seat_request_count'].toString()) ?? 0);
    final peakViewers = json['peak_viewers'] == null
        ? 0
        : (int.tryParse(json['peak_viewers'].toString()) ?? 0);
    final followerCount = json['follower_count'] == null
        ? 0
        : (int.tryParse(json['follower_count'].toString()) ?? 0);
    final thumb = _normalizeThumb(json['thumbnail']?.toString());
    final topic = json['topic']?.toString();
    final language = json['language']?.toString();
    final isLocked = json['is_locked'] == true || json['is_locked']?.toString() == '1';

    return LiveRoomModel(
      id: id,
      title: title,
      roomType: roomType,
      status: status,
      hostId: hostId,
      hostProfileId: hostProfileId,
      hostName: hostName,
      capacity: capacity,
      maxSpeakers: maxSpeakers,
      maxParticipants: maxParticipants,
      thumbnail: thumb,
      participantCount: participantCount,
      viewerCount: viewerCount,
      audienceCount: audienceCount,
      speakerCount: speakerCount,
      pendingSeatRequestCount: pendingSeatRequestCount,
      peakViewers: peakViewers,
      followerCount: followerCount,
      topic: topic,
      language: language,
      isLocked: isLocked,
      isFollowingHost: json['is_following_host'] == true,
      startedAt: DateTime.tryParse((json['started_at'] ?? '').toString()),
      updatedAt: DateTime.tryParse((json['updated_at'] ?? '').toString()),
    );
  }

  Map<String, dynamic> toJson() => {
        'room_id': id,
        'title': title,
        'room_type': roomType,
        'status': status,
        'host_id': hostId,
        'host_profile_id': hostProfileId,
        'host_name': hostName,
        'capacity': capacity,
        'max_speakers': maxSpeakers,
        'max_participants': maxParticipants,
        'thumbnail': thumbnail,
        'participant_count': participantCount,
        'viewer_count': viewerCount,
        'audience_count': audienceCount,
        'speaker_count': speakerCount,
        'pending_seat_request_count': pendingSeatRequestCount,
        'peak_viewers': peakViewers,
        'follower_count': followerCount,
        'topic': topic,
        'language': language,
        'is_locked': isLocked,
        'is_following_host': isFollowingHost,
        'started_at': startedAt?.toIso8601String(),
        'updated_at': updatedAt?.toIso8601String(),
      };

  static String? _normalizeThumb(String? value) {
    if (value == null || value.trim().isEmpty) return null;
    final trimmed = value.trim();
    if (trimmed.startsWith('http://') || trimmed.startsWith('https://')) {
      return trimmed;
    }
    if (trimmed.startsWith('/')) return '${AppUrls.apiOrigin}$trimmed';
    return '${AppUrls.apiOrigin}/$trimmed';
  }

  static String _normalizeRoomType(dynamic value) {
    final normalized = value?.toString().trim().toLowerCase() ?? '';
    if (normalized == 'video' || normalized == 'video_room') return 'video';
    return 'video';
  }
}
