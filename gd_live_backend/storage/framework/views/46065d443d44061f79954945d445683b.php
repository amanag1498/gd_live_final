<?php
  $menuContext = 'admin';
  $homeRoute = route('admin.dashboard');
  $panelLabel = 'Admin Panel';
  $defaultTitle = 'GD Live Admin';
  $roleLabel = 'Administrator';
?>


<?php echo $__env->make('layouts.tailadmin-app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/layouts/admin-tailadmin.blade.php ENDPATH**/ ?>