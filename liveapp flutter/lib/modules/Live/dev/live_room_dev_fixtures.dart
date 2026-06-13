import '../../home/models/live_room_dto.dart' as home_dto;
import '../models/live_gift_item.dart';
import '../models/live_room_chat_message.dart';
import '../models/live_room_model.dart';

class LiveRoomDevFixtures {
  static const String mockFeedVideoId = 'mock-feed-video-host-room';

  static List<home_dto.LiveRoomModel> mockFeedRooms() {
    return <home_dto.LiveRoomModel>[
      mockFeedVideoRoom(),
      ..._mockVideoFeedRooms(),
    ];
  }

  static home_dto.LiveRoomModel mockFeedVideoRoom() {
    final now = DateTime.now();
    return home_dto.LiveRoomModel(
      id: mockFeedVideoId,
      title: 'Mock Video Host Room',
      roomType: 'video',
      status: 'live',
      hostId: 501,
      hostName: 'Host Aman',
      maxSpeakers: 4,
      maxParticipants: 200,
      participantCount: 1,
      viewerCount: 0,
      speakerCount: 1,
      audienceCount: 0,
      topic: 'Gift animation test',
      language: 'English',
      startedAt: now.subtract(const Duration(minutes: 18)),
      updatedAt: now,
    );
  }

  static List<home_dto.LiveRoomModel> _mockVideoFeedRooms() {
    const hosts = <String>[
      'Dev',
      'Anaya',
      'Maya',
      'Rohan',
      'Tara',
      'Zoya',
      'Karan',
      'Aarav',
      'Sana',
      'Jatin',
      'Vik',
      'Neha',
    ];
    const titles = <String>[
      'Premium stage',
      'Dance night',
      'PK ready',
      'Just chatting',
      'Singer live',
      'Host vibes',
      'Gaming facecam',
      'Fashion talk',
      'Beauty room',
      'Fans hangout',
      'Travel stories',
      'Late live',
    ];
    const languages = <String>[
      'English',
      'Hindi',
      'English',
      'Urdu',
      'Hindi',
      'Punjabi',
      'English',
      'Tamil',
      'Hindi',
      'English',
      'Bengali',
      'English',
    ];

    return List<home_dto.LiveRoomModel>.generate(hosts.length, (index) {
      final now = DateTime.now();
      return home_dto.LiveRoomModel(
        id: 'mock-feed-video-${index + 1}',
        title: titles[index],
        roomType: 'video',
        status: 'live',
        hostId: 800 + index,
        hostName: hosts[index],
        maxSpeakers: 4,
        maxParticipants: 200,
        participantCount: 30 + (index * 4),
        viewerCount: 24 + (index * 17),
        speakerCount: 1 + (index % 4),
        audienceCount: 35 + (index * 18),
        topic: titles[index],
        language: languages[index],
        thumbnail: 'https://picsum.photos/seed/livehost${index + 1}/420/620',
        startedAt: now.subtract(Duration(minutes: 8 + (index * 5))),
        updatedAt: now,
      );
    });
  }

  static LiveRoomModel videoRoom() {
    return LiveRoomModel(
      roomId: 'dev-video-room',
      title: 'GD Stage Preview',
      roomType: 'video',
      status: 'live',
      role: 'host',
      participantCount: 146,
      viewerCount: 140,
      speakerCount: 6,
      maxSpeakers: 4,
      maxParticipants: 200,
      topic: 'Video UI preview',
      language: 'English',
      meta: const <String, dynamic>{
        'host_name': 'Host Aman',
        'dev_video_tiles': <Map<String, dynamic>>[
          {
            'label': 'You',
            'brand_key': 'midnight',
            'is_host': true,
            'is_vip': true,
            'is_speaking': true,
          },
          {
            'label': 'Maya',
            'brand_key': 'midnight',
            'is_host': false,
            'is_vip': true,
            'is_speaking': false,
          },
          {
            'label': 'Karan',
            'brand_key': 'midnight',
            'is_host': false,
            'is_vip': false,
            'is_speaking': true,
          },
          {
            'label': 'Zoya',
            'brand_key': 'midnight',
            'is_host': false,
            'is_vip': true,
            'is_speaking': false,
          },
          {
            'label': 'Aarav',
            'brand_key': 'midnight',
            'is_host': false,
            'is_vip': true,
            'is_speaking': false,
          },
          {
            'label': 'Nina',
            'brand_key': 'midnight',
            'is_host': false,
            'is_vip': false,
            'is_speaking': true,
          },
        ],
      },
    );
  }

  static LiveRoomModel videoSoloRoom() {
    return LiveRoomModel(
      roomId: mockFeedVideoId,
      title: 'Mock Video Host Room',
      roomType: 'video',
      status: 'live',
      role: 'viewer',
      participantCount: 1,
      viewerCount: 0,
      speakerCount: 1,
      maxSpeakers: 4,
      maxParticipants: 200,
      topic: 'Gift animation test',
      language: 'English',
      meta: const <String, dynamic>{
        'host_user_id': 501,
        'host_name': 'Host Aman',
        'host_avatar': '',
        'mock_feed_room': true,
        'dev_video_tiles': <Map<String, dynamic>>[
          {
            'user_id': 501,
            'label': 'Host Aman',
            'brand_key': 'gold_black',
            'is_host': true,
            'is_vip': true,
            'is_speaking': true,
            'level': 17,
            'avatar_url': '',
          },
        ],
      },
    );
  }

