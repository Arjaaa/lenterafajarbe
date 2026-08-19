

<?php $__env->startSection('content'); ?>
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Manajemen Guru /</span> Rapor Kinerja Bulanan
    </h4>

    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 15px;">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 15px;">
            <?php echo e($errors->first('error') ?? $errors->first()); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if($rapor): ?>
        <?php
            // Ambil year dan month langsung dari $rapor
            $namaBulan = \Carbon\Carbon::createFromDate($rapor->year, $rapor->month, 1)
                ->locale('id')->translatedFormat('F');
        ?>

        <div class="row">
            
           
            <div class="col-12 mb-4">
                <div class="card bg-white" style="border-radius: 20px; border: 1px solid #e0ebfc; box-shadow: none;">
                    <div class="card-body d-flex align-items-center p-4">
                        
                        <div class="avatar avatar-xl me-4 d-flex align-items-center justify-content-center rounded-circle flex-shrink-0" 
                             style="background-color: #eff6ff; width: 65px; height: 65px;">
                            <span class="text-primary fw-bold fs-2">
                                <?php echo e(strtoupper(substr($rapor->teacher->name ?? 'T', 0, 1))); ?>

                            </span>
                        </div>
                        
                        
                        <div>
                            <h4 class="text-dark mb-1 fw-bold">Terapis: <?php echo e(ucwords($rapor->teacher->name ?? 'Tanpa Nama')); ?></h4>
                            <p class="mb-0 text-muted" style="font-size: 0.95rem;">
                                Periode Laporan: <span class="fw-semibold text-dark"><?php echo e($namaBulan); ?> <?php echo e($rapor->year); ?></span>
                                <span class="mx-2">|</span>
                                Role: <span class="fw-semibold text-dark"><?php echo e(ucwords(str_replace('_', ' ', $rapor->teacher->role ?? '-'))); ?></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="col-md-3 col-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <span class="d-block mb-1 text-muted">Hari Mengajar</span>
                        <h2 class="mb-0 text-primary"><?php echo e($rapor->total_teaching_days ?? 0); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <span class="d-block mb-1 text-muted">Skor Kelengkapan</span>
                        <h2 class="mb-0 text-success"><?php echo e(round($rapor->completeness_score ?? 0)); ?>%</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <span class="d-block mb-1 text-muted">Total Laporan</span>
                        <h2 class="mb-0 text-info"><?php echo e($rapor->total_reports_created ?? 0); ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <span class="d-block mb-1 text-muted">Indikator Performa</span>
                        <?php if(($rapor->performance_indicator ?? '') == 'sangat_kurang'): ?>
                            <h4 class="mb-0 text-danger mt-2">Sangat Kurang</h4>
                        <?php else: ?>
                            <h4 class="mb-0 text-warning mt-2">
                                <?php echo e(ucwords(str_replace('_', ' ', $rapor->performance_indicator ?? '-'))); ?>

                            </h4>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            
            <div class="col-md-3 col-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <span class="d-block mb-1 text-muted">Ketepatan Waktu</span>
                        <h2 class="mb-0 text-primary"><?php echo e(round($rapor->timeliness_score ?? 0)); ?>%</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <span class="d-block mb-1 text-muted">Konsistensi Mingguan</span>
                        <h2 class="mb-0 text-primary"><?php echo e(round($rapor->weekly_consistency ?? 0)); ?>%</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <span class="d-block mb-1 text-muted">Dokumentasi</span>
                        <h2 class="mb-0 text-primary"><?php echo e(round($rapor->documentation_pct ?? 0)); ?>%</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <span class="d-block mb-1 text-muted">Siswa Progres Positif</span>
                        <h2 class="mb-0 text-primary"><?php echo e(round($rapor->student_positive_progress_pct ?? 0)); ?>%</h2>
                    </div>
                </div>
            </div>

            
            
            
            <div class="row">
                
                <div class="col-md-6 mb-4">
                    <div class="card h-100 bg-white"
                        style="border-radius: 20px; border: 1px solid #e0ebfc; box-shadow: none; overflow: hidden;">
                        <div class="card-header bg-white border-bottom p-4 d-flex align-items-center">
                            <div class="avatar avatar-sm rounded-circle me-3 d-flex align-items-center justify-content-center"
                                style="background-color: #f1f5f9; color: #475569; width: 40px; height: 40px;">
                                <i class="bx bx-bot fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">Ringkasan Performa AI</h5>
                                <small class="text-muted">Analisis otomatis dari sistem</small>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            
                            <div class="p-3 mb-4 rounded-4" style="background-color: #fff5f5; border: 1px solid #fed7d7;">
                                <div class="d-flex">
                                    <i class="bx bx-error-circle fs-4 text-danger me-2 flex-shrink-0 mt-1"></i>
                                    <p class="mb-0 text-danger fw-semibold" style="font-size: 0.95rem;">
                                        <?php echo e($rapor->ai_performance_summary ?? '-'); ?>

                                    </p>
                                </div>
                            </div>

                            <?php if(!empty($rapor->ai_improvement_areas)): ?>
                                <h6 class="fw-bold text-dark mb-3"><i></i> Area Perbaikan yang Disarankan:</h6>
                                <div class="d-flex flex-column gap-2">
                                    <?php $__currentLoopData = $rapor->ai_improvement_areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="d-flex align-items-center px-3 py-2 rounded-pill"
                                            style="background-color: #f8fafc; border: 1px solid #f1f5f9;">
                                            <i class="bx bx-check-shield text-warning me-2 fs-5"></i>
                                            <span class="text-dark fw-medium" style="font-size: 0.9rem;"><?php echo e($area); ?></span>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                
                <div class="col-md-6 mb-4">
                    <div class="card h-100 bg-white"
                        style="border-radius: 20px; border: 1px solid #e0ebfc; box-shadow: none; overflow: hidden;">
                        <div class="card-header bg-white border-bottom p-4 d-flex align-items-center">
                            <div class="avatar avatar-sm rounded-circle me-3 d-flex align-items-center justify-content-center"
                                style="background-color: #fef3c7; color: #d97706; width: 40px; height: 40px;">
                                <i class="bx bx-message-square-edit fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">Rekomendasi Koordinator</h5>
                                <small class="text-muted">Catatan & arahan untuk guru</small>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <?php if(!empty($rapor->coordinator_recommendation)): ?>
                                
                                <div class="p-3 mb-3 rounded-4" style="background-color: #f8fafc; border: 1px solid #e2e8f0;">
                                    <p class="mb-0 text-dark" style="font-size: 0.95rem; line-height: 1.5;">
                                        <?php echo e($rapor->coordinator_recommendation); ?>

                                    </p>
                                </div>

                                
                                <button type="button"
                                    class="btn rounded-pill px-4 fw-semibold text-primary shadow-none d-inline-flex align-items-center"
                                    style="background-color: #eff6ff; border: 1px solid #bfdbfe; font-size: 0.85rem;"
                                    data-bs-toggle="collapse" data-bs-target="#editRecommendationForm">
                                    <i class="bx bx-edit-alt me-1 fs-5"></i> Edit Rekomendasi
                                </button>

                                
                                <div class="collapse mt-3" id="editRecommendationForm">
                                    <form action="<?php echo e(route('koor.raporGuru.feedback', $rapor->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <div class="mb-3">
                                            <label class="form-label text-dark fw-semibold small">Ubah Catatan / Rekomendasi</label>
                                            <textarea name="coordinator_recommendation"
                                                class="form-control rounded-4 shadow-none p-3" rows="3"
                                                style="border-color: #cbd5e1;" placeholder="Tulis rekomendasi untuk guru ini..."
                                                required><?php echo e($rapor->coordinator_recommendation); ?></textarea>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label text-dark fw-semibold small">Update Indikator Performa
                                                (Opsional)</label>
                                            <select name="performance_indicator" class="form-select rounded-pill shadow-none px-3"
                                                style="border-color: #cbd5e1;">
                                                <option value="">-- Tidak diubah --</option>
                                                <option value="sangat_baik" <?php echo e(($rapor->performance_indicator ?? '') == 'sangat_baik' ? 'selected' : ''); ?>>Sangat Baik</option>
                                                <option value="baik" <?php echo e(($rapor->performance_indicator ?? '') == 'baik' ? 'selected' : ''); ?>>Baik</option>
                                                <option value="cukup" <?php echo e(($rapor->performance_indicator ?? '') == 'cukup' ? 'selected' : ''); ?>>Cukup</option>
                                                <option value="kurang" <?php echo e(($rapor->performance_indicator ?? '') == 'kurang' ? 'selected' : ''); ?>>Kurang</option>
                                                <option value="sangat_kurang" <?php echo e(($rapor->performance_indicator ?? '') == 'sangat_kurang' ? 'selected' : ''); ?>>Sangat Kurang</option>
                                            </select>
                                        </div>

                                        <button type="submit" class="btn rounded-pill px-4 fw-semibold text-white shadow-none"
                                            style="background-color: #5b9cf6; border: none;">
                                            Update Rekomendasi
                                        </button>
                                    </form>
                                </div>
                            <?php else: ?>
                                
                                <div class="alert p-3 mb-3 text-muted rounded-4"
                                    style="background-color: #f8fafc; border: 1px dashed #cbd5e1; font-size: 0.9rem;">
                                    <i class="bx bx-info-circle me-1"></i> Belum ada rekomendasi yang diberikan untuk guru ini.
                                </div>

                                <form action="<?php echo e(route('koor.raporGuru.feedback', $rapor->id)); ?>" method="POST">
                                    <?php echo csrf_field(); ?>
                                    <div class="mb-3">
                                        <label class="form-label text-dark fw-semibold small">Tulis Rekomendasi</label>
                                        <textarea name="coordinator_recommendation" class="form-control rounded-4 shadow-none p-3"
                                            rows="3" style="border-color: #cbd5e1;"
                                            placeholder="Tulis rekomendasi atau arahan untuk guru ini..." required></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label text-dark fw-semibold small">Indikator Performa (Opsional)</label>
                                        <select name="performance_indicator" class="form-select rounded-pill shadow-none px-3"
                                            style="border-color: #cbd5e1;">
                                            <option value="">-- Pilih Indikator --</option>
                                            <option value="sangat_baik">Sangat Baik</option>
                                            <option value="baik">Baik</option>
                                            <option value="cukup">Cukup</option>
                                            <option value="kurang">Kurang</option>
                                            <option value="sangat_kurang">Sangat Kurang</option>
                                        </select>
                                    </div>

                                    <button type="submit" class="btn rounded-pill px-4 fw-semibold text-white shadow-none"
                                        style="background-color: #5b9cf6; border: none;">
                                        Kirim Rekomendasi
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">
            Data rapor bulanan untuk guru ini belum tersedia atau gagal ditarik dari server.
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/rapor-guru-detail.blade.php ENDPATH**/ ?>