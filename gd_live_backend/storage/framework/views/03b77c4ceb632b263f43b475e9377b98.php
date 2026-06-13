<?php $__env->startSection('title', 'PK Battles'); ?>
<?php $__env->startSection('page_intro', 'Agency-scoped PK battle history for video rooms involving your hosts.'); ?>

<?php $__env->startSection('page_actions'); ?>
  <a class="btn btn-light border" href="<?php echo e($videoRoomsRoute ?? route('agency.video-rooms.index')); ?>">Video Rooms</a>
  <a class="btn btn-primary" href="<?php echo e($overviewRoute ?? route('agency.dashboard')); ?>">Dashboard</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
  <section class="row g-3 mb-3">
    <div class="col-md-3"><div class="card agency-stat-card"><div class="card-body"><small class="text-muted">Active</small><div class="stat-value mt-1"><?php echo e(number_format($summary['active'])); ?></div></div></div></div>
    <div class="col-md-3"><div class="card agency-stat-card"><div class="card-body"><small class="text-muted">Pending</small><div class="stat-value mt-1"><?php echo e(number_format($summary['pending'])); ?></div></div></div></div>
    <div class="col-md-3"><div class="card agency-stat-card"><div class="card-body"><small class="text-muted">Completed</small><div class="stat-value mt-1"><?php echo e(number_format($summary['completed'])); ?></div></div></div></div>
    <div class="col-md-3"><div class="card agency-stat-card"><div class="card-body"><small class="text-muted">PK Coins</small><div class="stat-value mt-1"><?php echo e(number_format($summary['total_pk_coins'] ?? 0)); ?></div></div></div></div>
  </section>

  <section class="card">
    <div class="card-header d-flex justify-content-between align-items-center gap-3 flex-wrap">
      <h5 class="mb-0">Battles</h5>
      <form method="get" class="d-flex gap-2 flex-wrap">
        <select name="status" class="form-select">
          <option value="">Any status</option>
          <?php $__currentLoopData = ['pending','active','completed','cancelled','failed','expired','rejected']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($status); ?>" <?php if(request('status') === $status): echo 'selected'; endif; ?>><?php echo e(ucfirst($status)); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button class="btn btn-light border">Filter</button>
      </form>
    </div>
    <div class="card-body table-responsive">
      <table class="table align-middle">
        <thead class="table-light"><tr><th>Battle</th><th>Room A</th><th>Room B</th><th>Status</th><th>Score</th><th class="text-end">Action</th></tr></thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $battles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $battle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($battle->battle_id); ?></td>
              <td><?php echo e($battle->roomA?->room_id ?? '—'); ?><div class="text-muted small"><?php echo e($battle->hostA?->user?->name ?? '—'); ?></div></td>
              <td><?php echo e($battle->roomB?->room_id ?? '—'); ?><div class="text-muted small"><?php echo e($battle->hostB?->user?->name ?? '—'); ?></div></td>
              <td><span class="badge bg-light text-dark border"><?php echo e(ucfirst($battle->status)); ?></span></td>
              <td><?php echo e(number_format((int) $battle->score_a)); ?> - <?php echo e(number_format((int) $battle->score_b)); ?></td>
              <td class="text-end">
                <a class="btn btn-sm btn-light border" href="<?php echo e(request()->routeIs('admin.*') ? route('admin.agencies.pk-battles.show', ['agency' => $agency->id, 'pk_battle' => $battle->id]) : route('agency.pk-battles.show', $battle)); ?>">View</a>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="6" class="text-center text-muted py-4">No PK battles found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      <div class="d-flex justify-content-end"><?php echo e($battles->links()); ?></div>
    </div>
  </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.agency-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/agency/pk-battles/index.blade.php ENDPATH**/ ?>