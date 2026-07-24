import 'dart:io' show Platform;
import 'package:flutter/foundation.dart';
import 'package:get/get.dart';
import 'package:firebase_auth/firebase_auth.dart' as fb;
import 'package:google_sign_in/google_sign_in.dart';
import 'package:dio/dio.dart';
import 'package:gd_live/services/presence_service.dart';
import 'package:gd_live/services/push_service.dart';
import 'package:gd_live/services/call_socket_service.dart';

import '../app/routes/app_routes.dart';
import '../data/models/user_model.dart';
import 'api_client.dart';
import 'storage_service.dart';

class AuthService {
  final ApiClient api;
  final StorageService storage;

  final String? iosClientId;
  late final GoogleSignIn _gsi;

  AuthService({required this.api, required this.storage, this.iosClientId}) {
    _gsi = GoogleSignIn(
      scopes: const ['email', 'profile'],
      clientId: (Platform.isIOS || Platform.isMacOS) ? iosClientId : null,
    );
  }

  bool get isLoggedIn => storage.token != null && storage.token!.isNotEmpty;

  UserModel? get currentUser {
    final j = storage.userJson;
    if (j == null) return null;
    try {
      return UserModel.fromJson(j);
    } catch (_) {
      return null;
    }
  }

  // --- helper: local-only cleanup (no API call, no navigation) ---
  Future<void> _signOutLocalOnly() async {
    try {
      await PresenceService.instance.stop();
    } catch (_) {}
    try {
      if (Get.isRegistered<CallSocketService>())
        await Get.find<CallSocketService>().stop();
    } catch (_) {}
    try {
      await _gsi.signOut();
    } catch (_) {}
    try {
      await fb.FirebaseAuth.instance.signOut();
    } catch (_) {}
    await storage.clear();
  }

  // Google -> Firebase -> Laravel (returns UserModel)
  Future<UserModel> signInWithGoogleAndBackend() async {
    try {
      debugPrint('[auth] starting Google sign-in');
      // 1) Google Sign-In
      final GoogleSignInAccount? gUser = await _gsi.signIn();
      if (gUser == null) {
        debugPrint('[auth] Google sign-in aborted by user');
        throw Exception('Sign-in aborted');
      }
      debugPrint('[auth] Google account selected: ${gUser.email}');

      final GoogleSignInAuthentication gAuth = await gUser.authentication;
      debugPrint(
        '[auth] Google auth received '
        'idToken=${gAuth.idToken != null && gAuth.idToken!.isNotEmpty} '
        'accessToken=${gAuth.accessToken != null && gAuth.accessToken!.isNotEmpty}',
      );

      // 2) Firebase sign-in to obtain a *fresh* ID token
      final credential = fb.GoogleAuthProvider.credential(
        idToken: gAuth.idToken,
        accessToken: gAuth.accessToken,
      );
      debugPrint('[auth] signing into Firebase with Google credential');
      final fb.UserCredential fbCred = await fb.FirebaseAuth.instance
          .signInWithCredential(credential);
      final fb.User? fUser = fbCred.user;
      if (fUser == null) {
        debugPrint('[auth] Firebase sign-in returned null user');
        throw Exception('Firebase user missing');
      }
      debugPrint('[auth] Firebase sign-in success uid=${fUser.uid}');
      return await _completeFirebaseLogin(fUser, deviceName: 'flutter-google');
    } on fb.FirebaseAuthException catch (e) {
      debugPrint(
        '[auth] FirebaseAuthException code=${e.code} message=${e.message}',
      );
      throw Exception('Firebase sign-in failed: ${e.message ?? e.code}');
    } on DioException catch (e) {
      // If backend actively signals block via 423 or payload, convert it
      final status = e.response?.statusCode;
      final body = e.response?.data;
      debugPrint(
        '[auth] Laravel login DioException status=$status body=$body message=${e.message}',
      );
      if (status == 423 ||
          body == 'blocked' ||
          (body is Map &&
              (body['blocked'] == true || body['error'] == 'blocked'))) {
        await _signOutLocalOnly();
        throw Exception('Your account has been blocked.');
      }
      if (body is Map<String, dynamic>) {
        throw Exception(
          _friendlyAuthMessage(
            status ?? 0,
            (body['msg'] ?? e.message ?? 'Login failed').toString(),
            (body['code'] ?? '').toString(),
          ),
        );
      }
      throw Exception('Login failed. ${e.message ?? 'Please try again.'}');
    } catch (e) {
      debugPrint('[auth] unexpected auth error: $e');
      rethrow;
    }
  }

