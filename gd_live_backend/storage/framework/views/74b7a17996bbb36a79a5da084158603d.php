<?php $__env->startSection('title','Edit User Subscription'); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Edit Subscription #<?php echo e($sub->id); ?> — <?php echo e($sub->user->name); ?></h4>
  <a href="<?php echo e(route('admin.user-subscriptions.index')); ?>" class="btn btn-outline-secondary">Back</a>
</div>

<div class="card">
  <div class="card-body">

    
    <form id="update-sub" method="POST"
          action="<?php echo e(route('admin.user-subscriptions.update', $sub)); ?>"
          class="vstack gap-3">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>

      <div class="row">
        <div class="col-md-6">
          <label class="form-label">User</label>
          <input class="form-control"
                 value="#<?php echo e($sub->user->id); ?> — <?php echo e($sub->user->name); ?> (<?php echo e($sub->user->email); ?>)"
                 disabled>
        </div>
        <div class="col-md-6">
          <label class="form-label">Plan</label>
          <select name="plan_id" class="form-select" required>
            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($p->id); ?>" <?php echo e($sub->subscription_plan_id==$p->id?'selected':''); ?>>
                <?php echo e($p->name); ?> — <?php echo e($p->price_coins); ?> coins / <?php echo e($p->duration_days); ?>d
              </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>

      <div class="row">
        <div class="col-md-4">
          <label class="form-label">Status</label>
          <select name="status" class="form-select">
            <?php $__currentLoopData = ['active','cancelled','expired']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($s); ?>" <?php echo e($sub->status===$s?'selected':''); ?>><?php echo e($s); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Starts at</label>
          <input type="datetime-local" name="starts_at" class="form-control"
                 value="<?php echo e($sub->starts_at?->format('Y-m-d\TH:i')); ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Ends at</label>
          <input type="datetime-local" name="ends_at" class="form-control"
                 value="<?php echo e($sub->ends_at?->format('Y-m-d\TH:i')); ?>">
        </div>
      </div>

      <div class="form-check mt-2">
        <input class="form-check-input" type="checkbox" id="charge_coins" name="charge_coins" value="1">
        <label class="form-check-label" for="charge_coins">Charge coins from user wallet now</label>
      </div>
    </form>

    
    <form id="delete-sub" method="POST"
          action="<?php echo e(route('admin.user-subscriptions.destroy', $sub)); ?>">
      <?php echo csrf_field(); ?>
      <?php echo method_field('DELETE'); ?>
    </form>

    
    <div class="d-flex gap-2 mt-3">
      <button type="submit" class="btn btn-primary" form="update-sub">Save</button>

      <a href="<?php echo e(route('admin.user-subscriptions.index')); ?>" class="btn btn-light">Cancel</a>

      <button type="submit"
              class="btn btn-outline-danger ms-auto"
              form="delete-sub"
              formnovalidate
              onclick="return confirm('Delete subscription?')">
        Delete
      </button>
    </div>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/subscriptions/users/edit.blade.php ENDPATH**/ ?>