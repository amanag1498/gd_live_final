<?php
  $calls = $report['calls'];
  $summary = $report['summary'];
  $filters = $report['filters'];
  $earnings = $report['earnings'];
  $activeTab = request('tab', 'all');
  $schemaReady = $report['schema_ready'] ?? true;
  $setupMessage = $report['setup_message'] ?? null;
  $layout = $layout ?? 'default';
  $isAdminStyle = in_array($layout, ['admin', 'agency'], true);
  $reportingLabel = $layout === 'agency' ? 'Agency Reporting' : 'Admin Reporting';
  $tabMeta = [
    'all' => 'Full call ledger across the platform',
    'active' => 'Calls currently in progress',
    'completed' => 'Ended calls with settled billing',
    'missed_rejected' => 'Calls that did not complete',
    'host_earnings' => 'Host-wise revenue leaderboard',
    'agency_earnings' => 'Agency-wise revenue leaderboard',
  ];
  $currentTabDescription = $tabMeta[$activeTab] ?? 'Call activity and earnings';
  $topHost = $earnings['hosts']->first();
  $topAgency = $earnings['agencies']->first();
  $filterKeys = ['date_from', 'date_to', 'type', 'status', 'host_id', 'agency_id'];
  $activeFiltersCount = collect($filterKeys)->filter(fn ($key) => filled(request($key)))->count();
  $statusBreakdown = [
    'accepted' => $summary['active_calls'] ?? 0,
    'ended' => $summary['completed_calls'] ?? 0,
    'failed_group' => $summary['missed_rejected_calls'] ?? 0,
  ];
  $statusTotal = max(array_sum($statusBreakdown), 1);
  $globalVideoRate = (int) config('calls.video_coin_rate_per_minute');
  $globalMinimumBalance = (int) config('calls.minimum_balance_to_start_call');
?>

<?php if(!$schemaReady && $setupMessage): ?>
  <div class="alert alert-warning">
    <?php echo e($setupMessage); ?>

  </div>
<?php endif; ?>

