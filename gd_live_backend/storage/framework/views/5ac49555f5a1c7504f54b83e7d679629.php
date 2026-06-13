<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?php echo $__env->yieldContent('title','GD Live'); ?></title>
  <link rel="icon" href="<?php echo e(asset('berry/assets/images/gd-live-logo.png')); ?>" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-light bg-light px-3">
  <a class="navbar-brand d-inline-flex align-items-center gap-2" href="<?php echo e(route('home')); ?>">
    <img src="<?php echo e(asset('berry/assets/images/gd-live-logo.png')); ?>" alt="GD Live" style="width: 32px; height: 32px; object-fit: contain;">
    <span>GD Live</span>
  </a>

  <?php if(auth()->guard()->check()): ?>
    <div class="d-flex gap-2">
      
      <?php if (! (auth()->user()->hasAnyRole(['agency','host']))): ?>
        <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('agency.apply')); ?>">Apply Agency</a>
        <a class="btn btn-sm btn-outline-primary" href="<?php echo e(route('host.apply')); ?>">Apply Host</a>
      <?php endif; ?>

      
      <?php if (\Illuminate\Support\Facades\Blade::check('hasrole', 'admin')): ?>
        <a class="btn btn-sm btn-outline-secondary" href="<?php echo e(route('admin.dashboard')); ?>">Admin</a>
      <?php endif; ?>
    </div>
    <a class="btn btn-sm btn-outline-secondary" href="<?php echo e(route('me.applications')); ?>">My Applications</a>

  <?php endif; ?>
</nav>

<main class="container py-4">
  <?php if(session('ok')): ?>  <div class="alert alert-success"><?php echo e(session('ok')); ?></div> <?php endif; ?>
  <?php if(session('err')): ?> <div class="alert alert-danger"><?php echo e(session('err')); ?></div> <?php endif; ?>

  <?php echo $__env->yieldContent('content'); ?>
</main>
</body>
</html>
<?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/layouts/app.blade.php ENDPATH**/ ?>