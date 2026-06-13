<?php $__env->startSection('title','Blocked Users'); ?>
<?php $__env->startSection('content'); ?>
<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <div>
      <h5 class="mb-0"><i class="ti ti-shield-lock me-2"></i>Blocked Users</h5>
      <div class="text-muted small">Permanent host-user blocks across all rooms.</div>
    </div>
    <form method="get" class="d-flex gap-2">
      <select name="host_user_id" class="form-select">
        <option value="">All hosts</option>
        <?php $__currentLoopData = $hosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $host): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($host->id); ?>" <?php if(request('host_user_id') == $host->id): echo 'selected'; endif; ?>><?php echo e($host->name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <input type="date" class="form-control" name="from" value="<?php echo e(request('from')); ?>">
      <input type="date" class="form-control" name="to" value="<?php echo e(request('to')); ?>">
      <button class="btn btn-light border">Filter</button>
    </form>
  </div>
  <div class="card-body table-responsive">
    <table class="table align-middle">
      <thead class="table-light">
        <tr><th>Host</th><th>Blocked User</th><th>Reason</th><th>Blocked By</th><th>Blocked Date</th><th class="text-end">Action</th></tr>
      </thead>
      <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
          <td><?php echo e($row->hostUser?->name ?? '—'); ?></td>
          <td><?php echo e($row->blockedUser?->name ?? '—'); ?></td>
          <td><?php echo e($row->reason ?: '—'); ?></td>
          <td><?php echo e($row->blockedBy?->name ?? '—'); ?> <span class="text-muted">(<?php echo e($row->blocked_by_role); ?>)</span></td>
          <td><?php echo e(optional($row->created_at)->format('d M Y H:i')); ?></td>
          <td class="text-end">
            <form method="post" action="<?php echo e(route('admin.moderation.blocked-users.unblock')); ?>" onsubmit="return confirm('Unblock this user?')" class="d-inline">
              <?php echo csrf_field(); ?>
              <input type="hidden" name="host_user_id" value="<?php echo e($row->host_user_id); ?>">
              <input type="hidden" name="blocked_user_id" value="<?php echo e($row->blocked_user_id); ?>">
              <button class="btn btn-sm btn-danger"><i class="ti ti-lock-open me-1"></i>Unblock</button>
            </form>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="6" class="text-center text-muted py-4">No blocked users found.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="card-footer"><?php echo e($rows->withQueryString()->links()); ?></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/moderation/blocked-users.blade.php ENDPATH**/ ?>