import 'dart:async';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:gd_live/modules/subscriptions/controllers/viewer_gate_controller.dart';
import 'package:url_launcher/url_launcher.dart';

import '../../../../app/brand/brand.dart';
import '../../../../app/widgets/app_avatar.dart';
import '../../../../app/widgets/coin_lottie.dart';
import '../../../../app/widgets/gd_live_logo.dart';
import '../../../../app/widgets/gd_modal_surface.dart';
import '../../../../services/app_settings_service.dart';
import '../../Live/services/live_service.dart';
import '../../calls/controllers/call_controller.dart';
import '../../banners/models/banner_item.dart';
import '../../banners/services/banner_service.dart';
import '../controllers/live_room_controller.dart';
import '../models/live_room_dto.dart';

class RoomsPage extends StatelessWidget {
  final double bottomPadding;
  const RoomsPage({super.key, required this.bottomPadding});

  Future<void> _handleBlockedJoin(
    BuildContext context, {
    required LiveService live,
    required LiveRoomModel room,
    required String message,
  }) async {
    if (room.hostId == null) {
      Get.snackbar(
        'Unable to join room',
        message,
        snackPosition: SnackPosition.BOTTOM,
      );
      return;
    }
    var hasPendingRequest = false;
    try {
      final rows = await live.fetchMyUnblockRequests(
        hostUserId: room.hostId as int,
        status: 'pending',
      );
      hasPendingRequest = rows.isNotEmpty;
    } catch (_) {}
    final controller = TextEditingController();
    final request = await showDialog<bool>(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: const Text('Blocked by host'),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              GdLottie(
                asset:
                    hasPendingRequest
                        ? GdLottieAssets.privateRequest
                        : GdLottieAssets.publicRequest,
                width: 92,
                height: 92,
              ),
              const SizedBox(height: 12),
              Text(message),
              if (hasPendingRequest) ...[
                const SizedBox(height: 12),
                const Text('Your unblock request is already pending review.'),
              ] else ...[
                const SizedBox(height: 12),
                TextField(
                  controller: controller,
                  minLines: 2,
                  maxLines: 4,
                  decoration: const InputDecoration(
                    labelText: 'Request unblock message',
                    hintText: 'Add a short note for the host',
                  ),
                ),
              ],
            ],
          ),
          actions: [
            TextButton(
              onPressed: () => Navigator.of(context).pop(false),
              child: const Text('Close'),
            ),
            if (!hasPendingRequest)
              FilledButton(
                onPressed: () => Navigator.of(context).pop(true),
                child: const Text('Request Unblock'),
              ),
          ],
        );
      },
    );
    if (request == true) {
      try {
        await live.requestUnblock(
          hostUserId: room.hostId as int,
          message: controller.text.trim(),
        );
        Get.snackbar(
          'Moderation',
          'Unblock request submitted.',
          snackPosition: SnackPosition.BOTTOM,
        );
      } catch (e) {
        Get.snackbar(
          'Moderation',
          e.toString().replaceFirst('Exception: ', ''),
          snackPosition: SnackPosition.BOTTOM,
        );
      }
    }
    controller.dispose();
  }

  Future<void> _joinRoom(
    BuildContext context, {
    required ViewerGateController gate,
    required AppCallController callController,
    required LiveService live,
    required LiveRoomsController ctrl,
    required LiveRoomModel room,
  }) async {
    Future<void> attemptJoin() async {
      final joined = await live.join(room.id, role: 'viewer');
      await Get.toNamed(
        '/live/video',
        arguments: {'room': joined, 'viewer_only': true},
      );
    }

    try {
      final hasActiveSubscription = await gate.hasActiveSubscription();
      if (hasActiveSubscription) {
        await attemptJoin();
      } else {
        await _showSubscriptionOrCallDialog(
          context,
          gate: gate,
          callController: callController,
          room: room,
          onSubscribed: attemptJoin,
        );
      }
    } catch (e) {
      final message = e.toString().replaceFirst('Exception: ', '');
      final normalized = message.toLowerCase();

      if (message == 'room_full') {
        await ctrl.refreshRooms();
        Get.snackbar(
          'Room full',
          'This room is full right now.',
          snackPosition: SnackPosition.BOTTOM,
          duration: const Duration(seconds: 3),
        );
        return;
      }

      if (normalized.contains('blocked by this host')) {
        await _handleBlockedJoin(
          context,
          live: live,
          room: room,
          message: message,
        );
        return;
      }

      if (message == 'room_not_found' || message == 'room_not_joinable') {
        await ctrl.refreshRooms();
      }

      Get.snackbar(
        'Unable to join room',
        message == 'room_not_found'
            ? 'This room is no longer live.'
            : message == 'room_not_joinable'
            ? 'This room is no longer joinable.'
            : message,
        snackPosition: SnackPosition.BOTTOM,
        duration: const Duration(seconds: 3),
      );
    }
  }

  Future<void> _showSubscriptionOrCallDialog(
    BuildContext context, {
    required ViewerGateController gate,
    required AppCallController callController,
    required LiveRoomModel room,
    required Future<void> Function() onSubscribed,
  }) async {
    final tokens = getBrandTokens(Get.find<AppSettingsService>().brandKey);
    await showDialog<void>(
      context: context,
      barrierDismissible: true,
      builder: (dialogContext) {
        return Dialog(
          backgroundColor: Colors.transparent,
          insetPadding: const EdgeInsets.symmetric(horizontal: 18, vertical: 24),
          child: GdModalSurface(
            tokens: tokens,
            scrollable: true,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                const GdLiveLogo(size: 52, showWordmark: false),
                const SizedBox(height: 12),
                Text(
                  'Choose how to continue',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: tokens.textPrimary,
                    fontSize: 20,
                    fontWeight: FontWeight.w900,
                  ),
                ),
                const SizedBox(height: 6),
                Text(
                  'Pick a simple path into ${room.hostName ?? 'this live room'}.',
                  textAlign: TextAlign.center,
                  style: TextStyle(
                    color: tokens.textSecondary,
                    height: 1.35,
                    fontWeight: FontWeight.w600,
                    fontSize: 13.5,
                  ),
                ),
                const SizedBox(height: 16),
                _ContinueOptionTile(
                  title: 'Buy Subscription',
                  subtitle: 'Unlock room access.',
                  icon: Icons.workspace_premium_rounded,
                  accent: tokens.primaryButtonGradient.first,
                  onTap: () async {
                    Navigator.of(dialogContext).pop();
                    await gate.ensureAccessThen(onGranted: onSubscribed);
                  },
                ),
                const SizedBox(height: 12),
                _ContinueOptionTile(
                  title: 'Request Video Call',
                  subtitle: 'Ask the host for a private call.',
                  icon: Icons.videocam_rounded,
                  accent: tokens.primaryButtonGradient.last,
                  onTap: () async {
                    Navigator.of(dialogContext).pop();
                    await Future<void>.delayed(const Duration(milliseconds: 120));
                    await callController.placeCallFromLiveRoom(
                      roomId: room.id,
                      type: 'video',
                    );
                  },
                ),
                const SizedBox(height: 10),
                TextButton(
                  onPressed: () => Navigator.of(dialogContext).pop(),
                  style: TextButton.styleFrom(
                    foregroundColor: tokens.textSecondary,
                    padding: const EdgeInsets.symmetric(
                      horizontal: 12,
                      vertical: 8,
                    ),
                  ),
                  child: const Text(
                    'Maybe later',
                    style: TextStyle(fontWeight: FontWeight.w700),
                  ),
                ),
              ],
            ),
          ),
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final ctrl = Get.find<LiveRoomsController>();
    final live = Get.find<LiveService>();
    final gate = Get.find<ViewerGateController>();
    final callController = Get.find<AppCallController>();

    return Obx(() {
      final tokens = getBrandTokens(Get.find<AppSettingsService>().brandKey);
      final rooms = ctrl.liveRooms.where((room) => room.isVideoRoom).toList();

      return RefreshIndicator(
        onRefresh: ctrl.refreshRooms,
        color: tokens.primaryButtonGradient.first,
        backgroundColor: tokens.cardGradient.first,
        child: LayoutBuilder(
          builder: (_, constraints) {
            final width = constraints.maxWidth;
            final crossAxisCount =
                width >= 1180
                    ? 4
                    : width >= 820
                    ? 3
                    : 2;

            return CustomScrollView(
              physics: const BouncingScrollPhysics(
                parent: AlwaysScrollableScrollPhysics(),
              ),
              slivers: [
                SliverToBoxAdapter(
                  child: _VideoBannerStrip(
                    placement: 'video_rooms',
                    tokens: tokens,
                  ),
                ),
                if (ctrl.loading.value && rooms.isEmpty)
                  SliverPadding(
                    padding: EdgeInsets.fromLTRB(16, 0, 16, bottomPadding),
                    sliver: SliverGrid(
                      delegate: SliverChildBuilderDelegate(
                        (_, index) => Transform.translate(
                          offset: Offset(
                            0,
                            _videoTileTopOffset(index, crossAxisCount),
                          ),
                          child: _CompactSkeletonTile(tokens: tokens),
                        ),
                        childCount: crossAxisCount * 4,
                      ),
                      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: crossAxisCount,
                        mainAxisSpacing: 0,
                        crossAxisSpacing: 0,
                        childAspectRatio: .78,
                      ),
                    ),
                  )
                else if (rooms.isEmpty)
                  SliverFillRemaining(
                    hasScrollBody: false,
                    child: _CompactEmptyState(
                      title: 'No video rooms right now',
                      message: 'Pull to refresh when hosts go live again.',
                      icon: Icons.ondemand_video_rounded,
                      tokens: tokens,
                    ),
                  )
                else
                  SliverPadding(
                    padding: EdgeInsets.fromLTRB(16, 0, 16, bottomPadding),
                    sliver: SliverGrid(
                      delegate: SliverChildBuilderDelegate((_, index) {
                        final room = rooms[index];
                        return Transform.translate(
                          offset: Offset(
                            0,
                            _videoTileTopOffset(index, crossAxisCount),
                          ),
                          child: _CompactVideoRoomTile(
                            room: room,
                            tokens: tokens,
                            onTap:
                                () => _joinRoom(
                                  context,
                                  gate: gate,
                                  callController: callController,
                                  live: live,
                                  ctrl: ctrl,
                                  room: room,
                                ),
                          ),
                        );
                      }, childCount: rooms.length),
                      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
                        crossAxisCount: crossAxisCount,
                        mainAxisSpacing: 0,
                        crossAxisSpacing: 0,
                        childAspectRatio: .78,
                      ),
                    ),
                  ),
              ],
            );
          },
        ),
      );
    });
  }
}

