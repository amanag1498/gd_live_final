<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h3 class="mb-1">Greedy Rounds</h3>
      <p class="text-muted mb-0">Round history, winners, timing, and settlement state.</p>
    </div>
    <div class="d-flex gap-2">
      <a href="<?php echo e(route('admin.games.greedy.dashboard')); ?>" class="btn btn-light border">Back to Dashboard</a>
      <form method="post" action="<?php echo e(route('admin.games.greedy.tick')); ?>">
        <?php echo csrf_field(); ?>
        <button class="btn btn-primary">Tick Current Round</button>
      </form>
    </div>
  </div>

  <form class="row g-3 mb-4">
    <div class="col-md-4"><input type="text" name="q" value="<?php echo e(request('q')); ?>" class="form-control" placeholder="Round key or id"></div>
    <div class="col-md-3"><input type="text" name="status" value="<?php echo e(request('status')); ?>" class="form-control" placeholder="Status"></div>
    <div class="col-md-2"><button class="btn btn-outline-dark w-100">Filter</button></div>
  </form>

  <div class="card border-0 shadow-sm">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table align-middle mb-0">
          <thead>
            <tr>
              <th>Round</th>
              <th>Status</th>
              <th>Winner</th>
              <th>Totals</th>
              <th>Locks</th>
              <th>Ends</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $rounds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $round): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr>
                <td>#<?php echo e($round->id); ?><div class="small text-muted"><?php echo e($round->round_key); ?></div></td>
                <td class="text-capitalize"><?php echo e($round->status); ?></td>
                <td><?php echo e($round->winning_pot ?? '—'); ?><div class="small text-muted"><?php echo e($round->winning_multiplier ? $round->winning_multiplier . 'x' : ''); ?></div></td>
                <td class="small text-muted">A <?php echo e(number_format((int) $round->total_bet_a)); ?> | B <?php echo e(number_format((int) $round->total_bet_b)); ?> | C <?php echo e(number_format((int) $round->total_bet_c)); ?> | D <?php echo e(number_format((int) $round->total_bet_d)); ?></td>
                <td><?php echo e(optional($round->locks_at)->toDateTimeString()); ?></td>
                <td><?php echo e(optional($round->ends_at)->toDateTimeString()); ?></td>
                <td class="text-end">
                  <form method="post" action="<?php echo e(route('admin.games.greedy.rounds.reconcile', $round)); ?>" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button class="btn btn-sm btn-outline-dark">Reconcile</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr><td colspan="7" class="text-center text-muted py-5">No rounds found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="mt-4"><?php echo e($rounds->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/games/greedy/rounds.blade.php ENDPATH**/ ?>