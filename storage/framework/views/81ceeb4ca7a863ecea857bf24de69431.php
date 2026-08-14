

<?php $__env->startSection('content'); ?>
    <style>
        .custom-table-striped tbody tr:nth-of-type(even) {
            background-color: #f4f8ff !important;
        }

        .custom-table-striped tbody tr td {
            border-bottom: none !important;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        
        
        
        <div class="d-flex align-items-center mb-4">
            <a href="<?php echo e(url()->previous()); ?>"
                class="btn btn-sm rounded-circle p-2 me-3 shadow-none d-flex justify-content-center align-items-center"
                style="border: none; background-color: #5b9cf6; width: 35px; height: 35px;">
                <i class="bx bx-arrow-back fs-5 text-white"></i>
            </a>
            <h4 class="fw-bold mb-0 text-dark">Detail Laporan Siswa</h4>
        </div>

        
        
        
        <div class="card bg-white mb-4" style="border-radius: 20px; border: 1px solid #e0ebfc; box-shadow: none;">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="avatar avatar-xl me-4" style="width: 70px; height: 70px;">
                    <?php if(!empty($siswa->photo)): ?>
                        <img src="<?php echo e(str_starts_with($siswa->photo, 'http') ? $siswa->photo : asset('storage/' . $siswa->photo)); ?>"
                            alt="Avatar" class="rounded-circle" style="object-fit: cover; width: 100%; height: 100%;" />
                    <?php else: ?>
                        <span class="avatar-initial rounded-circle fs-2" style="background-color: #cce5ff; color: #5b9cf6;">
                            <i class="bx bx-user"></i>
                        </span>
                    <?php endif; ?>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-1"><?php echo e($siswa->name ?? 'Nama Siswa'); ?></h5>
                    <div class="d-flex align-items-center mt-2">
                        <span class="badge bg-label-primary rounded-pill px-3 me-2">
                            <?php echo e(ucwords(str_replace('_', ' ', $siswa->special_needs ?? 'Reguler'))); ?>

                        </span>
                        <span class="text-muted" style="font-size: 0.85rem;">
                            <i class="bx bx-male-female me-1"></i>
                            <?php echo e(ucwords(str_replace('-', ' ', $siswa->gender ?? '-'))); ?>

                        </span>
                    </div>
                </div>
            </div>
        </div>

        
        
        
        <div class="card bg-white"
            style="border-radius: 20px; border: 1px solid #e0ebfc; box-shadow: none; overflow: hidden;">
            <div
                class="card-header bg-white border-bottom p-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <h5 class="fw-bold mb-3 mb-md-0 text-dark">Riwayat Laporan</h5>

                <div class="d-flex flex-column flex-md-row gap-2 w-100 justify-content-md-end" style="max-width: 600px;">
                    
                    

                    
                    
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table custom-table-striped table-borderless">
                    <thead style="border-bottom: 2px solid #f0f4f9;">
                        <tr>
                            <th class="text-center py-3 text-muted fw-semibold">No</th>
                            <th class="py-3 text-muted fw-semibold">Tanggal</th>
                            <th class="py-3 text-muted fw-semibold">Terapis / Guru</th>
                            <th class="py-3 text-muted fw-semibold">Status Kehadiran</th>
                            <th class="text-center py-3 text-muted fw-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php $__empty_1 = true; $__currentLoopData = $siswa->reports ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="text-center align-middle text-dark fw-medium"><?php echo e($index + 1); ?></td>
                                <td class="align-middle text-dark fw-bold">
                                    <?php echo e(\Carbon\Carbon::parse($report->date ?? now())->locale('id')->translatedFormat('d F Y')); ?>

                                </td>
                                <td class="align-middle text-dark"><?php echo e($report->teacher->name ?? '-'); ?></td>
                                <td class="align-middle">
                                    <?php if(($report->attendance_status ?? '') == 'hadir'): ?>
                                        <span class="badge rounded-pill bg-label-success px-3">Hadir</span>
                                    <?php elseif(($report->attendance_status ?? '') == 'sakit'): ?>
                                        <span class="badge rounded-pill bg-label-warning px-3">Sakit</span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill bg-label-danger px-3">Alpha/Izin</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none"
                                        style="font-size: 0.75rem; background-color: #5b9cf6; border: none;"
                                        title="Lihat Detail">
                                        <i class="bx bx-info-circle me-1"></i> Baca Laporan
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bx bx-folder-open fs-1 d-block mb-2"></i> Belum ada riwayat laporan untuk siswa
                                    ini.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                </table>
                
                <?php if(isset($pagination) && $pagination['last_page'] > 1): ?>
                    <div class="card-footer bg-white border-top text-center py-4">
                        <div class="d-inline-flex align-items-center bg-white px-3 py-2 border shadow-sm"
                            style="border-radius: 50px;">
                            
                            <a href="<?php echo e(($pagination['current_page'] ?? 1) > 1 ? request()->fullUrlWithQuery(['page' => ($pagination['current_page'] ?? 1) - 1]) : 'javascript:void(0)'); ?>"
                                class="text-dark text-decoration-none me-3 <?php echo e(($pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 pe-none' : ''); ?>">
                                <i class="bx bx-chevron-left fs-5"></i>
                            </a>

                            
                            <span class="fw-semibold text-dark mx-2" style="font-size: 0.9rem;">
                                <?php echo e($pagination['current_page'] ?? 1); ?> / <?php echo e($pagination['last_page'] ?? 1); ?>

                            </span>

                            
                            <a href="<?php echo e(($pagination['current_page'] ?? 1) < ($pagination['last_page'] ?? 1) ? request()->fullUrlWithQuery(['page' => ($pagination['current_page'] ?? 1) + 1]) : 'javascript:void(0)'); ?>"
                                class="text-dark text-decoration-none ms-3 <?php echo e(($pagination['current_page'] ?? 1) >= ($pagination['last_page'] ?? 1) ? 'opacity-50 pe-none' : ''); ?>">
                                <i class="bx bx-chevron-right fs-5"></i>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>



    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/raport-siswa/detail.blade.php ENDPATH**/ ?>