class _VideoBannerStrip extends StatefulWidget {
  const _VideoBannerStrip({required this.placement, required this.tokens});

  final String placement;
  final BrandTokens tokens;

  @override
  State<_VideoBannerStrip> createState() => _VideoBannerStripState();
}

class _VideoBannerStripState extends State<_VideoBannerStrip>
    with SingleTickerProviderStateMixin {
  static const EdgeInsets _outerPadding = EdgeInsets.fromLTRB(6, 8, 6, 4);
  static const double _bannerHeight = 208;

  late final PageController _pc = PageController(viewportFraction: 1);
  late final AnimationController _flow;
  Timer? _ticker;
  int _index = 0;
  bool _loading = true;
  List<BannerItem> _banners = const <BannerItem>[];
  final Set<int> _impressed = <int>{};

  String _shortTitle(String value) {
    final t = value.trim();
    if (t.length <= 24) return t;
    return '${t.substring(0, 24).trimRight()}…';
  }

  @override
  void initState() {
    super.initState();
    _flow = AnimationController(
      vsync: this,
      duration: const Duration(seconds: 7),
    )..repeat();
    _loadBanners();
    _startTicker();
  }

  @override
  void dispose() {
    _ticker?.cancel();
    _flow.dispose();
    _pc.dispose();
    super.dispose();
  }

  void _startTicker() {
    _ticker?.cancel();
    _ticker = Timer.periodic(const Duration(seconds: 4), (_) {
      if (!mounted || !_pc.hasClients || _banners.length < 2) return;
      _index = (_index + 1) % _banners.length;
      _pc.animateToPage(
        _index,
        duration: const Duration(milliseconds: 420),
        curve: Curves.easeOutCubic,
      );
    });
  }

  Future<void> _loadBanners() async {
    final service = Get.find<BannerService>();
    final remote = await service.fetchBanners(
      placement: widget.placement,
      forceRefresh: true,
    );
    if (!mounted) return;

    setState(() {
      _loading = false;
      _banners = remote;
      _index = 0;
      _impressed.clear();
    });
    _startTicker();
    if (remote.isNotEmpty) {
      await _trackImpression(0);
    }
  }

  Future<void> _trackImpression(int index) async {
    if (index < 0 || index >= _banners.length) return;
    final b = _banners[index];
    if (b.id <= 0 || _impressed.contains(b.id)) return;
    _impressed.add(b.id);
    await Get.find<BannerService>().trackImpression(
      bannerId: b.id,
      placement: widget.placement,
      context: {'screen': widget.placement, 'slot': 'top_carousel'},
    );
  }

  Future<void> _onBannerTap(BannerItem banner) async {
    final actionType = banner.actionType.trim().toLowerCase();
    final actionValue = banner.actionValue?.trim();

    if (banner.id > 0) {
      await Get.find<BannerService>().trackClick(
        bannerId: banner.id,
        placement: widget.placement,
        context: {'screen': widget.placement, 'slot': 'top_carousel'},
      );
    }

    if (actionType == 'none' || actionValue == null || actionValue.isEmpty) {
      return;
    }

    try {
      if (actionType == 'url') {
        final uri = Uri.tryParse(actionValue);
        if (uri == null) return;
        await launchUrl(uri, mode: LaunchMode.externalApplication);
        return;
      }
      if (actionType == 'deeplink') {
        if (actionValue.startsWith('/')) {
          await Get.toNamed(actionValue);
          return;
        }
        final uri = Uri.tryParse(actionValue);
        if (uri == null) return;
        await launchUrl(uri, mode: LaunchMode.externalApplication);
        return;
      }
      if (actionType == 'route') {
        await Get.toNamed(actionValue);
      }
    } catch (_) {}
  }

  @override
  Widget build(BuildContext context) {
    final banners = _banners;
    final tokens = widget.tokens;
    if (banners.isEmpty) {
      return const SizedBox.shrink();
    }

    return Padding(
      padding: _outerPadding,
      child: SizedBox(
        height: _bannerHeight,
        child: Stack(
          children: [
            PageView.builder(
              controller: _pc,
              onPageChanged: (v) async {
                setState(() => _index = v);
                await _trackImpression(v);
              },
              itemCount: banners.length,
              itemBuilder:
                  (_, i) => InkWell(
                    borderRadius: BorderRadius.circular(18),
                    onTap: () => _onBannerTap(banners[i]),
                    child: Stack(
                      fit: StackFit.expand,
                      children: [
                        if (banners[i].hasImage)
                          ClipRRect(
                            borderRadius: BorderRadius.circular(18),
                            child: Container(
                              color: Colors.black.withOpacity(.18),
                              alignment: Alignment.center,
                              child: Image.network(
                                banners[i].imageUrl,
                                fit: BoxFit.contain,
                                errorBuilder:
                                    (_, __, ___) => const SizedBox.shrink(),
                              ),
                            ),
                          ),
                        Container(
                          decoration: BoxDecoration(
                            borderRadius: BorderRadius.circular(18),
                            gradient: LinearGradient(
                              begin: Alignment.topCenter,
                              end: Alignment.bottomCenter,
                              colors:
                                  banners[i].hasImage
                                      ? const [
                                        Color(0x2B06040C),
                                        Color(0x8A151020),
                                        Color(0xCC1E1731),
                                      ]
                                      : [
                                        tokens.cardGradient.first,
                                        tokens.cardGradient.last,
                                        tokens.primaryButtonGradient.last
                                            .withOpacity(.78),
                                      ],
                            ),
                            border: Border.all(
                              color: tokens.borderColor.withOpacity(.46),
                            ),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withOpacity(.28),
                                blurRadius: 14,
                                offset: const Offset(0, 6),
                              ),
                            ],
                          ),
                        ),
                        Positioned.fill(
                          child: IgnorePointer(
                            child: AnimatedBuilder(
                              animation: _flow,
                              builder: (_, __) {
                                final x = (_flow.value * 2) - 1;
                                return Transform.translate(
                                  offset: Offset(x * 26, 0),
                                  child: DecoratedBox(
                                    decoration: BoxDecoration(
                                      gradient: LinearGradient(
                                        begin: Alignment.topLeft,
                                        end: Alignment.bottomRight,
                                        colors: [
                                          Colors.transparent,
                                          Colors.white.withOpacity(.04),
                                          Colors.transparent,
                                        ],
                                        stops: const [0.3, 0.5, 0.7],
                                      ),
                                    ),
                                  ),
                                );
                              },
                            ),
                          ),
                        ),
                        Padding(
                          padding: const EdgeInsets.symmetric(
                            horizontal: 14,
                            vertical: 12,
                          ),
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  mainAxisAlignment: MainAxisAlignment.start,
                                  children: [
                                    Text(
                                      _shortTitle(banners[i].title),
                                      maxLines: 1,
                                      overflow: TextOverflow.ellipsis,
                                      style: const TextStyle(
                                        color: Colors.white,
                                        fontWeight: FontWeight.w800,
                                        fontSize: 18,
                                      ),
                                    ),
                                    if (banners[i].buttonText
                                            ?.trim()
                                            .isNotEmpty ??
                                        false)
                                      Padding(
                                        padding: const EdgeInsets.only(top: 2),
                                        child: Text(
                                          banners[i].buttonText!,
                                          maxLines: 1,
                                          overflow: TextOverflow.ellipsis,
                                          style: TextStyle(
                                            color: tokens.textSecondary
                                                .withOpacity(.84),
                                            fontWeight: FontWeight.w600,
                                            fontSize: 11.5,
                                          ),
                                        ),
                                      ),
                                  ],
                                ),
                              ),
                              if ((banners[i].actionType.toLowerCase() !=
                                      'none') &&
                                  (banners[i].actionValue?.trim().isNotEmpty ??
                                      false))
                                Container(
                                  width: 30,
                                  height: 30,
                                  decoration: BoxDecoration(
                                    color: tokens.glassColor.withOpacity(.18),
                                    borderRadius: BorderRadius.circular(8),
                                  ),
                                  child: const Icon(
                                    Icons.arrow_outward_rounded,
                                    color: Colors.white,
                                    size: 15,
                                  ),
                                ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
            ),
            if (banners.length > 1)
              Positioned(
                left: 0,
                right: 0,
                bottom: 6,
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: List.generate(
                    banners.length,
                    (i) => AnimatedContainer(
                      duration: const Duration(milliseconds: 220),
                      margin: const EdgeInsets.symmetric(horizontal: 3),
                      width: i == _index ? 14 : 6,
                      height: 6,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(999),
                        color:
                            i == _index
                                ? tokens.textPrimary.withOpacity(.90)
                                : tokens.textSecondary.withOpacity(.40),
                      ),
                    ),
                  ),
                ),
              ),
          ],
        ),
      ),
    );
  }
}

