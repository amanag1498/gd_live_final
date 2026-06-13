import 'package:livekit_client/livekit_client.dart';

/// Centralized LiveKit video profiles so capture and subscription quality do
/// not silently change when SDK defaults change.
abstract final class LiveKitVideoQuality {
  static const roomCamera = CameraCaptureOptions(
    params: VideoParametersPresets.h720_169,
  );

  static const callCamera = CameraCaptureOptions(
    params: VideoParametersPresets.h720_169,
  );

  static const roomOptions = RoomOptions(
    // The Flutter SDK measures adaptive stream views in logical pixels. On
    // high-density phones that can incorrectly select a low simulcast layer
    // for a full-screen video.
    adaptiveStream: false,
    dynacast: true,
    defaultCameraCaptureOptions: roomCamera,
    defaultVideoPublishOptions: VideoPublishOptions(
      videoEncoding: VideoEncoding(maxBitrate: 1700 * 1000, maxFramerate: 30),
      simulcast: true,
    ),
  );

  static const callOptions = RoomOptions(
    adaptiveStream: false,
    dynacast: true,
    defaultCameraCaptureOptions: callCamera,
    defaultVideoPublishOptions: VideoPublishOptions(
      videoEncoding: VideoEncoding(maxBitrate: 1700 * 1000, maxFramerate: 30),
      simulcast: true,
    ),
  );

  static CameraCaptureOptions roomCameraAt(CameraPosition position) {
    return CameraCaptureOptions(
      cameraPosition: position,
      params: VideoParametersPresets.h720_169,
    );
  }

  static CameraCaptureOptions callCameraAt(CameraPosition position) {
    return CameraCaptureOptions(
      cameraPosition: position,
      params: VideoParametersPresets.h720_169,
    );
  }
}
