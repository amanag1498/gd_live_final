<?php $__env->startSection('title', 'Weekly Payout Reports'); ?>
<?php $__env->startSection('page_intro', 'Read-only weekly payout reports scoped to your agency, with offline payout tracking and host-level breakdowns.'); ?>

<?php $__env->startSection('content'); ?>
  <div class="card mb-3">
    <div class="card-body">
      <form method="get" class="row g-2 align-items-end">
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="">All</option>
            <?php $__currentLoopData = $statuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($status); ?>" <?php if(request('status') === $status): echo 'selected'; endif; ?>><?php echo e(ucwords(str_replace('_', ' ', $status))); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Week Start</label>
          <input type="date" name="week_start" class="form-control" value="<?php echo e(request('week_start')); ?>">
        </div>
        <div class="col-md-2 d-grid">
          <button class="btn btn-outline-primary">Apply</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h5 class="mb-0">Reports</h5></div>
    <div class="card-body table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Week</th>
            <th>Hosts</th>
            <th>Active Hosts</th>
            <th>Gross</th>
            <th>Final Payable</th>
            <th>Status</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e(optional($report->period_start)->format('d M Y')); ?> - <?php echo e(optional($report->period_end)->format('d M Y')); ?></td>
              <td><?php echo e(number_format($report->total_hosts)); ?></td>
              <td><?php echo e(number_format($report->active_hosts_count)); ?></td>
              <td><?php echo e(number_format($report->gross_earnings)); ?></td>
              <td><?php echo e(number_format($report->final_payable)); ?></td>
              <td><?php echo e(ucwords(str_replace('_', ' ', $report->status))); ?></td>
              <td class="text-end"><a href="<?php echo e(route('agency.payout-reports.show', $report)); ?>" class="btn btn-sm btn-outline-secondary">View</a></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">No payout reports yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      <?php echo e($reports->links()); ?>

    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.agency-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/agency/payout-reports/index.blade.php ENDPATH**/ ?>