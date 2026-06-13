import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';

import '../../../app/brand/brand.dart';
import '../../../app/widgets/coin_lottie.dart';
import '../../Live/services/live_service.dart';

class ModerationHistoryPage extends StatefulWidget {
  const ModerationHistoryPage({super.key});

  @override
  State<ModerationHistoryPage> createState() => _ModerationHistoryPageState();
}

class _ModerationHistoryPageState extends State<ModerationHistoryPage> {
  final LiveService _live = Get.find<LiveService>();
  bool _loading = true;
  String? _error;
  List<Map<String, dynamic>> _rows = const <Map<String, dynamic>>[];

  BrandTokens get _tokens => getBrandTokens(
    'midnight',
  );

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      _loading = true;
      _error = null;
    });
    try {
      final rows = await _live.fetchHostModerationHistory();
      if (!mounted) return;
      setState(() {
        _rows = rows;
        _loading = false;
      });
    } catch (e) {
      if (!mounted) return;
      setState(() {
        _error = e.toString().replaceFirst('Exception: ', '');
        _loading = false;
      });
    }
  }

  Map<String, dynamic> _mapOf(dynamic value) {
    if (value is Map<String, dynamic>) return value;
    if (value is Map) return Map<String, dynamic>.from(value);
    return const <String, dynamic>{};
  }

  String _targetName(Map<String, dynamic> row) {
    final target = _mapOf(row['target_user']);
    final name = target['name']?.toString().trim() ?? '';
    if (name.isNotEmpty) return name;
    return 'User #${row['target_user_id'] ?? ''}';
  }

  @override
  Widget build(BuildContext context) {
    final tokens = _tokens;
    return Scaffold(
      backgroundColor: tokens.backgroundGradient.first,
      appBar: AppBar(
        titleSpacing: 0,
        foregroundColor: tokens.textPrimary,
        iconTheme: IconThemeData(color: tokens.textPrimary),
        title: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              'Moderation History',
              style: TextStyle(
                color: tokens.textPrimary,
                fontWeight: FontWeight.w900,
                fontSize: 20,
              ),
            ),
            Text(
              'Track actions, reasons, and review timing',
              style: TextStyle(
                color: tokens.textSecondary,
                fontWeight: FontWeight.w600,
                fontSize: 12,
              ),
            ),
          ],
        ),
        toolbarHeight: 68,
        backgroundColor: Colors.transparent,
        elevation: 0,
      ),
      body: DecoratedBox(
        decoration: BoxDecoration(
          gradient: LinearGradient(
            colors: [
              tokens.backgroundGradient.first,
              tokens.backgroundGradient.last,
            ],
          ),
        ),
        child: RefreshIndicator(
          onRefresh: _load,
          child: _loading
              ? const Center(child: CircularProgressIndicator())
              : _error != null
                  ? ListView(
                      children: [
                        const SizedBox(height: 140),
                        Center(
                          child: Text(
                            _error!,
                            style: TextStyle(color: tokens.textPrimary),
                          ),
                        ),
                      ],
                    )
                  : _rows.isEmpty
                      ? ListView(
                          children: [
                            const SizedBox(height: 20),
                            Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 16),
                              child: Container(
                                padding: const EdgeInsets.all(20),
                                decoration: BoxDecoration(
                                  gradient: LinearGradient(
                                    colors: [
                                      Colors.white.withOpacity(.97),
                                      const Color(0xFFF4FBF5).withOpacity(.98),
                                    ],
                                  ),
                                  borderRadius: BorderRadius.circular(24),
                                  border: Border.all(color: tokens.borderColor.withOpacity(.35)),
                                ),
                                child: Column(
                                  children: [
                                    const GdLottie(
                                      asset: GdLottieAssets.docer,
                                      width: 86,
                                      height: 86,
                                    ),
                                    const SizedBox(height: 12),
                                    Text(
                                      'No moderation history yet.',
                                      style: TextStyle(
                                        color: tokens.textPrimary,
                                        fontWeight: FontWeight.w800,
                                        fontSize: 16,
                                      ),
                                    ),
                                    const SizedBox(height: 6),
                                    Text(
                                      'Actions will appear here after hosts moderate requests.',
                                      textAlign: TextAlign.center,
                                      style: TextStyle(
                                        color: tokens.textSecondary,
                                        fontWeight: FontWeight.w600,
                                        height: 1.35,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ),
                          ],
                        )
                      : ListView.separated(
                          padding: const EdgeInsets.fromLTRB(16, 12, 16, 20),
                          itemCount: _rows.length,
                          separatorBuilder: (_, __) => const SizedBox(height: 12),
                          itemBuilder: (_, index) {
                            final row = _rows[index];
                            final createdAt = DateTime.tryParse(
                              (row['created_at'] ?? '').toString(),
                            );
                            final actionType = (row['action_type'] ?? 'action')
                                .toString()
                                .replaceAll('_', ' ')
                                .toUpperCase();
                            return Container(
                              padding: const EdgeInsets.all(16),
                              decoration: BoxDecoration(
                                gradient: LinearGradient(
                                  begin: Alignment.topLeft,
                                  end: Alignment.bottomRight,
                                  colors: [
                                    Colors.white.withOpacity(.98),
                                    const Color(0xFFF4FBF5).withOpacity(.96),
                                  ],
                                ),
                                borderRadius: BorderRadius.circular(24),
                                border: Border.all(color: tokens.borderColor.withOpacity(.35)),
                                boxShadow: [
                                  BoxShadow(
                                    color: tokens.primaryButtonGradient.first.withOpacity(.06),
                                    blurRadius: 18,
                                    offset: const Offset(0, 10),
                                  ),
                                ],
                              ),
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Row(
                                    children: [
                                      Container(
                                        width: 42,
                                        height: 42,
                                        decoration: BoxDecoration(
                                          color: tokens.chipColor,
                                          borderRadius: BorderRadius.circular(14),
                                        ),
                                        child: Icon(
                                          _actionIcon(row['action_type']?.toString() ?? ''),
                                          color: tokens.primaryButtonGradient.first,
                                          size: 20,
                                        ),
                                      ),
                                      const SizedBox(width: 12),
                                      Expanded(
                                        child: Column(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Text(
                                              actionType,
                                              style: TextStyle(
                                                color: tokens.textPrimary,
                                                fontWeight: FontWeight.w900,
                                                fontSize: 13,
                                                letterSpacing: .4,
                                              ),
                                            ),
                                            const SizedBox(height: 4),
                                            Text(
                                              _targetName(row),
                                              style: TextStyle(
                                                color: tokens.textPrimary,
                                                fontWeight: FontWeight.w800,
                                                fontSize: 17,
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                    ],
                                  ),
                                  if ((row['reason'] ?? '')
                                      .toString()
                                      .trim()
                                      .isNotEmpty) ...[
                                    const SizedBox(height: 10),
                                    Text(
                                      row['reason'].toString().trim(),
                                      style: TextStyle(
                                        color: tokens.textSecondary,
                                        fontWeight: FontWeight.w600,
                                      ),
                                    ),
                                  ],
                                  const SizedBox(height: 12),
                                  Row(
                                    children: [
                                      _MiniHistoryChip(
                                        label: createdAt == null
                                            ? 'Unknown time'
                                            : DateFormat('dd MMM yyyy • hh:mm a').format(createdAt),
                                      ),
                                      const SizedBox(width: 8),
                                      _MiniHistoryChip(label: actionType),
                                    ],
                                  ),
                                ],
                              ),
                            );
                          },
                        ),
        ),
      ),
    );
  }
}

IconData _actionIcon(String actionType) {
  final normalized = actionType.toLowerCase();
  if (normalized.contains('block')) return Icons.block_rounded;
  if (normalized.contains('unblock')) return Icons.how_to_reg_rounded;
  if (normalized.contains('reject')) return Icons.close_rounded;
  if (normalized.contains('approve')) return Icons.check_circle_rounded;
  return Icons.history_rounded;
}

class _MiniHistoryChip extends StatelessWidget {
  const _MiniHistoryChip({required this.label});

  final String label;

  @override
  Widget build(BuildContext context) {
    final tokens = getBrandTokens('midnight');
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        color: tokens.chipColor,
        borderRadius: BorderRadius.circular(999),
        border: Border.all(color: tokens.borderColor.withOpacity(.45)),
      ),
      child: Text(
        label,
        style: TextStyle(
          color: tokens.textSecondary,
          fontSize: 11,
          fontWeight: FontWeight.w700,
        ),
      ),
    );
  }
}
