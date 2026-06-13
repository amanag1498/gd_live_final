<?php $__env->startSection('title','Edit User Entry Pack'); ?>

<?php $__env->startSection('content'); ?>
<div class="row g-3">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-header">
        <h6 class="mb-0"><i class="ti ti-user-star me-2"></i>Edit User Entry Pack</h6>
      </div>
      <div class="card-body">
        <?php if($errors->any()): ?>
          <div class="alert alert-danger">
            <div class="fw-semibold mb-1">Please fix the following:</div>
            <ul class="mb-0 ps-3"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
          </div>
        <?php endif; ?>
        <form method="post" action="<?php echo e(route('admin.entry-packs.purchases.update', $userPack)); ?>" class="vstack gap-3">
          <?php echo csrf_field(); ?>
          <?php echo method_field('PUT'); ?>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">User</label>
              <input class="form-control" value="<?php echo e($userPack->user?->name); ?> @ <?php echo e($userPack->user?->email); ?>" disabled>
            </div>
            <div class="col-md-6">
              <label class="form-label">Entry Pack</label>
              <select name="entry_pack_id" class="form-select">
                <?php $__currentLoopData = $packs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pack): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <option value="<?php echo e($pack->id); ?>" <?php if((int) old('entry_pack_id', $userPack->entry_pack_id) === (int) $pack->id): echo 'selected'; endif; ?>><?php echo e($pack->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </select>
            </div>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Purchased At</label>
              <input type="datetime-local" name="purchased_at" class="form-control" value="<?php echo e(old('purchased_at', optional($userPack->purchased_at)->format('Y-m-d\TH:i'))); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label">Expires At</label>
              <input type="datetime-local" name="expires_at" class="form-control" value="<?php echo e(old('expires_at', optional($userPack->expires_at)->format('Y-m-d\TH:i'))); ?>">
            </div>
          </div>
          <div class="form-check">
            <input type="hidden" name="is_active" value="0">
            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?php echo e(old('is_active', (int) $userPack->is_active) ? 'checked' : ''); ?>>
            <label class="form-check-label" for="is_active">Active for this user</label>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-primary">Update</button>
            <a class="btn btn-light border" href="<?php echo e(route('admin.entry-packs.reports')); ?>">Back</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/entry-packs/edit-purchase.blade.php ENDPATH**/ ?>