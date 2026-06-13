class UserModel {
  final int id;
  final String name;
  final String email;
  final String? avatarUrl;
  final String provider;
  final bool emailVerified;
  final bool isBlocked;
  final List<String> roles;
  final List<String> permissions;
  final bool canGoLive;
  final int? level;
  final String? levelTitle;
  final String? badgeIcon;
  final String? badgeColor;
  final int? lifetimeSpendCoins;
  final int? nextLevel;
  final String? nextLevelTitle;
  final int? nextLevelRequiredSpend;
  final int? remainingSpendToNextLevel;
  final double? progressPercent;
  final HostProfile? hostProfile;

  const UserModel({
    required this.id,
    required this.name,
    required this.email,
    required this.provider,
    required this.emailVerified,
    required this.isBlocked,
    this.avatarUrl,
    this.roles = const [],
    this.permissions = const [],
    this.canGoLive = false,
    this.level,
    this.levelTitle,
    this.badgeIcon,
    this.badgeColor,
    this.lifetimeSpendCoins,
    this.nextLevel,
    this.nextLevelTitle,
    this.nextLevelRequiredSpend,
    this.remainingSpendToNextLevel,
    this.progressPercent,
    this.hostProfile,
  });

  bool get isHost => roles.contains('host');
  bool get isAdmin => roles.contains('admin');
  bool get isAgency => roles.contains('agency');
  bool get isNormalUser => !isHost && !isAdmin && !isAgency;

  factory UserModel.fromJson(Map<String, dynamic> json) {
    final roles = (json['roles'] as List?)
            ?.whereType<String>()
            .toList(growable: false) ??
        const <String>[];
    final permissions = (json['permissions'] as List?)
            ?.whereType<String>()
            .toList(growable: false) ??
        const <String>[];

    return UserModel(
      id: json['id'] as int,
      name: (json['name'] ?? '') as String,
      email: (json['email'] ?? '') as String,
      avatarUrl: json['avatar_url'] as String?,
      provider: (json['provider'] ?? '') as String,
      emailVerified: (json['email_verified'] ?? false) as bool,
      isBlocked: (json['is_blocked'] ?? false) as bool,
      roles: roles,
      permissions: permissions,
      canGoLive: (json['can_go_live'] ?? false) as bool,
      level: (json['level'] as num?)?.toInt(),
      levelTitle: json['level_title']?.toString(),
      badgeIcon: json['badge_icon']?.toString(),
      badgeColor: json['badge_color']?.toString(),
      lifetimeSpendCoins: (json['lifetime_spend_coins'] as num?)?.toInt(),
      nextLevel: (json['next_level'] as num?)?.toInt(),
      nextLevelTitle: json['next_level_title']?.toString(),
      nextLevelRequiredSpend: (json['next_level_required_spend'] as num?)?.toInt(),
      remainingSpendToNextLevel: (json['remaining_spend_to_next_level'] as num?)?.toInt(),
      progressPercent: (json['progress_percent'] as num?)?.toDouble(),
      hostProfile: json['host_profile'] == null
          ? null
          : HostProfile.fromJson(
              Map<String, dynamic>.from(json['host_profile'] as Map),
            ),
    );
  }

  Map<String, dynamic> toJson() => {
        'id': id,
        'name': name,
        'email': email,
        'avatar_url': avatarUrl,
        'provider': provider,
        'email_verified': emailVerified,
        'is_blocked': isBlocked,
        'roles': roles,
        'permissions': permissions,
        'can_go_live': canGoLive,
        'level': level,
        'level_title': levelTitle,
        'badge_icon': badgeIcon,
        'badge_color': badgeColor,
        'lifetime_spend_coins': lifetimeSpendCoins,
        'next_level': nextLevel,
        'next_level_title': nextLevelTitle,
        'next_level_required_spend': nextLevelRequiredSpend,
        'remaining_spend_to_next_level': remainingSpendToNextLevel,
        'progress_percent': progressPercent,
        'host_profile': hostProfile?.toJson(),
      };

  UserModel copyWith({
    int? id,
    String? name,
    String? email,
    String? avatarUrl,
    String? provider,
    bool? emailVerified,
    bool? isBlocked,
    List<String>? roles,
    List<String>? permissions,
    bool? canGoLive,
    int? level,
    String? levelTitle,
    String? badgeIcon,
    String? badgeColor,
    int? lifetimeSpendCoins,
    int? nextLevel,
    String? nextLevelTitle,
    int? nextLevelRequiredSpend,
    int? remainingSpendToNextLevel,
    double? progressPercent,
    HostProfile? hostProfile,
  }) {
    return UserModel(
      id: id ?? this.id,
      name: name ?? this.name,
      email: email ?? this.email,
      avatarUrl: avatarUrl ?? this.avatarUrl,
      provider: provider ?? this.provider,
      emailVerified: emailVerified ?? this.emailVerified,
      isBlocked: isBlocked ?? this.isBlocked,
      roles: roles ?? this.roles,
      permissions: permissions ?? this.permissions,
      canGoLive: canGoLive ?? this.canGoLive,
      level: level ?? this.level,
      levelTitle: levelTitle ?? this.levelTitle,
      badgeIcon: badgeIcon ?? this.badgeIcon,
      badgeColor: badgeColor ?? this.badgeColor,
      lifetimeSpendCoins: lifetimeSpendCoins ?? this.lifetimeSpendCoins,
      nextLevel: nextLevel ?? this.nextLevel,
      nextLevelTitle: nextLevelTitle ?? this.nextLevelTitle,
      nextLevelRequiredSpend: nextLevelRequiredSpend ?? this.nextLevelRequiredSpend,
      remainingSpendToNextLevel: remainingSpendToNextLevel ?? this.remainingSpendToNextLevel,
      progressPercent: progressPercent ?? this.progressPercent,
      hostProfile: hostProfile ?? this.hostProfile,
    );
  }
}

class HostProfile {
  final String? stageName;
  final String? country;
  final String? city;
  final String? bio;
  final String? contactPhone;

  const HostProfile({
    this.stageName,
    this.country,
    this.city,
    this.bio,
    this.contactPhone,
  });

  factory HostProfile.fromJson(Map<String, dynamic> json) => HostProfile(
        stageName: json['stage_name'] as String?,
        country: json['country'] as String?,
        city: json['city'] as String?,
        bio: json['bio'] as String?,
        contactPhone: json['contact_phone'] as String?,
      );

  Map<String, dynamic> toJson() => {
        'stage_name': stageName,
        'country': country,
        'city': city,
        'bio': bio,
        'contact_phone': contactPhone,
      };
}
