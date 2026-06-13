<?php $__env->startSection('title','Moderation History'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <div>
      <h5 class="mb-0"><i class="ti ti-history me-2"></i>Moderation History</h5>
      <div class="text-muted small">Global moderation actions across host and admin flows.</div>
    </div>
    <form method="get" class="d-flex gap-2">
      <input name="action_type" class="form-control" value="<?php echo e(request('action_type')); ?>" placeholder="Action type">
      <input name="target_user_id" class="form-control" value="<?php echo e(request('target_user_id')); ?>" placeholder="Target user ID">
      <button class="btn btn-light border">Filter</button>
    </form>
  </div>
  <div class="card-body table-responsive">
    <table class="table align-middle">
      <thead class="table-light">
        <tr><th>Action</th><th>Actor</th><th>Target</th><th>Host</th><th>Room</th><th>Reason</th><th>When</th></tr>
      </thead>
      <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td><span class="badge bg-light text-dark border"><?php echo e($row->action_type); ?></span></td>
          <td><?php echo e($row->actor?->name ?? 'System'); ?> <div class="text-muted small"><?php echo e($row->actor_role); ?></div></td>
          <td><?php echo e($row->targetUser?->name ?? '—'); ?></td>
          <td><?php echo e($row->hostUser?->name ?? '—'); ?></td>
          <td><?php echo e($row->room_id ?: '—'); ?> <?php if($row->room_type): ?><div class="text-muted small"><?php echo e($row->room_type); ?></div><?php endif; ?></td>
          <td><?php echo e($row->reason ?: '—'); ?></td>
          <td><?php echo e(optional($row->created_at)->format('d M Y H:i')); ?></td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="7" class="text-center text-muted py-4">No moderation history found.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer"><?php echo e($rows->withQueryString()->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/moderation/history.blade.php ENDPATH**/ ?>