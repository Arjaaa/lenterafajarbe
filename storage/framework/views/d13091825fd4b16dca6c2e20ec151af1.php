

<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Manajemen Pengguna /</span> Data Guru & Terapis</h4>

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
            <h5 class="mb-0 text-white">Daftar Guru dan Terapis</h5>
            <button type="button" class="btn btn-sm btn-light text-primary" data-bs-toggle="modal"
                data-bs-target="#modalTambahGuru">
                <i class="bx bx-plus me-1"></i> Tambah Pegawai
            </button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Jabatan (Role)</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    <?php $__empty_1 = true; $__currentLoopData = $gurus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $guru): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><strong><?php echo e($guru->name); ?></strong></td>
                            <td>
                                <?php if($guru->role == 'shadow_pj'): ?> <span class="badge bg-label-primary">PJ Shadow</span>
                                <?php elseif($guru->role == 'shadow_teacher'): ?> <span class="badge bg-label-info">Guru Shadow</span>
                                <?php elseif($guru->role == 'therapist_homeroom'): ?> <span class="badge bg-label-warning">Wali Kelas
                                    (Terapis)</span>
                                <?php elseif($guru->role == 'therapist'): ?> <span class="badge bg-label-success">Terapis</span>
                                <?php else: ?> <?php echo e($guru->role); ?> <?php endif; ?>
                            </td>
                            <td><?php echo e($guru->email); ?></td>
                            <td><?php echo e($guru->phone ?? '-'); ?></td>

                            
                            <td>
                                <?php if($guru->is_active): ?>
                                    <span class="badge bg-label-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-label-danger">Nonaktif / Resign</span>
                                <?php endif; ?>
                            </td>

                            
                            <td>
                                <button type="button" class="btn btn-sm btn-icon btn-info" data-bs-toggle="modal"
                                    data-bs-target="#modalDetailGuru<?php echo e($guru->id); ?>" title="Lihat Detail">
                                    <i class="bx bx-show"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalEditGuru<?php echo e($guru->id); ?>" title="Edit">
                                    <i class="bx bx-edit-alt"></i>
                                </button>

                                <form action="<?php echo e(route('koor.destroyGuru', $guru->id)); ?>" method="POST" class="d-inline"
                                    onsubmit="return confirm('Kamu yakin ingin menghapus data Pegawai <?php echo e($guru->name); ?>?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus"><i
                                            class="bx bx-trash"></i></button>
                                </form>

                                
                                <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalDetailGuru'.e($guru->id).'','title' => 'Detail Pegawai: '.e($guru->name).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalDetailGuru'.e($guru->id).'','title' => 'Detail Pegawai: '.e($guru->name).'']); ?>
                                    <div class="modal-body text-wrap text-start">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td width="35%" class="ps-0"><strong>Nama Lengkap</strong></td>
                                                <td>: <?php echo e($guru->name); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0"><strong>Jabatan</strong></td>
                                                <td>:
                                                    <?php if($guru->role == 'shadow_pj'): ?> PJ Shadow
                                                    <?php elseif($guru->role == 'shadow_teacher'): ?> Guru Shadow
                                                    <?php elseif($guru->role == 'therapist_homeroom'): ?> Wali Kelas (Terapis)
                                                    <?php elseif($guru->role == 'therapist'): ?> Terapis
                                                    <?php else: ?> <?php echo e($guru->role); ?> <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0"><strong>Email Login</strong></td>
                                                <td>: <?php echo e($guru->email); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0"><strong>No. HP (WA)</strong></td>
                                                <td>: <?php echo e($guru->phone ?? '-'); ?></td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0"><strong>Status Pegawai</strong></td>
                                                <td>:
                                                    <?php if($guru->is_active): ?>
                                                        <span class="badge bg-label-success">Aktif</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-label-danger">Nonaktif / Resign</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="modal-footer border-top pt-3">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
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

                                
                                <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalEditGuru'.e($guru->id).'','title' => 'Edit Data Guru & Terapis']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalEditGuru'.e($guru->id).'','title' => 'Edit Data Guru & Terapis']); ?>
                                    <form action="<?php echo e(route('koor.updateGuru', $guru->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Lengkap <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="<?php echo e($guru->name); ?>"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Jabatan (Role) <span
                                                        class="text-danger">*</span></label>
                                                <select name="role" class="form-select" required>
                                                    <option value="shadow_pj" <?php echo e($guru->role == 'shadow_pj' ? 'selected' : ''); ?>>
                                                        PJ Shadow</option>
                                                    <option value="shadow_teacher" <?php echo e($guru->role == 'shadow_teacher' ? 'selected' : ''); ?>>Guru Shadow</option>
                                                    <option value="therapist_homeroom" <?php echo e($guru->role == 'therapist_homeroom' ? 'selected' : ''); ?>>Wali Kelas (Terapis)</option>
                                                    <option value="therapist" <?php echo e($guru->role == 'therapist' ? 'selected' : ''); ?>>
                                                        Terapis</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control" value="<?php echo e($guru->email); ?>"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">No. HP (WhatsApp)</label>
                                                <input type="text" name="phone" class="form-control" value="<?php echo e($guru->phone); ?>">
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label">Status Pegawai <span
                                                        class="text-danger">*</span></label>
                                                <select name="is_active" class="form-select" required>
                                                    <option value="1" <?php echo e($guru->is_active ? 'selected' : ''); ?>>Aktif
                                                    </option>
                                                    <option value="0" <?php echo e(!$guru->is_active ? 'selected' : ''); ?>>Nonaktif /
                                                        Resign</option>
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
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data guru/terapis.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalTambahGuru','title' => 'Tambah Pegawai Baru']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalTambahGuru','title' => 'Tambah Pegawai Baru']); ?>
        <form action="<?php echo e(route('koor.storeGuru')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jabatan (Role) <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="">-- Pilih Jabatan --</option>
                        <option value="shadow_pj">PJ Shadow</option>
                        <option value="shadow_teacher">Guru Shadow</option>
                        <option value="therapist_homeroom">Wali Kelas (Terapis)</option>
                        <option value="therapist">Terapis</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-0">
                    <label class="form-label">No. HP (WhatsApp)</label>
                    <input type="text" name="phone" class="form-control">
                </div>
            </div>
            <div class="modal-footer border-top pt-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/data-guru.blade.php ENDPATH**/ ?>