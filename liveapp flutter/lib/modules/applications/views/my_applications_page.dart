import 'dart:ui';
import 'dart:math' as math;

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:intl/intl.dart';

import '../../../app/brand/brand.dart';
import '../../../app/widgets/coin_lottie.dart';
import '../../../app/widgets/gd_modal_surface.dart';
import '../../../app/widgets/haptics.dart';
import '../controllers/applications_controller.dart';
import '../models/application_dto.dart';

BrandTokens _applicationsTokens() => getBrandTokens('midnight');

Future<T?> showMyApplicationsSheet<T>() {
  return Get.bottomSheet<T>(
    const _MyApplicationsSheet(),
    isScrollControlled: true,
  );
}

class MyApplicationsPage extends GetView<ApplicationsController> {
  const MyApplicationsPage({super.key});

  @override
  Widget build(BuildContext context) {
    final tokens = _applicationsTokens();
    return Scaffold(
      backgroundColor: tokens.backgroundGradient.first,
      appBar: AppBar(
        title: Text(
          'My Applications',
          style: TextStyle(
            color: tokens.textPrimary,
            fontWeight: FontWeight.w800,
          ),
        ),
        iconTheme: IconThemeData(color: tokens.textPrimary),
        backgroundColor: Colors.transparent,
        elevation: 0,
      ),
      body: const _ApplicationsBody(
        paddedTop: 18,
        surface: _ApplicationsSurface.page,
      ),
    );
  }
}

class _MyApplicationsSheet extends StatelessWidget {
  const _MyApplicationsSheet();

  @override
  Widget build(BuildContext context) {
    final tokens = _applicationsTokens();
    final bottomInset = MediaQuery.viewInsetsOf(context).bottom;
    return SafeArea(
      top: false,
      child: AnimatedPadding(
        duration: const Duration(milliseconds: 180),
        curve: Curves.easeOut,
        padding: EdgeInsets.only(bottom: bottomInset),
        child: LayoutBuilder(
          builder: (context, constraints) {
            final availableHeight =
                constraints.maxHeight.isFinite
                    ? constraints.maxHeight
                    : MediaQuery.sizeOf(context).height * .86;
            final bodyHeight = math.max(260.0, availableHeight - 120);

            return GdModalSurface(
              tokens: tokens,
              radius: 30,
              padding: const EdgeInsets.fromLTRB(18, 12, 18, 18),
              child: SizedBox(
                height: bodyHeight,
                child: Column(
                  children: [
                    Text(
                      'My Applications',
                      style: TextStyle(
                        color: tokens.textPrimary,
                        fontSize: 22,
                        fontWeight: FontWeight.w900,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      'Track your host and agency requests in one place.',
                      style: TextStyle(
                        color: tokens.textSecondary.withOpacity(.82),
                        fontSize: 13,
                        fontWeight: FontWeight.w600,
                      ),
                      textAlign: TextAlign.center,
                    ),
                    Align(
                      alignment: Alignment.centerRight,
                      child: IconButton(
                        onPressed: () {
                          Haptics.selection();
                          Get.back<void>();
                        },
                        icon: Icon(
                          Icons.close_rounded,
                          color: tokens.textPrimary,
                        ),
                      ),
                    ),
                    const Expanded(
                      child: _ApplicationsBody(
                        paddedTop: 0,
                        surface: _ApplicationsSurface.sheet,
                      ),
                    ),
                  ],
                ),
              ),
            );
          },
        ),
      ),
    );
  }
}

enum _ApplicationsSurface { page, sheet }

class _ApplicationsBody extends GetView<ApplicationsController> {
  final double paddedTop;
  final _ApplicationsSurface surface;

  const _ApplicationsBody({required this.paddedTop, required this.surface});

