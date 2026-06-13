import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:permission_handler/permission_handler.dart';

import '../../../../app/widgets/haptics.dart';
import '../../../../services/app_settings_service.dart';
import '../services/live_service.dart';
import 'video_call_page.dart';

Future<void> showLivePreflightSheet(
  BuildContext context, {
  String initialTitle = 'GD Live',
}) async {
  final live = Get.find<LiveService>();
  final appSettings = Get.find<AppSettingsService>();

  if (!appSettings.anyLiveCreationEnabled) {
    Get.snackbar(
      'Live unavailable',
      'Live creation is currently disabled by the platform.',
      snackPosition: SnackPosition.BOTTOM,
    );
    return;
  }

  try {
    if (!appSettings.videoRoomsEnabled) {
      throw Exception('Video live is currently unavailable.');
    }

    final mic = await Permission.microphone.request();
    if (!mic.isGranted) {
      throw Exception('Microphone permission is required.');
    }

    final cam = await Permission.camera.request();
    if (!cam.isGranted) {
      throw Exception('Camera permission is required for video live.');
    }

    final room = await live.createOrStart(
      title: initialTitle,
    );

    Haptics.success();
    Get.to(
      () => VideoCallPage(
        room: room,
        live: live,
        initialMicOn: true,
        initialCamOn: true,
      ),
      transition: Transition.cupertino,
      duration: const Duration(milliseconds: 420),
      curve: Curves.easeOutCubic,
    );
  } catch (e) {
    if (!context.mounted) return;
    Get.snackbar(
      'Unable to start live',
      '$e',
      snackPosition: SnackPosition.BOTTOM,
    );
  }
}
