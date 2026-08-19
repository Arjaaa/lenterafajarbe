

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
            <a href="<?php echo e(route('koor.dataShadowGroup', ['page' => $backPage ?? 1])); ?>"
                class="btn btn-sm rounded-circle p-2 me-3 shadow-none d-flex justify-content-center align-items-center"
                style="border: none; background-color: #5b9cf6; width: 35px; height: 35px;">
                <i class="bx bx-arrow-back fs-5 text-white"></i>
            </a>
            <h4 class="fw-bold mb-0" style="color: #5b9cf6;"><?php echo e($group->name ?? 'Detail Group Shadow'); ?></h4>
        </div>

        
        
        
        <div class="row align-items-end mb-2">



            
            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <span class="text-muted d-block mb-2 fw-semibold" style="font-size: 0.85rem;">Koordinator / PJ</span>
                <div class="d-flex align-items-center bg-white px-2 py-2"
                    style="border-radius: 50px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                    <div class="avatar avatar-sm me-3" style="width: 45px; height: 45px;">
                        <span class="avatar-initial rounded-circle fw-bold"
                            style="background-color: #e0ebfc; color: #5b9cf6;">
                            <i class="bx bx-user fs-4"></i>
                        </span>
                    </div>
                    <span class="fw-bold text-dark fs-6 text-truncate"
                        style="max-width: 150px;"><?php echo e($group->pic->name ?? '-'); ?></span>
                </div>
            </div>

            
            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <span class="text-muted d-block mb-2 fw-semibold" style="font-size: 0.85rem;">Guru Partner</span>
                <div class="d-flex align-items-center bg-white px-2 py-2"
                    style="border-radius: 50px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                    <div class="avatar avatar-sm me-3" style="width: 45px; height: 45px;">
                        <span class="avatar-initial rounded-circle fw-bold"
                            style="background-color: #e0f2fe; color: #0ea5e9;">
                            <i class="bx bx-user-voice fs-4"></i>
                        </span>
                    </div>
                    <span class="fw-bold text-dark fs-6 text-truncate"
                        style="max-width: 150px;"><?php echo e($group->partner->name ?? '-'); ?></span>
                </div>
            </div>

            
            
            <div class="col-lg-6 col-md-12 col-12 mb-3">
                <span class="text-muted d-block mb-2 fw-semibold" style="font-size: 0.85rem;">Sekolah</span>
                
                
                <div class="d-inline-flex align-items-center bg-white px-2 py-2 w-100"
                    style="border-radius: 50px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); max-width: 100%;">
                    
                    
                    <div class="avatar avatar-sm me-3 flex-shrink-0" style="width: 45px; height: 45px;">
                        <span class="avatar-initial rounded-circle fw-bold d-flex align-items-center justify-content-center w-100 h-100"
                            style="background-color: #fff3e0; color: #f97316;">
                            <i class="bx bx-building-house fs-4"></i>
                        </span>
                    </div>
                    
                    
                    <span class="fw-bold text-dark fs-6 text-truncate pe-3 flex-grow-1" style="min-width: 0;">
                        <?php echo e($group->school_name ?? '-'); ?>

                    </span>
                    
                </div>
            </div>
        </div>

        
        <div class="text-end mb-4">
            
        </div>

        
        
        
        <div class="card bg-white"
            style="border-radius: 20px; border: 1px solid #5b9cf6; box-shadow: none; overflow: hidden;">

            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">Daftar Siswa</h5>
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
                        <?php if(isset($group->student) && !empty($group->student)): ?>
                            <tr>
                                <td class="text-center align-middle text-dark fw-medium">1</td>

                                
                                <td class="align-middle">
                                    <div class="avatar avatar-md" style="width: 45px; height: 45px;">
                                        <?php if(!empty($group->student->photo)): ?>
                                            <img src="<?php echo e(str_starts_with($group->student->photo, 'http') ? $group->student->photo : asset('storage/' . $group->student->photo)); ?>"
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

                                
                                <td class="align-middle text-dark fw-medium"><?php echo e($group->student->name ?? '-'); ?></td>
                                <td class="align-middle text-dark">
                                    <?php echo e(ucwords(str_replace('-', ' ', $group->student->gender ?? '-'))); ?></td>
                                <td class="align-middle text-dark">
                                    <?php echo e(ucwords(str_replace('_', ' ', $group->student->special_needs ?? 'Reguler'))); ?></td>
                                <td class="align-middle text-dark">
                                    <?php echo e($group->student->parent->name ?? $group->student->mother_name ?? '-'); ?></td>

                                
                                <td class="text-center align-middle">
                                    <a href="<?php echo e(route('koor.detailAnak', $group->student->id ?? 0)); ?>"
                                        class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                        style="font-size: 0.75rem; background-color: #4ade80; border: none;" title="View">
                                        <i class="bx bx-info-circle me-1"></i> View
                                    </a>

                                    
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bx bx-folder-open fs-1 d-block mb-2"></i>
                                    Belum ada siswa yang didaftarkan di group shadow ini.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/detail-shadow.blade.php ENDPATH**/ ?>