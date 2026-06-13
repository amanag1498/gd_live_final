// lib/modules/home/views/home_view.dart
import 'dart:ui' show ImageFilter;
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:get/get.dart';
import 'package:gd_live/services/app_settings_service.dart';

import '../../../../app/brand/brand.dart';
import '../../../../app/routes/app_routes.dart';
import '../../../../app/widgets/haptics.dart';
import '../../../../app/widgets/gd_modal_surface.dart';
import '../../Live/views/live_preflight_sheet.dart';
import '../../dashboard/views/dashboard_page.dart';
import '../pages/rooms_page.dart';
import '../pages/settings_page.dart';
import '../controllers/home_controller.dart';

class HomeView extends GetView<HomeController> {
  const HomeView({super.key});

  @override
  Widget build(BuildContext context) {
    return Obx(() {
      final u = controller.currentUser.value ?? controller.auth.currentUser;
      if (u == null) {
        return const SizedBox.shrink();
      }
      final appSettings = Get.find<AppSettingsService>();
      final mq = MediaQuery.of(context);
      final reduceMotion = mq.disableAnimations || mq.accessibleNavigation;
      final canGoLive =
          u.isHost && u.canGoLive && appSettings.anyLiveCreationEnabled;

      return _HomeShell(
        userName: u.name,
        avatarUrl: u.avatarUrl,
        canGoLive: canGoLive,
        reduceMotion: reduceMotion,
        onLogout: () async {
          Haptics.medium();
          await controller.logout();
          Get.offAllNamed(Routes.login);
        },
        onGoLive: () async {
          Haptics.medium();
          await showLivePreflightSheet(context, initialTitle: 'GD Live');
        },
      );
    });
  }
}

class _HomeShell extends StatefulWidget {
  final String userName;
  final String? avatarUrl;
  final bool canGoLive;
  final bool reduceMotion;
  final Future<void> Function() onLogout;
  final Future<void> Function() onGoLive;

  const _HomeShell({
    required this.userName,
    required this.avatarUrl,
    required this.canGoLive,
    required this.reduceMotion,
    required this.onLogout,
    required this.onGoLive,
  });

  @override
  State<_HomeShell> createState() => _HomeShellState();
}

class _HomeShellState extends State<_HomeShell> {
  int _index = 0;
  int _previousIndex = 0;
  bool _handlingExitPrompt = false;

  void _setTabIndex(int nextIndex) {
    if (!mounted || nextIndex == _index) return;
    setState(() {
      _previousIndex = _index;
      _index = nextIndex;
    });
  }

  void _handleHorizontalSwipe(DragEndDetails details, int tabCount) {
    final velocity = details.primaryVelocity ?? 0;
    if (velocity.abs() < 180 || tabCount <= 1) return;

    if (velocity < 0 && _index < tabCount - 1) {
      Haptics.selection();
      _setTabIndex(_index + 1);
    } else if (velocity > 0 && _index > 0) {
      Haptics.selection();
      _setTabIndex(_index - 1);
    }
  }

  BrandTokens _tokens() {
    final settings = Get.find<AppSettingsService>();
    return getBrandTokens(settings.brandKey);
  }

