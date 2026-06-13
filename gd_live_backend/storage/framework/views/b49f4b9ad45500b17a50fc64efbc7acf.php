<?php $__env->startSection('title','Host Dashboard'); ?>

<?php $__env->startSection('content'); ?>
  <h3 class="mb-3">Host Dashboard</h3>
  <p>Welcome, <?php echo e(auth()->user()->name); ?></p>

  <div class="d-flex gap-2 flex-wrap">
    <a class="btn btn-outline-secondary" href="<?php echo e(route('me.applications')); ?>">My Applications</a>
    <a class="btn btn-outline-dark" href="<?php echo e(route('host.calls.index')); ?>">My Calls</a>
  </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/host/dashboard.blade.php ENDPATH**/ ?>