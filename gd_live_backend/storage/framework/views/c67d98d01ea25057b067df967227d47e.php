<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="mb-1">Greedy Payouts</h3>
      <p class="text-muted mb-0">Winning bets credited through wallet ledger for Greedy rounds.</p>
    </div>
    <a href="<?php echo e(route('admin.games.greedy.dashboard')); ?>" class="btn btn-light border">Back to Dashboard</a>
  </div>

  <form class="row g-3 mb-4">
    <div class="col-md-4"><input type="text" name="q" value="<?php echo e(request('q')); ?>" class="form-control" placeholder="Payout id, user, or round"></div>
    <div class="col-md-3"><input type="text" name="status" value="<?php echo e(request('status')); ?>" class="form-control" placeholder="Status"></div>
    <div class="col-md-2"><button class="btn btn-outline-dark w-100">Filter</button></div>
  </form>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th>Payout</th>
              <th>User</th>
              <th>Round</th>
              <th>Bet</th>
              <th>Pot</th>
              <th>Coins</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $payouts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payout): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr>
                <td>#<?php echo e($payout->id); ?><div class="small text-muted"><?php echo e(optional($payout->settled_at)->toDateTimeString()); ?></div></td>
                <td><?php echo e($payout->user?->name ?? 'User'); ?><div class="small text-muted">#<?php echo e($payout->user_id); ?></div></td>
                <td><?php echo e($payout->round?->round_key ?? '—'); ?></td>
                <td>#<?php echo e($payout->greedy_bet_id); ?></td>
                <td><?php echo e(data_get($payout->meta, 'winning_pot', '—')); ?></td>
                <td><?php echo e(number_format((int) $payout->payout_coins)); ?></td>
                <td class="text-capitalize"><?php echo e($payout->status); ?></td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="7" class="text-center text-muted py-5">No payouts found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-4"><?php echo e($payouts->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/games/greedy/payouts.blade.php ENDPATH**/ ?>