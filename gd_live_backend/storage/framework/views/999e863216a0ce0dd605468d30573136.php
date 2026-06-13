<?php $__env->startSection('title','Moderation Analytics'); ?>
<?php $__env->startSection('content'); ?>
<div class="row g-3">
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Pending Reports</div><div class="display-6 fw-semibold"><?php echo e($analytics['pending_reports']); ?></div></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Blocks Today</div><div class="display-6 fw-semibold"><?php echo e($analytics['blocks_today']); ?></div></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Kicks Today</div><div class="display-6 fw-semibold"><?php echo e($analytics['kicks_today']); ?></div></div></div></div>
  <div class="col-md-3"><div class="card"><div class="card-body"><div class="text-muted small">Auto Triggers</div><div class="display-6 fw-semibold"><?php echo e($analytics['auto_moderation_triggers']); ?></div></div></div></div>
</div>
<div class="row g-3 mt-1">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header"><h5 class="mb-0">Top Reported Users</h5></div>
      <div class="card-body table-responsive">
        <table class="table table-sm align-middle"><thead><tr><th>User</th><th>Reports</th></tr></thead><tbody>
          <?php $__currentLoopData = $analytics['top_reported_users']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr><td><?php echo e($row->reportedUser?->name ?? ('User #'.$row->reported_user_id)); ?></td><td><?php echo e($row->report_count); ?></td></tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody></table>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card">
      <div class="card-header"><h5 class="mb-0">Hosts With Most Blocks</h5></div>
      <div class="card-body table-responsive">
        <table class="table table-sm align-middle"><thead><tr><th>Host</th><th>Blocks</th></tr></thead><tbody>
          <?php $__currentLoopData = $analytics['hosts_with_most_blocks']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr><td><?php echo e($row->hostUser?->name ?? ('User #'.$row->host_user_id)); ?></td><td><?php echo e($row->block_count); ?></td></tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody></table>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/moderation/analytics.blade.php ENDPATH**/ ?>