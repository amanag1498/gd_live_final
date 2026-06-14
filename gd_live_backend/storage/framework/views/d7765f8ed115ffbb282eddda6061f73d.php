<?php $__env->startSection('title', 'Payout Report #' . $report->id); ?>
<?php $__env->startSection('page_intro', 'Read-only settlement detail for your agency.'); ?>

<?php $__env->startSection('content'); ?>
  <div class="d-flex gap-2 justify-content-end mb-3">
    <a href="<?php echo e(route('agency.payout-reports.index')); ?>" class="btn btn-light border">Back</a>
    <a href="<?php echo e(route('agency.payout-reports.export', $report)); ?>" class="btn btn-primary">Download PDF</a>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Status</small><div class="fs-5 fw-semibold mt-1"><?php echo e(ucwords(str_replace('_', ' ', $report->status))); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Total Coins</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->total_coins)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Final Payable</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->final_payable)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Total INR</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->total_inr, 2)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Video Room Timing</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->total_video_room_minutes)); ?> min</div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Video Room Gifts</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->total_video_gift_coins)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">PK Gifts</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->total_pk_gift_coins)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Video Calls</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->total_video_call_coins)); ?> / <?php echo e(number_format($report->total_video_call_minutes)); ?> min</div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Bonus Coins</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->total_bonus_coins)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Host Payout INR</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->total_host_payout_inr, 2)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Agency Commission INR</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->total_agency_commission_inr, 2)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Total Hosts</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->total_hosts)); ?></div></div></div></div>
  </div>

  <?php if($report->admin_remarks): ?>
    <div class="alert alert-light border"><?php echo e($report->admin_remarks); ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-header"><h5 class="mb-0">Per-Host Breakdown</h5></div>
    <div class="card-body table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Host</th>
            <th>Total Video Room Timing</th>
            <th>Total Video Room Gifts</th>
            <th>Total PK Gifts</th>
            <th>Video Calls Coins</th>
            <th>Video Calls Min</th>
            <th>Bonus Coins</th>
            <th>Total Coins</th>
            <th>Host Payout INR</th>
            <th>Agency Commission INR</th>
            <th>Total INR</th>
            <th>Admin Notes</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $report->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($item->host?->user?->name ?? $item->host?->stage_name ?? '—'); ?></td>
              <td><?php echo e(number_format($item->video_room_minutes)); ?></td>
              <td><?php echo e(number_format($item->video_gift_coins)); ?></td>
              <td><?php echo e(number_format($item->pk_gift_coins)); ?></td>
              <td><?php echo e(number_format($item->video_call_coins)); ?></td>
              <td><?php echo e(number_format($item->video_call_minutes)); ?></td>
              <td><?php echo e(number_format($item->bonus_coins)); ?></td>
              <td><?php echo e(number_format($item->total_coins)); ?></td>
              <td><?php echo e(number_format($item->host_payout_inr, 2)); ?></td>
              <td><?php echo e(number_format($item->agency_commission_inr, 2)); ?></td>
              <td><?php echo e(number_format($item->total_inr, 2)); ?></td>
              <td><?php echo e($item->admin_note ?: '—'); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="12" class="text-center text-muted py-4">No host rows in this report.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.agency-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/agency/payout-reports/show.blade.php ENDPATH**/ ?>