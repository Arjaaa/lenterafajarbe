

<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Manajemen Kelas /</span> Data Kelas Terapis</h4>

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
            <h5 class="mb-0 text-white">Daftar Ruang Kelas</h5>
            <button type="button" class="btn btn-sm btn-light text-primary" data-bs-toggle="modal"
                data-bs-target="#modalTambahKelas">
                <i class="bx bx-plus me-1"></i> Tambah Kelas
            </button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kelas</th>
                        <th>Wali Kelas 1</th>
                        <th>Wali Kelas 2</th>
                        <th>Jumlah Anak</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php $__empty_1 = true; $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $kelas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><strong><?php echo e($kelas->name); ?></strong></td>

                            <td>
                                <?php if($kelas->homeroomTeacher): ?>
                                    <span class="text-primary fw-semibold"><?php echo e($kelas->homeroomTeacher->name); ?></span>
                                <?php else: ?>
                                    <span class="text-danger"><i>Belum Ditentukan</i></span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if($kelas->homeroomTeacher2): ?>
                                    <span class="text-info fw-semibold"><?php echo e($kelas->homeroomTeacher2->name); ?></span>
                                <?php else: ?>
                                    <span class="text-muted"><i>Tidak Ada</i></span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <span class="badge bg-label-info"><?php echo e($kelas->students_count); ?> Siswa</span>
                            </td>

                            <td>
                                <a href="<?php echo e(route('koor.detailKelas', $kelas->id)); ?>" class="btn btn-sm btn-icon btn-info"
                                    title="Kelola Isi Kelas">
                                    <i class="bx bx-show"></i>
                                </a>

                                <button type="button" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalEditKelas<?php echo e($kelas->id); ?>" title="Edit">
                                    <i class="bx bx-edit-alt"></i>
                                </button>

                                <form action="<?php echo e(route('koor.destroyKelas', $kelas->id)); ?>" method="POST" class="d-inline"
                                    onsubmit="return confirm('Kamu yakin ingin menghapus <?php echo e($kelas->name); ?>?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus"><i
                                            class="bx bx-trash"></i></button>
                                </form>

                                <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalEditKelas'.e($kelas->id).'','title' => 'Edit Ruang Kelas']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalEditKelas'.e($kelas->id).'','title' => 'Edit Ruang Kelas']); ?>
                                    <form action="<?php echo e(route('koor.updateKelas', $kelas->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="<?php echo e($kelas->name); ?>"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Wali Kelas 1 (Utama) <span
                                                        class="text-danger">*</span></label>
                                                <select name="homeroom_teacher_id" class="form-select" required>
                                                    <option value="">-- Pilih Terapis 1 --</option>
                                                    <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        
                                                        <?php if(!in_array($teacher->id, $assignedTeacherIds) || $kelas->homeroom_teacher_id == $teacher->id || $kelas->homeroom_teacher_2_id == $teacher->id): ?>
                                                            <option value="<?php echo e($teacher->id); ?>" <?php echo e($kelas->homeroom_teacher_id == $teacher->id ? 'selected' : ''); ?>>
                                                                <?php echo e($teacher->name); ?>

                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label">Wali Kelas 2 (Pendamping)</label>
                                                <select name="homeroom_teacher_2_id" class="form-select">
                                                    <option value="">-- Pilih Terapis 2 (Opsional) --</option>
                                                    <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        
                                                        <?php if(!in_array($teacher->id, $assignedTeacherIds) || $kelas->homeroom_teacher_id == $teacher->id || $kelas->homeroom_teacher_2_id == $teacher->id): ?>
                                                            <option value="<?php echo e($teacher->id); ?>" <?php echo e($kelas->homeroom_teacher_2_id == $teacher->id ? 'selected' : ''); ?>>
                                                                <?php echo e($teacher->name); ?>

                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
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
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data kelas yang dibuat.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalTambahKelas','title' => 'Buat Ruang Kelas Baru']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalTambahKelas','title' => 'Buat Ruang Kelas Baru']); ?>
        <form action="<?php echo e(route('koor.storeKelas')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Kelas Bintang" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Wali Kelas 1 (Utama) <span class="text-danger">*</span></label>
                    <select name="homeroom_teacher_id" class="form-select" required>
                        <option value="">-- Pilih Terapis 1 --</option>
                        <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php if(!in_array($teacher->id, $assignedTeacherIds)): ?>
                                <option value="<?php echo e($teacher->id); ?>"><?php echo e($teacher->name); ?></option>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label">Wali Kelas 2 (Pendamping)</label>
                    <select name="homeroom_teacher_2_id" class="form-select">
                        <option value="">-- Pilih Terapis 2 (Opsional) --</option>
                        <?php $__currentLoopData = $teachers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            
                            <?php if(!in_array($teacher->id, $assignedTeacherIds)): ?>
                                <option value="<?php echo e($teacher->id); ?>"><?php echo e($teacher->name); ?></option>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-top pt-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Buat Kelas</button>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/data-kelas.blade.php ENDPATH**/ ?>