  Future<UserModel> signInWithAppleAndBackend() async {
    if (!Platform.isIOS && !Platform.isMacOS) {
      throw Exception('Sign in with Apple is only available on Apple devices.');
    }

    try {
      debugPrint('[auth] starting Apple sign-in');
      final provider =
          fb.AppleAuthProvider()
            ..addScope('email')
            ..addScope('name');
      final credential = await fb.FirebaseAuth.instance.signInWithProvider(
        provider,
      );
      final user = credential.user;
      if (user == null) {
        throw Exception('Firebase user missing');
      }

      return await _completeFirebaseLogin(user, deviceName: 'flutter-apple');
    } on fb.FirebaseAuthException catch (e) {
      debugPrint(
        '[auth] Apple FirebaseAuthException '
        'code=${e.code} message=${e.message}',
      );
      throw Exception('Apple sign-in failed: ${e.message ?? e.code}');
    } on DioException catch (e) {
      await _throwFriendlyBackendError(e);
      rethrow;
    }
  }

  Future<UserModel> _completeFirebaseLogin(
    fb.User firebaseUser, {
    required String deviceName,
  }) async {
    final idToken = await firebaseUser.getIdToken(true);
    debugPrint('[auth] Firebase ID token fetched successfully');

    final res = await api.post<Map<String, dynamic>>(
      'auth/firebase/login',
      data: {'idToken': idToken, 'device_name': deviceName},
    );

    final status = res.statusCode ?? 0;
    final data = res.data ?? {};
    if (status != 200 || data['ok'] != true) {
      throw Exception(
        _friendlyAuthMessage(
          status,
          (data['msg'] ?? 'Login failed').toString(),
          (data['code'] ?? '').toString(),
        ),
      );
    }

    final token = (data['token'] as String?) ?? '';
    final userMap = Map<String, dynamic>.from(data['user'] as Map);
    final model = UserModel.fromJson(userMap);
    final blockedAtLogin =
        userMap['is_blocked'] == true ||
        data['blocked'] == true ||
        data['error'] == 'blocked' ||
        status == 423;
    if (blockedAtLogin) {
      await _signOutLocalOnly();
      throw Exception('Your account has been blocked.');
    }

    await storage.saveAuth(token, model.toJson());
    try {
      await PushService.instance.init(api: api);
      await PushService.instance.requestPermissionAndRegister();
    } catch (error) {
      debugPrint('[push] registration after login failed: $error');
    }

    try {
      final verification = await api.get<Map<String, dynamic>>('ws/verify');
      final verificationData = verification.data ?? {};
      if ((verification.statusCode ?? 0) != 200 ||
          verificationData['blocked'] == true) {
        await _signOutLocalOnly();
        throw Exception('Your account has been blocked.');
      }
    } catch (error) {
      debugPrint('[auth] ws verify failed: $error');
      await _signOutLocalOnly();
      rethrow;
    }

    debugPrint('[auth] login flow completed successfully');
    return model;
  }

  Future<Never> _throwFriendlyBackendError(DioException error) async {
    final status = error.response?.statusCode;
    final body = error.response?.data;
    if (status == 423 ||
        body == 'blocked' ||
        (body is Map &&
            (body['blocked'] == true || body['error'] == 'blocked'))) {
      await _signOutLocalOnly();
      throw Exception('Your account has been blocked.');
    }
    if (body is Map<String, dynamic>) {
      throw Exception(
        _friendlyAuthMessage(
          status ?? 0,
          (body['msg'] ?? error.message ?? 'Login failed').toString(),
          (body['code'] ?? '').toString(),
        ),
      );
    }
    throw Exception('Login failed. ${error.message ?? 'Please try again.'}');
  }

  Future<void> logout() async {
    await PushService.instance.unregisterToken();
    try {
      await api.post('auth/logout');
    } catch (_) {}
    try {
      await _gsi.signOut();
    } catch (_) {}
    await fb.FirebaseAuth.instance.signOut();
    await PresenceService.instance.stop();
    try {
      if (Get.isRegistered<CallSocketService>())
        await Get.find<CallSocketService>().stop();
    } catch (_) {}
    await storage.clear();
    Get.offAllNamed(Routes.login);
  }

  Future<void> forceLogout(String reason) async {
    await PushService.instance.unregisterToken();
    await _signOutLocalOnly();
    Get.offAllNamed(Routes.login);
  }

  String _friendlyAuthMessage(int status, String msg, String code) {
    if (status == 503 && code == 'firebase_service_account_missing') {
      return 'Server login is not configured yet. Firebase admin credentials are missing.';
    }
    if (status == 503 && code == 'firebase_project_id_missing') {
      return 'Server login is not configured correctly. Firebase project id is missing.';
    }
    if (status == 401 && code == 'firebase_token_invalid') {
      return 'Google sign-in expired or is invalid. Please sign in again.';
    }
    if (status == 422 && code == 'firebase_email_missing') {
      return 'This Google account did not provide an email address.';
    }
    return msg;
  }
}
