<?php $__env->startSection('title', 'Teen Patti'); ?>

<?php $__env->startSection('content'); ?>
  <?php
    $round = $payload['current_round'] ?? null;
    $recentRounds = collect($payload['recent_rounds'] ?? []);
    $recentBets = collect($payload['recent_bets'] ?? []);
    $recentPayouts = collect($payload['recent_payouts'] ?? []);
    $companySummary = $payload['company_summary'] ?? [];

    $currentTotal = (int) data_get($round, 'totals.A', 0)
      + (int) data_get($round, 'totals.B', 0)
      + (int) data_get($round, 'totals.C', 0);
    $winnerPot = data_get($round, 'winning_pot');
    $currentStatus = (string) data_get($round, 'status', 'idle');

    $settledRounds = $recentRounds->where('status', 'settled')->count();
    $openRounds = $recentRounds->whereIn('status', ['open', 'locked'])->count();
    $recentBetVolume = (int) $recentBets->sum('amount');
    $recentPayoutVolume = (int) $recentPayouts->sum('payout_coins');

    $statusTone = match ($currentStatus) {
      'open' => 'success',
      'locked' => 'warning',
      'settled' => 'primary',
      'cancelled' => 'danger',
      default => 'secondary',
    };
  ?>

  <div class="admin-page-shell teen-patti-admin">
    <section class="admin-page-hero">
      <div class="row g-3 align-items-center">
        <div class="col-lg-8">
          <span class="admin-page-eyebrow"><i class="ti ti-device-gamepad-2"></i> Game Operations</span>
          <h1 class="admin-page-title">Teen Patti Control Room</h1>
          <p class="admin-page-subtitle">
            See whether the game is live, how much is in play, what changed recently, and jump directly to rounds, bets, payouts, or settings.
          </p>
        </div>
        <div class="col-lg-4">
          <div class="admin-page-actions">
            <a href="<?php echo e(route('admin.settings.games.edit', ['game' => 'teen_patti'])); ?>" class="btn btn-light border">Game Settings</a>
            <a href="<?php echo e(route('admin.games.teen-patti.report')); ?>" class="btn btn-light border">User Report</a>
            <a href="<?php echo e(route('admin.games.teen-patti.rounds')); ?>" class="btn btn-light border">Rounds</a>
            <a href="<?php echo e(route('admin.games.teen-patti.bets')); ?>" class="btn btn-light border">Bets</a>
            <a href="<?php echo e(route('admin.games.teen-patti.payouts')); ?>" class="btn btn-light border">Payouts</a>
            <form method="post" action="<?php echo e(route('admin.games.teen-patti.tick')); ?>">
              <?php echo csrf_field(); ?>
              <button class="btn btn-primary"><i class="ti ti-player-play me-1"></i> Tick Round</button>
            </form>
          </div>
        </div>
      </div>
    </section>

    <div class="row g-3">
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Current Status</span>
            <div class="tp-stat-value"><?php echo e(ucfirst($currentStatus === 'idle' ? 'not started' : $currentStatus)); ?></div>
            <span class="badge text-bg-<?php echo e($statusTone); ?>"><?php echo e(strtoupper($currentStatus)); ?></span>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Current Round Exposure</span>
            <div class="tp-stat-value"><?php echo e(number_format($currentTotal)); ?></div>
            <div class="tp-stat-meta">Total coins shown across A, B, and C</div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Recent Bet Volume</span>
            <div class="tp-stat-value"><?php echo e(number_format($recentBetVolume)); ?></div>
            <div class="tp-stat-meta"><?php echo e($recentBets->count()); ?> recent ledger rows</div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Recent Payout Volume</span>
            <div class="tp-stat-value"><?php echo e(number_format($recentPayoutVolume)); ?></div>
            <div class="tp-stat-meta"><?php echo e($recentPayouts->count()); ?> credited payouts</div>
          </div>
        </div>
      </div>
    </div>

    <?php $companyProfit = (int) data_get($companySummary, 'profit_amount', 0); ?>
    <div class="row g-3 mt-1">
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Company Bet Volume</span>
            <div class="tp-stat-value"><?php echo e(number_format((int) data_get($companySummary, 'total_bet_amount', 0))); ?></div>
            <div class="tp-stat-meta"><?php echo e(data_get($companySummary, 'label', 'Last 30 days')); ?></div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Win Amount Given</span>
            <div class="tp-stat-value"><?php echo e(number_format((int) data_get($companySummary, 'total_win_amount', 0))); ?></div>
            <div class="tp-stat-meta">Payouts credited to users</div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Refunded</span>
            <div class="tp-stat-value"><?php echo e(number_format((int) data_get($companySummary, 'refunded_amount', 0))); ?></div>
            <div class="tp-stat-meta">Refunded bets in same window</div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card tp-stat-card h-100">
          <div class="card-body">
            <span class="tp-stat-label">Company Profit</span>
            <div class="tp-stat-value <?php echo e($companyProfit >= 0 ? 'text-success' : 'text-danger'); ?>"><?php echo e(number_format($companyProfit)); ?></div>
            <div class="tp-stat-meta">Bet volume - payouts - refunds</div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-xl-4">
        <div class="card h-100">
          <div class="card-header">
            <h5 class="mb-0">Live Round Snapshot</h5>
          </div>
          <div class="card-body">
            <?php if($round): ?>
              <div class="tp-detail-list">
                <div class="tp-detail-row">
                  <span>Round key</span>
                  <strong><?php echo e($round['round_key'] ?? '—'); ?></strong>
                </div>
                <div class="tp-detail-row">
                  <span>Winner</span>
                  <strong><?php echo e($winnerPot ? "Pot {$winnerPot}" : '—'); ?></strong>
                </div>
                <div class="tp-detail-row">
                  <span>Display until</span>
                  <strong><?php echo e(!empty($round['display_until']) ? \Illuminate\Support\Carbon::parse($round['display_until'])->format('d M H:i:s') : '—'); ?></strong>
                </div>
                <div class="tp-detail-row">
                  <span>Bet count</span>
                  <strong><?php echo e(data_get($round, 'total_bets_count', 0)); ?></strong>
                </div>
                <div class="tp-detail-row">
                  <span>Participants</span>
                  <strong><?php echo e(data_get($round, 'participant_count', 0)); ?></strong>
                </div>
              </div>

              <div class="tp-pot-grid mt-3">
                <div class="tp-pot-card">
                  <span>Pot A</span>
                  <strong><?php echo e(number_format((int) data_get($round, 'totals.A', 0))); ?></strong>
                </div>
                <div class="tp-pot-card">
                  <span>Pot B</span>
                  <strong><?php echo e(number_format((int) data_get($round, 'totals.B', 0))); ?></strong>
                </div>
                <div class="tp-pot-card">
                  <span>Pot C</span>
                  <strong><?php echo e(number_format((int) data_get($round, 'totals.C', 0))); ?></strong>
                </div>
              </div>

              <div class="mt-3 d-grid gap-2">
                <form method="post" action="<?php echo e(route('admin.games.teen-patti.tick')); ?>">
                  <?php echo csrf_field(); ?>
                  <button class="btn btn-primary w-100">Refresh Game State</button>
                </form>
                <form method="post" action="<?php echo e(route('admin.games.teen-patti.rounds.reconcile', $round['id'])); ?>">
                  <?php echo csrf_field(); ?>
                  <button class="btn btn-outline-primary w-100">Reconcile Current Round</button>
                </form>
              </div>
            <?php else: ?>
              <p class="text-muted mb-0">No current round payload is available yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="col-xl-8">
        <div class="card h-100">
          <div class="card-header">
            <h5 class="mb-0">Game Configuration Summary</h5>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-6">
                <div class="tp-summary-panel">
                  <div class="tp-summary-title">Status</div>
                  <div class="tp-chip-row">
                    <span class="badge text-bg-<?php echo e($payload['settings']['enabled'] ? 'success' : 'danger'); ?>">
                      <?php echo e($payload['settings']['enabled'] ? 'Engine enabled' : 'Engine disabled'); ?>

                    </span>
                    <span class="badge text-bg-<?php echo e($payload['settings']['visible_in_video_room_strip'] ? 'primary' : 'secondary'); ?>">
                      <?php echo e($payload['settings']['visible_in_video_room_strip'] ? 'Visible in strip' : 'Hidden from strip'); ?>

                    </span>
                    <span class="badge text-bg-<?php echo e($payload['settings']['fake_bets_enabled'] ? 'warning' : 'secondary'); ?>">
                      <?php echo e($payload['settings']['fake_bets_enabled'] ? 'Fake bets on' : 'Fake bets off'); ?>

                    </span>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="tp-summary-panel">
                  <div class="tp-summary-title">Limits and timing</div>
                  <div class="tp-summary-copy">
                    Min bet <?php echo e($payload['settings']['min_bet']); ?>, max bet <?php echo e($payload['settings']['max_bet']); ?>, round <?php echo e($payload['settings']['round_duration_seconds']); ?>s, lock <?php echo e($payload['settings']['betting_lock_seconds']); ?>s before result, display <?php echo e($payload['settings']['result_display_seconds']); ?>s.
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="tp-summary-panel">
                  <div class="tp-summary-title">Payout rule</div>
                  <div class="tp-summary-copy">
                    Winners receive <?php echo e($payload['settings']['payout_multiplier']); ?>x. Strategy mode is <strong><?php echo e(ucfirst(str_replace('_', ' ', $payload['settings']['winning_strategy_mode']))); ?></strong>.
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="tp-summary-panel">
                  <div class="tp-summary-title">Recent health</div>
                  <div class="tp-summary-copy">
                    <?php echo e($settledRounds); ?> settled rounds, <?php echo e($openRounds); ?> open or locked rounds visible in the recent sample.
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-xl-7">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Rounds</h5>
            <a href="<?php echo e(route('admin.games.teen-patti.rounds')); ?>" class="btn btn-sm btn-light border">Open full ledger</a>
          </div>
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead>
                <tr>
                  <th>Round</th>
                  <th>Status</th>
                  <th>Totals</th>
                  <th>Winner</th>
                  <th>Window</th>
                </tr>
              </thead>
              <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $recentRounds->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recentRound): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <tr>
                    <td>
                      <div class="fw-semibold"><?php echo e($recentRound->round_key); ?></div>
                      <div class="small text-muted">#<?php echo e($recentRound->id); ?></div>
                    </td>
                    <td><span class="badge bg-light text-dark border"><?php echo e(ucfirst($recentRound->status)); ?></span></td>
                    <td>A <?php echo e($recentRound->total_bet_a); ?> · B <?php echo e($recentRound->total_bet_b); ?> · C <?php echo e($recentRound->total_bet_c); ?></td>
                    <td><?php echo e($recentRound->winning_pot ?? '—'); ?></td>
                    <td>
                      <div><?php echo e(optional($recentRound->starts_at)->format('d M H:i:s')); ?></div>
                      <div class="small text-muted">to <?php echo e(optional($recentRound->ends_at)->format('H:i:s')); ?></div>
                    </td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr><td colspan="5" class="text-center text-muted py-5">No rounds yet.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="col-xl-5">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Latest Money Movement</h5>
            <div class="small text-muted">Recent bets and payouts only</div>
          </div>
          <div class="card-body">
            <div class="tp-activity-list">
              <?php $__empty_1 = true; $__currentLoopData = $recentBets->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="tp-activity-item">
                  <div>
                    <div class="fw-semibold"><?php echo e($bet->user?->name ?? 'Unknown user'); ?></div>
                    <div class="small text-muted">Bet <?php echo e($bet->amount); ?> on pot <?php echo e($bet->pot); ?></div>
                  </div>
                  <span class="badge bg-light text-dark border"><?php echo e(ucfirst($bet->status)); ?></span>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-muted">No recent bets.</div>
              <?php endif; ?>

              <?php $__empty_1 = true; $__currentLoopData = $recentPayouts->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="tp-activity-item">
                  <div>
                    <div class="fw-semibold"><?php echo e($payout->user?->name ?? 'Unknown user'); ?></div>
                    <div class="small text-muted">Payout <?php echo e($payout->payout_coins); ?> from <?php echo e($payout->round?->round_key ?? '—'); ?></div>
                  </div>
                  <span class="badge text-bg-success"><?php echo e(ucfirst($payout->status)); ?></span>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-muted">No recent payouts.</div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <style>
    .teen-patti-admin .tp-stat-card {
      border-radius: 18px;
    }

    .teen-patti-admin .tp-stat-label {
      display: block;
      color: var(--admin-muted);
      font-size: .8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .04em;
      margin-bottom: .45rem;
    }

    .teen-patti-admin .tp-stat-value {
      font-size: 1.7rem;
      font-weight: 800;
      line-height: 1.1;
      margin-bottom: .45rem;
    }

    .teen-patti-admin .tp-stat-meta {
      color: var(--admin-muted);
      font-size: .88rem;
    }

    .teen-patti-admin .tp-detail-list {
      display: grid;
      gap: .7rem;
    }

    .teen-patti-admin .tp-detail-row {
      display: flex;
      justify-content: space-between;
      gap: 1rem;
      padding-bottom: .7rem;
      border-bottom: 1px solid rgba(148, 163, 184, 0.14);
    }

    .teen-patti-admin .tp-detail-row:last-child {
      padding-bottom: 0;
      border-bottom: 0;
    }

    .teen-patti-admin .tp-detail-row span {
      color: var(--admin-muted);
    }

    .teen-patti-admin .tp-pot-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: .75rem;
    }

    .teen-patti-admin .tp-pot-card,
    .teen-patti-admin .tp-summary-panel {
      border: 1px solid rgba(148, 163, 184, 0.16);
      border-radius: 16px;
      background: rgba(248, 250, 252, 0.72);
      padding: .9rem 1rem;
    }

    .teen-patti-admin .tp-pot-card span,
    .teen-patti-admin .tp-summary-title {
      display: block;
      color: var(--admin-muted);
      font-size: .78rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .04em;
      margin-bottom: .3rem;
    }

    .teen-patti-admin .tp-pot-card strong {
      font-size: 1.15rem;
      font-weight: 800;
    }

    .teen-patti-admin .tp-chip-row {
      display: flex;
      flex-wrap: wrap;
      gap: .45rem;
    }

    .teen-patti-admin .tp-summary-copy {
      color: var(--admin-text);
      line-height: 1.45;
      font-size: .92rem;
    }

    .teen-patti-admin .tp-activity-list {
      display: grid;
      gap: .8rem;
    }

    .teen-patti-admin .tp-activity-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
      border: 1px solid rgba(148, 163, 184, 0.16);
      border-radius: 14px;
      padding: .85rem .95rem;
      background: rgba(248, 250, 252, 0.72);
    }
  </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/games/teen-patti/dashboard.blade.php ENDPATH**/ ?>