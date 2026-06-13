<?php $__env->startSection('title','Apply as Host'); ?>
<?php $__env->startSection('content'); ?>
<h1>Apply as Host</h1>
<form method="post" action="<?php echo e(route('host.apply.store')); ?>"><?php echo csrf_field(); ?>
  <div class="mb-3">
    <label class="form-label">Agency</label>
    <select name="agency_id" class="form-select" required>
      <option value="">-- Select agency --</option>
      <?php $__currentLoopData = $agencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <option value="<?php echo e($agency->id); ?>" <?php if(old('agency_id') == $agency->id): echo 'selected'; endif; ?>><?php echo e($agency->name); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
  </div>
  <div class="mb-3"><label class="form-label">Stage Name</label><input name="stage_name" class="form-control"></div>
  <div class="mb-3"><label class="form-label">Contact Phone</label><input name="contact_phone" class="form-control"></div>
  <div class="row">
    <div class="col-md-6 mb-3"><label class="form-label">Country</label><input name="country" class="form-control"></div>
    <div class="col-md-6 mb-3"><label class="form-label">City</label><input name="city" class="form-control"></div>
  </div>
  <div class="mb-3"><label class="form-label">About</label><textarea name="about" rows="4" class="form-control"></textarea></div>
  <button class="btn btn-primary">Submit</button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/host/apply.blade.php ENDPATH**/ ?>