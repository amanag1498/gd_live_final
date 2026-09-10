import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';

import '../../../app/brand/brand.dart';
import '../../../app/widgets/app_avatar.dart';
import '../../../app/widgets/gd_modal_surface.dart';
import '../controllers/user_block_controller.dart';

class PersonalBlockedUsersPage extends StatefulWidget {
  const PersonalBlockedUsersPage({super.key});

  @override
  State<PersonalBlockedUsersPage> createState() =>
      _PersonalBlockedUsersPageState();
}

class _PersonalBlockedUsersPageState extends State<PersonalBlockedUsersPage> {
  final UserBlockController _blocks = Get.find<UserBlockController>();
  int? _busyUserId;

  BrandTokens get _tokens => getBrandTokens('midnight');

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _blocks.refreshForCurrentAuth();
    });
  }

  Future<void> _unblock(Map<String, dynamic> row) async {
    final userId = _asInt(row['user_id']);
    if (userId == null || _busyUserId != null) return;
    final confirmed = await showDialog<bool>(
      context: context,
      builder:
          (dialogContext) => Dialog(
            backgroundColor: Colors.transparent,
            insetPadding: const EdgeInsets.symmetric(horizontal: 22),
            child: GdModalSurface(
              tokens: _tokens,
              scrollable: true,
              child: Column(
                mainAxisSize: MainAxisSize.min,
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    'Unblock user?',
                    style: TextStyle(
                      color: _tokens.textPrimary,
                      fontSize: 20,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                  const SizedBox(height: 12),
                  Text(
                    'This will allow ${_label(row)} to interact with you again.',
                    style: TextStyle(
                      color: _tokens.textSecondary,
                      height: 1.35,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                  const SizedBox(height: 16),
                  Row(
                    children: [
                      Expanded(
                        child: OutlinedButton(
                          onPressed:
                              () => Navigator.of(dialogContext).pop(false),
                          child: const Text('Cancel'),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: FilledButton(
                          onPressed:
                              () => Navigator.of(dialogContext).pop(true),
                          child: const Text('Unblock'),
                        ),
                      ),
                    ],
                  ),
                ],
              ),
            ),
          ),
    );
    if (confirmed != true || !mounted) return;

    setState(() => _busyUserId = userId);
    try {
      await _blocks.unblock(userId);
      Get.snackbar(
        'Privacy',
        '${_label(row)} was unblocked.',
        snackPosition: SnackPosition.BOTTOM,
      );
    } catch (exception) {
      Get.snackbar(
        'Could not unblock',
        exception.toString().replaceFirst('Exception: ', ''),
        snackPosition: SnackPosition.BOTTOM,
      );
    } finally {
      if (mounted) setState(() => _busyUserId = null);
    }
  }

  String _label(Map<String, dynamic> row) {
    final value = (row['name'] ?? 'User').toString().trim();
    return value.isEmpty ? 'User' : value;
  }

  int? _asInt(dynamic value) {
    if (value is int) return value;
    if (value is num) return value.toInt();
    return int.tryParse(value?.toString() ?? '');
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
              'People You Blocked',
              style: TextStyle(
                color: tokens.textPrimary,
                fontWeight: FontWeight.w900,
                fontSize: 20,
              ),
            ),
            Text(
              'Review and unblock personal restrictions',
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
            begin: Alignment.topLeft,
            end: Alignment.bottomRight,
            colors: [
              tokens.backgroundGradient.first,
              tokens.backgroundGradient.last,
            ],
          ),
        ),
        child: Obx(() {
          final rows = _blocks.blockedUsers;
          return RefreshIndicator(
            onRefresh: _blocks.refreshForCurrentAuth,
            child:
                _blocks.loading.value && rows.isEmpty
                    ? const Center(child: CircularProgressIndicator())
                    : _blocks.error.value != null && rows.isEmpty
                    ? ListView(
                      children: [
                        const SizedBox(height: 140),
                        Center(
                          child: Padding(
                            padding: const EdgeInsets.symmetric(horizontal: 28),
                            child: Column(
                              children: [
                                Text(
                                  _blocks.error.value!,
                                  style: TextStyle(color: tokens.textPrimary),
                                  textAlign: TextAlign.center,
                                ),
                                const SizedBox(height: 12),
                                TextButton(
                                  onPressed: _blocks.refreshForCurrentAuth,
                                  child: const Text('Retry'),
                                ),
                              ],
                            ),
                          ),
                        ),
                      ],
                    )
                    : rows.isEmpty
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
                              border: Border.all(
                                color: tokens.borderColor.withOpacity(.35),
                              ),
                            ),
                            child: Column(
                              children: [
                                Container(
                                  width: 68,
                                  height: 68,
                                  decoration: BoxDecoration(
                                    color: tokens.chipColor,
                                    borderRadius: BorderRadius.circular(22),
                                  ),
                                  child: Icon(
                                    Icons.person_off_rounded,
                                    color: tokens.primaryButtonGradient.first,
                                    size: 32,
                                  ),
                                ),
                                const SizedBox(height: 14),
                                Text(
                                  'No blocked users yet.',
                                  style: TextStyle(
                                    color: tokens.textPrimary,
                                    fontSize: 16,
                                    fontWeight: FontWeight.w800,
                                  ),
                                ),
                                const SizedBox(height: 6),
                                Text(
                                  'People you block will appear here so you can unblock them later.',
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
                      itemCount: rows.length,
                      separatorBuilder: (_, __) => const SizedBox(height: 12),
                      itemBuilder: (_, index) {
                        final row = rows[index];
                        final userId = _asInt(row['user_id']);
                        final blockedAt = DateTime.tryParse(
                          (row['blocked_at'] ?? '').toString(),
                        );
                        final level = _asInt(row['level']);
                        final isHost = row['is_host'] == true;
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
                            border: Border.all(
                              color: tokens.borderColor.withOpacity(.35),
                            ),
                            boxShadow: [
                              BoxShadow(
                                color: tokens.primaryButtonGradient.first
                                    .withOpacity(.06),
                                blurRadius: 18,
                                offset: const Offset(0, 10),
                              ),
                            ],
                          ),
                          child: Row(
                            children: [
                              SizedBox(
                                width: 48,
                                height: 48,
                                child: AppAvatar(
                                  avatarUrl: row['avatar_url']?.toString(),
                                  label: _label(row),
                                  size: 48,
                                ),
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      _label(row),
                                      style: TextStyle(
                                        color: tokens.textPrimary,
                                        fontWeight: FontWeight.w900,
                                        fontSize: 16,
                                      ),
                                    ),
                                    const SizedBox(height: 8),
                                    Wrap(
                                      spacing: 8,
                                      runSpacing: 6,
                                      children: [
                                        if (level != null)
                                          _MiniChip(label: 'LV $level'),
                                        if (isHost)
                                          const _MiniChip(label: 'HOST'),
                                        if (blockedAt != null)
                                          _MiniChip(
                                            label: DateFormat(
                                              'dd MMM yyyy',
                                            ).format(blockedAt),
                                          ),
                                      ],
                                    ),
                                    const SizedBox(height: 12),
                                    Text(
                                      'Tap unblock to restore interactions',
                                      style: TextStyle(
                                        color: tokens.textSecondary.withOpacity(
                                          .85,
                                        ),
                                        fontSize: 11.5,
                                        fontWeight: FontWeight.w600,
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              const SizedBox(width: 12),
                              FilledButton(
                                onPressed:
                                    userId == null || _busyUserId != null
                                        ? null
                                        : () => _unblock(row),
                                child:
                                    _busyUserId == userId
                                        ? const SizedBox(
                                          width: 16,
                                          height: 16,
                                          child: CircularProgressIndicator(
                                            strokeWidth: 2,
                                          ),
                                        )
                                        : const Text('Unblock'),
                              ),
                            ],
                          ),
                        );
                      },
                    ),
          );
        }),
      ),
    );
  }
}

class _MiniChip extends StatelessWidget {
  const _MiniChip({required this.label});

  final String label;

  @override
  Widget build(BuildContext context) {
    final tokens = getBrandTokens('midnight');
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 5),
      decoration: BoxDecoration(
        color: tokens.glassColor.withOpacity(.18),
        borderRadius: BorderRadius.circular(999),
        border: Border.all(color: tokens.borderColor.withOpacity(.6)),
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
