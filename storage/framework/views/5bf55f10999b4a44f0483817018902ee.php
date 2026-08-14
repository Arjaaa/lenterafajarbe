

<?php $__env->startSection('content'); ?>
    <div class="container-xxl flex-grow-1 container-p-y">

        
        <div class="d-flex align-items-center mb-4">
            <a href="<?php echo e(route('koor.dataAnak', ['page' => $backPage ?? 1])); ?>"
                class="btn btn-sm btn-outline-primary rounded-circle p-2 me-3 shadow-none"
                style="border: none; background-color: #e0ebfc; color: #5b9cf6;">
                <i class="bx bx-arrow-back fs-4"></i>
            </a>
            <h4 class="fw-bold mb-0" style="color: #5b9cf6;">Biodata Lengkap</h4>
        </div>

        
        <div class="card mb-4 bg-white"
            style="border-radius: 20px; border: 1px solid #e0ebfc; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
            <div class="card-body d-flex align-items-center p-4">
                <div class="avatar avatar-xl me-4" style="width: 100px; height: 100px;">
                    <?php if(isset($student->photo) && $student->photo != ''): ?>
                        <img src="<?php echo e($student->photo); ?>" alt="Foto Siswa" class="rounded-circle"
                            style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <span class="avatar-initial rounded-circle bg-label-secondary" style="font-size: 3rem;"><i
                                class="bx bx-user"></i></span>
                    <?php endif; ?>
                </div>
                <div>
                    <h3 class="fw-bold text-dark mb-1"><?php echo e($student->name ?? 'Nama Tidak Tersedia'); ?></h3>
                    <div class="d-flex align-items-center mb-2">
                        <span class="text-muted me-2" style="font-size: 0.9rem;">ID Siswa</span>
                        <span class="badge bg-label-primary rounded-pill px-3"><?php echo e($student->id ?? '-'); ?></span>
                    </div>
                    <p class="text-muted mb-0" style="font-size: 0.9rem;">Informasi profil anak yang terdaftar dalam sistem.
                    </p>
                </div>
            </div>
        </div>

        
        <div class="row g-4 mb-4">

            
            <div class="col-md-6">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; border: 1px solid #e0ebfc; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="p-2 rounded me-3" style="background-color: #e0ebfc; color: #5b9cf6;">
                                <i class="bx bx-user fs-4"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">Informasi Pribadi</h6>
                        </div>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted ps-0" width="40%">Nama Lengkap</td>
                                <td class="text-dark fw-medium"><?php echo e($student->name ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Tanggal Lahir</td>
                                <td class="text-dark fw-medium">
                                    <?php echo e(isset($student->birth_date) ? \Carbon\Carbon::parse($student->birth_date)->locale('id')->translatedFormat('d F Y') : '-'); ?>

                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0">Jenis Kelamin</td>
                                <td class="text-dark fw-medium">
                                    <?php echo e(ucwords(str_replace('-', ' ', $student->gender ?? '-'))); ?>

                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="col-md-6">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; border: 1px solid #e0ebfc; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="p-2 rounded me-3" style="background-color: #e8f5e9; color: #4ade80;">
                                <i class="bx bx-building-house fs-4"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">Informasi Sekolah dan Alamat Tempat Tinggal</h6>
                        </div>
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="text-muted ps-0" width="40%">Nama Sekolah</td>
                                <td class="text-dark fw-medium"><?php echo e($student->school_name ?? '-'); ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted ps-0 align-top">Alamat</td>
                                <td class="text-dark fw-medium text-wrap"><?php echo e($student->address ?? '-'); ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            
            <div class="col-md-6">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; border: 1px solid #e0ebfc; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="p-2 rounded me-3" style="background-color: #f3e8ff; color: #a855f7;">
                                <i class="bx bx-donate-heart fs-4"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">Kebutuhan Khusus</h6>
                        </div>
                        <p class="text-dark fw-medium mb-0">
                            <?php echo e(ucwords(str_replace('_', ' ', $student->special_needs ?? '-'))); ?>

                        </p>
                    </div>
                </div>
            </div>

            
            <div class="col-md-6">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; border: 1px solid #e0ebfc; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="p-2 rounded me-3" style="background-color: #fff3e0; color: #f97316;">
                                <i class="bx bx-clipboard fs-4"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">Catatan Diagnosis</h6>
                        </div>
                        <p class="text-dark fw-medium mb-0"><?php echo e($student->diagnosis_notes ?? '-'); ?></p>
                    </div>
                </div>
            </div>

            
            <div class="col-12">
                <div class="card bg-white"
                    style="border-radius: 20px; border: 1px solid #e0ebfc; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="p-2 rounded me-3" style="background-color: #e0f2fe; color: #0ea5e9;">
                                <i class="bx bx-group fs-4"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark">Informasi Orang Tua</h6>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td class="text-muted ps-0" width="40%">Nama Ayah</td>
                                        <td class="text-dark fw-medium"><?php echo e($student->father_name ?? '-'); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0">Nama Ibu</td>
                                        <td class="text-dark fw-medium"><?php echo e($student->mother_name ?? '-'); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6 mt-3 mt-md-0">
                                <table class="table table-borderless table-sm mb-0">
                                    <tr>
                                        <td class="text-muted ps-0" width="40%">No. Telepon</td>
                                        <td class="text-dark fw-medium">
                                            <?php echo e($student->parent_phone ?? $student->parent->phone ?? '-'); ?>

                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0">Email</td>
                                        <td class="text-dark fw-medium"><?php echo e($student->parent->email ?? '-'); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/detail-anak.blade.php ENDPATH**/ ?>