

<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Manajemen Kelas /</span> Group Shadow Teacher</h4>

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
            <h5 class="mb-0 text-white">Daftar Penugasan Shadow Teacher</h5>
            <button type="button" class="btn btn-sm btn-light text-primary" data-bs-toggle="modal"
                data-bs-target="#modalTambahShadow">
                <i class="bx bx-plus me-1"></i> Buat Group Baru
            </button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Group</th>
                        <th>Anak didampingi</th>
                        <th>PJ Shadow (Koor)</th>
                        <th>Guru Shadow (Partner)</th>
                        <th>Lokasi Sekolah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php $__empty_1 = true; $__currentLoopData = $shadowGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><strong><?php echo e($group->name); ?></strong></td>

                            <td><span class="text-primary fw-semibold"><?php echo e($group->student->name ?? 'Data Hilang'); ?></span></td>
                            <td><span class="text-info fw-semibold"><?php echo e($group->pic->name ?? 'Data Hilang'); ?></span></td>
                            <td><span class="text-warning fw-semibold"><?php echo e($group->partner->name ?? 'Data Hilang'); ?></span></td>
                            <td><?php echo e($group->school_name); ?></td>
                           

                            <td>
                                <a href="<?php echo e(route('koor.shadow.show', $group->id)); ?>" class="btn btn-sm btn-icon btn-info" title="Lihat Detail">
                                    <i class="bx bx-show"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalEditShadow<?php echo e($group->id); ?>" title="Edit">
                                    <i class="bx bx-edit-alt"></i>
                                </button>

                                <form action="<?php echo e(route('koor.destroyShadowGroup', $group->id)); ?>" method="POST" class="d-inline"
                                    onsubmit="return confirm('Kamu yakin ingin menghapus grup <?php echo e($group->name); ?>?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus"><i
                                            class="bx bx-trash"></i></button>
                                </form>

                                <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalEditShadow'.e($group->id).'','title' => 'Edit Group Shadow']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalEditShadow'.e($group->id).'','title' => 'Edit Group Shadow']); ?>
                                    <form action="<?php echo e(route('koor.updateShadowGroup', $group->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Group <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="<?php echo e($group->name); ?>"
                                                    placeholder="Contoh: Group Bermain A" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Anak yang Didampingi <span
                                                        class="text-danger">*</span></label>
                                                <select name="student_id" class="form-select" required>
                                                    <option value="">-- Pilih Anak --</option>
                                                    <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        
                                                        <?php if(!in_array($student->id, $busyStudentIds) || $group->student_id == $student->id): ?>
                                                            <option value="<?php echo e($student->id); ?>" <?php echo e($group->student_id == $student->id ? 'selected' : ''); ?>>
                                                                <?php echo e($student->name); ?>

                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Penanggung Jawab (PJ Shadow) <span
                                                        class="text-danger">*</span></label>
                                                <select name="pic_id" class="form-select" required>
                                                    <option value="">-- Pilih PJ --</option>
                                                    <?php $__currentLoopData = $pjs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        
                                                        <?php if(!in_array($pj->id, $assignedPicIds) || $group->pic_id == $pj->id): ?>
                                                            <option value="<?php echo e($pj->id); ?>" <?php echo e($group->pic_id == $pj->id ? 'selected' : ''); ?>>
                                                                <?php echo e($pj->name); ?>

                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Guru Pendamping (Partner) <span
                                                        class="text-danger">*</span></label>
                                                <select name="partner_id" class="form-select" required>
                                                    <option value="">-- Pilih Guru Shadow --</option>
                                                    <?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        
                                                        <?php if(!in_array($partner->id, $assignedPartnerIds) || $group->partner_id == $partner->id): ?>
                                                            <option value="<?php echo e($partner->id); ?>" <?php echo e($group->partner_id == $partner->id ? 'selected' : ''); ?>>
                                                                <?php echo e($partner->name); ?>

                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label">Lokasi Sekolah <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="school_name" class="form-control"
                                                    value="<?php echo e($group->school_name); ?>" placeholder="Contoh: TK Tunas Bangsa"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top pt-3">
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">Batal</button>
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
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada Group Shadow Teacher yang dibuat.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalTambahShadow','title' => 'Buat Group Shadow Baru']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalTambahShadow','title' => 'Buat Group Shadow Baru']); ?>
        <form action="<?php echo e(route('koor.storeShadowGroup')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Group <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Group Bermain A" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Anak yang Didampingi <span class="text-danger">*</span></label>
                    <select name="student_id" class="form-select" required>
                        <option value="">-- Pilih Anak --</option>
                        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php if(!in_array($student->id, $busyStudentIds)): ?>
                                <option value="<?php echo e($student->id); ?>"><?php echo e($student->name); ?></option>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Penanggung Jawab (PJ Shadow) <span class="text-danger">*</span></label>
                    <select name="pic_id" class="form-select" required>
                        <option value="">-- Pilih PJ --</option>
                        <?php $__currentLoopData = $pjs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php if(!in_array($pj->id, $assignedPicIds)): ?>
                                <option value="<?php echo e($pj->id); ?>"><?php echo e($pj->name); ?></option>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Guru Pendamping (Partner) <span class="text-danger">*</span></label>
                    <select name="partner_id" class="form-select" required>
                        <option value="">-- Pilih Guru Shadow --</option>
                        <?php $__currentLoopData = $partners; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php if(!in_array($partner->id, $assignedPartnerIds)): ?>
                                <option value="<?php echo e($partner->id); ?>"><?php echo e($partner->name); ?></option>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label">Lokasi Sekolah <span class="text-danger">*</span></label>
                    <input type="text" name="school_name" class="form-control" placeholder="Contoh: TK Tunas Bangsa"
                        required>
                </div>
            </div>
            <div class="modal-footer border-top pt-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Buat Group</button>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/data-shadow.blade.php ENDPATH**/ ?>