<?php if($isAdminStyle): ?>
  <div class="card call-admin-hero mb-4">
    <div class="card-body p-4 p-lg-5">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge bg-light text-dark"><?php echo e($reportingLabel); ?></span>
            <span class="metric-chip"><i class="ti ti-filter"></i><?php echo e($activeFiltersCount); ?> active filters</span>
            <span class="metric-chip"><i class="ti ti-layout-kanban"></i><?php echo e($tabs[$activeTab] ?? ucfirst(str_replace('_', ' ', $activeTab))); ?></span>
          </div>
          <h3 class="mb-2 text-white"><?php echo e($scopeLabel); ?></h3>
          <p class="mb-0 text-white-50"><?php echo e($currentTabDescription); ?></p>
        </div>
        <div class="col-lg-5">
          <div class="row g-3">
            <div class="col-6">
              <div class="metric-chip w-100 justify-content-between">
                <span><i class="ti ti-phone-call me-1"></i>Total Calls</span>
                <strong><?php echo e(number_format($summary['total_calls'])); ?></strong>
              </div>
            </div>
            <div class="col-6">
              <div class="metric-chip w-100 justify-content-between">
                <span><i class="ti ti-coins me-1"></i>Coins</span>
                <strong><?php echo e(number_format($summary['total_coins_charged'])); ?></strong>
              </div>
            </div>
            <div class="col-6">
              <div class="metric-chip w-100 justify-content-between">
                <span><i class="ti ti-clock-hour-4 me-1"></i>Minutes</span>
                <strong><?php echo e(number_format($summary['total_minutes'])); ?></strong>
              </div>
            </div>
            <div class="col-6">
              <div class="metric-chip w-100 justify-content-between">
                <span><i class="ti ti-building-bank me-1"></i>Platform</span>
                <strong><?php echo e(number_format($summary['total_platform_earnings'])); ?></strong>
              </div>
            </div>
            <div class="col-6">
              <div class="metric-chip w-100 justify-content-between">
                <span><i class="ti ti-video me-1"></i>Video Rate</span>
                <strong><?php echo e(number_format($globalVideoRate)); ?>/min</strong>
              </div>
            </div>
            <div class="col-12">
              <div class="metric-chip w-100 justify-content-between">
                <span><i class="ti ti-coins me-1"></i>Minimum Balance</span>
                <strong><?php echo e(number_format($globalMinimumBalance)); ?></strong>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<div class="row g-3 mb-4">
  <div class="col-md-6 col-xl-3">
    <div class="card <?php echo e($isAdminStyle ? 'call-admin-card call-admin-kpi' : ''); ?>">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <small class="text-muted">Total Calls</small>
          <div class="fs-3 fw-semibold mt-1"><?php echo e(number_format($summary['total_calls'])); ?></div>
          <div class="text-muted small mt-2">All request states included</div>
        </div>
        <?php if($isAdminStyle): ?>
          <span class="icon-wrap bg-light text-primary"><i class="ti ti-phone"></i></span>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card <?php echo e($isAdminStyle ? 'call-admin-card call-admin-kpi' : ''); ?>">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <small class="text-muted">Active Calls</small>
          <div class="fs-3 fw-semibold mt-1"><?php echo e(number_format($summary['active_calls'])); ?></div>
          <div class="text-muted small mt-2">Accepted and still running</div>
        </div>
        <?php if($isAdminStyle): ?>
          <span class="icon-wrap bg-success-subtle text-success"><i class="ti ti-activity-heartbeat"></i></span>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card <?php echo e($isAdminStyle ? 'call-admin-card call-admin-kpi' : ''); ?>">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <small class="text-muted">Completed Calls</small>
          <div class="fs-3 fw-semibold mt-1"><?php echo e(number_format($summary['completed_calls'])); ?></div>
          <div class="text-muted small mt-2">Ended and billable sessions</div>
        </div>
        <?php if($isAdminStyle): ?>
          <span class="icon-wrap bg-info-subtle text-info"><i class="ti ti-checks"></i></span>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card <?php echo e($isAdminStyle ? 'call-admin-card call-admin-kpi' : ''); ?>">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <small class="text-muted">Missed / Rejected</small>
          <div class="fs-3 fw-semibold mt-1"><?php echo e(number_format($summary['missed_rejected_calls'])); ?></div>
          <div class="text-muted small mt-2">Missed, rejected, or failed</div>
        </div>
        <?php if($isAdminStyle): ?>
          <span class="icon-wrap bg-danger-subtle text-danger"><i class="ti ti-phone-x"></i></span>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card <?php echo e($isAdminStyle ? 'call-admin-card call-admin-kpi' : ''); ?>">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <small class="text-muted">Total Minutes</small>
          <div class="fs-3 fw-semibold mt-1"><?php echo e(number_format($summary['total_minutes'])); ?></div>
          <div class="text-muted small mt-2">Billed minutes across calls</div>
        </div>
        <?php if($isAdminStyle): ?>
          <span class="icon-wrap bg-warning-subtle text-warning"><i class="ti ti-clock"></i></span>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-3">
    <div class="card <?php echo e($isAdminStyle ? 'call-admin-card call-admin-kpi' : ''); ?>">
      <div class="card-body d-flex justify-content-between align-items-start">
        <div>
          <small class="text-muted">Coins Charged</small>
          <div class="fs-3 fw-semibold mt-1"><?php echo e(number_format($summary['total_coins_charged'])); ?></div>
          <div class="text-muted small mt-2">Total caller coin spend</div>
        </div>
        <?php if($isAdminStyle): ?>
          <span class="icon-wrap bg-primary-subtle text-primary"><i class="ti ti-coins"></i></span>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-2">
    <div class="card <?php echo e($isAdminStyle ? 'call-admin-card call-admin-kpi' : ''); ?>">
      <div class="card-body">
        <small class="text-muted">Host Earnings</small>
        <div class="fs-4 fw-semibold mt-1"><?php echo e(number_format($summary['total_host_earnings'])); ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-6 col-xl-2">
    <div class="card <?php echo e($isAdminStyle ? 'call-admin-card call-admin-kpi' : ''); ?>">
      <div class="card-body">
        <small class="text-muted">Agency Earnings</small>
        <div class="fs-4 fw-semibold mt-1"><?php echo e(number_format($summary['total_agency_earnings'])); ?></div>
      </div>
    </div>
  </div>
  <div class="col-md-12 col-xl-2">
    <div class="card <?php echo e($isAdminStyle ? 'call-admin-card call-admin-kpi' : ''); ?>">
      <div class="card-body">
        <small class="text-muted">Platform Earnings</small>
        <div class="fs-4 fw-semibold mt-1"><?php echo e(number_format($summary['total_platform_earnings'])); ?></div>
      </div>
    </div>
  </div>
</div>

