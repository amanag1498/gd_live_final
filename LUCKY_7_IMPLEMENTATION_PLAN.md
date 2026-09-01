# Lucky 7 Implementation Plan

## Naming

The product-facing name is `Lucky 7` everywhere. The internal compatibility key remains `seven_up_down`, including existing class names, database tables, API paths, feature flags, and realtime channels. Keeping that identifier stable prevents a display-name change from breaking migrations, stored access grants, ledger references, or deployed clients.

## Current implementation status

The game is implemented end to end in the local workspace across Laravel, admin, the realtime server, and the active Flutter app. It is intentionally disabled by default until migration, test-user access, deployment, and business review are completed.

Completed game presentation work:

- Original premium table artwork at `liveapp flutter/assets/games/seven_up_down/table_background.png`.
- Code-rendered dice with correct pips, depth shading, perspective rotation, bounce, and a backend-result landing frame.
- Betting chip selection, chip-flight animation into the selected pot, pot press feedback, and accepted-bet confirmation.
- Dedicated lockout transition which disables all pots and covers the dice with a visible lock state.
- Lock timing is also derived locally from backend `locks_at`/`ends_at` timestamps, preventing a stale websocket frame from leaving a pot tappable after betting closes.
- Circular phase progress, a final-five-second urgency pulse, settling shake, and smooth new-round entrance cover the transitions between every round phase.
- Inline result reveal, winning-pot highlight, win/loss message, recent-result strip, and result countdown based on `display_until`.
- Lightweight ambient motion and inline win particles are isolated in repaint boundaries, with selection/acceptance/lock/result haptics and no full-screen visual interception.
- Authenticated refresh on settlement/refund so private wallet, bet status, and payout cannot remain stale after public websocket updates.
- Round-key guards and full disposal of controllers, timers, streams, and sockets; no root overlay and no game audio are used.

The table backdrop is raster artwork because it is decorative. Dice faces are rendered in Flutter rather than stored as six static images so every animation can deterministically land on the exact two values persisted by Laravel.

Remaining rollout work only:

- Run the migration in the target environment.
- Deploy/restart Laravel and the realtime service.
- Grant `seven_up_down` access to selected test users.
- Enable the Android/iOS platform flag, game master flag, and room-strip flag.
- Validate one funded betting cycle in a real video room before wider enablement.
- Complete the RTP/business approval described below.

## Architecture decision

Lucky 7 is an independent game module modeled on Teen Patti. It reuses shared wallet, authentication, settings, realtime transport, recharge, and admin UI infrastructure, but has its own services, models, tables, routes, feature access, socket events, Flutter state, and audit pages. It does not inherit from or persist into Teen Patti.

## Rules

| Pot | Dice total | Default total-return multiplier |
| --- | --- | ---: |
| `DOWN` | 2-6 | 3x |
| `SEVEN` | 7 | 4x |
| `UP` | 8-12 | 3x |

Laravel is authoritative for both dice, their total, the winning pot, settlement, and wallet effects. Flutter only animates the persisted backend result.

## Backend

- Add dedicated round, bet, payout, financial-account, and financial-ledger tables and models.
- Add `SevenUpDownService`, `SevenUpDownFinancialService`, and `SevenUpDownBroadcaster` using Teen Patti's locking, idempotency, refunds, reconciliation, activity lease, and round lifecycle.
- Snapshot all three payout multipliers into each round so settings changes cannot alter an active round.
- Generate a valid dice pair after the backend selects the winning pot; the displayed pair must always agree with the persisted winner.
- Add authenticated snapshot/history/bet APIs and public/internal websocket snapshots.
- Add the `seven_up_down` game-access key, Android/iOS flags, middleware protection, and app-settings payload.

## Realtime server

- Subscribe to `games:seven_up_down:events`.
- Cache and relay the Laravel snapshot through the existing `/games` namespace.
- Support subscribe/unsubscribe, feature disable, maintenance recovery, and one-second active polling.
- Keep result generation and wallet mutations exclusively in Laravel.

## Flutter

- Add isolated models, API, socket service, and `SevenUpDownGamePanel`.
- Add a game card to the existing room-games sheet behind platform, user-access, and video-room flags.
- Reuse Teen Patti chip selection, bet validation, recharge flow, countdown synchronization, and snapshot merging.
- Render two dice and the three pots inline.
- On settlement, animate first and reveal/highlight the backend result only after the animation completes.
- Dispose every timer, animation, stream subscription, socket, and sound with the game panel; do not use a root result overlay.

## Admin

- Add dashboard, user report, rounds, bets, payouts, financial ledger, manual tick, reconciliation, and safe refund controls.
- Add settings for enablement, room visibility, fake bets, bet limits, round timing, result timing, three multipliers, and winning strategy.
- Add daily, weekly, custom-range, user, round, pot, outcome, dice-total, and status filters with wallet and ledger references.

## Validation and rollout

- Cover dice mapping, multiplier snapshots, wallet locking, idempotency, concurrent settlement, refunds, reconciliation, access flags, reports, websocket delivery, animation ordering, and panel disposal.
- Deploy migrations/backend/server/app with the new feature disabled.
- Grant access only to test users, validate inside a real video room, then enable gradually from admin.

## Financial review gate

With fair dice, Down and Up each occur 15/36 of the time; a 3x total-return multiplier gives those bets a 125% expected return. The requested values remain configurable defaults, but production enablement requires an explicit RTP/business review.
