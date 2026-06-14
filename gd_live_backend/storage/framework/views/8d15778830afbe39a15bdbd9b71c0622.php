<?php $__env->startSection('title','Agency Calls'); ?>
<?php $__env->startSection('page_intro','Call activity, minutes, and spend across hosts attached to your agency.'); ?>

<?php $__env->startSection('content'); ?>
  <?php echo $__env->make('partials.call-report-table', ['layout' => 'agency'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.agency-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/agency/calls/index.blade.php ENDPATH**/ ?>