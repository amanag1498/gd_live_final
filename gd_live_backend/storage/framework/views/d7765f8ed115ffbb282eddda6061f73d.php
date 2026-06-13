<?php $__env->startSection('title', 'Payout Report #' . $report->id); ?>
<?php $__env->startSection('page_intro', 'Period-scoped payout detail for your agency with read-only host breakdown and offline settlement status.'); ?>

<?php $__env->startSection('content'); ?>
  <div class="d-flex gap-2 justify-content-end mb-3">
    <a href="<?php echo e(route('agency.payout-reports.index')); ?>" class="btn btn-light border">Back</a>
    <a href="<?php echo e(route('agency.payout-reports.export', $report)); ?>" class="btn btn-primary">Export CSV</a>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Status</small><div class="fs-5 fw-semibold mt-1"><?php echo e(ucwords(str_replace('_', ' ', $report->status))); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Gross Earnings</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->gross_earnings)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Agency Payout</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->agency_commission)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Final Payable</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->final_payable)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Total Hosts</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->total_hosts)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Active Hosts</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->active_hosts_count)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Video Rooms</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->total_video_room_minutes)); ?> min</div><div class="text-muted small mt-1">Gifts <?php echo e(number_format($report->total_video_gift_gross)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Video Calls</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->total_video_call_minutes)); ?> min</div><div class="text-muted small mt-1">Gross <?php echo e(number_format($report->total_video_call_gross)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">PK Gross / Events</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->total_pk_earnings)); ?> / <?php echo e(number_format($report->total_pk_event_count)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Host Payout</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->host_share)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Combined Payout</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->total_payout)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Gift Events / Qty</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->total_gift_events)); ?> / <?php echo e(number_format($report->total_gift_quantity)); ?></div></div></div></div>
    <div class="col-md-6 col-xl-3"><div class="card"><div class="card-body"><small class="text-muted">Call Count / Minutes</small><div class="fs-5 fw-semibold mt-1"><?php echo e(number_format($report->total_call_count)); ?> / <?php echo e(number_format($report->total_billable_minutes)); ?></div></div></div></div>
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
            <th>Video Room Min</th>
            <th>Video Gifts</th>
            <th>Video Call Min / Earn</th>
            <th>Gross</th>
            <th>Host Payout</th>
            <th>Agency Payout</th>
            <th>Total Payout</th>
            <th>Gift Events / Qty</th>
            <th>PK Gross</th>
            <th>PK Events</th>
            <th>Final Payable</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $report->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($item->host?->user?->name ?? $item->host?->stage_name ?? '—'); ?></td>
              <td><?php echo e(number_format((int) data_get($item->meta, 'video_room_minutes', 0))); ?></td>
              <td><?php echo e(number_format((int) data_get($item->meta, 'video_gift_gross', 0))); ?></td>
              <td><?php echo e(number_format((int) data_get($item->meta, 'video_call_minutes', 0))); ?> / <?php echo e(number_format((int) data_get($item->meta, 'video_call_gross', 0))); ?></td>
              <td><?php echo e(number_format($item->gross_earnings)); ?></td>
              <td><?php echo e(number_format($item->host_share)); ?></td>
              <td><?php echo e(number_format($item->agency_commission)); ?></td>
              <td><?php echo e(number_format($item->total_payout)); ?></td>
              <td><?php echo e(number_format((int) data_get($item->meta, 'gift_events', 0))); ?> / <?php echo e(number_format((int) data_get($item->meta, 'gift_quantity', 0))); ?></td>
              <td><?php echo e(number_format($item->pk_earnings)); ?></td>
              <td><?php echo e(number_format((int) data_get($item->meta, 'pk_event_count', 0))); ?></td>
              <td><?php echo e(number_format($item->final_payable)); ?></td>
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