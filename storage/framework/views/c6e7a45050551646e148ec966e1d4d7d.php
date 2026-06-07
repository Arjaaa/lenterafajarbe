<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag; ?>
<?php foreach($attributes->onlyProps(['id', 'title', 'size' => '']) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $attributes = $attributes->exceptProps(['id', 'title', 'size' => '']); ?>
<?php foreach (array_filter((['id', 'title', 'size' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>

<div class="modal fade" id="<?php echo e($id); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered <?php echo e($size); ?>" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo e($title); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            
            <?php echo e($slot); ?>


        </div>
    </div>
</div><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/components/modal.blade.php ENDPATH**/ ?>