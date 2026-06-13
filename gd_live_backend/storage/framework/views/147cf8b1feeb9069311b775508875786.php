<?php $__env->startSection('title', 'PK Battle #' . $pk_battle->id); ?>
<?php $__env->startSection('page_intro', 'Read-only PK battle detail for agency operations, host matchup, and contribution visibility.'); ?>

<?php $__env->startSection('page_actions'); ?>
  <a class="btn btn-light border" href="<?php echo e(request()->routeIs('admin.*') ? route('admin.agencies.pk-battles.index', $agency) : route('agency.pk-battles.index')); ?>">Back to PK Battles</a>
  <a class="btn btn-primary" href="<?php echo e($videoRoomsRoute ?? route('agency.video-rooms.index')); ?>">Video Rooms</a>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
  <section class="row g-3 mb-3">
    <div class="col-md-3"><div class="card agency-stat-card"><div class="card-body"><small class="text-muted">Status</small><div class="stat-value mt-1"><?php echo e(ucfirst($pk_battle->status)); ?></div></div></div></div>
    <div class="col-md-3"><div class="card agency-stat-card"><div class="card-body"><small class="text-muted">Score A</small><div class="stat-value mt-1"><?php echo e(number_format((int) $pk_battle->score_a)); ?></div></div></div></div>
    <div class="col-md-3"><div class="card agency-stat-card"><div class="card-body"><small class="text-muted">Score B</small><div class="stat-value mt-1"><?php echo e(number_format((int) $pk_battle->score_b)); ?></div></div></div></div>
    <div class="col-md-3"><div class="card agency-stat-card"><div class="card-body"><small class="text-muted">Duration</small><div class="stat-value mt-1"><?php echo e(number_format((int) $pk_battle->duration_seconds)); ?>s</div></div></div></div>
  </section>

  <section class="row g-3">
    <div class="col-xl-6">
      <div class="card h-100">
        <div class="card-header"><h5 class="mb-0">Room A</h5></div>
        <div class="card-body">
          <div class="fw-semibold"><?php echo e($pk_battle->roomA?->room_id ?? '—'); ?></div>
          <div class="text-muted small"><?php echo e($pk_battle->hostA?->user?->name ?? '—'); ?></div>
        </div>
      </div>
    </div>
    <div class="col-xl-6">
      <div class="card h-100">
        <div class="card-header"><h5 class="mb-0">Room B</h5></div>
        <div class="card-body">
          <div class="fw-semibold"><?php echo e($pk_battle->roomB?->room_id ?? '—'); ?></div>
          <div class="text-muted small"><?php echo e($pk_battle->hostB?->user?->name ?? '—'); ?></div>
        </div>
      </div>
    </div>
  </section>

  <section class="card mt-3">
    <div class="card-header"><h5 class="mb-0">Event Timeline</h5></div>
    <div class="card-body table-responsive">
      <table class="table align-middle">
        <thead class="table-light"><tr><th>Room</th><th>Type</th><th>Coins</th><th>User</th><th>Created</th></tr></thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $pk_battle->events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($event->room_id); ?></td>
              <td><?php echo e($event->event_type); ?></td>
              <td><?php echo e(number_format((int) $event->coins)); ?></td>
              <td><?php echo e($event->user?->name ?? '—'); ?></td>
              <td><?php echo e(optional($event->created_at)->format('d M Y H:i:s') ?: '—'); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="text-center text-muted py-4">No PK events recorded.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.agency-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/agency/pk-battles/show.blade.php ENDPATH**/ ?>