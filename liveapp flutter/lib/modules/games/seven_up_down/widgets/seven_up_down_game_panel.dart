import 'dart:async';
import 'dart:math';

import 'package:flutter/material.dart';
import 'package:get/get.dart';

import '../../../../app/routes/app_urls.dart';
import '../../../../app/widgets/haptics.dart';
import '../../../../services/api_client.dart';
import '../../../../services/storage_service.dart';
import '../../../wallet/widgets/recharge_bottom_sheet.dart';
import '../models/seven_up_down_models.dart';
import '../services/seven_up_down_api.dart';
import '../services/seven_up_down_socket_service.dart';

class SevenUpDownGamePanel extends StatefulWidget {
  const SevenUpDownGamePanel({super.key});

  @override
  State<SevenUpDownGamePanel> createState() => _SevenUpDownGamePanelState();
}

class _SevenUpDownGamePanelState extends State<SevenUpDownGamePanel>
    with TickerProviderStateMixin {
  static const _chips = [50, 200, 500, 1000, 5000];
  static const _potColors = {
    'DOWN': Color(0xFF4BD5FF),
    'SEVEN': Color(0xFFFFD45E),
    'UP': Color(0xFFFF6FA8),
  };

  final _socket = SevenUpDownSocketService();
  final _random = Random();
  late final SevenUpDownApi _api;
  late final AnimationController _idleController;
  late final AnimationController _diceController;
  late final AnimationController _lockController;
  late final AnimationController _chipFlightController;
  late final AnimationController _resultController;
  late final AnimationController _roundIntroController;
  late final AnimationController _celebrationController;
  StreamSubscription<Map<String, dynamic>>? _snapshotSub;
  StreamSubscription<Map<String, dynamic>>? _eventSub;
  Timer? _clock;
  Timer? _feedbackTimer;
  SevenUpDownSnapshot? _snapshot;
  SevenUpDownRound? _pendingDiceResult;
  DateTime _now = DateTime.now();
  int _selectedChip = 50;
  int _shownDiceOne = 1;
  int _shownDiceTwo = 6;
  int _diceFrame = -1;
  bool _loading = true;
  bool _placingBet = false;
  bool _rolling = false;
  String? _revealedRoundKey;
  String? _animatingRoundKey;
  String? _lastRoundKey;
  String? _lastPhase;
  String? _lastVisualPhase;
  String? _flyingPot;
  String? _betPulsePot;
  String? _betFeedback;
  String? _error;

  @override
  void initState() {
    super.initState();
    _api = SevenUpDownApi(Get.find<ApiClient>());
    _idleController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 2800),
    )..repeat();
    _diceController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1850),
    )..addListener(_updateRollingFaces);
    _lockController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 520),
    );
    _chipFlightController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 620),
    )..addStatusListener((status) {
      if (status == AnimationStatus.completed && mounted) {
        setState(() => _flyingPot = null);
      }
    });
    _resultController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 520),
    );
    _roundIntroController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 560),
    );
    _celebrationController = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1500),
    );
    _clock = Timer.periodic(
      const Duration(milliseconds: 250),
      (_) => _tickClock(),
    );
    unawaited(_start());
  }

  Future<void> _start() async {
    await _load();
    final token = Get.find<StorageService>().token;
    if (!mounted || token == null || token.isEmpty) return;
    _snapshotSub = _socket.snapshots.listen((payload) {
      if (!mounted) return;
      final data =
          payload['data'] is Map
              ? Map<String, dynamic>.from(payload['data'] as Map)
              : Map<String, dynamic>.from(payload);
      final next = SevenUpDownSnapshot.fromJson(data);
      _apply(
        _snapshot == null || data.containsKey('wallet_balance')
            ? next
            : _snapshot!.mergePublic(next),
      );
    });
    _eventSub = _socket.events.listen((event) {
      if (!mounted) return;
      if (event['event'] == 'feature:error') {
        setState(
          () => _error = (event['message'] ?? 'Game unavailable.').toString(),
        );
      } else if (event['event'] == 'seven_up_down:round_settled' ||
          event['event'] == 'seven_up_down:bet_refunded') {
        // Public room snapshots intentionally omit private wallet and bet state.
        // Refresh the authenticated snapshot when settlement changes either.
        unawaited(_load());
      }
    });
    await _socket.start(url: AppUrls.wsGames, token: token);
  }

  Future<void> _load() async {
    try {
      final next = await _api.fetchSnapshot();
      if (mounted) _apply(next);
    } catch (error) {
      if (mounted) {
        setState(
          () => _error = error.toString().replaceFirst('Exception: ', ''),
        );
      }
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _apply(SevenUpDownSnapshot next) {
    final round = next.round;
    final isNewRound = _lastRoundKey != round.roundKey;
    final phaseChanged = isNewRound || _lastPhase != round.phase;
    final visualPhase = _visualPhaseFor(round, DateTime.now());
    final visualPhaseChanged = isNewRound || _lastVisualPhase != visualPhase;
    final allowed =
        _chips
            .where(
              (chip) =>
                  chip >= next.settings.minBet && chip <= next.settings.maxBet,
            )
            .toList();

    if (isNewRound) {
      _pendingDiceResult = null;
      _animatingRoundKey = null;
      _revealedRoundKey = null;
      _rolling = false;
      _shownDiceOne = 1;
      _shownDiceTwo = 6;
      _diceController.reset();
      _lockController.reset();
      _resultController.reset();
      _roundIntroController.reset();
      _celebrationController.reset();
    }

    setState(() {
      _snapshot = next;
      _selectedChip =
          allowed.contains(_selectedChip)
              ? _selectedChip
              : (allowed.isEmpty ? next.settings.minBet : allowed.first);
      _lastRoundKey = round.roundKey;
      _lastPhase = round.phase;
      _lastVisualPhase = visualPhase;
      _error = null;
      _loading = false;
    });

    if (isNewRound) {
      unawaited(_roundIntroController.forward(from: 0));
    }
    if ((phaseChanged || visualPhaseChanged) && visualPhase == 'locked') {
      unawaited(_lockController.forward(from: 0));
      unawaited(Haptics.warning());
    }
    if (round.phase == 'result' &&
        round.diceOne != null &&
        round.diceTwo != null &&
        _revealedRoundKey != round.roundKey &&
        _animatingRoundKey != round.roundKey) {
      unawaited(_animateResult(round));
    }
  }

  void _updateRollingFaces() {
    if (!_rolling || !mounted) return;
    final frame = (_diceController.value * 18).floor();
    if (frame == _diceFrame) return;
    _diceFrame = frame;
    setState(() {
      _shownDiceOne = 1 + _random.nextInt(6);
      _shownDiceTwo = 1 + _random.nextInt(6);
    });
  }

  Future<void> _animateResult(SevenUpDownRound round) async {
    _pendingDiceResult = round;
    _animatingRoundKey = round.roundKey;
    _diceFrame = -1;
    _resultController.reset();
    _lockController.value = 1;
    setState(() => _rolling = true);
    try {
      await _diceController.forward(from: 0).orCancel;
    } on TickerCanceled {
      return;
    }
    if (!mounted || _pendingDiceResult?.roundKey != round.roundKey) return;
    setState(() {
      _shownDiceOne = round.diceOne!;
      _shownDiceTwo = round.diceTwo!;
      _rolling = false;
      _revealedRoundKey = round.roundKey;
      _animatingRoundKey = null;
    });
    final currentRound = _snapshot?.round;
    if (currentRound != null && _viewerPayout(currentRound) > 0) {
      unawaited(Haptics.success());
      unawaited(_celebrationController.forward(from: 0));
    } else {
      unawaited(Haptics.light());
    }
    await _resultController.forward(from: 0);
  }

  void _tickClock() {
    if (!mounted) return;
    final nextNow = DateTime.now();
    final round = _snapshot?.round;
    if (round == null) {
      setState(() => _now = nextNow);
      return;
    }
    final visualPhase = _visualPhaseFor(round, nextNow);
    final changed = _lastVisualPhase != visualPhase;
    setState(() {
      _now = nextNow;
      _lastVisualPhase = visualPhase;
    });
    if (changed && visualPhase == 'locked') {
      unawaited(_lockController.forward(from: 0));
      unawaited(Haptics.warning());
    }
  }

  String _visualPhaseFor(SevenUpDownRound round, DateTime now) {
    if (round.phase == 'result' || round.phase == 'cancelled') {
      return round.phase;
    }
    if (round.endsAt != null && !now.isBefore(round.endsAt!)) {
      return 'settling';
    }
    if (round.locksAt != null && !now.isBefore(round.locksAt!)) {
      return 'locked';
    }
    return round.phase;
  }

  String _visualPhase(SevenUpDownRound round) => _visualPhaseFor(round, _now);

  Future<void> _placeBet(String pot) async {
    final snapshot = _snapshot;
    if (_placingBet ||
        snapshot == null ||
        _visualPhase(snapshot.round) != 'betting' ||
        _countdown(snapshot.round) <= 0) {
      return;
    }
    if (_selectedChip > snapshot.walletBalance) {
      await showRechargeWalletSheet(
        context: context,
        reasonMessage: 'You need more coins to place this Lucky 7 bet.',
      );
      return;
    }

    _feedbackTimer?.cancel();
    setState(() {
      _placingBet = true;
      _flyingPot = pot;
      _betPulsePot = pot;
      _betFeedback = null;
      _error = null;
    });
    unawaited(Haptics.medium());
    unawaited(_chipFlightController.forward(from: 0));

    try {
      final next = await _api.placeBet(
        pot: pot,
        amount: _selectedChip,
        idempotencyKey: 'sud_${DateTime.now().microsecondsSinceEpoch}_$pot',
      );
      if (!mounted) return;
      _apply(next);
      unawaited(Haptics.success());
      setState(() => _betFeedback = '$_selectedChip coins placed on $pot');
      _feedbackTimer = Timer(const Duration(milliseconds: 1800), () {
        if (mounted) setState(() => _betFeedback = null);
      });
    } catch (error) {
      if (mounted) {
        unawaited(Haptics.error());
        setState(() {
          _betPulsePot = null;
          _betFeedback = null;
          _error = error.toString().replaceFirst('Exception: ', '');
        });
      }
    } finally {
      if (mounted) setState(() => _placingBet = false);
    }
  }

  int _countdown(SevenUpDownRound round) {
    final target = switch (_visualPhase(round)) {
      'betting' => round.locksAt,
      'locked' || 'settling' => round.endsAt,
      'result' => round.displayUntil,
      _ => null,
    };
    if (target == null) return 0;
    return max(0, target.difference(_now).inSeconds);
  }

  double _phaseProgress(SevenUpDownRound round) {
    final phase = _visualPhase(round);
    final (start, end) = switch (phase) {
      'betting' => (round.startsAt, round.locksAt),
      'locked' || 'settling' => (round.locksAt, round.endsAt),
      'result' => (round.endsAt, round.displayUntil),
      _ => (null, null),
    };
    if (start == null || end == null || !end.isAfter(start)) return 0;
    final total = end.difference(start).inMilliseconds;
    final remaining = end.difference(_now).inMilliseconds;
    return (remaining / total).clamp(0.0, 1.0).toDouble();
  }

  int _viewerAmount(SevenUpDownRound round, String pot) => round.viewerBets
      .where((bet) => bet.pot == pot && bet.status != 'refunded')
      .fold(0, (sum, bet) => sum + bet.amount);

  int _viewerPayout(SevenUpDownRound round) => round.viewerBets
      .where((bet) => bet.status == 'won')
      .fold(0, (sum, bet) => sum + bet.payoutCoins);

  @override
  void dispose() {
    _clock?.cancel();
    _feedbackTimer?.cancel();
    _snapshotSub?.cancel();
    _eventSub?.cancel();
    _idleController.dispose();
    _diceController.dispose();
    _lockController.dispose();
    _chipFlightController.dispose();
    _resultController.dispose();
    _roundIntroController.dispose();
    _celebrationController.dispose();
    _socket.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    if (_loading) {
      return const ColoredBox(
        color: Color(0xFF090B22),
        child: Center(child: CircularProgressIndicator()),
      );
    }
    final snapshot = _snapshot;
    if (snapshot == null) return _message(_error ?? 'Unable to load game.');
    final round = snapshot.round;
    final revealed = _revealedRoundKey == round.roundKey && !_rolling;
    final won = _viewerPayout(round);
    final chips = _chips.where(
      (chip) =>
          chip >= snapshot.settings.minBet && chip <= snapshot.settings.maxBet,
    );

    return LayoutBuilder(
      builder: (context, constraints) {
        return Stack(
          clipBehavior: Clip.hardEdge,
          children: [
            Positioned.fill(
              child: Image.asset(
                'assets/games/seven_up_down/table_background.png',
                fit: BoxFit.cover,
                alignment: Alignment.topCenter,
              ),
            ),
            const Positioned.fill(
              child: DecoratedBox(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    colors: [Color(0x33000000), Color(0xAA07091E)],
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                    stops: [0.2, 1],
                  ),
                ),
              ),
            ),
            Positioned.fill(
              child: IgnorePointer(
                child: RepaintBoundary(
                  child: AnimatedBuilder(
                    animation: _idleController,
                    builder:
                        (context, _) => CustomPaint(
                          painter: _AmbientParticlePainter(
                            _idleController.value,
                          ),
                        ),
                  ),
                ),
              ),
            ),
            if (revealed && won > 0)
              Positioned.fill(
                child: IgnorePointer(
                  child: RepaintBoundary(
                    child: AnimatedBuilder(
                      animation: _celebrationController,
                      builder:
                          (context, _) => CustomPaint(
                            painter: _CelebrationPainter(
                              progress: _celebrationController.value,
                              color:
                                  _potColors[round.winningPot] ?? Colors.white,
                            ),
                          ),
                    ),
                  ),
                ),
              ),
            AnimatedBuilder(
              animation: _roundIntroController,
              builder: (context, child) {
                final progress = Curves.easeOutCubic.transform(
                  _roundIntroController.value,
                );
                return Opacity(
                  opacity: .25 + (.75 * progress),
                  child: Transform.translate(
                    offset: Offset(0, 18 * (1 - progress)),
                    child: child,
                  ),
                );
              },
              child: SafeArea(
                top: false,
                child: ListView(
                  padding: const EdgeInsets.fromLTRB(14, 8, 14, 28),
                  children: [
                    _header(
                      snapshot.walletBalance,
                      snapshot.settings.displayName,
                    ),
                    const SizedBox(height: 8),
                    _phaseBanner(round, revealed),
                    const SizedBox(height: 10),
                    _diceStage(round, revealed),
                    if (revealed) ...[
                      const SizedBox(height: 8),
                      _resultBanner(round),
                    ],
                    const SizedBox(height: 14),
                    Row(
                      children: [
                        Expanded(
                          child: _pot(
                            round,
                            'DOWN',
                            '7 DOWN',
                            'TOTAL 2 - 6',
                            revealed,
                          ),
                        ),
                        const SizedBox(width: 7),
                        Expanded(
                          child: _pot(
                            round,
                            'SEVEN',
                            'EXACT 7',
                            'TOTAL 7',
                            revealed,
                          ),
                        ),
                        const SizedBox(width: 7),
                        Expanded(
                          child: _pot(
                            round,
                            'UP',
                            '7 UP',
                            'TOTAL 8 - 12',
                            revealed,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 14),
                    _chipTray(chips, round),
                    const SizedBox(height: 12),
                    _gameDetails(snapshot),
                    if (_betFeedback != null)
                      Padding(
                        padding: const EdgeInsets.only(top: 10),
                        child: Text(
                          _betFeedback!,
                          textAlign: TextAlign.center,
                          style: const TextStyle(
                            color: Color(0xFF83F3BB),
                            fontWeight: FontWeight.w800,
                          ),
                        ),
                      ),
                    if (_error != null)
                      Padding(
                        padding: const EdgeInsets.only(top: 10),
                        child: Text(
                          _error!,
                          textAlign: TextAlign.center,
                          style: const TextStyle(color: Color(0xFFFF9B9B)),
                        ),
                      ),
                    if (snapshot.history.isNotEmpty) ...[
                      const SizedBox(height: 18),
                      _history(snapshot.history),
                    ],
                  ],
                ),
              ),
            ),
            if (_flyingPot != null)
              _flyingChip(constraints.maxWidth, constraints.maxHeight),
          ],
        );
      },
    );
  }

  Widget _header(int walletBalance, String displayName) => Row(
    children: [
      Container(
        width: 38,
        height: 38,
        decoration: BoxDecoration(
          color: const Color(0xFFFFD45E).withValues(alpha: .13),
          border: Border.all(
            color: const Color(0xFFFFD45E).withValues(alpha: .5),
          ),
          borderRadius: BorderRadius.circular(12),
        ),
        child: const Icon(Icons.casino_rounded, color: Color(0xFFFFD45E)),
      ),
      const SizedBox(width: 10),
      Expanded(
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              displayName.toUpperCase(),
              style: const TextStyle(
                color: Colors.white,
                fontSize: 20,
                fontWeight: FontWeight.w900,
                letterSpacing: .7,
              ),
            ),
            const Text(
              'TWO DICE · THREE OUTCOMES',
              style: TextStyle(
                color: Colors.white54,
                fontSize: 9,
                fontWeight: FontWeight.w700,
                letterSpacing: 1.2,
              ),
            ),
          ],
        ),
      ),
      _pill('$walletBalance', const Color(0xFFFFD45E), Icons.paid_rounded),
    ],
  );

  Widget _phaseBanner(SevenUpDownRound round, bool revealed) {
    final countdown = _countdown(round);
    final phase = _visualPhase(round);
    final (label, color, icon) = switch (phase) {
      'betting' => (
        'PLACE YOUR BET · ${countdown}s',
        const Color(0xFF72F1C4),
        Icons.touch_app_rounded,
      ),
      'locked' => (
        'BETS LOCKED · ${countdown}s',
        const Color(0xFFFFD45E),
        Icons.lock_rounded,
      ),
      'settling' => (
        'RESULT INCOMING',
        const Color(0xFFFFD45E),
        Icons.hourglass_top_rounded,
      ),
      'result' when _rolling => (
        'DICE ROLLING…',
        const Color(0xFFFFD45E),
        Icons.casino_rounded,
      ),
      'result' when revealed => (
        'RESULT · ${round.diceTotal} · ${countdown}s',
        _potColors[round.winningPot] ?? Colors.white,
        Icons.emoji_events_rounded,
      ),
      _ => ('WAITING FOR ROUND', Colors.white70, Icons.schedule_rounded),
    };
    final urgent = phase == 'betting' && countdown <= 5;
    return AnimatedBuilder(
      animation: _idleController,
      builder: (context, _) {
        final pulse =
            urgent
                ? .92 + (.08 * sin(_idleController.value * pi * 4).abs())
                : 1.0;
        return Center(
          child: Transform.scale(
            scale: pulse,
            child: AnimatedContainer(
              duration: const Duration(milliseconds: 260),
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(
                color: color.withValues(alpha: urgent ? .2 : .12),
                border: Border.all(color: color.withValues(alpha: .65)),
                borderRadius: BorderRadius.circular(30),
                boxShadow: [
                  BoxShadow(
                    color: color.withValues(alpha: urgent ? .28 : .12),
                    blurRadius: urgent ? 22 : 16,
                  ),
                ],
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  SizedBox(
                    width: 20,
                    height: 20,
                    child: Stack(
                      alignment: Alignment.center,
                      children: [
                        CircularProgressIndicator(
                          value: _phaseProgress(round),
                          strokeWidth: 2.2,
                          color: color,
                          backgroundColor: Colors.white12,
                        ),
                        Icon(icon, color: color, size: 11),
                      ],
                    ),
                  ),
                  const SizedBox(width: 7),
                  Text(
                    label,
                    style: TextStyle(
                      color: color,
                      fontSize: 12,
                      fontWeight: FontWeight.w900,
                      letterSpacing: .5,
                    ),
                  ),
                ],
              ),
            ),
          ),
        );
      },
    );
  }

  Widget _diceStage(SevenUpDownRound round, bool revealed) {
    return AnimatedBuilder(
      animation: Listenable.merge([
        _idleController,
        _diceController,
        _lockController,
        _resultController,
      ]),
      builder: (context, _) {
        final phase = _visualPhase(round);
        final roll = Curves.easeInOutCubic.transform(_diceController.value);
        final idle = sin(_idleController.value * pi * 2);
        final lock = Curves.easeOutBack.transform(_lockController.value);
        final resultPulse = sin(_resultController.value * pi);
        final diceScale = 1 + (resultPulse * .12);
        return SizedBox(
          height: 170,
          child: Stack(
            alignment: Alignment.center,
            children: [
              Positioned(
                bottom: 17,
                child: Container(
                  width: 230,
                  height: 34,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(50),
                    boxShadow: const [
                      BoxShadow(
                        color: Color(0xAA050617),
                        blurRadius: 24,
                        spreadRadius: 7,
                      ),
                    ],
                  ),
                ),
              ),
              Transform.translate(
                offset: Offset(
                  phase == 'settling' ? idle * 5 : 0,
                  _rolling ? -40 * sin(roll * pi) : idle * 2,
                ),
                child: Transform.scale(
                  scale: diceScale,
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      _animatedDie(_shownDiceOne, roll, idle, false),
                      const SizedBox(width: 22),
                      _animatedDie(_shownDiceTwo, roll, -idle, true),
                    ],
                  ),
                ),
              ),
              if (phase == 'locked' || phase == 'settling')
                Opacity(
                  opacity: lock.clamp(0, 1),
                  child: Transform.scale(
                    scale: .7 + (.3 * lock),
                    child: Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 15,
                        vertical: 8,
                      ),
                      decoration: BoxDecoration(
                        color: const Color(0xDD11132B),
                        border: Border.all(color: const Color(0xFFFFD45E)),
                        borderRadius: BorderRadius.circular(30),
                        boxShadow: const [
                          BoxShadow(color: Color(0x88FFD45E), blurRadius: 20),
                        ],
                      ),
                      child: const Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Icon(
                            Icons.lock_rounded,
                            size: 16,
                            color: Color(0xFFFFD45E),
                          ),
                          SizedBox(width: 6),
                          Text(
                            'LOCKED',
                            style: TextStyle(
                              color: Colors.white,
                              fontWeight: FontWeight.w900,
                              letterSpacing: 1.4,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              if (_rolling)
                Positioned(
                  bottom: 1,
                  child: Text(
                    'ROLLING BACKEND RESULT',
                    style: TextStyle(
                      color: Colors.white.withValues(alpha: .7),
                      fontSize: 9,
                      fontWeight: FontWeight.w800,
                      letterSpacing: 1.1,
                    ),
                  ),
                ),
            ],
          ),
        );
      },
    );
  }

  Widget _animatedDie(int value, double roll, double idle, bool second) {
    final direction = second ? -1.0 : 1.0;
    final spinning = _rolling;
    final settling = _lastVisualPhase == 'settling';
    final rotation =
        spinning ? direction * roll * pi * 7 : idle * (settling ? .13 : .035);
    final tilt =
        spinning ? roll * pi * 5 : -.06 + (idle * (settling ? .07 : .025));
    return Transform(
      alignment: Alignment.center,
      transform:
          Matrix4.identity()
            ..setEntry(3, 2, .0018)
            ..rotateX(tilt)
            ..rotateY(rotation * .8)
            ..rotateZ(rotation),
      child: _ThreeDDice(value: value, size: 82),
    );
  }

  Widget _resultBanner(SevenUpDownRound round) {
    final won = _viewerPayout(round);
    final userHadBet = round.viewerBets.any((bet) => bet.status != 'refunded');
    final color = _potColors[round.winningPot] ?? Colors.white;
    return FadeTransition(
      opacity: CurvedAnimation(
        parent: _resultController,
        curve: Curves.easeOut,
      ),
      child: ScaleTransition(
        scale: Tween(begin: .88, end: 1.0).animate(
          CurvedAnimation(parent: _resultController, curve: Curves.easeOutBack),
        ),
        child: Container(
          padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 9),
          decoration: BoxDecoration(
            gradient: LinearGradient(
              colors: [color.withValues(alpha: .24), const Color(0xCC11132B)],
            ),
            border: Border.all(color: color.withValues(alpha: .75)),
            borderRadius: BorderRadius.circular(16),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                won > 0 ? Icons.emoji_events_rounded : Icons.casino_rounded,
                color: color,
              ),
              const SizedBox(width: 8),
              Flexible(
                child: Text(
                  won > 0
                      ? 'YOU WON $won COINS · ${round.winningPot}'
                      : userHadBet
                      ? '${round.winningPot} WINS · BETTER LUCK NEXT ROUND'
                      : '${round.winningPot} WINS · ${round.diceOne} + ${round.diceTwo} = ${round.diceTotal}',
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 12,
                    fontWeight: FontWeight.w900,
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _pot(
    SevenUpDownRound round,
    String pot,
    String title,
    String range,
    bool revealed,
  ) {
    final color = _potColors[pot]!;
    final winner = revealed && round.winningPot == pot;
    final bettingOpen =
        _visualPhase(round) == 'betting' &&
        _countdown(round) > 0 &&
        !_placingBet;
    final yourAmount = _viewerAmount(round, pot);
    final pulsing = _betPulsePot == pot && _placingBet;
    return GestureDetector(
      onTap: bettingOpen ? () => _placeBet(pot) : null,
      child: AnimatedScale(
        scale:
            pulsing
                ? .96
                : winner
                ? 1.035
                : 1,
        duration: const Duration(milliseconds: 180),
        child: AnimatedContainer(
          duration: const Duration(milliseconds: 280),
          padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 12),
          decoration: BoxDecoration(
            gradient: LinearGradient(
              begin: Alignment.topCenter,
              end: Alignment.bottomCenter,
              colors:
                  winner
                      ? [
                        color.withValues(alpha: .46),
                        color.withValues(alpha: .18),
                      ]
                      : [
                        Colors.white.withValues(alpha: .11),
                        const Color(0x9910132D),
                      ],
            ),
            border: Border.all(
              color: winner ? color : color.withValues(alpha: .48),
              width: winner ? 2.5 : 1,
            ),
            borderRadius: BorderRadius.circular(17),
            boxShadow:
                winner
                    ? [
                      BoxShadow(
                        color: color.withValues(alpha: .5),
                        blurRadius: 22,
                      ),
                    ]
                    : null,
          ),
          child: Column(
            children: [
              Text(
                title,
                textAlign: TextAlign.center,
                style: TextStyle(
                  color: color,
                  fontWeight: FontWeight.w900,
                  fontSize: 13,
                ),
              ),
              const SizedBox(height: 3),
              Text(
                range,
                textAlign: TextAlign.center,
                style: const TextStyle(
                  color: Colors.white60,
                  fontSize: 8,
                  fontWeight: FontWeight.w700,
                ),
              ),
              const SizedBox(height: 7),
              Text(
                '${round.multipliers[pot] ?? 0}x',
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 21,
                  fontWeight: FontWeight.w900,
                ),
              ),
              const SizedBox(height: 5),
              Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  const Icon(
                    Icons.paid_rounded,
                    color: Color(0xFFFFD45E),
                    size: 12,
                  ),
                  const SizedBox(width: 3),
                  Flexible(
                    child: Text(
                      '${round.totals[pot] ?? 0}',
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                        color: Colors.white70,
                        fontSize: 10,
                        fontWeight: FontWeight.w700,
                      ),
                    ),
                  ),
                ],
              ),
              if (yourAmount > 0) ...[
                const SizedBox(height: 5),
                Container(
                  padding: const EdgeInsets.symmetric(
                    horizontal: 6,
                    vertical: 3,
                  ),
                  decoration: BoxDecoration(
                    color: color.withValues(alpha: .17),
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: Text(
                    'YOU $yourAmount',
                    style: TextStyle(
                      color: color,
                      fontSize: 9,
                      fontWeight: FontWeight.w900,
                    ),
                  ),
                ),
              ],
              if (!bettingOpen && !winner) ...[
                const SizedBox(height: 5),
                const Icon(
                  Icons.lock_outline_rounded,
                  color: Colors.white38,
                  size: 13,
                ),
              ],
            ],
          ),
        ),
      ),
    );
  }

  Widget _chipTray(Iterable<int> chips, SevenUpDownRound round) {
    final enabled = _visualPhase(round) == 'betting' && _countdown(round) > 0;
    return AnimatedOpacity(
      opacity: enabled ? 1 : .5,
      duration: const Duration(milliseconds: 220),
      child: Container(
        padding: const EdgeInsets.fromLTRB(10, 9, 10, 10),
        decoration: BoxDecoration(
          color: const Color(0xCC090B21),
          border: Border.all(color: Colors.white.withValues(alpha: .12)),
          borderRadius: BorderRadius.circular(19),
        ),
        child: Column(
          children: [
            Text(
              enabled
                  ? 'SELECT CHIP & TAP A POT'
                  : 'CHIPS LOCKED FOR THIS ROUND',
              style: const TextStyle(
                color: Colors.white60,
                fontSize: 9,
                fontWeight: FontWeight.w800,
                letterSpacing: 1,
              ),
            ),
            const SizedBox(height: 8),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                for (final chip in chips)
                  GestureDetector(
                    onTap:
                        !enabled || _placingBet
                            ? null
                            : () {
                              unawaited(Haptics.selection());
                              setState(() => _selectedChip = chip);
                            },
                    child: AnimatedScale(
                      scale: chip == _selectedChip ? 1.12 : .94,
                      duration: const Duration(milliseconds: 180),
                      child: _GameChip(
                        value: chip,
                        selected: chip == _selectedChip,
                      ),
                    ),
                  ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _gameDetails(SevenUpDownSnapshot snapshot) {
    final round = snapshot.round;
    final rules = snapshot.settings.rules;
    final totalStake = round.viewerBets
        .where((bet) => bet.status != 'refunded')
        .fold(0, (sum, bet) => sum + bet.amount);
    return Theme(
      data: Theme.of(context).copyWith(dividerColor: Colors.transparent),
      child: Container(
        decoration: BoxDecoration(
          color: const Color(0xB3090B21),
          border: Border.all(color: Colors.white.withValues(alpha: .12)),
          borderRadius: BorderRadius.circular(17),
        ),
        child: Material(
          type: MaterialType.transparency,
          borderRadius: BorderRadius.circular(17),
          clipBehavior: Clip.antiAlias,
          child: ExpansionTile(
            tilePadding: const EdgeInsets.symmetric(
              horizontal: 13,
              vertical: 1,
            ),
            childrenPadding: const EdgeInsets.fromLTRB(13, 0, 13, 13),
            iconColor: const Color(0xFFFFD45E),
            collapsedIconColor: Colors.white54,
            title: const Text(
              'HOW LUCKY 7 WORKS',
              style: TextStyle(
                color: Colors.white,
                fontSize: 11,
                fontWeight: FontWeight.w900,
                letterSpacing: .7,
              ),
            ),
            subtitle: Text(
              totalStake > 0
                  ? 'Your current-round stake: $totalStake coins'
                  : 'Rules, returns and settlement details',
              style: const TextStyle(color: Colors.white54, fontSize: 10),
            ),
            children: [
              _ruleRow(
                color: _potColors['DOWN']!,
                title:
                    '7 Down · totals ${rules['DOWN']!.minTotal}–${rules['DOWN']!.maxTotal}',
                detail:
                    '${rules['DOWN']!.diceCombinations} dice combinations · ${round.multipliers['DOWN'] ?? 0}x total return',
              ),
              _ruleRow(
                color: _potColors['SEVEN']!,
                title: 'Exact 7 · total ${rules['SEVEN']!.minTotal}',
                detail:
                    '${rules['SEVEN']!.diceCombinations} dice combinations · ${round.multipliers['SEVEN'] ?? 0}x total return',
              ),
              _ruleRow(
                color: _potColors['UP']!,
                title:
                    '7 Up · totals ${rules['UP']!.minTotal}–${rules['UP']!.maxTotal}',
                detail:
                    '${rules['UP']!.diceCombinations} dice combinations · ${round.multipliers['UP'] ?? 0}x total return',
              ),
              const SizedBox(height: 8),
              const Text(
                'Select a chip and tap a pot before the timer locks. A multiplier is the total amount returned for a winning bet, including its stake. The server stores both dice before this app animates them; wallet payouts are credited during settlement and protected against duplicate processing.',
                style: TextStyle(
                  color: Colors.white60,
                  fontSize: 10,
                  height: 1.45,
                ),
              ),
              const SizedBox(height: 7),
              Row(
                children: [
                  const Icon(
                    Icons.verified_user_rounded,
                    color: Color(0xFF72F1C4),
                    size: 14,
                  ),
                  const SizedBox(width: 6),
                  Expanded(
                    child: Text(
                      'Round ${round.id} · ${round.roundKey}',
                      overflow: TextOverflow.ellipsis,
                      style: const TextStyle(
                        color: Color(0xFF72F1C4),
                        fontSize: 9,
                        fontWeight: FontWeight.w700,
                      ),
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

  Widget _ruleRow({
    required Color color,
    required String title,
    required String detail,
  }) => Padding(
    padding: const EdgeInsets.only(top: 8),
    child: Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Container(
          width: 7,
          height: 7,
          margin: const EdgeInsets.only(top: 4),
          decoration: BoxDecoration(
            color: color,
            shape: BoxShape.circle,
            boxShadow: [BoxShadow(color: color, blurRadius: 7)],
          ),
        ),
        const SizedBox(width: 8),
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                title,
                style: TextStyle(
                  color: color,
                  fontSize: 10,
                  fontWeight: FontWeight.w800,
                ),
              ),
              Text(
                detail,
                style: const TextStyle(color: Colors.white54, fontSize: 9),
              ),
            ],
          ),
        ),
      ],
    ),
  );

  Widget _flyingChip(double width, double height) {
    final pot = _flyingPot!;
    final targetFactor = switch (pot) {
      'DOWN' => -.29,
      'UP' => .29,
      _ => 0.0,
    };
    return Positioned.fill(
      child: IgnorePointer(
        child: AnimatedBuilder(
          animation: _chipFlightController,
          builder: (context, child) {
            final t = Curves.easeInOutCubic.transform(
              _chipFlightController.value,
            );
            final dx = width * targetFactor * t;
            final travel = min(280.0, max(170.0, height * .38));
            final dy = -(travel * t) - (sin(pi * t) * 48);
            final scale = 1 - (.25 * t);
            return Align(
              alignment: Alignment.bottomCenter,
              child: Padding(
                padding: const EdgeInsets.only(bottom: 48),
                child: Transform.translate(
                  offset: Offset(dx, dy),
                  child: Transform.scale(scale: scale, child: child),
                ),
              ),
            );
          },
          child: _GameChip(value: _selectedChip, selected: true, large: true),
        ),
      ),
    );
  }

  Widget _history(List<SevenUpDownRound> rounds) => Column(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      const Text(
        'RECENT RESULTS',
        style: TextStyle(
          color: Colors.white60,
          fontSize: 9,
          fontWeight: FontWeight.w800,
          letterSpacing: 1,
        ),
      ),
      const SizedBox(height: 7),
      SingleChildScrollView(
        scrollDirection: Axis.horizontal,
        child: Row(
          children: [
            for (final item in rounds.take(8))
              Container(
                margin: const EdgeInsets.only(right: 7),
                padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 6),
                decoration: BoxDecoration(
                  color: (_potColors[item.winningPot] ?? Colors.white)
                      .withValues(alpha: .12),
                  border: Border.all(
                    color: (_potColors[item.winningPot] ?? Colors.white)
                        .withValues(alpha: .35),
                  ),
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Text(
                  '${item.diceOne ?? '—'} + ${item.diceTwo ?? '—'} = ${item.diceTotal ?? '—'} · ${item.winningPot ?? '—'}',
                  style: const TextStyle(
                    color: Colors.white70,
                    fontSize: 10,
                    fontWeight: FontWeight.w700,
                  ),
                ),
              ),
          ],
        ),
      ),
    ],
  );

  Widget _pill(String text, Color color, IconData icon) => Container(
    padding: const EdgeInsets.symmetric(horizontal: 11, vertical: 7),
    decoration: BoxDecoration(
      color: const Color(0xDD11132B),
      border: Border.all(color: color.withValues(alpha: .6)),
      borderRadius: BorderRadius.circular(30),
    ),
    child: Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Icon(icon, color: color, size: 15),
        const SizedBox(width: 4),
        Text(
          text,
          style: const TextStyle(
            color: Colors.white,
            fontWeight: FontWeight.w900,
          ),
        ),
      ],
    ),
  );

  Widget _message(String value) => ColoredBox(
    color: const Color(0xFF090B22),
    child: Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Text(
          value,
          textAlign: TextAlign.center,
          style: const TextStyle(color: Colors.white),
        ),
      ),
    ),
  );
}

class _GameChip extends StatelessWidget {
  const _GameChip({
    required this.value,
    required this.selected,
    this.large = false,
  });

  final int value;
  final bool selected;
  final bool large;

  @override
  Widget build(BuildContext context) {
    final size = large ? 50.0 : 43.0;
    return Container(
      width: size,
      height: size,
      alignment: Alignment.center,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        gradient: LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors:
              selected
                  ? const [Color(0xFFFFE58F), Color(0xFFE99A19)]
                  : const [Color(0xFF774CCB), Color(0xFF38226F)],
        ),
        border: Border.all(
          color: selected ? Colors.white : const Color(0xFFAE91E8),
          width: selected ? 3 : 2,
        ),
        boxShadow:
            selected
                ? const [BoxShadow(color: Color(0x99FFD45E), blurRadius: 13)]
                : null,
      ),
      child: Container(
        width: size - 10,
        height: size - 10,
        alignment: Alignment.center,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          border: Border.all(
            color: Colors.white.withValues(alpha: .65),
            width: 1.5,
          ),
        ),
        child: Text(
          _compact(value),
          style: TextStyle(
            color: selected ? const Color(0xFF321A06) : Colors.white,
            fontSize: value >= 1000 ? 10 : 11,
            fontWeight: FontWeight.w900,
          ),
        ),
      ),
    );
  }

  static String _compact(int value) =>
      value >= 1000 && value % 1000 == 0 ? '${value ~/ 1000}K' : '$value';
}

class _ThreeDDice extends StatelessWidget {
  const _ThreeDDice({required this.value, required this.size});

  final int value;
  final double size;

  @override
  Widget build(BuildContext context) => RepaintBoundary(
    child: CustomPaint(
      size: Size(size, size + 9),
      painter: _DicePainter(value),
    ),
  );
}

class _DicePainter extends CustomPainter {
  const _DicePainter(this.value);

  final int value;

  @override
  void paint(Canvas canvas, Size size) {
    final front = RRect.fromRectAndRadius(
      Rect.fromLTRB(4, 10, size.width - 11, size.height - 3),
      const Radius.circular(15),
    );
    final shadow =
        Paint()
          ..color = const Color(0x77000000)
          ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 8);
    canvas.drawRRect(front.shift(const Offset(3, 5)), shadow);

    final top =
        Path()
          ..moveTo(8, 10)
          ..lineTo(17, 2)
          ..lineTo(size.width - 3, 2)
          ..lineTo(size.width - 11, 10)
          ..close();
    canvas.drawPath(
      top,
      Paint()
        ..shader = const LinearGradient(
          colors: [Color(0xFFFFFFFF), Color(0xFFBFC5DE)],
        ).createShader(Offset.zero & size),
    );
    final side =
        Path()
          ..moveTo(size.width - 11, 10)
          ..lineTo(size.width - 3, 2)
          ..lineTo(size.width - 3, size.height - 12)
          ..lineTo(size.width - 11, size.height - 3)
          ..close();
    canvas.drawPath(
      side,
      Paint()
        ..shader = const LinearGradient(
          colors: [Color(0xFFCED3E7), Color(0xFF888FAA)],
        ).createShader(Offset.zero & size),
    );
    canvas.drawRRect(
      front,
      Paint()
        ..shader = const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Colors.white, Color(0xFFE8EBF5), Color(0xFFC9CEE1)],
        ).createShader(front.outerRect),
    );
    canvas.drawRRect(
      front,
      Paint()
        ..color = const Color(0x669A7AD4)
        ..style = PaintingStyle.stroke
        ..strokeWidth = 1.3,
    );

    final positions =
        <int, List<Offset>>{
          1: const [Offset(.5, .5)],
          2: const [Offset(.28, .28), Offset(.72, .72)],
          3: const [Offset(.28, .28), Offset(.5, .5), Offset(.72, .72)],
          4: const [
            Offset(.28, .28),
            Offset(.72, .28),
            Offset(.28, .72),
            Offset(.72, .72),
          ],
          5: const [
            Offset(.28, .28),
            Offset(.72, .28),
            Offset(.5, .5),
            Offset(.28, .72),
            Offset(.72, .72),
          ],
          6: const [
            Offset(.28, .25),
            Offset(.72, .25),
            Offset(.28, .5),
            Offset(.72, .5),
            Offset(.28, .75),
            Offset(.72, .75),
          ],
        }[value.clamp(1, 6)]!;
    final pipPaint =
        Paint()
          ..shader = const RadialGradient(
            colors: [Color(0xFF6A35AA), Color(0xFF25123E)],
          ).createShader(front.outerRect);
    for (final point in positions) {
      canvas.drawCircle(
        Offset(
          front.left + (front.width * point.dx),
          front.top + (front.height * point.dy),
        ),
        size.width * .065,
        pipPaint,
      );
    }
  }

  @override
  bool shouldRepaint(covariant _DicePainter oldDelegate) =>
      oldDelegate.value != value;
}

