class DashboardLeaderboardsDto {
  final List<LeaderboardUserItemDto> usersAlltime;
  final List<LeaderboardUserItemDto> usersWeekly;
  final List<LeaderboardHostItemDto> hostsAlltime;
  final List<LeaderboardHostItemDto> hostsWeekly;
  final List<LeaderboardAgencyItemDto> agenciesAlltime;
  final List<LeaderboardAgencyItemDto> agenciesWeekly;

  const DashboardLeaderboardsDto({
    this.usersAlltime = const [],
    this.usersWeekly = const [],
    this.hostsAlltime = const [],
    this.hostsWeekly = const [],
    this.agenciesAlltime = const [],
    this.agenciesWeekly = const [],
  });

  factory DashboardLeaderboardsDto.fromJson(Map<String, dynamic> json) {
    List<T> parseList<T>(dynamic source, T Function(Map<String, dynamic>) parser) {
      final list = source is List ? source : const [];
      return list
          .whereType<Map>()
          .map((row) => parser(Map<String, dynamic>.from(row)))
          .toList(growable: false);
    }

    dynamic preferList(List<dynamic> sources) {
      for (final source in sources) {
        if (source is List && source.isNotEmpty) {
          return source;
        }
      }
      for (final source in sources) {
        if (source is List) {
          return source;
        }
      }
      return const [];
    }

    return DashboardLeaderboardsDto(
      usersAlltime: parseList(json['users_alltime'], LeaderboardUserItemDto.fromJson),
      usersWeekly: parseList(preferList([json['top_users_weekly'], json['users_weekly']]), LeaderboardUserItemDto.fromJson),
      hostsAlltime: parseList(preferList([json['hosts_alltime']]), LeaderboardHostItemDto.fromJson),
      hostsWeekly: parseList(preferList([json['top_hosts_weekly'], json['hosts_weekly'], json['hosts']]), LeaderboardHostItemDto.fromJson),
      agenciesAlltime: parseList(preferList([json['agencies_alltime']]), LeaderboardAgencyItemDto.fromJson),
      agenciesWeekly: parseList(preferList([json['top_agencies_weekly'], json['agencies_weekly'], json['agencies']]), LeaderboardAgencyItemDto.fromJson),
    );
  }
}

class LeaderboardUserItemDto {
  final int id;
  final String name;
  final String? avatar;
  final int? level;
  final int lifetimeSpendCoins;
  final int giftCoins;
  final int callCoins;
  final int subscriptionCoins;
  final int entryCoins;
  final int totalCoins;
  final int rank;

  const LeaderboardUserItemDto({
    required this.id,
    required this.name,
    required this.lifetimeSpendCoins,
    required this.giftCoins,
    required this.callCoins,
    required this.subscriptionCoins,
    required this.entryCoins,
    required this.totalCoins,
    required this.rank,
    this.avatar,
    this.level,
  });

  factory LeaderboardUserItemDto.fromJson(Map<String, dynamic> json) =>
      LeaderboardUserItemDto(
        id: _asInt([
          json['id'],
          json['user_id'],
        ]),
        name: _asString([
          json['name'],
          json['display_name'],
          json['username'],
        ]),
        avatar: _nullableString([
          json['avatar_url'],
          json['avatar'],
          json['image'],
        ]),
        level: _asNullableInt([
          json['level'],
          json['level_id'],
        ]),
        lifetimeSpendCoins: _asInt([
          json['lifetime_spend_coins'],
          json['lifetime_spend'],
        ]),
        giftCoins: _asInt([
          json['gift_coins'],
        ]),
        callCoins: _asInt([
          json['call_coins'],
          json['video_call_coins'],
        ]),
        subscriptionCoins: _asInt([
          json['subscription_coins'],
        ]),
        entryCoins: _asInt([
          json['entry_coins'],
        ]),
        totalCoins: _asInt([
          json['total_coins'],
          json['lifetime_spend_coins'],
        ]),
        rank: _asInt([
          json['rank'],
        ]),
      );
}

class LeaderboardHostItemDto {
  final int hostId;
  final int hostUserId;
  final String name;
  final String? avatar;
  final int? agencyId;
  final int giftCoins;
  final int callCoins;
  final int totalCoins;
  final int rank;

  const LeaderboardHostItemDto({
    required this.hostId,
    required this.hostUserId,
    required this.name,
    required this.giftCoins,
    required this.callCoins,
    required this.totalCoins,
    required this.rank,
    this.avatar,
    this.agencyId,
  });

  factory LeaderboardHostItemDto.fromJson(Map<String, dynamic> json) =>
      LeaderboardHostItemDto(
        hostId: _asInt([
          json['host_id'],
          json['id'],
        ]),
        hostUserId: _asInt([
          json['host_user_id'],
          json['user_id'],
          json['id'],
        ]),
        name: _asString([
          json['name'],
          json['display_name'],
          json['stage_name'],
          json['username'],
        ]),
        avatar: _nullableString([
          json['avatar_url'],
          json['avatar'],
          json['image'],
        ]),
        agencyId: _asNullableInt([
          json['agency_id'],
        ]),
        giftCoins: _asInt([
          json['gift_coins'],
          json['video_gifts'],
        ]),
        callCoins: _asInt([
          json['call_coins'],
          json['video_call_coins'],
        ]),
        totalCoins: _asInt([
          json['total_coins'],
          json['gift_coins'],
        ]),
        rank: _asInt([
          json['rank'],
        ]),
      );
}

class LeaderboardAgencyItemDto {
  final int agencyId;
  final String name;
  final int giftCoins;
  final int callCoins;
  final int totalCoins;
  final int rank;

  const LeaderboardAgencyItemDto({
    required this.agencyId,
    required this.name,
    required this.giftCoins,
    required this.callCoins,
    required this.totalCoins,
    required this.rank,
  });

  factory LeaderboardAgencyItemDto.fromJson(Map<String, dynamic> json) =>
      LeaderboardAgencyItemDto(
        agencyId: _asInt([
          json['agency_id'],
          json['id'],
        ]),
        name: _asString([
          json['name'],
          json['agency_name'],
        ]),
        giftCoins: _asInt([
          json['gift_coins'],
        ]),
        callCoins: _asInt([
          json['call_coins'],
          json['video_call_coins'],
        ]),
        totalCoins: _asInt([
          json['total_coins'],
          json['gift_coins'],
        ]),
        rank: _asInt([
          json['rank'],
        ]),
      );
}

int _asInt(List<dynamic> values) {
  for (final value in values) {
    if (value is int) return value;
    if (value is num) return value.toInt();
    if (value == null) continue;
    final parsed = int.tryParse(value.toString());
    if (parsed != null) return parsed;
  }
  return 0;
}

int? _asNullableInt(List<dynamic> values) {
  for (final value in values) {
    if (value is int) return value;
    if (value is num) return value.toInt();
    if (value == null) continue;
    final parsed = int.tryParse(value.toString());
    if (parsed != null) return parsed;
  }
  return null;
}

String _asString(List<dynamic> values) {
  for (final value in values) {
    final text = value?.toString().trim() ?? '';
    if (text.isNotEmpty) return text;
  }
  return '';
}

String? _nullableString(List<dynamic> values) {
  for (final value in values) {
    final text = value?.toString().trim() ?? '';
    if (text.isNotEmpty && text.toLowerCase() != 'null') return text;
  }
  return null;
}
