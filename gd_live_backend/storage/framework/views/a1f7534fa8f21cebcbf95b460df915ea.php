<?php $__env->startSection('title', 'Teen Patti Payouts'); ?>

<?php $__env->startSection('content'); ?>
  <?php
    $payoutCollection = $payouts->getCollection();
    $creditedCount = $payoutCollection->where('status', 'credited')->count();
    $creditedCoins = (int) $payoutCollection->sum('payout_coins');
    $uniqueUsers = $payoutCollection->pluck('user_id')->filter()->unique()->count();
  ?>

  <div class="admin-page-shell teen-patti-admin">
    <section class="admin-page-hero">
      <div class="row g-3 align-items-center">
        <div class="col-lg-8">
          <span class="admin-page-eyebrow"><i class="ti ti-receipt-2"></i> Payout Ledger</span>
          <h1 class="admin-page-title">Teen Patti Payouts</h1>
          <p class="admin-page-subtitle">
            Confirm who got paid, from which round, and which wallet transaction credited the winning amount.
          </p>
        </div>
        <div class="col-lg-4">
          <div class="admin-page-actions">
            <a href="<?php echo e(route('admin.games.teen-patti.dashboard')); ?>" class="btn btn-light border">Back to Dashboard</a>
          </div>
        </div>
      </div>
    </section>

    <div class="row g-3">
      <div class="col-md-4">
        <div class="card tp-stat-card h-100"><div class="card-body"><span class="tp-stat-label">Credited Rows</span><div class="tp-stat-value"><?php echo e($creditedCount); ?></div></div></div>
      </div>
      <div class="col-md-4">
        <div class="card tp-stat-card h-100"><div class="card-body"><span class="tp-stat-label">Coins Credited</span><div class="tp-stat-value"><?php echo e(number_format($creditedCoins)); ?></div></div></div>
      </div>
      <div class="col-md-4">
        <div class="card tp-stat-card h-100"><div class="card-body"><span class="tp-stat-label">Winning Users</span><div class="tp-stat-value"><?php echo e($uniqueUsers); ?></div></div></div>
      </div>
    </div>

    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
          <h5 class="mb-1">Find Payouts</h5>
          <div class="small text-muted">Search by payout ID, user, email, or round key.</div>
        </div>
        <form method="get" class="d-flex gap-2 flex-wrap">
          <input class="form-control" name="q" value="<?php echo e(request('q')); ?>" placeholder="Payout id, user, email, round key">
          <select class="form-select" name="status">
            <option value="">Any status</option>
            <?php $__currentLoopData = ['credited']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($status); ?>" <?php if(request('status') === $status): echo 'selected'; endif; ?>><?php echo e(ucfirst($status)); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <button class="btn btn-light border">Apply</button>
        </form>
      </div>
      <div class="card-body table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Payout</th>
              <th>User</th>
              <th>Round</th>
              <th>Source Bet</th>
              <th>Coins</th>
              <th>Status</th>
              <th>Wallet Tx</th>
              <th>Settled</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $payouts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr>
                <td>
                  <div class="fw-semibold">#<?php echo e($payout->id); ?></div>
                  <div class="small text-muted">Bet #<?php echo e($payout->teen_patti_bet_id); ?></div>
                </td>
                <td>
                  <div class="fw-semibold"><?php echo e($payout->user?->name ?? 'Unknown'); ?></div>
                  <div class="small text-muted">#<?php echo e($payout->user_id); ?> · <?php echo e($payout->user?->email); ?></div>
                </td>
                <td><?php echo e($payout->round?->round_key ?? '—'); ?></td>
                <td>#<?php echo e($payout->teen_patti_bet_id); ?></td>
                <td><?php echo e($payout->payout_coins); ?></td>
                <td><span class="badge text-bg-success"><?php echo e(ucfirst($payout->status)); ?></span></td>
                <td><?php echo e($payout->wallet_transaction_id ?? '—'); ?></td>
                <td><?php echo e(optional($payout->settled_at)->format('d M H:i:s') ?? '—'); ?></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="8" class="text-center text-muted py-5">No payouts found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex justify-content-end">
        <?php echo e($payouts->links()); ?>

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
    }
  </style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/games/teen-patti/payouts.blade.php ENDPATH**/ ?>