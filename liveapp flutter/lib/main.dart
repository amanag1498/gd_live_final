import 'dart:async';

import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:firebase_core/firebase_core.dart';

import 'package:gd_live/app/routes/app_urls.dart';
import 'package:gd_live/firebase_options.dart';
import 'package:gd_live/services/push_service.dart';

import 'app/routes/app_pages.dart';
import 'app/routes/app_routes.dart';
import 'app/brand/brand.dart';
import 'app/widgets/app_runtime_gate.dart';
import 'services/app_settings_service.dart';

void main() {
  WidgetsFlutterBinding.ensureInitialized();
  runApp(const _StartupGate());
}

Future<_StartupConfig> _initializeApp() async {
  await SystemChrome.setPreferredOrientations(const [
    DeviceOrientation.portraitUp,
  ]);
  await Firebase.initializeApp(
    options: DefaultFirebaseOptions.currentPlatform,
  ).timeout(
    const Duration(seconds: 20),
    onTimeout:
        () =>
            throw TimeoutException(
              'Firebase initialization timed out. Check the network and Firebase configuration.',
            ),
  );
  FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);
  await GetStorage.init().timeout(
    const Duration(seconds: 10),
    onTimeout:
        () => throw TimeoutException('Local storage initialization timed out.'),
  );

  final box = GetStorage();
  final token = box.read<String>('auth_token');
  const devInitialRoute = '';
  final initialRoute =
      devInitialRoute.isNotEmpty
          ? devInitialRoute
          : (token != null && token.isNotEmpty)
          ? Routes.home
          : Routes.login;

  return _StartupConfig(apiBase: AppUrls.apiBase, initialRoute: initialRoute);
}

class _StartupConfig {
  const _StartupConfig({required this.apiBase, required this.initialRoute});

  final String apiBase;
  final String initialRoute;
}

class _StartupGate extends StatefulWidget {
  const _StartupGate();

  @override
  State<_StartupGate> createState() => _StartupGateState();
}

class _StartupGateState extends State<_StartupGate> {
  late Future<_StartupConfig> _initialization;

  @override
  void initState() {
    super.initState();
    _initialization = _initializeApp();
  }

  void _retry() {
    setState(() {
      _initialization = _initializeApp();
    });
  }

  @override
  Widget build(BuildContext context) {
    return FutureBuilder<_StartupConfig>(
      future: _initialization,
      builder: (context, snapshot) {
        final config = snapshot.data;
        if (config != null) {
          return MyApp(
            apiBase: config.apiBase,
            initialRoute: config.initialRoute,
          );
        }

        return _StartupScreen(
          error: snapshot.hasError ? snapshot.error.toString() : null,
          onRetry: _retry,
        );
      },
    );
  }
}

class _StartupScreen extends StatelessWidget {
  const _StartupScreen({required this.error, required this.onRetry});

  final String? error;
  final VoidCallback onRetry;

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'GD Live',
      debugShowCheckedModeBanner: false,
      home: Scaffold(
        body: DecoratedBox(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              colors: [Color(0xFFFFFFFF), Color(0xFFE3F8E6)],
            ),
          ),
          child: SafeArea(
            child: Center(
              child: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 32),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    Image.asset(
                      'assets/logos/gd-live-logo.png',
                      width: 132,
                      height: 132,
                      fit: BoxFit.contain,
                    ),
                    const SizedBox(height: 24),
                    if (error == null)
                      const SizedBox(
                        width: 28,
                        height: 28,
                        child: CircularProgressIndicator(
                          strokeWidth: 3,
                          color: kGdLivePrimary,
                        ),
                      )
                    else ...[
                      const Text(
                        'Unable to start GD Live',
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          color: Color(0xFF15351C),
                          fontSize: 18,
                          fontWeight: FontWeight.w800,
                        ),
                      ),
                      const SizedBox(height: 8),
                      Text(
                        error!,
                        maxLines: 4,
                        overflow: TextOverflow.ellipsis,
                        textAlign: TextAlign.center,
                        style: const TextStyle(
                          color: Color(0xFF587561),
                          fontSize: 13,
                          height: 1.35,
                        ),
                      ),
                      const SizedBox(height: 20),
                      FilledButton(
                        onPressed: onRetry,
                        style: FilledButton.styleFrom(
                          backgroundColor: kGdLivePrimary,
                          foregroundColor: Colors.white,
                        ),
                        child: const Text('Try again'),
                      ),
                    ],
                  ],
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class MyApp extends StatelessWidget {
  final String apiBase;
  final String initialRoute;
  const MyApp({super.key, required this.apiBase, required this.initialRoute});

  @override
  Widget build(BuildContext context) {
    final pages = AppPages.pages(apiBase);
    final appSettings = Get.find<AppSettingsService>();

    return Obx(() {
      final brandKey = appSettings.brandKey;

      return GetMaterialApp(
        title: 'GD Live',
        debugShowCheckedModeBanner: false,
        theme: gdLiveLightTheme(brandKey),
        darkTheme: gdLiveDarkTheme(brandKey),
        themeMode: ThemeMode.dark,
        initialRoute: initialRoute,
        getPages: pages,
        builder: (context, child) {
          return AppRuntimeGate(child: child ?? const SizedBox.shrink());
        },
      );
    });
  }
}
