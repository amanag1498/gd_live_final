<?php $__env->startSection('content'); ?>
<?php
  $settings = $payload['settings'] ?? [];
  $round = $payload['current_round'] ?? null;
  $recentRounds = $payload['recent_rounds'] ?? collect();
  $recentBets = $payload['recent_bets'] ?? collect();
  $recentPayouts = $payload['recent_payouts'] ?? collect();
  $companySummary = $payload['company_summary'] ?? [];
  $multipliers = data_get($settings, 'pot_multipliers', []);
  $sectors = data_get($settings, 'pot_sectors', []);
?>
<div class="container-fluid py-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
      <h3 class="mb-1">Greedy</h3>
      <p class="text-muted mb-0">Realtime spinner game with 4 weighted pots and admin-controlled multipliers.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="<?php echo e(route('admin.settings.games.edit', ['game' => 'greedy'])); ?>" class="btn btn-light border">Game Settings</a>
      <a href="<?php echo e(route('admin.games.greedy.report')); ?>" class="btn btn-light border">User Report</a>
      <a href="<?php echo e(route('admin.games.greedy.rounds')); ?>" class="btn btn-light border">Rounds</a>
      <a href="<?php echo e(route('admin.games.greedy.bets')); ?>" class="btn btn-light border">Bets</a>
      <a href="<?php echo e(route('admin.games.greedy.payouts')); ?>" class="btn btn-light border">Payouts</a>
      <form method="post" action="<?php echo e(route('admin.games.greedy.tick')); ?>">
        <?php echo csrf_field(); ?>
        <button class="btn btn-primary">Tick Round</button>
      </form>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Enabled</div><div class="fs-4 fw-semibold"><?php echo e(data_get($settings, 'enabled') ? 'Yes' : 'No'); ?></div></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Visible In Room</div><div class="fs-4 fw-semibold"><?php echo e(data_get($settings, 'visible_in_video_room_strip') ? 'Yes' : 'No'); ?></div></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Fake Bets</div><div class="fs-4 fw-semibold"><?php echo e(data_get($settings, 'fake_bets_enabled') ? 'On' : 'Off'); ?></div></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Current Strategy</div><div class="fs-4 fw-semibold text-capitalize"><?php echo e(str_replace('_', ' ', data_get($settings, 'winning_strategy_mode', 'probability'))); ?></div></div></div></div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Company Bet Volume</div><div class="fs-4 fw-semibold"><?php echo e(number_format((int) data_get($companySummary, 'total_bet_amount', 0))); ?></div><div class="small text-muted mt-1"><?php echo e(data_get($companySummary, 'label', 'Last 30 days')); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Win Amount Given</div><div class="fs-4 fw-semibold"><?php echo e(number_format((int) data_get($companySummary, 'total_win_amount', 0))); ?></div><div class="small text-muted mt-1">Payouts credited to users</div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Refunded</div><div class="fs-4 fw-semibold"><?php echo e(number_format((int) data_get($companySummary, 'refunded_amount', 0))); ?></div><div class="small text-muted mt-1">Refunded bets in same window</div></div></div></div>
    <?php $companyProfit = (int) data_get($companySummary, 'profit_amount', 0); ?>
    <div class="col-md-6 col-xl-3"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-muted small">Company Profit</div><div class="fs-4 fw-semibold <?php echo e($companyProfit >= 0 ? 'text-success' : 'text-danger'); ?>"><?php echo e(number_format($companyProfit)); ?></div><div class="small text-muted mt-1">Bet volume - payouts - refunds</div></div></div></div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-lg-7">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Live Round</h5>
            <?php if($round): ?>
              <span class="badge text-bg-dark"><?php echo e($round['round_key']); ?></span>
            <?php endif; ?>
          </div>
          <?php if($round): ?>
            <div class="row g-3 mb-3">
              <div class="col-md-4"><div class="border rounded-3 p-3 h-100"><div class="text-muted small">Status</div><div class="fw-semibold text-capitalize"><?php echo e($round['status']); ?></div><div class="small text-muted mt-1">Phase: <?php echo e($round['phase']); ?></div></div></div>
              <div class="col-md-4"><div class="border rounded-3 p-3 h-100"><div class="text-muted small">Countdown</div><div class="fw-semibold"><?php echo e($round['countdown_seconds'] ?? 0); ?>s</div><div class="small text-muted mt-1">Locks: <?php echo e($round['locks_at'] ?? '—'); ?></div></div></div>
              <div class="col-md-4"><div class="border rounded-3 p-3 h-100"><div class="text-muted small">Winning Pot</div><div class="fw-semibold"><?php echo e($round['winning_pot'] ?? '—'); ?></div><div class="small text-muted mt-1">Multiplier: <?php echo e($round['winning_multiplier'] ?? '—'); ?></div></div></div>
            </div>
            <div class="row g-3 mb-3">
              <?php $__currentLoopData = ['A','B','C','D']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-3">
                  <div class="border rounded-3 p-3 h-100">
                    <div class="text-muted small">Pot <?php echo e($pot); ?></div>
                    <div class="fw-semibold"><?php echo e(number_format(data_get($round, "totals.$pot", 0))); ?></div>
                    <div class="small text-muted mt-1"><?php echo e(data_get($multipliers, $pot, 0)); ?>x | <?php echo e(data_get($sectors, $pot, 0)); ?> sectors</div>
                  </div>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="d-flex gap-2">
              <form method="post" action="<?php echo e(route('admin.games.greedy.tick')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="round_id" value="<?php echo e($round['id']); ?>">
                <button class="btn btn-outline-primary btn-sm">Tick This Round</button>
              </form>
              <form method="post" action="<?php echo e(route('admin.games.greedy.rounds.reconcile', $round['id'])); ?>">
                <?php echo csrf_field(); ?>
                <button class="btn btn-outline-dark btn-sm">Reconcile This Round</button>
              </form>
            </div>
          <?php else: ?>
            <div class="text-muted">Greedy is disabled or no round is available yet.</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body">
          <h5 class="mb-3">Wheel Configuration</h5>
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead><tr><th>Pot</th><th>Multiplier</th><th>Sectors</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = ['A','B','C','D']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td class="fw-semibold">Pot <?php echo e($pot); ?></td>
                    <td><?php echo e(data_get($multipliers, $pot, 0)); ?>x</td>
                    <td><?php echo e(data_get($sectors, $pot, 0)); ?></td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Recent Rounds</h5>
            <a href="<?php echo e(route('admin.games.greedy.rounds')); ?>" class="btn btn-sm btn-light border">Open Ledger</a>
          </div>
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead><tr><th>Round</th><th>Status</th><th>Winner</th><th>Total Bets</th></tr></thead>
              <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $recentRounds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                  <tr>
                    <td>#<?php echo e($item->id); ?><div class="small text-muted"><?php echo e($item->round_key); ?></div></td>
                    <td class="text-capitalize"><?php echo e($item->status); ?></td>
                    <td><?php echo e($item->winning_pot ?? '—'); ?></td>
                    <td><?php echo e(number_format((int) $item->total_bets_count)); ?></td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                  <tr><td colspan="4" class="text-muted text-center py-4">No rounds yet.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Recent Money Movement</h5>
            <a href="<?php echo e(route('admin.games.greedy.payouts')); ?>" class="btn btn-sm btn-light border">Payouts</a>
          </div>
          <div class="small text-muted mb-2">Latest bets and credited payouts.</div>
          <div class="table-responsive">
            <table class="table align-middle mb-0">
              <thead><tr><th>Type</th><th>User</th><th>Pot</th><th>Coins</th></tr></thead>
              <tbody>
                <?php $__currentLoopData = $recentBets->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td>Bet</td>
                    <td><?php echo e($bet->user?->name ?? 'User'); ?></td>
                    <td><?php echo e($bet->pot); ?></td>
                    <td><?php echo e(number_format((int) $bet->amount)); ?></td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php $__currentLoopData = $recentPayouts->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <tr>
                    <td>Payout</td>
                    <td><?php echo e($payout->user?->name ?? 'User'); ?></td>
                    <td><?php echo e(data_get($payout->meta, 'winning_pot', '—')); ?></td>
                    <td><?php echo e(number_format((int) $payout->payout_coins)); ?></td>
                  </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/games/greedy/dashboard.blade.php ENDPATH**/ ?>