  @override
  Widget build(BuildContext context) {
    final isSheet = surface == _ApplicationsSurface.sheet;
    final tokens = _applicationsTokens();
    final content = Obx(() {
      if (controller.isLoading.value && controller.summary.value == null) {
        return Center(
          child: CircularProgressIndicator(
            color:
                isSheet
                    ? tokens.textPrimary
                    : tokens.primaryButtonGradient.first,
          ),
        );
      }
      if (controller.error.value != null && controller.summary.value == null) {
        return _ErrorState(
          title: 'Unable to load applications',
          subtitle: controller.error.value!,
          onRetry: () {
            Haptics.selection();
            controller.load();
          },
          dark: isSheet,
        );
      }
      final items = controller.applications;
      return RefreshIndicator(
        onRefresh: controller.load,
        child: ListView(
          padding: EdgeInsets.fromLTRB(18, paddedTop, 18, 24),
          children: [
            _ApplicationsSummaryCard(
              total: items.length,
              pending: items.where((e) => e.isPending).length,
              approved: items.where((e) => e.isApproved).length,
              rejected: items.where((e) => e.isRejected).length,
            ),
            const SizedBox(height: 18),
            if (items.isEmpty)
              _EmptyState(
                title: 'No applications yet',
                subtitle:
                    'When you submit a host or agency request, the status will appear here.',
                dark: isSheet,
              )
            else
              ...items.map(
                (item) => Padding(
                  padding: const EdgeInsets.only(bottom: 14),
                  child: _ApplicationCard(item: item, dark: isSheet),
                ),
              ),
          ],
        ),
      );
    });

    if (isSheet) {
      return content;
    }

    return Container(
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: tokens.backgroundGradient,
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
      ),
      child: content,
    );
  }
}

class _ErrorState extends StatelessWidget {
  final String title;
  final String subtitle;
  final VoidCallback onRetry;
  final bool dark;

  const _ErrorState({
    required this.title,
    required this.subtitle,
    required this.onRetry,
    this.dark = false,
  });

  @override
  Widget build(BuildContext context) {
    final tokens = _applicationsTokens();
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 40),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          GdLottie(asset: GdLottieAssets.addUser, width: 108, height: 108),
          const SizedBox(height: 14),
          Text(
            title,
            style: Theme.of(context).textTheme.titleLarge?.copyWith(
              fontWeight: FontWeight.w800,
              color: tokens.textPrimary,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            subtitle,
            textAlign: TextAlign.center,
            style: Theme.of(context).textTheme.bodyMedium?.copyWith(
              color:
                  dark
                      ? tokens.textSecondary.withOpacity(.82)
                      : tokens.textSecondary,
            ),
          ),
          const SizedBox(height: 14),
          FilledButton(onPressed: onRetry, child: const Text('Retry')),
        ],
      ),
    );
  }
}

class _ApplicationsSummaryCard extends StatelessWidget {
  final int total;
  final int pending;
  final int approved;
  final int rejected;

  const _ApplicationsSummaryCard({
    required this.total,
    required this.pending,
    required this.approved,
    required this.rejected,
  });

  @override
  Widget build(BuildContext context) {
    final tokens = _applicationsTokens();
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: tokens.cardGradient,
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        borderRadius: BorderRadius.circular(28),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'Application Status',
            style: Theme.of(context).textTheme.titleLarge?.copyWith(
              color: tokens.textPrimary,
              fontWeight: FontWeight.w800,
            ),
          ),
          const SizedBox(height: 6),
          Text(
            'This page only shows submitted requests and their review status.',
            style: Theme.of(
              context,
            ).textTheme.bodyMedium?.copyWith(color: tokens.textSecondary),
          ),
          const SizedBox(height: 18),
          GridView.count(
            shrinkWrap: true,
            physics: const NeverScrollableScrollPhysics(),
            crossAxisCount: 2,
            crossAxisSpacing: 10,
            mainAxisSpacing: 10,
            childAspectRatio: 1.8,
            children: [
              _SummaryStat(label: 'Total', value: '$total'),
              _SummaryStat(label: 'Pending', value: '$pending'),
              _SummaryStat(label: 'Approved', value: '$approved'),
              _SummaryStat(label: 'Rejected', value: '$rejected'),
            ],
          ),
        ],
      ),
    );
  }
}

