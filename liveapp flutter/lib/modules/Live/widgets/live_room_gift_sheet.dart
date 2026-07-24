import 'package:flutter/material.dart';
import 'dart:math' as math;

import '../../../app/widgets/gd_live_logo.dart';
import '../../../app/widgets/gd_modal_surface.dart';
import '../../../app/widgets/coin_lottie.dart';
import '../../../app/widgets/remote_media_art.dart';
import '../../../app/brand/brand.dart';
import '../models/live_gift_item.dart';

class LiveRoomGiftSelection {
  final LiveGiftItem gift;
  final int quantity;

  const LiveRoomGiftSelection({required this.gift, required this.quantity});
}

class LiveRoomGiftSheet extends StatefulWidget {
  const LiveRoomGiftSheet({super.key, required this.gifts, this.balanceCoins});

  final List<LiveGiftItem> gifts;
  final int? balanceCoins;

  static Future<LiveRoomGiftSelection?> show(
    BuildContext context, {
    required List<LiveGiftItem> gifts,
    int? balanceCoins,
  }) {
    final tokens = getBrandTokens('midnight');
    return showModalBottomSheet<LiveRoomGiftSelection>(
      context: context,
      isScrollControlled: true,
      backgroundColor: tokens.backgroundGradient.first,
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
      ),
      builder:
          (_) => LiveRoomGiftSheet(gifts: gifts, balanceCoins: balanceCoins),
    );
  }

  @override
  State<LiveRoomGiftSheet> createState() => _LiveRoomGiftSheetState();
}

class _LiveRoomGiftSheetState extends State<LiveRoomGiftSheet> {
  LiveGiftItem? _selected;
  int _quantity = 1;

  @override
  void initState() {
    super.initState();
    if (widget.gifts.isNotEmpty) {
      _selected = widget.gifts.first;
    }
  }