class _CompactVideoRoomTile extends StatelessWidget {
  const _CompactVideoRoomTile({
    required this.room,
    required this.tokens,
    required this.onTap,
  });

  final LiveRoomModel room;
  final BrandTokens tokens;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final imageUrl = room.thumbnail;
    final hostName =
        room.hostName?.trim().isNotEmpty == true
            ? room.hostName!.trim()
            : 'Host';

    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(14),
      child: Ink(
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(14),
          gradient: LinearGradient(
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              tokens.cardGradient.first.withOpacity(.94),
              tokens.cardGradient.last.withOpacity(.98),
            ],
          ),
          border: Border.all(color: tokens.borderColor.withOpacity(.20)),
        ),
        child: Stack(
          fit: StackFit.expand,
          children: [
            if (imageUrl != null && imageUrl.isNotEmpty)
              ClipRRect(
                borderRadius: BorderRadius.circular(14),
                child: Image.network(
                  imageUrl,
                  fit: BoxFit.cover,
                  errorBuilder:
                      (_, __, ___) => _CompactTileFallback(tokens: tokens),
                ),
              )
            else
              ClipRRect(
                borderRadius: BorderRadius.circular(14),
                child: _CompactTileFallback(tokens: tokens),
              ),
            DecoratedBox(
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(14),
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [
                    Colors.black.withOpacity(.04),
                    Colors.black.withOpacity(.18),
                    Colors.black.withOpacity(.70),
                  ],
                ),
              ),
            ),
            Positioned(
              top: 10,
              left: 10,
              right: 10,
              child: Row(
                children: [
                  Container(
                    padding: const EdgeInsets.symmetric(
                      horizontal: 8,
                      vertical: 5,
                    ),
                    decoration: BoxDecoration(
                      color: const Color(0xFFFF385C),
                      borderRadius: BorderRadius.circular(999),
                    ),
                    child: const Text(
                      'LIVE',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 10,
                        fontWeight: FontWeight.w900,
                        letterSpacing: .7,
                      ),
                    ),
                  ),
                  const Spacer(),
                  _CompactCornerPill(
                    label: _compactCount(room.liveAudience),
                    icon: Icons.visibility_rounded,
                  ),
                ],
              ),
            ),
            Positioned(
              left: 10,
              right: 10,
              bottom: 10,
              child: Row(
                children: [
                  _CompactAvatar(name: hostName, imageUrl: room.thumbnail),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      hostName,
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: TextStyle(
                        color: Colors.white.withOpacity(.92),
                        fontWeight: FontWeight.w700,
                        fontSize: 12,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _CompactAvatar extends StatelessWidget {
  const _CompactAvatar({required this.name, required this.imageUrl});

  final String name;
  final String? imageUrl;
  @override
  Widget build(BuildContext context) {
    final first = name.isEmpty ? 'H' : name.characters.first.toUpperCase();
    return SizedBox(
      width: 28,
      height: 28,
      child: AppAvatar(
        avatarUrl: imageUrl,
        label: first,
        size: 28,
        backgroundColor: Colors.white.withOpacity(.18),
      ),
    );
  }
}

class _CompactCornerPill extends StatelessWidget {
  const _CompactCornerPill({required this.label, required this.icon});

  final String label;
  final IconData icon;

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 5),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(.12),
        borderRadius: BorderRadius.circular(999),
        border: Border.all(color: Colors.white.withOpacity(.08)),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(icon, color: Colors.white, size: 12),
          const SizedBox(width: 4),
          Text(
            label,
            style: const TextStyle(
              color: Colors.white,
              fontSize: 11,
              fontWeight: FontWeight.w800,
            ),
          ),
        ],
      ),
    );
  }
}