class _SummaryStat extends StatelessWidget {
  final String label;
  final String value;

  const _SummaryStat({required this.label, required this.value});

  @override
  Widget build(BuildContext context) {
    final tokens = _applicationsTokens();
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 12),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [
            tokens.chipColor.withOpacity(.96),
            tokens.glassColor.withOpacity(.74),
          ],
        ),
        borderRadius: BorderRadius.circular(18),
        border: Border.all(color: tokens.borderColor.withOpacity(.82)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Text(
            value,
            style: TextStyle(
              color: tokens.textPrimary,
              fontSize: 18,
              fontWeight: FontWeight.w800,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            label,
            style: TextStyle(
              color: tokens.textSecondary,
              fontSize: 12,
              fontWeight: FontWeight.w600,
            ),
          ),
        ],
      ),
    );
  }
}

class _ApplicationCard extends StatelessWidget {
  final ApplicationItemDto item;
  final bool dark;
  const _ApplicationCard({required this.item, this.dark = false});

  @override
  Widget build(BuildContext context) {
    final tokens = _applicationsTokens();
    final palette = _ApplicationStatusPalette.resolve(item, tokens);
    final reviewedText =
        item.reviewedAt == null
            ? null
            : DateFormat.yMMMd().add_jm().format(item.reviewedAt!.toLocal());
    final submittedText =
        item.submittedAt == null
            ? 'Unknown'
            : DateFormat.yMMMd().add_jm().format(item.submittedAt!.toLocal());
    return Container(
      padding: const EdgeInsets.all(18),
      decoration: BoxDecoration(
        gradient: LinearGradient(
          colors: [palette.panelStart, palette.panelEnd],
          begin: Alignment.topLeft,
          end: Alignment.bottomCenter,
        ),
        borderRadius: BorderRadius.circular(28),
        border: Border.all(color: palette.borderColor),
        boxShadow: [
          BoxShadow(
            color: palette.glowColor.withOpacity(.18),
            blurRadius: 28,
            offset: const Offset(0, 14),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Container(
                width: 54,
                height: 54,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(18),
                  gradient: LinearGradient(
                    colors: [
                      palette.iconTint.withOpacity(.28),
                      palette.iconTint.withOpacity(.08),
                    ],
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                  ),
                  border: Border.all(color: palette.iconTint.withOpacity(.34)),
                ),
                child: Icon(palette.icon, color: palette.iconTint, size: 26),
              ),
              const SizedBox(width: 14),
              Expanded(
                child: Text(
                  item.title,
                  style: Theme.of(context).textTheme.titleMedium?.copyWith(
                    fontWeight: FontWeight.w900,
                    color: tokens.textPrimary,
                  ),
                ),
              ),
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 12,
                  vertical: 6,
                ),
                decoration: BoxDecoration(
                  color: palette.badgeColor.withOpacity(.16),
                  borderRadius: BorderRadius.circular(999),
                  border: Border.all(
                    color: palette.badgeColor.withOpacity(.28),
                  ),
                ),
                child: Text(
                  palette.statusLabel,
                  style: TextStyle(
                    color: palette.badgeColor,
                    fontWeight: FontWeight.w800,
                    fontSize: 12,
                  ),
                ),
              ),
            ],
          ),
          const SizedBox(height: 14),
          Container(
            padding: const EdgeInsets.all(14),
            decoration: BoxDecoration(
              color: Colors.white.withOpacity(.045),
              borderRadius: BorderRadius.circular(20),
              border: Border.all(color: tokens.borderColor.withOpacity(.24)),
            ),
            child: Row(
              children: [
                Expanded(
                  child: _DetailPill(
                    label: 'Request',
                    value: _labelForType(item.type),
                    icon: Icons.description_outlined,
                  ),
                ),
                const SizedBox(width: 10),
                Expanded(
                  child: _DetailPill(
                    label: 'Submitted',
                    value: submittedText,
                    icon: Icons.schedule_rounded,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 14),
          Text(
            palette.headline,
            style: TextStyle(
              color: tokens.textPrimary,
              fontSize: 15,
              fontWeight: FontWeight.w800,
            ),
          ),
          const SizedBox(height: 6),
          Text(
            palette.message,
            style: TextStyle(
              color: tokens.textSecondary,
              height: 1.45,
              fontWeight: FontWeight.w500,
            ),
          ),
          if (reviewedText != null) ...[
            const SizedBox(height: 14),
            _MetaRow(label: 'Reviewed', value: reviewedText, dark: dark),
          ],
          if ((item.reviewNotes ?? '').trim().isNotEmpty)
            Container(
              margin: const EdgeInsets.only(top: 12),
              padding: const EdgeInsets.all(14),
              decoration: BoxDecoration(
                color: palette.noteBackground,
                borderRadius: BorderRadius.circular(18),
                border: Border.all(color: palette.noteBorder),
              ),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(
                    item.isRejected
                        ? Icons.info_outline_rounded
                        : Icons.task_alt_rounded,
                    size: 18,
                    color: palette.badgeColor,
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          item.isRejected ? 'Review notes' : 'Admin note',
                          style: TextStyle(
                            color: tokens.textPrimary,
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                        const SizedBox(height: 4),
                        Text(
                          item.reviewNotes!.trim(),
                          style: TextStyle(
                            color: tokens.textSecondary,
                            height: 1.4,
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
        ],
      ),
    );
  }

  static String _labelForType(String type) {
    switch (type) {
      case 'agency':
        return 'Agency application';
      case 'host':
        return 'Host application';
      default:
        return type;
    }
  }
}

class _DetailPill extends StatelessWidget {
  const _DetailPill({
    required this.label,
    required this.value,
    required this.icon,
  });

  final String label;
  final String value;
  final IconData icon;

  @override
  Widget build(BuildContext context) {
    final tokens = _applicationsTokens();
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 11),
      decoration: BoxDecoration(
        color: Colors.white.withOpacity(.04),
        borderRadius: BorderRadius.circular(16),
        border: Border.all(color: tokens.borderColor.withOpacity(.22)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Icon(icon, size: 16, color: tokens.textSecondary),
          const SizedBox(width: 8),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  label,
                  style: TextStyle(
                    color: tokens.textSecondary.withOpacity(.78),
                    fontSize: 11.5,
                    fontWeight: FontWeight.w700,
                  ),
                ),
                const SizedBox(height: 3),
                Text(
                  value,
                  maxLines: 2,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    color: tokens.textPrimary,
                    fontSize: 12.5,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _ApplicationStatusPalette {
  const _ApplicationStatusPalette({
    required this.statusLabel,
    required this.headline,
    required this.message,
    required this.icon,
    required this.badgeColor,
    required this.iconTint,
    required this.panelStart,
    required this.panelEnd,
    required this.borderColor,
    required this.glowColor,
    required this.noteBackground,
    required this.noteBorder,
  });

  final String statusLabel;
  final String headline;
  final String message;
  final IconData icon;
  final Color badgeColor;
  final Color iconTint;
  final Color panelStart;
  final Color panelEnd;
  final Color borderColor;
  final Color glowColor;
  final Color noteBackground;
  final Color noteBorder;

  static _ApplicationStatusPalette resolve(
    ApplicationItemDto item,
    BrandTokens tokens,
  ) {
    if (item.isApproved) {
      const accent = Color(0xFF39C88B);
      return _ApplicationStatusPalette(
        statusLabel: 'APPROVED',
        headline: 'You are cleared to move forward.',
        message:
            'Your ${_ApplicationCard._labelForType(item.type).toLowerCase()} has been approved in GD Live.',
        icon: Icons.verified_rounded,
        badgeColor: accent,
        iconTint: accent,
        panelStart: tokens.cardGradient.first.withOpacity(.98),
        panelEnd: const Color(0xFF0F2E25),
        borderColor: accent.withOpacity(.34),
        glowColor: accent,
        noteBackground: accent.withOpacity(.10),
        noteBorder: accent.withOpacity(.22),
      );
    }
    if (item.isRejected) {
      final accent = tokens.dangerColor;
      return _ApplicationStatusPalette(
        statusLabel: 'REJECTED',
        headline: 'This request needs another pass.',
        message:
            'The team did not approve this ${_ApplicationCard._labelForType(item.type).toLowerCase()} yet.',
        icon: Icons.cancel_rounded,
        badgeColor: accent,
        iconTint: accent,
        panelStart: tokens.cardGradient.first.withOpacity(.98),
        panelEnd: const Color(0xFF32151C),
        borderColor: accent.withOpacity(.30),
        glowColor: accent,
        noteBackground: accent.withOpacity(.10),
        noteBorder: accent.withOpacity(.22),
      );
    }
    final accent = tokens.primaryButtonGradient.first;
    return _ApplicationStatusPalette(
      statusLabel: 'PENDING',
      headline: 'Your request is under review.',
      message:
          'GD Live is still reviewing this ${_ApplicationCard._labelForType(item.type).toLowerCase()}.',
      icon: Icons.hourglass_top_rounded,
      badgeColor: accent,
      iconTint: accent,
      panelStart: tokens.cardGradient.first.withOpacity(.98),
      panelEnd: tokens.cardGradient.last.withOpacity(.98),
      borderColor: accent.withOpacity(.24),
      glowColor: accent,
      noteBackground: accent.withOpacity(.08),
      noteBorder: accent.withOpacity(.18),
    );
  }
}

class _MetaRow extends StatelessWidget {
  final String label;
  final String value;
  final bool dark;
  const _MetaRow({required this.label, required this.value, this.dark = false});

  @override
  Widget build(BuildContext context) {
    final tokens = _applicationsTokens();
    return Padding(
      padding: const EdgeInsets.only(bottom: 8),
      child: RichText(
        text: TextSpan(
          style: DefaultTextStyle.of(context).style.copyWith(fontSize: 14),
          children: [
            TextSpan(
              text: '$label: ',
              style: TextStyle(
                color: _applicationsTokens().textSecondary.withOpacity(.78),
                fontWeight: FontWeight.w700,
              ),
            ),
            TextSpan(
              text: value,
              style: TextStyle(
                color: _applicationsTokens().textPrimary,
                fontWeight: FontWeight.w600,
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _EmptyState extends StatelessWidget {
  final String title;
  final String subtitle;
  final bool dark;
  const _EmptyState({
    required this.title,
    required this.subtitle,
    this.dark = false,
  });

  @override
  Widget build(BuildContext context) {
    final tokens = _applicationsTokens();
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 40),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(
            Icons.assignment_late_rounded,
            size: 56,
            color:
                dark ? tokens.textPrimary : tokens.primaryButtonGradient.first,
          ),
          const SizedBox(height: 14),
          Text(
            title,
            style: Theme.of(context).textTheme.titleLarge?.copyWith(
              fontWeight: FontWeight.w800,
              color: tokens.textPrimary,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            subtitle,
            textAlign: TextAlign.center,
            style: Theme.of(context).textTheme.bodyMedium?.copyWith(
              color:
                  dark
                      ? tokens.textSecondary.withOpacity(.8)
                      : tokens.textSecondary,
            ),
          ),
        ],
      ),
    );
  }
}
