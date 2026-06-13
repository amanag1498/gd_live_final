<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="mb-1">Greedy Bets</h3>
      <p class="text-muted mb-0">Accepted bets, wallet debits, pot choice, and refund actions.</p>
    </div>
    <a href="<?php echo e(route('admin.games.greedy.dashboard')); ?>" class="btn btn-light border">Back to Dashboard</a>
  </div>

  <form class="row g-3 mb-4">
    <div class="col-md-4"><input type="text" name="q" value="<?php echo e(request('q')); ?>" class="form-control" placeholder="Bet id, user, or round"></div>
    <div class="col-md-3"><input type="text" name="status" value="<?php echo e(request('status')); ?>" class="form-control" placeholder="Status"></div>
    <div class="col-md-2"><input type="text" name="pot" value="<?php echo e(request('pot')); ?>" class="form-control" placeholder="Pot"></div>
    <div class="col-md-2"><button class="btn btn-outline-dark w-100">Filter</button></div>
  </form>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th>Bet</th>
              <th>User</th>
              <th>Round</th>
              <th>Pot</th>
              <th>Amount</th>
              <th>Multiplier</th>
              <th>Status</th>
              <th>Payout</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $bets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr>
                <td>#<?php echo e($bet->id); ?><div class="small text-muted"><?php echo e(optional($bet->placed_at)->toDateTimeString()); ?></div></td>
                <td><?php echo e($bet->user?->name ?? 'User'); ?><div class="small text-muted">#<?php echo e($bet->user_id); ?></div></td>
                <td><?php echo e($bet->round?->round_key ?? '—'); ?></td>
                <td><?php echo e($bet->pot); ?></td>
                <td><?php echo e(number_format((int) $bet->amount)); ?></td>
                <td><?php echo e((int) $bet->multiplier); ?>x</td>
                <td class="text-capitalize"><?php echo e($bet->status); ?></td>
                <td><?php echo e(number_format((int) $bet->payout_coins)); ?></td>
                <td class="text-end">
                  <?php if(!$bet->refunded_at && !$bet->payout): ?>
                    <form method="post" action="<?php echo e(route('admin.games.greedy.bets.refund', $bet)); ?>" class="d-inline">
                      <?php echo csrf_field(); ?>
                      <button class="btn btn-sm btn-outline-danger">Refund</button>
                    </form>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="9" class="text-center text-muted py-5">No bets found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-4"><?php echo e($bets->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/games/greedy/bets.blade.php ENDPATH**/ ?>