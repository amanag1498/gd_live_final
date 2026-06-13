<?php $__env->startSection('title', 'Teen Patti Bets'); ?>

<?php $__env->startSection('content'); ?>
  <?php
    $betCollection = $bets->getCollection();
    $placedCount = $betCollection->where('status', 'placed')->count();
    $wonCount = $betCollection->where('status', 'won')->count();
    $lostCount = $betCollection->where('status', 'lost')->count();
    $refundedCount = $betCollection->where('status', 'refunded')->count();
  ?>

  <div class="admin-page-shell teen-patti-admin">
    <section class="admin-page-hero">
      <div class="row g-3 align-items-center">
        <div class="col-lg-8">
          <span class="admin-page-eyebrow"><i class="ti ti-coins"></i> Bet Ledger</span>
          <h1 class="admin-page-title">Teen Patti Bets</h1>
          <p class="admin-page-subtitle">
            Review who bet, on which pot, what happened to the bet, and refund only unresolved entries that were not already paid out.
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
      <div class="col-md-6 col-xl-3"><div class="card tp-stat-card h-100"><div class="card-body"><span class="tp-stat-label">Placed</span><div class="tp-stat-value"><?php echo e($placedCount); ?></div></div></div></div>
      <div class="col-md-6 col-xl-3"><div class="card tp-stat-card h-100"><div class="card-body"><span class="tp-stat-label">Won</span><div class="tp-stat-value"><?php echo e($wonCount); ?></div></div></div></div>
      <div class="col-md-6 col-xl-3"><div class="card tp-stat-card h-100"><div class="card-body"><span class="tp-stat-label">Lost</span><div class="tp-stat-value"><?php echo e($lostCount); ?></div></div></div></div>
      <div class="col-md-6 col-xl-3"><div class="card tp-stat-card h-100"><div class="card-body"><span class="tp-stat-label">Refunded</span><div class="tp-stat-value"><?php echo e($refundedCount); ?></div></div></div></div>
    </div>

    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between gap-3 flex-wrap">
        <div>
          <h5 class="mb-1">Find Bets</h5>
          <div class="small text-muted">Search by bet ID, user name, email, or round key.</div>
        </div>
        <form method="get" class="d-flex gap-2 flex-wrap">
          <input class="form-control" name="q" value="<?php echo e(request('q')); ?>" placeholder="Bet id, user, email, round key">
          <select class="form-select" name="pot">
            <option value="">Any pot</option>
            <?php $__currentLoopData = ['A', 'B', 'C']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($pot); ?>" <?php if(request('pot') === $pot): echo 'selected'; endif; ?>><?php echo e($pot); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
          <select class="form-select" name="status">
            <option value="">Any status</option>
            <?php $__currentLoopData = ['placed', 'won', 'lost', 'refunded']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
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
              <th>Bet</th>
              <th>User</th>
              <th>Round</th>
              <th>Pot</th>
              <th>Amount</th>
              <th>Outcome</th>
              <th>Wallet Link</th>
              <th>Placed</th>
              <th class="text-end">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $bets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr>
                <td>
                  <div class="fw-semibold">#<?php echo e($bet->id); ?></div>
                  <div class="small text-muted">Payout <?php echo e($bet->payout_coins); ?></div>
                </td>
                <td>
                  <div class="fw-semibold"><?php echo e($bet->user?->name ?? 'Unknown'); ?></div>
                  <div class="small text-muted">#<?php echo e($bet->user_id); ?> · <?php echo e($bet->user?->email); ?></div>
                </td>
                <td><?php echo e($bet->round?->round_key ?? '—'); ?></td>
                <td><span class="badge bg-light text-dark border"><?php echo e($bet->pot); ?></span></td>
                <td><?php echo e($bet->amount); ?></td>
                <td>
                  <span class="badge text-bg-<?php echo e(match($bet->status) { 'won' => 'success', 'lost' => 'secondary', 'refunded' => 'warning', default => 'primary' }); ?>">
                    <?php echo e(ucfirst($bet->status)); ?>

                  </span>
                </td>
                <td><?php echo e($bet->wallet_transaction_id ?? '—'); ?></td>
                <td><?php echo e(optional($bet->placed_at)->format('d M H:i:s') ?? '—'); ?></td>
                <td class="text-end">
                  <?php if(!$bet->refunded_at && !$bet->payout): ?>
                    <form method="post" action="<?php echo e(route('admin.games.teen-patti.bets.refund', $bet)); ?>" class="d-inline">
                      <?php echo csrf_field(); ?>
                      <input type="hidden" name="note" value="Admin refund from Teen Patti ledger">
                      <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Refund this bet and return the coins?')">Refund</button>
                    </form>
                  <?php else: ?>
                    <span class="text-muted small">No action</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="9" class="text-center text-muted py-5">No bets found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="card-footer d-flex justify-content-end">
        <?php echo e($bets->links()); ?>

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

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/games/teen-patti/bets.blade.php ENDPATH**/ ?>