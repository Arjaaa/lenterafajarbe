

<?php $__env->startSection('content'); ?>
    <div class="mb-4">
        <a href="<?php echo e(route('koor.dataKelas')); ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bx bx-chevron-left me-1"></i> Kembali ke Daftar Kelas
        </a>
    </div>

    <h4 class="fw-bold py-3 mb-2"><span class="text-muted fw-light">Manajemen Kelas /</span> Kelola Murid:
        <?php echo e($class->name); ?>

    </h4>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Yay! 🎉</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0 text-white">Info Ruang Kelas</h5>
                </div>
                <div class="card-body pt-3">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="ps-0" width="40%"><strong>Nama Kelas</strong></td>
                            <td>: <?php echo e($class->name); ?></td>
                        </tr>
                        <tr>
                            <td class="ps-0"><strong>Wali Kelas 1</strong></td>
                            <td>: <?php echo e($class->homeroomTeacher->name ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td class="ps-0"><strong>Wali Kelas 2</strong></td>
                            <td>: <?php echo e($class->homeroomTeacher2->name ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td class="ps-0"><strong>Total Murid</strong></td>
                            <td>: <span class="badge bg-label-primary"><?php echo e($class->students->count()); ?> Anak</span></td>
                        </tr>
                    </table>
                    <hr>

                    <form action="<?php echo e(route('koor.tambahMuridKeKelas', $class->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <label class="form-label fw-bold mb-2">Pilih Murid untuk Dimasukkan:</label>

                        <div class="p-2 border rounded bg-light mb-3" style="max-height: 250px; overflow-y: auto;">
                            <?php
                                // Ambil ID semua anak yang sudah masuk ke kelas ini agar bisa kita skip/sembunyikan dari daftar centang
                                $currentStudentIds = $class->students->pluck('id')->toArray();
                            ?>

                            <?php $__empty_1 = true; $__currentLoopData = $allStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $mhs): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                
                                <?php if(!in_array($mhs->id, $currentStudentIds)): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="student_ids[]" value="<?php echo e($mhs->id); ?>"
                                            id="chkStudent<?php echo e($mhs->id); ?>">
                                        <label class="form-check-input-label text-wrap" for="chkStudent<?php echo e($mhs->id); ?>">
                                            <strong><?php echo e($mhs->name); ?></strong>
                                            <small class="text-muted d-block">(<?php echo e($mhs->special_needs ?? 'Umum'); ?>)</small>
                                        </label>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <p class="text-muted text-center mb-0 py-2">Tidak ada data anak.</p>
                            <?php endif; ?>

                            
                            <?php if(count($allStudents) == count($currentStudentIds)): ?>
                                <p class="text-success text-center mb-0 py-2"><i class="bx bx-check-circle me-1"></i> Semua anak
                                    sudah masuk kelas</p>
                            <?php endif; ?>
                        </div>

                        <?php if(count($allStudents) != count($currentStudentIds)): ?>
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="bx bx-plus me-1"></i> Masukkan yang Dicentang
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center bg-lentera-blue-light text-white">
                    <h5 class="mb-0 text-white">Daftar Murid Kelas <?php echo e($class->name); ?></h5>
                </div>
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Anak</th>
                                <th>Kebutuhan Khusus</th>
                                <th>Asal Sekolah</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            <?php $__empty_1 = true; $__currentLoopData = $class->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($index + 1); ?></td>
                                    <td><strong><?php echo e($student->name); ?></strong></td>
                                    <td><span class="badge bg-label-danger"><?php echo e($student->special_needs ?? '-'); ?></span></td>
                                    <td><?php echo e($student->school_name ?? '-'); ?></td>
                                    <td>
                                        <form action="<?php echo e(route('koor.keluarkanMurid', [$class->id, $student->id])); ?>"
                                            method="POST" class="d-inline"
                                            onsubmit="return confirm('Keluarkan <?php echo e($student->name); ?> dari kelas <?php echo e($class->name); ?>?');">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                title="Keluarkan dari Kelas">
                                                <i class="bx bx-log-out me-1"></i> Keluarkan
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada murid di kelas ini. Pilih
                                        murid di panel sebelah kiri untuk menambahkan.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/detail-kelas.blade.php ENDPATH**/ ?>