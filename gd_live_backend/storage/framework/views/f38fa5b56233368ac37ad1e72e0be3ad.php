<?php
  $isAdminPreview = request()->routeIs('admin.*');
  $menuContext = $isAdminPreview ? 'admin' : 'agency';
  $homeRoute = $isAdminPreview ? route('admin.dashboard') : route('agency.dashboard');
  $panelLabel = $isAdminPreview ? 'Admin Panel' : 'Agency Panel';
  $defaultTitle = $isAdminPreview ? 'GD Live Admin' : 'GD Live Agency';
  $roleLabel = $isAdminPreview ? 'Administrator' : 'Agency';
?>


<?php echo $__env->make('layouts.tailadmin-app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/layouts/agency-tailadmin.blade.php ENDPATH**/ ?>