<?php $__env->startSection('title','My Applications'); ?>

<?php $__env->startSection('content'); ?>
  <h3 class="mb-3">My Applications</h3>

  <div class="row g-4">
    <div class="col-12">
      <div class="card">
        <div class="card-header"><h5 class="mb-0">Agency Applications</h5></div>
        <div class="card-body table-responsive">
          <table class="table align-middle">
            <thead><tr><th>#</th><th>Agency</th><th>Status</th><th>Reviewed</th><th>Updated</th></tr></thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $agencyRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e(data_get($r, 'id')); ?></td>
                  <td><?php echo e(data_get($r, 'details.agency_name', data_get($r, 'title', '—'))); ?></td>
                  <td>
                    <?php ($status = data_get($r, 'status')); ?>
                    <span class="badge <?php echo e($status==='pending'?'bg-warning text-dark':($status==='approved'?'bg-success':'bg-danger')); ?>">
                      <?php echo e(ucfirst($status)); ?>

                    </span>
                  </td>
                  <td><?php echo e(data_get($r, 'reviewed_at') ? \Illuminate\Support\Carbon::parse(data_get($r, 'reviewed_at'))->format('d M Y H:i') : '—'); ?></td>
                  <td><?php echo e(data_get($r, 'submitted_at') ? \Illuminate\Support\Carbon::parse(data_get($r, 'submitted_at'))->format('d M Y H:i') : '—'); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="5" class="text-center text-muted py-3">No agency applications yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card">
        <div class="card-header"><h5 class="mb-0">Host Applications</h5></div>
        <div class="card-body table-responsive">
          <table class="table align-middle">
            <thead><tr><th>#</th><th>Agency</th><th>Stage Name</th><th>Status</th><th>Reviewed</th><th>Updated</th></tr></thead>
            <tbody>
              <?php $__empty_1 = true; $__currentLoopData = $hostRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                  <td><?php echo e(data_get($r, 'id')); ?></td>
                  <td><?php echo e(data_get($r, 'details.agency_name', '—')); ?></td>
                  <td><?php echo e(data_get($r, 'details.stage_name', data_get($r, 'title', '—'))); ?></td>
                  <td>
                    <?php ($status = data_get($r, 'status')); ?>
                    <span class="badge <?php echo e($status==='pending'?'bg-warning text-dark':($status==='approved'?'bg-success':'bg-danger')); ?>">
                      <?php echo e(ucfirst($status)); ?>

                    </span>
                  </td>
                  <td><?php echo e(data_get($r, 'reviewed_at') ? \Illuminate\Support\Carbon::parse(data_get($r, 'reviewed_at'))->format('d M Y H:i') : '—'); ?></td>
                  <td><?php echo e(data_get($r, 'submitted_at') ? \Illuminate\Support\Carbon::parse(data_get($r, 'submitted_at'))->format('d M Y H:i') : '—'); ?></td>
                </tr>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="text-center text-muted py-3">No host applications yet.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/me/applications.blade.php ENDPATH**/ ?>