import 'package:flutter_test/flutter_test.dart';
import 'package:gd_live/modules/profile/models/host_earnings_report_dto.dart';

void main() {
  test('grand total counts room, PK, and video-call coins once', () {
    final summary = HostEarningsSummaryDto.fromJson({
      'total_video_room_minutes': 1254,
      'total_gifted_coins': 86501,
      'total_room_gifts_coins': 81001,
      'video_room_gifts_coins': 81001,
      'video_room_gift_earnings': 81001,
      'video_call_minutes': 93,
      'video_call_earnings': 18600,
      'pk_room_count': 26,
      'pk_gift_coins': 5500,
      'pk_earnings': 5500,
    });

    expect(summary.grandTotalCoins, 105101);
  });
}