<?php if($isAdminStyle): ?>
  <div class="row g-3 mb-4">
    <div class="col-lg-4">
      <div class="call-admin-insight">
        <div class="text-muted small mb-2">Top Host</div>
        <div class="fw-semibold fs-5"><?php echo e($topHost?->host?->user?->name ?? 'No host data yet'); ?></div>
        <div class="text-muted mt-2">Host earnings: <?php echo e(number_format((int) ($topHost->host_earning ?? 0))); ?></div>
        <div class="text-muted">Billable minutes: <?php echo e(number_format((int) ($topHost->billable_minutes ?? 0))); ?></div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="call-admin-insight">
        <div class="text-muted small mb-2">Top Agency</div>
        <div class="fw-semibold fs-5"><?php echo e($topAgency?->agency?->name ?? 'No agency data yet'); ?></div>
        <div class="text-muted mt-2">Agency earnings: <?php echo e(number_format((int) ($topAgency->agency_earning ?? 0))); ?></div>
        <div class="text-muted">Billable minutes: <?php echo e(number_format((int) ($topAgency->billable_minutes ?? 0))); ?></div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="call-admin-insight">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="text-muted small">Status Mix</div>
          <div class="small text-muted"><?php echo e(number_format($summary['total_calls'])); ?> total</div>
        </div>
        <div class="mb-3">
          <div class="d-flex justify-content-between small mb-1"><span>Accepted</span><span><?php echo e(number_format($statusBreakdown['accepted'])); ?></span></div>
          <div class="progress" style="height: 8px;"><div class="progress-bar bg-success" style="width: <?php echo e(($statusBreakdown['accepted'] / $statusTotal) * 100); ?>%"></div></div>
        </div>
        <div class="mb-3">
          <div class="d-flex justify-content-between small mb-1"><span>Ended</span><span><?php echo e(number_format($statusBreakdown['ended'])); ?></span></div>
          <div class="progress" style="height: 8px;"><div class="progress-bar bg-secondary" style="width: <?php echo e(($statusBreakdown['ended'] / $statusTotal) * 100); ?>%"></div></div>
        </div>
        <div>
          <div class="d-flex justify-content-between small mb-1"><span>Missed / Rejected / Failed</span><span><?php echo e(number_format($statusBreakdown['failed_group'])); ?></span></div>
          <div class="progress" style="height: 8px;"><div class="progress-bar bg-danger" style="width: <?php echo e(($statusBreakdown['failed_group'] / $statusTotal) * 100); ?>%"></div></div>
        </div>
      </div>
    </div>
  </div>
<?php endif; ?>

