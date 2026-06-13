<?php $__env->startSection('title','Apply as Agency'); ?>
<?php $__env->startSection('content'); ?>
<h1>Apply to become an Agency</h1>
<form method="post" action="<?php echo e(route('agency.apply.store')); ?>"><?php echo csrf_field(); ?>
  <div class="mb-3"><label class="form-label">Agency Name*</label><input name="agency_name" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">Legal Name</label><input name="legal_name" class="form-control"></div>
  <div class="mb-3"><label class="form-label">Contact Phone</label><input name="contact_phone" class="form-control"></div>
  <div class="mb-3"><label class="form-label">Website</label><input name="website" class="form-control" placeholder="https://"></div>
  <div class="mb-3"><label class="form-label">About</label><textarea name="about" rows="4" class="form-control"></textarea></div>
  <button class="btn btn-primary">Submit</button>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/amanagarwal/Desktop/gd_remake/gd_live_backend/resources/views/agency/apply.blade.php ENDPATH**/ ?>