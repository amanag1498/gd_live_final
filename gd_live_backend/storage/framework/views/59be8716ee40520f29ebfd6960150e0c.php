<?php $__env->startSection('title', 'Edit Recharge Plan'); ?>

<?php $__env->startSection('content'); ?>
<form method="post" action="<?php echo e(route('admin.recharge-plans.update', $plan)); ?>" class="space-y-6">
  <?php echo csrf_field(); ?>
  <?php echo method_field('PUT'); ?>
  <?php echo $__env->make('admin.recharge-plans._form', ['mode' => 'edit'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/recharge-plans/edit.blade.php ENDPATH**/ ?>