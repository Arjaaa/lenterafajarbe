

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

        /* CSS Donut Chart Murni */
        .donut-chart {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .donut-inner {
            width: 52px;
            height: 52px;
            background: white;
            border-radius: 50%;
            position: absolute;
        }

        .donut-text {
            position: relative;
            z-index: 1;
            font-size: 0.9rem;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        
        <div class="mb-4">
            <h4 class="fw-bold mb-0 text-dark">Raport Guru</h4>
        </div>

        
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 15px;">
                <strong>Yay! 🎉</strong> <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        
        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 15px;">
                <?php echo e($errors->first('error') ?? $errors->first()); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        
        <div class="row g-3 mb-4 text-center">

            
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: none;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <?php
                            $fbTotal = $stats['feedback_total'] ?? 0;
                            $fbGiven = $stats['feedback_given'] ?? 0;
                            $fbPercent = $fbTotal > 0 ? round(($fbGiven / $fbTotal) * 100) : 0;
                        ?>
                        <div class="mb-2 donut-chart"
                            style="background: conic-gradient(#5b9cf6 <?php echo e($fbPercent); ?>%, #e2e8f0 0);">
                            <div class="donut-inner"></div>
                            <span class="fw-bold text-dark donut-text"><?php echo e($fbGiven); ?>/<?php echo e($fbTotal); ?></span>
                        </div>
                        <span class="text-muted" style="font-size: 0.85rem;">Raport Telah diberi Feedback</span>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: none;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <?php
                            $lmReports = $stats['last_month_reports'] ?? 0;
                            $lmFeedback = $stats['last_month_feedback'] ?? 0;
                            $lmPercent = $lmReports > 0 ? round(($lmFeedback / $lmReports) * 100) : 0;
                        ?>
                        <div class="mb-2 donut-chart"
                            style="background: conic-gradient(#5b9cf6 <?php echo e($lmPercent); ?>%, #e2e8f0 0);">
                            <div class="donut-inner"></div>
                            <span class="fw-bold text-dark donut-text"><?php echo e($lmFeedback); ?>/<?php echo e($lmReports); ?></span>
                        </div>
                        <span class="text-muted" style="font-size: 0.85rem;">Rapor Diterima Bulan Lalu</span>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: none;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="mb-2 rounded d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; background-color: #e0ebfc;">
                            <i class="bx bx-clipboard fs-3" style="color: #5b9cf6;"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-1"><?php echo e($stats['total_reports'] ?? 0); ?></h2>
                        <span class="text-muted" style="font-size: 0.85rem;">Total Seluruh Rapor Guru</span>
                    </div>
                </div>
            </div>

            
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: none;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="mb-2 rounded d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; background-color: #f3e8ff;">
                            <i class="bx bx-user-pin fs-3" style="color: #a855f7;"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-1"><?php echo e($stats['total_guru'] ?? 0); ?></h2>
                        <span class="text-muted" style="font-size: 0.85rem;">Total Seluruh Guru</span>
                    </div>
                </div>
            </div>

        </div>

        
        <div class="card bg-white"
            style="border: 1px solid #a3c7fb; border-radius: 20px; box-shadow: none; overflow: hidden;">

            
            <div class="card-header d-flex flex-column flex-md-row align-items-center bg-white border-bottom p-4">
                <div class="d-flex align-items-stretch w-100" style="max-width: 550px; gap: 12px;">
                    <div class="input-group"
                        style="border-radius: 50px; overflow: hidden; border: 1px solid #a3c7fb; background: white; flex-grow: 1;">
                        <span class="input-group-text bg-transparent border-0 pe-1 ps-3">
                            <i class="bx bx-search" style="color: #a3c7fb;"></i>
                        </span>
                        <input type="text" class="form-control border-0 shadow-none px-2" placeholder="Cari..."
                            style="background: transparent;">
                        <button class="btn fw-semibold text-white px-4 m-0 shadow-none"
                            style="background-color: #5b9cf6; border: none; border-radius: 0;">
                            Cari
                        </button>
                    </div>

                    
                </div>
            </div>

            <div class="table-responsive text-nowrap" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table custom-table-striped table-borderless" style="min-width: 900px;">
                    <thead style="border-bottom: 1px solid #e0ebfc;">
                        <tr>
                            <th class="py-3 text-muted fw-semibold text-center" style="width: 5%;">No</th>
                            
                            <th class="py-3 text-muted fw-semibold">Nama</th>
                            <th class="py-3 text-muted fw-semibold">Email / Kontak</th>
                            <th class="py-3 px-4 text-muted fw-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $teachers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="align-middle text-dark text-center"><?php echo e($index + 1); ?></td>
                                

                                <td class="align-middle text-dark fw-medium"><?php echo e($teacher->teacher->name ?? '-'); ?></td>
                                <td class="align-middle text-dark"><?php echo e($teacher->teacher->phone ?? '-'); ?></td>

                                
                                <td class="align-middle px-4"> 
                                    <div class="d-flex justify-content-start align-items-center gap-2">

                                        
                                        <a href="<?php echo e(route('koor.raporGuruDetail', $teacher->id ?? 0)); ?>"
                                            class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none d-flex justify-content-center align-items-center"
                                            style="font-size: 0.75rem; background-color: #4ade80; border: none; min-width: 85px;">
                                            <i class="bx bx-info-circle me-1" style="font-size: 0.9rem;"></i> View
                                        </a>

                                        
                                        <?php if(!($teacher->has_feedback ?? false)): ?>
                                            <button type="button"
                                                class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none d-flex justify-content-center align-items-center"
                                                style="font-size: 0.75rem; background-color: #2ea4ff; border: none;"
                                                data-bs-toggle="modal" data-bs-target="#feedbackModal<?php echo e($teacher->id); ?>">
                                                <i class="bx bx-message-square-error me-1" style="font-size: 0.9rem;"></i> Beri
                                                Feedback
                                            </button>
                                        <?php endif; ?>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            
                            <?php for($i = 0; $i < 5; $i++): ?>
                                <tr>
                                    <td class="align-middle text-center py-3"><?php echo e($i + 1); ?></td>
                                    <td class="align-middle text-center">
                                        <div class="avatar mx-auto" style="width: 40px; height: 40px;">
                                            <span class="avatar-initial rounded-circle" style="background-color: #cbd5e1;"></span>
                                        </div>
                                    </td>
                                    <td colspan="2" class="py-4"></td>
                                    <td class="text-center align-middle">
                                        <span class="text-muted" style="font-size: 0.8rem;">-</span>
                                    </td>
                                </tr>
                            <?php endfor; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
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
        </div>

        
        
        
        
        
        <?php $__currentLoopData = $teachers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $teacher): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!($teacher->has_feedback ?? false)): ?>
                <div class="modal fade" id="feedbackModal<?php echo e($teacher->id); ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content" style="border-radius: 16px;">
                            <form action="<?php echo e(route('koor.raporGuru.feedback', $teacher->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <div class="modal-header">
                                    <h5 class="modal-title">Feedback untuk <?php echo e($teacher->teacher->name ?? '-'); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-start">
                                    <textarea name="coordinator_recommendation" class="form-control mb-3" rows="4"
                                        placeholder="Tulis rekomendasi untuk guru ini..."
                                        required><?php echo e($teacher->coordinator_recommendation); ?></textarea>

                                    <label class="form-label small text-muted">Indikator Performa (opsional)</label>
                                    <select name="performance_indicator" class="form-select">
                                        <option value="">-- Tidak diubah --</option>
                                        <option value="sangat_baik" <?php echo e(($teacher->performance_indicator ?? '') == 'sangat_baik' ? 'selected' : ''); ?>>Sangat Baik</option>
                                        <option value="baik" <?php echo e(($teacher->performance_indicator ?? '') == 'baik' ? 'selected' : ''); ?>>Baik</option>
                                        <option value="cukup" <?php echo e(($teacher->performance_indicator ?? '') == 'cukup' ? 'selected' : ''); ?>>Cukup</option>
                                        <option value="kurang" <?php echo e(($teacher->performance_indicator ?? '') == 'kurang' ? 'selected' : ''); ?>>Kurang</option>
                                        <option value="sangat_kurang" <?php echo e(($teacher->performance_indicator ?? '') == 'sangat_kurang' ? 'selected' : ''); ?>>Sangat Kurang</option>
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn text-white" style="background-color: #2ea4ff;">Kirim</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/rapor-guru-index.blade.php ENDPATH**/ ?>