class _CompactTileFallback extends StatelessWidget {
  const _CompactTileFallback({required this.tokens});

  final BrandTokens tokens;

  @override
  Widget build(BuildContext context) {
    return DecoratedBox(
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            tokens.primaryButtonGradient.first,
            tokens.cardGradient.last,
          ],
        ),
      ),
      child: const Center(
        child: Icon(Icons.videocam_rounded, color: Colors.white, size: 32),
      ),
    );
  }
}

class _CompactSkeletonTile extends StatelessWidget {
  const _CompactSkeletonTile({required this.tokens});

  final BrandTokens tokens;

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(20),
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            tokens.cardGradient.first.withOpacity(.40),
            tokens.cardGradient.last.withOpacity(.24),
          ],
        ),
        border: Border.all(color: tokens.borderColor.withOpacity(.16)),
      ),
    );
  }
}

class _CompactEmptyState extends StatelessWidget {
  const _CompactEmptyState({
    required this.title,
    required this.message,
    required this.icon,
    required this.tokens,
    this.animationAsset = GdLottieAssets.live,
  });

  final String title;
  final String message;
  final IconData icon;
  final BrandTokens tokens;
  final String animationAsset;

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            animationAsset.isNotEmpty
                ? GdLottie(asset: animationAsset, width: 96, height: 96)
                : Icon(icon, color: tokens.textSecondary, size: 34),
            const SizedBox(height: 12),
            Text(
              title,
              style: TextStyle(
                color: tokens.textPrimary,
                fontWeight: FontWeight.w900,
                fontSize: 18,
              ),
            ),
            const SizedBox(height: 6),
            Text(
              message,
              textAlign: TextAlign.center,
              style: TextStyle(
                color: tokens.textSecondary,
                fontWeight: FontWeight.w600,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _ContinueOptionTile extends StatelessWidget {
  const _ContinueOptionTile({
    required this.title,
    required this.subtitle,
    required this.icon,
    required this.accent,
    required this.onTap,
  });

  final String title;
  final String subtitle;
  final IconData icon;
  final Color accent;
  final VoidCallback onTap;

  @override
  Widget build(BuildContext context) {
    final tokens = getBrandTokens(Get.find<AppSettingsService>().brandKey);
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(18),
        child: Ink(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(18),
            color: Colors.white,
            border: Border.all(color: tokens.borderColor.withOpacity(.34)),
          ),
          child: Row(
            children: [
              Container(
                width: 46,
                height: 46,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(15),
                  color: accent.withOpacity(.10),
                  border: Border.all(color: accent.withOpacity(.18)),
                ),
                child: Icon(icon, color: accent, size: 21),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: TextStyle(
                        color: tokens.textPrimary,
                        fontSize: 15,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                    const SizedBox(height: 3),
                    Text(
                      subtitle,
                      style: TextStyle(
                        color: tokens.textSecondary,
                        height: 1.3,
                        fontSize: 12.5,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

String _compactCount(int value) {
  if (value >= 1000000) return '${(value / 1000000).toStringAsFixed(1)}M';
  if (value >= 1000) return '${(value / 1000).toStringAsFixed(1)}K';
  return '$value';
}

double _videoTileTopOffset(int index, int crossAxisCount) {
  final column = index % crossAxisCount;
  if (crossAxisCount <= 2) {
    return column.isOdd ? 18 : 0;
  }
  if (crossAxisCount == 3) {
    if (column == 1) return 20;
    if (column == 2) return 10;
    return 0;
  }
  if (column == 1 || column == 3) return 18;
  if (column == 2) return 10;
  return 0;
}