class _AmbientParticlePainter extends CustomPainter {
  const _AmbientParticlePainter(this.progress);

  final double progress;

  @override
  void paint(Canvas canvas, Size size) {
    for (var index = 0; index < 14; index++) {
      final seed = index + 1.0;
      final x = (.08 + ((sin(seed * 9.73) + 1) * .42)) * size.width;
      final baseY = ((cos(seed * 5.17) + 1) * .5) * size.height;
      final y = (baseY - (progress * (28 + (index % 4) * 11))) % size.height;
      final shimmer = .25 + (.35 * sin((progress * pi * 2) + seed).abs());
      final color =
          index.isEven ? const Color(0xFFB985FF) : const Color(0xFFFFD45E);
      canvas.drawCircle(
        Offset(x, y),
        1.1 + (index % 3) * .55,
        Paint()
          ..color = color.withValues(alpha: shimmer)
          ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 2.5),
      );
    }
  }

  @override
  bool shouldRepaint(covariant _AmbientParticlePainter oldDelegate) =>
      oldDelegate.progress != progress;
}

class _CelebrationPainter extends CustomPainter {
  const _CelebrationPainter({required this.progress, required this.color});

  final double progress;
  final Color color;

  @override
  void paint(Canvas canvas, Size size) {
    if (progress <= 0 || progress >= 1) return;
    final eased = Curves.easeOutCubic.transform(progress);
    final fade =
        (1 - Curves.easeIn.transform(progress)).clamp(0.0, 1.0).toDouble();
    final origin = Offset(size.width * .5, min(size.height * .32, 230.0));
    for (var index = 0; index < 24; index++) {
      final angle = (-pi * .92) + ((pi * 1.84 / 23) * index);
      final distance = (70 + (index % 6) * 18) * eased;
      final gravity = 95 * progress * progress;
      final point = Offset(
        origin.dx + (cos(angle) * distance),
        origin.dy + (sin(angle) * distance) + gravity,
      );
      final particleColor = switch (index % 3) {
        0 => color,
        1 => const Color(0xFFFFD45E),
        _ => const Color(0xFFFFFFFF),
      };
      final paint = Paint()..color = particleColor.withValues(alpha: fade);
      if (index.isEven) {
        canvas.drawCircle(point, 2.5 + (index % 4), paint);
      } else {
        canvas.save();
        canvas.translate(point.dx, point.dy);
        canvas.rotate(progress * pi * 4 + index);
        canvas.drawRRect(
          RRect.fromRectAndRadius(
            const Rect.fromLTWH(-2.5, -5, 5, 10),
            const Radius.circular(1.5),
          ),
          paint,
        );
        canvas.restore();
      }
    }
  }

  @override
  bool shouldRepaint(covariant _CelebrationPainter oldDelegate) =>
      oldDelegate.progress != progress || oldDelegate.color != color;
}
