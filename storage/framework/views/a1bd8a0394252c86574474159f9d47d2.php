

<?php $__env->startSection('content'); ?>
    <style>
        .custom-table-striped tbody tr:nth-of-type(even) {
            background-color: #f4f8ff !important;
        }

        .custom-table-striped tbody tr td {
            border-bottom: none !important;
        }

        .custom-table-striped th,
        .custom-table-striped td {
            white-space: nowrap !important;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        
        
        
        <div class="d-flex align-items-center mb-4">
            <a href="<?php echo e(route('koor.dataKelas', ['page' => $backPage ?? 1])); ?>"
                class="btn btn-sm rounded-circle p-2 me-3 shadow-none d-flex justify-content-center align-items-center"
                style="border: none; background-color: #5b9cf6; width: 35px; height: 35px;">
                <i class="bx bx-arrow-back fs-5 text-white"></i>
            </a>
            <h4 class="fw-bold mb-0" style="color: #5b9cf6;"><?php echo e($kelas->name ?? 'Nama Kelas'); ?></h4>
        </div>

        
        
        
        <div class="row align-items-end mb-4">
            <div class="col-md-4 col-12 mb-3 mb-md-0">
                <span class="text-muted d-block mb-2 fw-semibold" style="font-size: 0.85rem;">Wali Kelas 1</span>
                <div class="d-flex align-items-center bg-white px-2 py-2"
                    style="border-radius: 50px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                    <div class="avatar avatar-sm me-3" style="width: 45px; height: 45px;">
                        <span class="avatar-initial rounded-circle fw-bold"
                            style="background-color: #ffcccb; color: #ff5b5c;">
                            <i class="bx bx-user fs-4"></i>
                        </span>
                    </div>
                    <span class="fw-bold text-dark fs-6"><?php echo e($kelas->homeroom_teacher->name ?? 'Belum Diatur'); ?></span>
                </div>
            </div>

            <div class="col-md-4 col-12 mb-3 mb-md-0">
                <span class="text-muted d-block mb-2 fw-semibold" style="font-size: 0.85rem;">Wali Kelas 2</span>
                <div class="d-flex align-items-center bg-white px-2 py-2"
                    style="border-radius: 50px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                    <div class="avatar avatar-sm me-3" style="width: 45px; height: 45px;">
                        <span class="avatar-initial rounded-circle fw-bold"
                            style="background-color: #cce5ff; color: #5b9cf6;">
                            <i class="bx bx-user fs-4"></i>
                        </span>
                    </div>
                    <span class="fw-bold text-dark fs-6"><?php echo e($kelas->homeroom_teacher_2->name ?? '-'); ?></span>
                </div>
            </div>
            
            
            
            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 15px;">
                    <strong></strong> <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 15px;">
                    <strong>Ada masalah:</strong>
                    <ul class="mb-0 mt-1">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
        </div>

        
        
        
        <div class="card bg-white"
            style="border-radius: 20px; border: 1px solid #5b9cf6; box-shadow: none; overflow: hidden;">

            
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">Daftar Siswa</h5>
                
                <button class="btn rounded-pill px-4 fw-semibold text-white shadow-none d-flex align-items-center"
                    style="background-color: #5b9cf6; border: none;" data-bs-toggle="modal"
                    data-bs-target="#modalAddStudent">
                    <i class="bx bx-plus me-1"></i> Add Student
                </button>
            </div>

            
            <div class="table-responsive text-nowrap">
                <table class="table custom-table-striped table-borderless" style="min-width: 1000px;">
                    <thead style="border-bottom: 1px solid #e0ebfc;">
                        <tr>
                            <th class="text-center py-3 text-muted fw-semibold">No</th>
                            <th class="py-3 text-muted fw-semibold">Photo</th>
                            <th class="py-3 text-muted fw-semibold">Nama</th>
                            <th class="py-3 text-muted fw-semibold">Gender</th>
                            <th class="py-3 text-muted fw-semibold">Kebutuhan Khusus</th>
                            <th class="py-3 text-muted fw-semibold">Parent</th>
                            <th class="text-center py-3 text-muted fw-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $kelas->students ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $siswa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="text-center align-middle text-dark fw-medium"><?php echo e($index + 1); ?></td>

                                
                                <td class="align-middle">
                                    <div class="avatar avatar-md" style="width: 45px; height: 45px;">
                                        <?php if(!empty($siswa->photo)): ?>
                                            <img src="<?php echo e(str_starts_with($siswa->photo, 'http') ? $siswa->photo : asset('storage/' . $siswa->photo)); ?>"
                                                alt="Avatar" class="rounded-circle"
                                                style="object-fit: cover; width: 100%; height: 100%;" />
                                        <?php else: ?>
                                            <span class="avatar-initial rounded-circle"
                                                style="background-color: #cbd5e1; color: white;">
                                                <i class="bx bx-user"></i>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                
                                <td class="align-middle text-dark fw-medium"><?php echo e($siswa->name ?? '-'); ?></td>
                                <td class="align-middle text-dark"><?php echo e(ucwords(str_replace('-', ' ', $siswa->gender ?? '-'))); ?>

                                </td>
                                <td class="align-middle text-dark">
                                    <?php echo e(ucwords(str_replace('_', ' ', $siswa->special_needs ?? 'Reguler'))); ?>

                                </td>
                                <td class="align-middle text-dark"><?php echo e($siswa->parent->name ?? $siswa->mother_name ?? '-'); ?></td>

                                
                                <td class="text-center align-middle">
                                    <a href="<?php echo e(route('koor.detailAnak', $siswa->id ?? 0)); ?>"
                                        class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                        style="font-size: 0.75rem; background-color: #4ade80; border: none;" title="View">
                                        <i class="bx bx-info-circle me-1"></i> View
                                    </a>
                                    

                                    <form
                                        action="<?php echo e(route('koor.keluarkanMurid', ['classId' => $kelas->id, 'studentId' => $siswa->id])); ?>"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Keluarkan siswa ini dari kelas?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none"
                                            style="font-size: 0.75rem; background-color: #f87171; border: none;" title="Remove">
                                            <i class="bx bx-trash me-1"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bx bx-folder-open fs-1 d-block mb-2"></i>
                                    Belum ada siswa di kelas ini.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>


        
        
        
        <div class="modal fade" id="modalAddStudent" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 20px; border: none;">
                    
                    <form action="<?php echo e(route('koor.tambahMuridKeKelas', $kelas->id ?? 0)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <div class="modal-header border-bottom p-4">
                            <h5 class="modal-title fw-bold text-dark">
                                <i class="bx bx-user-plus me-2 text-primary"></i>Tambah Siswa ke Kelas
                            </h5>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label text-dark fw-semibold">Pilih Siswa <span
                                        class="text-danger">*</span></label>
                                <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                    <i class="bx bx-check-square me-1"></i> Centang kotak di samping nama siswa untuk
                                    menambahkannya ke kelas ini.
                                </p>

                                
                                <div class="border rounded p-3"
                                    style="max-height: 250px; overflow-y: auto; background-color: #f8fafc; border-color: #cbd5e1 !important;">

                                    <?php $__empty_1 = true; $__currentLoopData = $allStudents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $siswa): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <div class="form-check d-flex align-items-center mb-3">
                                            
                                            <input class="form-check-input shadow-none" type="checkbox" name="student_ids[]"
                                                value="<?php echo e($siswa->id); ?>" id="student_<?php echo e($siswa->id); ?>"
                                                style="width: 22px; height: 22px; cursor: pointer; border-color: #94a3b8;">

                                            
                                            <label class="form-check-label ms-3 w-100" for="student_<?php echo e($siswa->id); ?>"
                                                style="cursor: pointer; padding-top: 2px;">
                                                <span class="fw-bold text-dark d-block"><?php echo e($siswa->name); ?></span>
                                                <span class="text-muted" style="font-size: 0.75rem;">
                                                    Kebutuhan Khusus:
                                                    <?php echo e(ucwords(str_replace('_', ' ', $siswa->special_needs ?? 'Reguler'))); ?>

                                                </span>
                                            </label>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <div class="text-center text-muted py-3">
                                            <i class="bx bx-folder-open fs-3 d-block mb-1"></i>
                                            Belum ada data siswa.
                                        </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-top pt-3 p-4">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 shadow-none"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-none"
                                style="background-color: #5b9cf6; border: none;">
                                Tambahkan Siswa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/detail-kelas.blade.php ENDPATH**/ ?>