  static List<LiveGiftItem> mockGiftCatalog() {
    return const <LiveGiftItem>[
      LiveGiftItem(
        id: 101,
        name: 'Rose',
        coins: 25,
        giftUrl: 'https://example.com/gifts/rose.gif',
        giftType: 'gif',
        animationTier: 'small',
        animationDurationMs: 1400,
      ),
      LiveGiftItem(
        id: 102,
        name: 'Heart',
        coins: 199,
        giftUrl: 'https://example.com/gifts/heart.svg',
        giftType: 'svg',
        animationTier: 'medium',
        animationDurationMs: 2400,
      ),
      LiveGiftItem(
        id: 103,
        name: 'Rocket',
        coins: 1299,
        giftUrl: 'https://example.com/gifts/rocket.webp',
        giftType: 'image',
        animationTier: 'premium',
        animationDurationMs: 5000,
      ),
      LiveGiftItem(
        id: 104,
        name: 'Crown',
        coins: 12000,
        giftUrl: 'https://example.com/gifts/crown.gif',
        giftType: 'gif',
        animationTier: 'legendary',
        animationDurationMs: 6800,
      ),
    ];
  }

  static Map<String, dynamic> mockGiftPayload({
    required String roomId,
    required String roomType,
    required int receiverId,
    required String receiverName,
    String? receiverAvatar,
    required LiveGiftItem gift,
    required int quantity,
    String? pkSide,
  }) {
    final senderTokens = pkSide == 'right' ? 'inferno' : 'cyberpunk';
    return <String, dynamic>{
      'event': 'room:gift',
      'room_id': roomId,
      'room_type': roomType,
      'host_user_id': receiverId,
      'sender_user_id': 90061,
      'sender_name': 'Gift Tester',
      'sender_avatar': '',
      'sender_level': 12,
      'sender_is_vip': true,
      'sender_tokens': senderTokens,
      'receiver_name': receiverName,
      'receiver_avatar': receiverAvatar ?? '',
      'pk_side': pkSide,
      'gift_id': gift.id,
      'gift_name': gift.name,
      'gift_url': gift.giftUrl ?? '',
      'gift_type': gift.giftType,
      'animation_tier': gift.animationTier,
      'animation_duration_ms': gift.animationDurationMs,
      'quantity': quantity,
      'coins_per_unit': gift.coins,
      'total_coins': gift.coins * quantity,
      'message': 'Testing premium gift overlay',
      'created_at': DateTime.now().toIso8601String(),
    };
  }

  static LiveRoomModel videoPkRoom() {
    final now = DateTime.now();
    final endsAt = now.add(const Duration(seconds: 82)).toIso8601String();
    final startedAt = now.subtract(const Duration(seconds: 38)).toIso8601String();
    return LiveRoomModel(
      roomId: 'dev-video-pk-room-a',
      title: 'PK Battle Preview',
      roomType: 'video',
      status: 'live',
      role: 'host',
      participantCount: 188,
      viewerCount: 182,
      speakerCount: 2,
      maxSpeakers: 4,
      maxParticipants: 200,
      topic: 'PK UI preview',
      language: 'English',
      pkActive: <String, dynamic>{
        'battle_id': 'dev-pk-battle-01',
        'status': 'active',
        'duration_seconds': 120,
        'score_a': 12400,
        'score_b': 9800,
        'started_at': startedAt,
        'ends_at': endsAt,
        'updated_at': now.toIso8601String(),
        'winner_room_id': null,
        'room_a': <String, dynamic>{
          'id': 'dev-video-pk-room-a',
          'name': 'Team Aman',
        },
        'room_b': <String, dynamic>{
          'id': 'dev-video-pk-room-b',
          'name': 'Team Zoya',
        },
        'host_a': <String, dynamic>{
          'user_id': 501,
          'name': 'Host Aman',
          'is_vip': true,
        },
        'host_b': <String, dynamic>{
          'user_id': 502,
          'name': 'Zoya',
          'is_vip': true,
        },
      },
      meta: const <String, dynamic>{
        'host_name': 'Host Aman',
        'dev_video_tiles': <Map<String, dynamic>>[
          {
            'label': 'You',
            'brand_key': 'gold_black',
            'is_host': true,
            'is_vip': true,
            'is_speaking': true,
          },
        ],
      },
    );
  }

  static List<LiveRoomChatMessage> videoMessages() {
    return <LiveRoomChatMessage>[
      LiveRoomChatMessage(
        id: 'dev-video-1',
        roomId: 'dev-video-room',
        roomType: 'video',
        senderId: 401,
        senderName: 'Maya',
        message: 'This route is for polishing the video stage.',
        messageType: 'text',
        createdAt: DateTime.now().subtract(const Duration(minutes: 3)),
        senderIsVip: true,
        senderLevel: 15,
      ),
      LiveRoomChatMessage(
        id: 'dev-video-2',
        roomId: 'dev-video-room',
        roomType: 'video',
        senderId: 402,
        senderName: 'Karan',
        message: 'Tile spacing, layout, and overlays can be tuned safely here.',
        messageType: 'text',
        createdAt: DateTime.now().subtract(const Duration(minutes: 1)),
        senderLevel: 9,
      ),
    ];
  }
}
