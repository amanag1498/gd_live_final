<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Recharge Audit <?php echo e($selectedMonth->format('F Y')); ?></title>
  <style>
    body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 12px; }
    .header { margin-bottom: 20px; }
    .title { font-size: 24px; font-weight: 700; margin: 0 0 4px; }
    .subtitle { color: #6b7280; margin: 0; }
    .cards { width: 100%; border-collapse: separate; border-spacing: 10px 0; margin: 16px 0 22px; }
    .cards td { border: 1px solid #dbe1ea; border-radius: 12px; padding: 12px; vertical-align: top; }
    .label { color: #6b7280; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; }
    .value { font-size: 18px; font-weight: 700; margin-top: 4px; }
    table.audit { width: 100%; border-collapse: collapse; }
    table.audit th, table.audit td { border: 1px solid #dbe1ea; padding: 8px; text-align: left; }
    table.audit th { background: #f5f7fb; font-size: 11px; text-transform: uppercase; letter-spacing: .04em; }
    .muted { color: #6b7280; }
    .status { font-weight: 700; }
    .footer-note { margin-top: 16px; color: #6b7280; font-size: 11px; }
  </style>
</head>
<body>
  <div class="header">
    <div class="title">Recharge Audit</div>
    <p class="subtitle"><?php echo e($selectedMonth->format('F Y')); ?> finance ledger snapshot</p>
  </div>

  <table class="cards">
    <tr>
      <td>
        <div class="label">Orders</div>
        <div class="value"><?php echo e(number_format((int) ($summary->total_orders ?? 0))); ?></div>
      </td>
      <td>
        <div class="label">Successful</div>
        <div class="value"><?php echo e(number_format((int) ($summary->successful_orders ?? 0))); ?></div>
      </td>
      <td>
        <div class="label">Gross Amount</div>
        <div class="value">Rs <?php echo e(number_format((float) ($summary->rupees_total ?? 0), 2)); ?></div>
      </td>
      <td>
        <div class="label">Taxable Amount</div>
        <div class="value">Rs <?php echo e(number_format((float) ($summary->taxable_total ?? 0), 2)); ?></div>
      </td>
      <td>
        <div class="label">GST @ 18%</div>
        <div class="value">Rs <?php echo e(number_format((float) ($summary->gst_total ?? 0), 2)); ?></div>
      </td>
      <td>
        <div class="label">Coins</div>
        <div class="value"><?php echo e(number_format((int) ($summary->coins_total ?? 0))); ?></div>
      </td>
    </tr>
  </table>

  <table class="audit">
    <thead>
      <tr>
        <th>Order</th>
        <th>User</th>
        <th>Gross Amount</th>
        <th>Taxable Amount</th>
        <th>GST (18%)</th>
        <th>Coins</th>
        <th>Created</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php
          $grossAmount = (float) $order->amount_rupees;
          $taxableAmount = round($grossAmount / 1.18, 2);
          $gstAmount = round($grossAmount - $taxableAmount, 2);
        ?>
        <tr>
          <td>
            <div><?php echo e($order->order_id); ?></div>
            <div class="muted"><?php echo e($order->gateway_order_id ?: ($order->gateway_payment_id ?: '—')); ?></div>
          </td>
          <td>
            <div><?php echo e($order->user?->name ?? 'User #'.$order->user_id); ?></div>
            <div class="muted"><?php echo e($order->user?->email ?? '—'); ?></div>
          </td>
          <td>Rs <?php echo e(number_format($grossAmount, 2)); ?></td>
          <td>Rs <?php echo e(number_format($taxableAmount, 2)); ?></td>
          <td>Rs <?php echo e(number_format($gstAmount, 2)); ?></td>
          <td><?php echo e(number_format((int) $order->total_coins)); ?></td>
          <td><?php echo e($order->created_at?->format('d M Y, h:i A')); ?></td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
          <td colspan="7" class="muted">No recharge orders found for this period.</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="footer-note">
    <?php if(!empty($filters['status']) || !empty($filters['gateway']) || !empty($filters['q'])): ?>
      Filtered export.
      <?php if(!empty($filters['status'])): ?> Status: <?php echo e($filters['status']); ?>. <?php endif; ?>
      <?php if(!empty($filters['gateway'])): ?> Gateway: <?php echo e($filters['gateway']); ?>. <?php endif; ?>
      <?php if(!empty($filters['q'])): ?> Search: <?php echo e($filters['q']); ?>. <?php endif; ?>
    <?php else: ?>
      Generated from GD Live admin recharge audit.
    <?php endif; ?>
  </div>
</body>
</html>
<?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/recharge-audit/pdf.blade.php ENDPATH**/ ?>