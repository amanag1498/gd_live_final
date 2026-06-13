import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../app/widgets/coin_lottie.dart';
import '../../../app/widgets/haptics.dart';
import '../controllers/call_controller.dart';
import 'call_ui.dart';

class OutgoingCallScreen extends StatelessWidget {
  const OutgoingCallScreen({super.key});

  @override
  Widget build(BuildContext context) {
    final controller = Get.find<AppCallController>();

    return CallScaffold(
      child: Obx(() {
        final call = controller.activeCall.value ?? <String, dynamic>{};
        final type = (call['type'] ?? 'video').toString();
        final countdown = controller.ringingSecondsLeft.value;
        final statusText =
            controller.reconnecting.value
                ? 'Reconnecting to the host'
                : controller.callState.value == 'outgoing_ringing'
                ? 'Waiting for host response'
                : 'Preparing secure call';

        return LayoutBuilder(
          builder: (context, constraints) {
            final compact =
                constraints.maxHeight < 760 || constraints.maxWidth < 370;

            return SafeArea(
              child: Center(
                child: SingleChildScrollView(
                  padding: EdgeInsets.fromLTRB(
                    compact ? 18 : 24,
                    compact ? 12 : 20,
                    compact ? 18 : 24,
                    compact ? 24 : 30,
                  ),
                  child: ConstrainedBox(
                    constraints: const BoxConstraints(maxWidth: 460),
                    child: Column(
                      children: [
                        CallPill(
                          label: type == 'video'
                              ? 'GD Live Video Call'
                              : 'GD Live Voice Call',
                          color: const Color(0xFF7D9BFF),
                        ),
                        SizedBox(height: compact ? 20 : 28),
                        CallHero(
                          name: controller.remoteDisplayName,
                          avatarUrl:
                              controller.remoteAvatarUrl.isEmpty
                                  ? null
                                  : controller.remoteAvatarUrl,
                          subtitle: 'Call request sent from GD Live',
                          icon:
                              type == 'video'
                                  ? Icons.videocam_rounded
                                  : Icons.call_rounded,
                        ),
                        SizedBox(height: compact ? 14 : 18),
                        CallStatusText(text: statusText),
                        SizedBox(height: compact ? 12 : 16),
                        AudioWaveform(
                          accent:
                              type == 'video'
                                  ? const Color(0xFF7D9BFF)
                                  : const Color(0xFF55D38A),
                        ),
                        SizedBox(height: compact ? 18 : 24),
                        CallGlassCard(
                          child: Column(
                            children: [
                              Row(
                                children: [
                                  Expanded(
                                    child: CallMetricTile(
                                      label: 'Request timer',
                                      value: countdown > 0
                                          ? '${countdown}s left'
                                          : 'Server managed',
                                      icon: Icons.schedule_rounded,
                                      accent: const Color(0xFF7D9BFF),
                                    ),
                                  ),
                                  const SizedBox(width: 12),
                                  Expanded(
                                    child: CallMetricTile(
                                      label: 'Rate',
                                      value:
                                          '${controller.ratePerMinute} coins/min',
                                      icon: Icons.toll_rounded,
                                      accent: const Color(0xFFFFC84A),
                                    ),
                                  ),
                                ],
                              ),
                              SizedBox(height: compact ? 14 : 16),
                              Container(
                                width: double.infinity,
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 14,
                                  vertical: 12,
                                ),
                                decoration: BoxDecoration(
                                  color: Colors.white.withValues(alpha: .05),
                                  borderRadius: BorderRadius.circular(18),
                                  border: Border.all(
                                    color: Colors.white.withValues(alpha: .08),
                                  ),
                                ),
                                child: Row(
                                  children: [
                                    const CoinLottie(size: 24),
                                    const SizedBox(width: 10),
                                    Expanded(
                                      child: Text(
                                        'Coins are charged only after the host accepts and the call starts.',
                                        style: TextStyle(
                                          color: Colors.white.withValues(
                                            alpha: .78,
                                          ),
                                          fontWeight: FontWeight.w600,
                                          height: 1.35,
                                        ),
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),
                        SizedBox(height: compact ? 22 : 30),
                        CallFabButton(
                          icon: Icons.call_end_rounded,
                          label: 'Cancel Request',
                          color: const Color(0xFFFF6B7A),
                          onTap:
                              controller.busy.value
                                  ? null
                                  : () {
                                    Haptics.medium();
                                    controller.cancelOutgoing();
                                  },
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            );
          },
        );
      }),
    );
  }
}