<div class="card <?php echo e($isAdminStyle ? 'call-admin-card' : ''); ?>">
  <div class="card-header border-0 pb-0">
    <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-3">
      <div>
        <h5 class="mb-1"><?php echo e($scopeLabel); ?></h5>
        <div class="text-muted"><?php echo e($currentTabDescription); ?></div>
      </div>
      <div class="d-flex gap-2">
        <?php if(isset($exportRoute)): ?>
          <a class="btn btn-primary" href="<?php echo e($exportRoute); ?>"><i class="ti ti-download me-1"></i>Export CSV</a>
        <?php endif; ?>
        <?php if($activeFiltersCount > 0): ?>
          <a class="btn btn-light border" href="<?php echo e(request()->url()); ?>?tab=<?php echo e($activeTab); ?>">Clear Filters</a>
        <?php endif; ?>
      </div>
    </div>
    <form method="get" class="mb-3">
      <input type="hidden" name="tab" value="<?php echo e($activeTab); ?>">
      <div class="<?php echo e($isAdminStyle ? 'call-admin-filter-grid' : 'row g-2'); ?>">
        <div>
          <label class="form-label">From</label>
          <input type="date" name="date_from" class="form-control" value="<?php echo e(request('date_from')); ?>">
        </div>
        <div>
          <label class="form-label">To</label>
          <input type="date" name="date_to" class="form-control" value="<?php echo e(request('date_to')); ?>">
        </div>
        <div>
          <label class="form-label">Call Type</label>
          <select name="type" class="form-select">
            <option value="">All types</option>
            <option value="video" <?php if(request('type') === 'video'): echo 'selected'; endif; ?>>Video</option>
          </select>
        </div>
        <div>
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="">All statuses</option>
            <?php $__currentLoopData = ['requested','ringing','accepted','rejected','missed','ended','failed']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($status); ?>" <?php if(request('status') === $status): echo 'selected'; endif; ?>><?php echo e(ucfirst($status)); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <?php if($filters): ?>
          <div>
            <label class="form-label">Host</label>
            <select name="host_id" class="form-select">
              <option value="">All hosts</option>
              <?php $__currentLoopData = $filters['hosts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $host): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($host->id); ?>" <?php if((int) request('host_id') === (int) $host->id): echo 'selected'; endif; ?>><?php echo e($host->user?->name ?? ('Host #' . $host->id)); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          <div>
            <label class="form-label">Agency</label>
            <select name="agency_id" class="form-select">
              <option value="">All agencies</option>
              <?php $__currentLoopData = $filters['agencies']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($agency->id); ?>" <?php if((int) request('agency_id') === (int) $agency->id): echo 'selected'; endif; ?>><?php echo e($agency->name); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
        <?php endif; ?>
      </div>
      <div class="d-flex gap-2 mt-3">
        <button class="btn btn-dark"><i class="ti ti-filter me-1"></i>Apply Filters</button>
      </div>
    </form>
  </div>
  <?php if(isset($tabs)): ?>
    <div class="card-body border-top border-bottom">
      <?php if($isAdminStyle): ?>
        <div class="call-admin-tabbar">
        <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <a class="tab-pill <?php echo e($activeTab === $key ? 'active' : ''); ?>"
             href="<?php echo e(request()->fullUrlWithQuery(['tab' => $key])); ?>">
            <?php echo e($label); ?>

          </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php else: ?>
      <ul class="nav nav-tabs">
        <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li class="nav-item">
            <a class="nav-link <?php echo e($activeTab === $key ? 'active' : ''); ?>"
               href="<?php echo e(request()->fullUrlWithQuery(['tab' => $key])); ?>">
              <?php echo e($label); ?>

            </a>
          </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
      <?php endif; ?>
    </div>
  <?php endif; ?>
  <div class="card-body table-responsive">
    <?php if($activeTab === 'host_earnings'): ?>
      <table class="table align-middle <?php echo e($isAdminStyle ? 'call-admin-table' : ''); ?>">
        <thead class="table-light">
          <tr>
            <th>Host</th>
            <th>Total Coins</th>
            <th>Minutes</th>
            <th>Duration</th>
            <th>Host Earnings</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $earnings['hosts']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($row->host?->user?->name ?? ('Host #' . $row->host_id)); ?></td>
              <td><?php echo e(number_format($row->total_coins)); ?></td>
              <td><?php echo e(number_format($row->billable_minutes)); ?></td>
              <td><?php echo e(number_format($row->duration_seconds)); ?></td>
              <td><?php echo e(number_format($row->host_earning)); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="text-center text-muted py-4">No earnings found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    <?php elseif($activeTab === 'agency_earnings'): ?>
      <table class="table align-middle <?php echo e($isAdminStyle ? 'call-admin-table' : ''); ?>">
        <thead class="table-light">
          <tr>
            <th>Agency</th>
            <th>Total Coins</th>
            <th>Minutes</th>
            <th>Duration</th>
            <th>Agency Earnings</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $earnings['agencies']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($row->agency?->name ?? ('Agency #' . $row->agency_id)); ?></td>
              <td><?php echo e(number_format($row->total_coins)); ?></td>
              <td><?php echo e(number_format($row->billable_minutes)); ?></td>
              <td><?php echo e(number_format($row->duration_seconds)); ?></td>
              <td><?php echo e(number_format($row->agency_earning)); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="5" class="text-center text-muted py-4">No earnings found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    <?php else: ?>
      <table class="table align-middle <?php echo e($isAdminStyle ? 'call-admin-table' : ''); ?>">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Caller</th>
            <th>Receiver</th>
            <th>Host</th>
            <th>Agency</th>
            <th>Type</th>
            <th>Status</th>
            <th>End Reason</th>
            <th>Rate / min</th>
            <th>Duration</th>
            <th>Minutes</th>
            <th>Coins</th>
            <th>Host</th>
            <th>Agency</th>
            <th>Platform</th>
            <th>Created</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $calls; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $call): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
              <td><?php echo e($call->id); ?></td>
              <td><?php echo e($call->caller?->name ?? '—'); ?></td>
              <td><?php echo e($call->receiver?->name ?? '—'); ?></td>
              <td><?php echo e($call->host?->user?->name ?? '—'); ?></td>
              <td><?php echo e($call->agency?->name ?? '—'); ?></td>
              <td><span class="call-badge-soft <?php echo e(strtolower($call->type)); ?>"><?php echo e(ucfirst($call->type)); ?></span></td>
              <td><span class="call-badge-soft <?php echo e(strtolower($call->status)); ?>"><?php echo e(ucfirst(str_replace('_', ' ', $call->status))); ?></span></td>
              <td><?php echo e($call->end_reason ? ucfirst(str_replace('_', ' ', $call->end_reason)) : '—'); ?></td>
              <td><?php echo e(number_format((int) $call->coin_rate_per_minute)); ?></td>
              <td><?php echo e($call->duration_seconds); ?></td>
              <td><?php echo e($call->billable_minutes); ?></td>
              <td><?php echo e(number_format($call->total_coins_charged)); ?></td>
              <td><?php echo e(number_format($call->host_earning)); ?></td>
              <td><?php echo e(number_format($call->agency_earning)); ?></td>
              <td><?php echo e(number_format($call->platform_earning)); ?></td>
              <td><?php echo e($call->created_at?->format('d M Y H:i')); ?></td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="16" class="text-center text-muted py-4">No calls found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>

      <?php echo e($calls->links()); ?>

    <?php endif; ?>
  </div>
</div>
<?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/partials/call-report-table.blade.php ENDPATH**/ ?>