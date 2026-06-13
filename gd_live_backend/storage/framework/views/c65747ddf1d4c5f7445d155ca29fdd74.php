<?php $__env->startSection('title','Edit Subscription Plan'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0">Edit Plan: <?php echo e($plan->name); ?></h4>
  <a href="<?php echo e(route('admin.subscription-plans.index')); ?>" class="btn btn-outline-secondary">
    <i class="ti ti-arrow-left me-1"></i> Back
  </a>
</div>

<?php if(session('success')): ?> 
  <div class="alert alert-success"><?php echo e(session('success')); ?></div> 
<?php endif; ?>

<?php if($errors->any()): ?>
  <div class="alert alert-danger">
    <strong>Whoops!</strong> Please fix the errors below.
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-body">
    <form id="planForm" method="post" action="<?php echo e(route('admin.subscription-plans.update', $plan)); ?>" class="vstack gap-3">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>

      <div>
        <label class="form-label">Name</label>
        <input name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
               value="<?php echo e(old('name', $plan->name)); ?>" required>
        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      <div class="row">
        <div class="col">
          <label class="form-label">Price (coins)</label>
          <input type="number" name="price_coins" min="1" 
                 class="form-control <?php $__errorArgs = ['price_coins'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                 value="<?php echo e(old('price_coins', $plan->price_coins)); ?>" required>
          <?php $__errorArgs = ['price_coins'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
        <div class="col">
          <label class="form-label">Duration (days)</label>
          <input type="number" name="duration_days" min="1" 
                 class="form-control <?php $__errorArgs = ['duration_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                 value="<?php echo e(old('duration_days', $plan->duration_days)); ?>" required>
          <?php $__errorArgs = ['duration_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>
      </div>

      <div>
        <label class="form-label d-flex align-items-center gap-2">
          Perks (JSON) 
          <small class="text-muted">Optional – Example: {"badge":"Pro","limits":{"daily":5}}</small>
        </label>
        <textarea name="perks" id="perks"
                  class="form-control <?php $__errorArgs = ['perks'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="6"
                  placeholder='{"badge":"Pro","limits":{"daily":5}}'><?php echo e(old('perks', $plan->perks ? json_encode($plan->perks, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) : '')); ?></textarea>
        <?php $__errorArgs = ['perks'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="invalid-feedback"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        <div id="perksError" class="text-danger small mt-1" style="display:none;"></div>
      </div>

      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
               value="1" <?php echo e(old('is_active', $plan->is_active) ? 'checked' : ''); ?>>
        <label class="form-check-label" for="is_active">Active</label>
      </div>

      <div class="d-flex gap-2">
        <button class="btn btn-primary" id="saveBtn">
          <i class="ti ti-device-floppy me-1"></i> Save changes
        </button>
        <a href="<?php echo e(route('admin.subscription-plans.index')); ?>" class="btn btn-light">Cancel</a>
      </div>
    </form>
  </div>
</div>


<?php $__env->startPush('scripts'); ?>
<script>
  document.getElementById('planForm').addEventListener('submit', function(e) {
    const perksEl = document.getElementById('perks');
    const errEl = document.getElementById('perksError');
    errEl.style.display = 'none'; errEl.textContent = '';
    const val = perksEl.value.trim();
    if (val.length) {
      try { JSON.parse(val); } 
      catch (ex) {
        e.preventDefault();
        errEl.textContent = 'Perks must be valid JSON.';
        errEl.style.display = 'block';
      }
    }
  });
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin-tailadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/admin/subscriptions/plans/edit.blade.php ENDPATH**/ ?>