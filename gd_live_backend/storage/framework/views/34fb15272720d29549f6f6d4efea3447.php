<?php $__env->startSection('title','Edit Entry Pack'); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('admin.entry-packs.partials.form', [
  'route' => route('admin.entry-packs.update', $pack),
  'method' => 'PUT',
  'pack' => $pack,
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/entry-packs/edit.blade.php ENDPATH**/ ?>