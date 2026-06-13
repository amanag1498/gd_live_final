<?php $__env->startSection('title','Create User Subscription'); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Create User Subscription</h4>
  <a href="<?php echo e(route('admin.user-subscriptions.index')); ?>" class="btn btn-outline-secondary">Back</a>
</div>

<div class="card">
  <div class="card-body">
    <form method="post" action="<?php echo e(route('admin.user-subscriptions.store')); ?>" class="vstack gap-3">
      <?php echo csrf_field(); ?>

      <div class="row">
        <div class="col-md-6">
          <label class="form-label">User</label>
          <select name="user_id" class="form-select" required>
            <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($u->id); ?>"><?php echo e($u->id); ?> — <?php echo e($u->name); ?> (<?php echo e($u->email); ?>)</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Plan</label>
          <select name="plan_id" class="form-select" required>
            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($p->id); ?>"><?php echo e($p->name); ?> — <?php echo e($p->price_coins); ?> coins / <?php echo e($p->duration_days); ?>d</option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <option value="active">active</option>
            <option value="cancelled">cancelled</option>
            <option value="expired">expired</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Starts at (optional)</label>
          <input type="datetime-local" name="starts_at" class="form-control">
        </div>
        <div class="col-md-4">
          <label class="form-label">Ends at (optional)</label>
          <input type="datetime-local" name="ends_at" class="form-control">
        </div>
      </div>

      <div class="form-check mt-2">
        <input class="form-check-input" type="checkbox" id="charge_coins" name="charge_coins" value="1">
        <label class="form-check-label" for="charge_coins">Charge coins from user wallet now</label>
      </div>

      <div>
        <button class="btn btn-primary">Create</button>
        <a href="<?php echo e(route('admin.user-subscriptions.index')); ?>" class="btn btn-light">Cancel</a>
      </div>
    </form>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/subscriptions/users/create.blade.php ENDPATH**/ ?>