  @override
  Widget build(BuildContext context) {
    final mq = MediaQuery.of(context);
    final appSettings = Get.find<AppSettingsService>();
    final tokens = _tokens();

    return Obx(() {
      final tabs = _buildTabs(appSettings, _index);
      final safeIndex = _index.clamp(0, tabs.length - 1);
      if (safeIndex != _index) {
        WidgetsBinding.instance.addPostFrameCallback((_) {
          if (!mounted) return;
          _setTabIndex(safeIndex);
        });
      }

      return PopScope(
        canPop: false,
        onPopInvokedWithResult: (didPop, _) async {
          if (didPop || _handlingExitPrompt) return;
          _handlingExitPrompt = true;
          try {
            final shouldExit = await _showExitPrompt(context);
            if (shouldExit == true) {
              await SystemNavigator.pop();
            }
          } finally {
            _handlingExitPrompt = false;
          }
        },
        child: Scaffold(
          backgroundColor: tokens.backgroundGradient.first,
          floatingActionButton: widget.canGoLive
              ? Padding(
                  padding: const EdgeInsets.fromLTRB(8, 0, 12, 16),
                  child: FloatingActionButton.extended(
                    onPressed: widget.onGoLive,
                    backgroundColor: tokens.primaryButtonGradient.first,
                    foregroundColor: Colors.white,
                    elevation: 4,
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(18),
                    ),
                    icon: const Icon(Icons.wifi_tethering_rounded),
                    label: const Text(
                      'Go Live',
                      style: TextStyle(fontWeight: FontWeight.w700),
                    ),
                  ),
                )
              : null,
          floatingActionButtonLocation: FloatingActionButtonLocation.endFloat,
          body: Stack(
            children: [
              Positioned.fill(child: _HomeBackdrop(tokens: tokens)),
              Column(
                children: [
                  SizedBox(height: mq.padding.top + 6),
                  Padding(
                    padding: const EdgeInsets.fromLTRB(12, 0, 12, 14),
                    child: SizedBox(
                      height: 58,
                      child: _HomeTopTabs(
                        tabs: tabs,
                        currentIndex: safeIndex,
                        tokens: tokens,
                        onChanged: (i) {
                          if (i != _index) HapticFeedback.selectionClick();
                          _setTabIndex(i);
                        },
                      ),
                    ),
                  ),
                  Expanded(
                    child: GestureDetector(
                      behavior: HitTestBehavior.translucent,
                      onHorizontalDragEnd: (details) =>
                          _handleHorizontalSwipe(details, tabs.length),
                      child: Stack(
                        children: [
                          for (var i = 0; i < tabs.length; i++)
                            _AnimatedHomePage(
                              active: i == safeIndex,
                              movingForward: safeIndex >= _previousIndex,
                              child: tabs[i].page,
                            ),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      );
    });
  }

  Future<bool?> _showExitPrompt(BuildContext context) {
    final tokens = _tokens();
    return showDialog<bool>(
      context: context,
      barrierDismissible: true,
      builder: (context) {
        return Dialog(
          backgroundColor: Colors.transparent,
          insetPadding: const EdgeInsets.symmetric(horizontal: 22),
          child: ClipRRect(
            borderRadius: BorderRadius.circular(30),
            child: BackdropFilter(
              filter: ImageFilter.blur(sigmaX: 18, sigmaY: 18),
              child: Container(
                padding: const EdgeInsets.fromLTRB(20, 20, 20, 18),
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topLeft,
                    end: Alignment.bottomRight,
                    colors: [
                      tokens.cardGradient.first.withOpacity(.96),
                      tokens.cardGradient.last.withOpacity(.92),
                    ],
                  ),
                  borderRadius: BorderRadius.circular(30),
                  border: Border.all(
                    color: tokens.borderColor.withOpacity(.9),
                  ),
                  boxShadow: [
                    BoxShadow(
                      color: tokens.glowColor.withOpacity(.18),
                      blurRadius: 28,
                      offset: const Offset(0, 18),
                    ),
                  ],
                ),
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Container(
                      width: 52,
                      height: 52,
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(18),
                        gradient: LinearGradient(
                          colors: tokens.primaryButtonGradient,
                        ),
                      ),
                      child: Icon(
                        Icons.exit_to_app_rounded,
                        color: tokens.textPrimary,
                        size: 26,
                      ),
                    ),
                    const SizedBox(height: 16),
                    Text(
                      'Exit GD Live?',
                      style: Theme.of(context).textTheme.titleLarge?.copyWith(
                        color: tokens.textPrimary,
                        fontWeight: FontWeight.w900,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'You are on the main screen. Do you want to close the app now?',
                      style: TextStyle(
                        color: tokens.textSecondary.withOpacity(.92),
                        fontWeight: FontWeight.w600,
                        height: 1.45,
                      ),
                    ),
                    const SizedBox(height: 18),
                    Row(
                      children: [
                        Expanded(
                          child: OutlinedButton(
                            onPressed: () => Navigator.of(context).pop(false),
                            style: OutlinedButton.styleFrom(
                              foregroundColor: tokens.textPrimary,
                              side: BorderSide(
                                color: tokens.borderColor.withOpacity(.9),
                              ),
                              padding: const EdgeInsets.symmetric(vertical: 14),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(18),
                              ),
                            ),
                            child: const Text('Stay'),
                          ),
                        ),
                        const SizedBox(width: 12),
                        Expanded(
                          child: FilledButton(
                            onPressed: () => Navigator.of(context).pop(true),
                            style: FilledButton.styleFrom(
                              backgroundColor: tokens.dangerColor,
                              foregroundColor: Colors.white,
                              padding: const EdgeInsets.symmetric(vertical: 14),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(18),
                              ),
                            ),
                            child: const Text('Exit'),
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
      },
    );
  }

  List<_HomeTabSpec> _buildTabs(AppSettingsService settings, int currentIndex) {
    final tabs = <_HomeTabSpec>[];

    void addTab({
      required IconData icon,
      required String label,
      required Widget Function(bool isActive) pageBuilder,
    }) {
      final tabIndex = tabs.length;
      tabs.add(
        _HomeTabSpec(
          icon: icon,
          label: label,
          page: pageBuilder(currentIndex == tabIndex),
        ),
      );
    }

    if (settings.videoRoomsEnabled) {
      addTab(
        icon: Icons.ondemand_video_rounded,
        label: 'Live',
        pageBuilder: (_) => const RoomsPage(bottomPadding: 120),
      );
    }
    addTab(
      icon: Icons.workspace_premium_rounded,
      label: 'Dashboard',
      pageBuilder:
          (isActive) => DashboardPage(
            bottomPadding: 120,
            isActive: isActive,
          ),
    );

    addTab(
      icon: Icons.tune_rounded,
      label: 'Settings',
      pageBuilder: (_) => const SettingsPage(bottomPadding: 120),
    );

    return tabs;
  }
}

class _AnimatedHomePage extends StatelessWidget {
  const _AnimatedHomePage({
    required this.active,
    required this.movingForward,
    required this.child,
  });

  final bool active;
  final bool movingForward;
  final Widget child;

  @override
  Widget build(BuildContext context) {
    final hiddenOffset = movingForward
        ? const Offset(0.05, 0)
        : const Offset(-0.05, 0);

    return IgnorePointer(
      ignoring: !active,
      child: AnimatedSlide(
        duration: const Duration(milliseconds: 420),
        curve: Curves.easeInOutCubicEmphasized,
        offset: active ? Offset.zero : hiddenOffset,
        child: AnimatedScale(
          duration: const Duration(milliseconds: 420),
          curve: Curves.easeInOutCubicEmphasized,
          scale: active ? 1 : 0.985,
          child: AnimatedOpacity(
            duration: const Duration(milliseconds: 280),
            curve: Curves.easeOutCubic,
            opacity: active ? 1 : 0,
            child: child,
          ),
        ),
      ),
    );
  }
}

class _HomeBackdrop extends StatelessWidget {
  const _HomeBackdrop({
    required this.tokens,
  });

  final BrandTokens tokens;

  @override
  Widget build(BuildContext context) {
    return DecoratedBox(
      decoration: const BoxDecoration(
        gradient: LinearGradient(
          colors: [
            Color(0xFFFFFFFF),
            Color(0xFFF2FBF4),
            Color(0xFFDAF5E5),
            Color(0xFFA8E6A1),
          ],
          stops: [0.0, 0.5, 0.8, 1.0],
          begin: Alignment.centerLeft,
          end: Alignment.centerRight,
        ),
      ),
      child: Stack(
        children: [
          Positioned(
            top: -80,
            right: -30,
            child: _HomeGlow(
              size: 220,
              color: tokens.primaryButtonGradient.last.withOpacity(.18),
            ),
          ),
          Positioned(
            left: -60,
            bottom: 120,
            child: _HomeGlow(
              size: 180,
              color: tokens.primaryButtonGradient.first.withOpacity(.12),
            ),
          ),
        ],
      ),
    );
  }
}

class _HomeGlow extends StatelessWidget {
  const _HomeGlow({
    required this.size,
    required this.color,
  });

  final double size;
  final Color color;

  @override
  Widget build(BuildContext context) {
    return IgnorePointer(
      child: Container(
        width: size,
        height: size,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          gradient: RadialGradient(
            colors: [
              color,
              color.withOpacity(.18),
              Colors.transparent,
            ],
          ),
        ),
      ),
    );
  }
}

class _HomeTopTabs extends StatelessWidget {
  const _HomeTopTabs({
    required this.tabs,
    required this.currentIndex,
    required this.tokens,
    required this.onChanged,
  });

  final List<_HomeTabSpec> tabs;
  final int currentIndex;
  final BrandTokens tokens;
  final ValueChanged<int> onChanged;

  @override
  Widget build(BuildContext context) {
    return ListView.separated(
      padding: const EdgeInsets.only(left: 14, right: 10, top: 6, bottom: 6),
      scrollDirection: Axis.horizontal,
      itemBuilder: (context, index) {
        final selected = index == currentIndex;
        final tab = tabs[index];
        return GestureDetector(
          onTap: () => onChanged(index),
          child: AnimatedContainer(
            duration: const Duration(milliseconds: 260),
            curve: Curves.easeOutCubic,
            padding: EdgeInsets.only(
              left: selected ? 2 : 0,
              right: selected ? 6 : 0,
              top: selected ? 4 : 10,
              bottom: selected ? 0 : 12,
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                AnimatedDefaultTextStyle(
                  duration: const Duration(milliseconds: 260),
                  curve: Curves.easeOutCubic,
                  style: TextStyle(
                    color: selected
                        ? const Color(0xFF102715)
                        : const Color(0xFF1E3B25).withOpacity(.44),
                    fontSize: selected ? 25 : 15,
                    fontWeight: selected ? FontWeight.w700 : FontWeight.w500,
                    letterSpacing: selected ? -0.65 : -0.05,
                    fontFamily: 'Poppins',
                    height: selected ? 1.0 : 1.06,
                  ),
                  child: Text(tab.label),
                ),
                const SizedBox(height: 3),
                AnimatedSlide(
                  duration: const Duration(milliseconds: 320),
                  curve: Curves.easeOutCubic,
                  offset: selected ? Offset.zero : const Offset(-0.18, 0),
                  child: AnimatedOpacity(
                    duration: const Duration(milliseconds: 220),
                    curve: Curves.easeOutCubic,
                    opacity: selected ? 1 : 0,
                    child: AnimatedContainer(
                      duration: const Duration(milliseconds: 320),
                      curve: Curves.easeOutCubic,
                      width: selected ? 20 : 12,
                      height: 2,
                      decoration: BoxDecoration(
                        gradient: LinearGradient(
                          colors: [
                            tokens.primaryButtonGradient.first.withOpacity(.98),
                            tokens.primaryButtonGradient.last.withOpacity(.86),
                          ],
                        ),
                        borderRadius: BorderRadius.circular(999),
                        boxShadow: [
                          BoxShadow(
                            color: tokens.primaryButtonGradient.first.withOpacity(.16),
                            blurRadius: 8,
                            offset: const Offset(0, 2),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        );
      },
      separatorBuilder: (_, __) => const SizedBox(width: 18),
      itemCount: tabs.length,
    );
  }
}

class _HomeTabSpec {
  const _HomeTabSpec({
    required this.icon,
    required this.label,
    required this.page,
  });

  final IconData icon;
  final String label;
  final Widget page;
}
