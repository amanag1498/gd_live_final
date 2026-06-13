<?php $__env->startSection('title','My Calls'); ?>

<?php $__env->startSection('content'); ?>
  <h3 class="mb-3">My Calls</h3>
  <?php echo $__env->make('partials.call-report-table', ['layout' => 'web'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/host/calls/index.blade.php ENDPATH**/ ?>