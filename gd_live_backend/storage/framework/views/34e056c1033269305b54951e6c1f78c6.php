<?php $__env->startSection('title', 'Create Recharge Plan'); ?>

<?php $__env->startSection('content'); ?>
<form method="post" action="<?php echo e(route('admin.recharge-plans.store')); ?>" class="space-y-6">
  <?php echo csrf_field(); ?>
  <?php echo $__env->make('admin.recharge-plans._form', ['mode' => 'create'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/recharge-plans/create.blade.php ENDPATH**/ ?>