  @override
  Widget build(BuildContext context) {
    final bottom = MediaQuery.of(context).viewInsets.bottom;
    final selected = _selected;
    final total = selected == null ? 0 : selected.coins * _quantity;
    final balanceCoins = widget.balanceCoins;
    final tokens = getBrandTokens('midnight');
    final screenHeight = MediaQuery.sizeOf(context).height;
    final sheetHeight = math.min(screenHeight * 0.84, 720.0);

    return SafeArea(
      top: false,
      child: Padding(
        padding: EdgeInsets.fromLTRB(12, 0, 12, 12 + bottom),
        child: GdModalSurface(
          tokens: tokens,
          radius: 30,
          padding: const EdgeInsets.fromLTRB(18, 12, 18, 18),
          child: SizedBox(
            height: sheetHeight,
            child: Column(
              children: [
                const GdLiveLogo(size: 48, showWordmark: false),
                const SizedBox(height: 12),
                if (balanceCoins != null)
                  Align(
                    alignment: Alignment.centerRight,
                    child: Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 10,
                        vertical: 7,
                      ),
                      decoration: BoxDecoration(
                        color: tokens.chipColor.withOpacity(.72),
                        borderRadius: BorderRadius.circular(999),
                        border: Border.all(
                          color: tokens.borderColor.withOpacity(.25),
                        ),
                      ),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          const CoinLottie(size: 18),
                          const SizedBox(width: 6),
                          Text(
                            _formatCoins(balanceCoins),
                            style: TextStyle(
                              color: tokens.textPrimary,
                              fontSize: 12,
                              fontWeight: FontWeight.w800,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                const SizedBox(height: 14),
                Expanded(
                  child: GridView.builder(
                    padding: EdgeInsets.zero,
                    itemCount: widget.gifts.length,
                    gridDelegate:
                        const SliverGridDelegateWithFixedCrossAxisCount(
                          crossAxisCount: 2,
                          crossAxisSpacing: 12,
                          mainAxisSpacing: 12,
                          childAspectRatio: 1.08,
                        ),
                    itemBuilder: (_, index) {
                      final gift = widget.gifts[index];
                      final selectedCard = selected?.id == gift.id;
                      return InkWell(
                        onTap: () => setState(() => _selected = gift),
                        onLongPress: () => _showGiftPreview(context, gift),
                        borderRadius: BorderRadius.circular(18),
                        child: Container(
                          decoration: BoxDecoration(
                            gradient: LinearGradient(
                              colors:
                                  selectedCard
                                      ? [
                                        tokens.primaryButtonGradient.first
                                            .withOpacity(.16),
                                        tokens.primaryButtonGradient.last
                                            .withOpacity(.08),
                                      ]
                                      : [
                                        Colors.white,
                                        const Color(0xFFF7FBF8),
                                      ],
                              begin: Alignment.topCenter,
                              end: Alignment.bottomCenter,
                            ),
                            borderRadius: BorderRadius.circular(18),
                            border: Border.all(
                              color:
                                  selectedCard
                                      ? tokens.primaryButtonGradient.first
                                          .withOpacity(.34)
                                      : tokens.borderColor.withOpacity(.24),
                              width: selectedCard ? 1.2 : 1,
                            ),
                          ),
                          child: Stack(
                            children: [
                              Positioned.fill(
                                child:
                                    gift.giftUrl != null &&
                                            gift.giftUrl!.isNotEmpty
                                        ? ClipRRect(
                                          borderRadius: BorderRadius.circular(
                                            17,
                                          ),
                                          child: RemoteMediaArt(
                                            url: gift.giftUrl!,
                                            explicitType: gift.giftType,
                                            width: double.infinity,
                                            height: double.infinity,
                                            fit: BoxFit.contain,
                                            enableAudio: false,
                                            fallback: Icon(
                                              Icons.card_giftcard_rounded,
                                              color: tokens.textSecondary,
                                              size: 52,
                                            ),
                                          ),
                                        )
                                        : Icon(
                                          Icons.card_giftcard_rounded,
                                          color: tokens.textSecondary,
                                          size: 52,
                                        ),
                              ),
                              Positioned(
                                top: 8,
                                right: 8,
                                child:
                                    selectedCard
                                        ? Container(
                                          width: 22,
                                          height: 22,
                                          decoration: BoxDecoration(
                                            color: Colors.white.withOpacity(.9),
                                            shape: BoxShape.circle,
                                          ),
                                          child: Icon(
                                            Icons.check_circle_rounded,
                                            color:
                                                tokens.primaryButtonGradient
                                                    .first,
                                            size: 18,
                                          ),
                                        )
                                        : const SizedBox.shrink(),
                              ),
                              Positioned(
                                left: 10,
                                right: 10,
                                bottom: 10,
                                child: Container(
                                  padding: const EdgeInsets.symmetric(
                                    horizontal: 10,
                                    vertical: 8,
                                  ),
                                  decoration: BoxDecoration(
                                    color: Colors.white.withOpacity(.9),
                                    borderRadius: BorderRadius.circular(14),
                                    border: Border.all(
                                      color: tokens.borderColor.withOpacity(.18),
                                    ),
                                  ),
                                  child: Column(
                                    mainAxisSize: MainAxisSize.min,
                                    crossAxisAlignment:
                                        CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        gift.name,
                                        maxLines: 1,
                                        overflow: TextOverflow.ellipsis,
                                        style: TextStyle(
                                          color: tokens.textPrimary,
                                          fontSize: 12,
                                          fontWeight: FontWeight.w800,
                                        ),
                                      ),
                                      const SizedBox(height: 2),
                                      Row(
                                        children: [
                                          const CoinLottie(size: 14),
                                          const SizedBox(width: 4),
                                          Text(
                                            '${gift.coins}',
                                            style: TextStyle(
                                              color: tokens.textSecondary,
                                              fontSize: 11,
                                              fontWeight: FontWeight.w700,
                                            ),
                                          ),
                                        ],
                                      ),
                                    ],
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      );
                    },
                  ),
                ),
                const SizedBox(height: 14),
                Row(
                  children: [
                    Container(
                      decoration: BoxDecoration(
                        color: tokens.chipColor.withOpacity(.72),
                        borderRadius: BorderRadius.circular(16),
                        border: Border.all(
                          color: tokens.borderColor.withOpacity(.24),
                        ),
                      ),
                      child: Row(
                        children: [
                          IconButton(
                            onPressed:
                                _quantity > 1
                                    ? () => setState(() => _quantity -= 1)
                                    : null,
                            icon: Icon(
                              Icons.remove_rounded,
                              color: tokens.textPrimary,
                            ),
                          ),
                          Text(
                            'x$_quantity',
                            style: TextStyle(
                              color: tokens.textPrimary,
                              fontWeight: FontWeight.w700,
                            ),
                          ),
                          IconButton(
                            onPressed:
                                _quantity < 99
                                    ? () => setState(() => _quantity += 1)
                                    : null,
                            icon: Icon(
                              Icons.add_rounded,
                              color: tokens.textPrimary,
                            ),
                          ),
                        ],
                      ),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: FilledButton.icon(
                        onPressed:
                            selected == null
                                ? null
                                : () => Navigator.of(context).pop(
                                  LiveRoomGiftSelection(
                                    gift: selected,
                                    quantity: _quantity,
                                  ),
                                ),
                        style: FilledButton.styleFrom(
                          backgroundColor: tokens.primaryButtonGradient.first,
                          foregroundColor: Colors.white,
                          padding: const EdgeInsets.symmetric(vertical: 15),
                          shape: RoundedRectangleBorder(
                            borderRadius: BorderRadius.circular(16),
                          ),
                        ),
                        icon: const Icon(Icons.redeem_rounded),
                        label: Text('Send • $total'),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}

Future<void> _showGiftPreview(BuildContext context, LiveGiftItem gift) {
  final tokens = getBrandTokens('midnight');
  return showDialog<void>(
    context: context,
    barrierColor: Colors.black.withOpacity(.56),
    builder:
        (_) => Dialog(
          backgroundColor: Colors.transparent,
          insetPadding: const EdgeInsets.symmetric(horizontal: 24, vertical: 32),
          child: GdModalSurface(
            tokens: tokens,
            radius: 28,
            padding: const EdgeInsets.fromLTRB(20, 12, 20, 20),
            transparentSurface: true,
            child: Column(
              mainAxisSize: MainAxisSize.min,
              children: [
                Container(
                  constraints: const BoxConstraints(maxHeight: 320),
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(24),
                    gradient: LinearGradient(
                      colors: [
                        tokens.primaryButtonGradient.first.withOpacity(.12),
                        Colors.white.withOpacity(.92),
                      ],
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                    ),
                    border: Border.all(
                      color: tokens.primaryButtonGradient.first.withOpacity(.18),
                    ),
                  ),
                  padding: const EdgeInsets.all(20),
                  child: AspectRatio(
                    aspectRatio: 1,
                    child:
                        gift.giftUrl != null && gift.giftUrl!.isNotEmpty
                            ? RemoteMediaArt(
                              url: gift.giftUrl!,
                              explicitType: gift.giftType,
                              width: double.infinity,
                              height: double.infinity,
                              fit: BoxFit.contain,
                              enableAudio: false,
                              fallback: Icon(
                                Icons.card_giftcard_rounded,
                                color: tokens.textSecondary,
                                size: 72,
                              ),
                            )
                            : Icon(
                              Icons.card_giftcard_rounded,
                              color: tokens.textSecondary,
                              size: 72,
                            ),
                  ),
                ),
                const SizedBox(height: 14),
                Text(
                  gift.name,
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 20,
                    fontWeight: FontWeight.w900,
                  ),
                ),
                const SizedBox(height: 8),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    const CoinLottie(size: 18),
                    const SizedBox(width: 6),
                    Text(
                      '${gift.coins}',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 16,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                  ],
                ),
              ],
            ),
          ),
        ),
  );
}

String _formatCoins(int value) {
  if (value >= 1000000) {
    final reduced = value / 1000000;
    return reduced % 1 == 0
        ? '${reduced.toStringAsFixed(0)}M'
        : '${reduced.toStringAsFixed(1)}M';
  }
  if (value >= 1000) {
    final reduced = value / 1000;
    return reduced % 1 == 0
        ? '${reduced.toStringAsFixed(0)}K'
        : '${reduced.toStringAsFixed(1)}K';
  }
  return '$value';
}
