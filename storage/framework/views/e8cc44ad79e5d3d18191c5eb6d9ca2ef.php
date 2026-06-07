

<?php $__env->startSection('content'); ?>
<h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Manajemen Kelas /</span> Sesi Terapi 1 on 1</h4>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Yay! 🎉</strong> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if($errors->any()): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Oops! Ada yang salah:</strong>
        <ul class="mb-0 mt-1">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center bg-lentera-blue-light text-white mb-3">
        <h5 class="mb-0 text-white">Daftar Kelas 1 on 1</h5>
        <button type="button" class="btn btn-sm btn-light text-primary" data-bs-toggle="modal" data-bs-target="#modalTambah1on1">
            <i class="bx bx-plus me-1"></i> Buat Sesi Baru
        </button>
    </div>
    <div class="table-responsive text-nowrap">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Program / Terapi</th>
                    <th>Nama Anak</th>
                    <th>Terapis (Guru)</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <?php $__empty_1 = true; $__currentLoopData = $oneOnOnes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $sesi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td><strong><?php echo e($sesi->name); ?></strong></td>
                    
                    <td><span class="text-primary fw-semibold"><?php echo e($sesi->student->name ?? 'Data Hilang'); ?></span></td>
                    <td><span class="text-info fw-semibold"><?php echo e($sesi->teacher->name ?? 'Data Hilang'); ?></span></td>
                    
                    <td>
                        <button type="button" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="modal" data-bs-target="#modalEdit1on1<?php echo e($sesi->id); ?>" title="Edit">
                            <i class="bx bx-edit-alt"></i>
                        </button>

                        <form action="<?php echo e(route('koor.destroy1on1', $sesi->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Kamu yakin ingin menghapus sesi <?php echo e($sesi->name); ?> ini?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus"><i class="bx bx-trash"></i></button>
                        </form>

                        <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalEdit1on1'.e($sesi->id).'','title' => 'Edit Sesi 1 on 1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalEdit1on1'.e($sesi->id).'','title' => 'Edit Sesi 1 on 1']); ?>
                            <form action="<?php echo e(route('koor.update1on1', $sesi->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <div class="modal-body text-start">
                                    <div class="mb-3">
                                        <label class="form-label">Nama Program / Terapi <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" value="<?php echo e($sesi->name); ?>" placeholder="Contoh: Terapi Wicara" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Pilih Anak <span class="text-danger">*</span></label>
                                        <select name="student_id" class="form-select" required>
                                            <option value="">-- Pilih Anak --</option>
                                            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                
                                                <?php if(!in_array($student->id, $busyStudentIds) || $sesi->student_id == $student->id): ?>
                                                    <option value="<?php echo e($student->id); ?>" <?php echo e($sesi->student_id == $student->id ? 'selected' : ''); ?>>
                                                        <?php echo e($student->name); ?>

                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label">Pilih Terapis <span class="text-danger">*</span></label>
                                        <select name="teacher_id" class="form-select" required>
                                            <option value="">-- Pilih Terapis --</option>
                                            <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                
                                                <?php if(!in_array($teacher->id, $busyTeacherIds) || $sesi->teacher_id == $teacher->id): ?>
                                                    <option value="<?php echo e($teacher->id); ?>" <?php echo e($sesi->teacher_id == $teacher->id ? 'selected' : ''); ?>>
                                                        <?php echo e($teacher->name); ?>

                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer border-top pt-3">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">Belum ada sesi Terapi 1 on 1 yang dibuat.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalTambah1on1','title' => 'Buat Sesi 1 on 1 Baru']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalTambah1on1','title' => 'Buat Sesi 1 on 1 Baru']); ?>
    <form action="<?php echo e(route('koor.store1on1')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="modal-body">
            <div class="mb-3">
                <label class="form-label">Nama Program / Terapi <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Terapi Okupasi" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Pilih Anak <span class="text-danger">*</span></label>
                <select name="student_id" class="form-select" required>
                    <option value="">-- Pilih Anak --</option>
                    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                        <?php if(!in_array($student->id, $busyStudentIds)): ?>
                            <option value="<?php echo e($student->id); ?>"><?php echo e($student->name); ?></option>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="mb-0">
                <label class="form-label">Pilih Terapis <span class="text-danger">*</span></label>
                <select name="teacher_id" class="form-select" required>
                    <option value="">-- Pilih Terapis --</option>
                    <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        
                        <?php if(!in_array($teacher->id, $busyTeacherIds)): ?>
                            <option value="<?php echo e($teacher->id); ?>"><?php echo e($teacher->name); ?></option>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div class="modal-footer border-top pt-3">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Buat Sesi</button>
        </div>
    </form>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/data-1on1.blade.php ENDPATH**/ ?>