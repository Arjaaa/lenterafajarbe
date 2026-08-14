

<?php $__env->startSection('content'); ?>
    <div class="container-xxl flex-grow-1 container-p-y">

        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0 text-dark">Dashboard</h4>

            
            <div class="bg-white px-4 py-2 shadow-sm d-flex align-items-center" style="border-radius: 50px;">
                <i class="bx bx-calendar text-success me-2 fs-5"></i>
                <span class="fw-semibold text-dark">
                    <?php echo e(\Carbon\Carbon::now()->locale('id')->translatedFormat('l, d - m - Y')); ?>

                </span>
            </div>
        </div>

        <div class="row">
            
            <div class="col-12 mb-4">
                <div class="card p-4" style="min-height: 120px;">
                    <div class="d-flex align-items-center h-100">
                        
                        <div class="rounded-circle d-flex justify-content-center align-items-center flex-shrink-0"
                            style="width: 55px; height: 55px; background-color: #d1f4e1;">
                            <i class="bx bx-info-circle text-success fs-3"></i>
                        </div>
                        <div class="ms-4">
                            <h4 class="mb-1 fw-bold text-dark">Selamat Datang, <?php echo e(auth()->user()->name ?? 'Koordinator'); ?>

                            </h4>
                            <p class="mb-0 text-muted">Kamu sedang berada di <span class="fw-bold">Pusat Kendali</span>.
                                Semua statistik dan manajemen sistem Lentera Fajar ada di bawah pantauanmu hari ini.</p>
                        </div>
                    </div>
                </div>
            </div>

            

            
            <div class="col-md-3 col-6 mb-4">
                <div class="card h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                        <div class="avatar avatar-md mb-3">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-face fs-4"></i></span>
                        </div>
                        <h2 class="mb-1 fw-bold text-dark"><?php echo e($totalAnak ?? 8); ?></h2>
                        <span class="text-muted small">Total siswa</span>
                    </div>
                </div>
            </div>

            
            <div class="col-md-3 col-6 mb-4">
                <div class="card h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                        <div class="avatar avatar-md mb-3">
                            <span class="avatar-initial rounded bg-label-danger"><i
                                    class="bx bx-chalkboard fs-4"></i></span>
                        </div>
                        <h2 class="mb-1 fw-bold text-dark"><?php echo e($totalWaliKelas ?? 14); ?></h2>
                        <span class="text-muted small">Total Wali Kelas</span>
                    </div>
                </div>
            </div>

            
            <div class="col-md-3 col-6 mb-4">
                <div class="card h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                        <div class="avatar avatar-md mb-3">
                            <span class="avatar-initial rounded bg-label-success"><i
                                    class="bx bx-user-voice fs-4"></i></span>
                        </div>
                        <h2 class="mb-1 fw-bold text-dark"><?php echo e($totalTerapis1on1 ?? 67); ?></h2>
                        <span class="text-muted small">Total Terapis 1 on 1</span>
                    </div>
                </div>
            </div>

            
            <div class="col-md-3 col-6 mb-4">
                <div class="card h-100">
                    <div class="card-body p-4 d-flex flex-column justify-content-center align-items-center text-center">
                        <div class="avatar avatar-md mb-3">
                            
                            <span class="avatar-initial rounded" style="background-color: #f3e8ff; color: #8b5cf6;"><i
                                    class="bx bx-group fs-4"></i></span>
                        </div>
                        <h2 class="mb-1 fw-bold text-dark"><?php echo e($totalShadowTeacher ?? 67); ?></h2>
                        <span class="text-muted small">Total Shadow Teacher</span>
                    </div>
                </div>
            </div>

            
            <div class="col-12 mb-4">
                <div class="card p-4">
                    <h6 class="text-dark mb-4 text-center fw-bold text-uppercase">SEBARAN PENEMPATAN SAAT INI</h6>
                    <div class="row text-center align-items-center" style="min-height: 100px;">

                        
                        <div class="col-md-4 col-12 border-end mb-3 mb-md-0">
                            <h1 class="fw-bold text-dark mb-2"><?php echo e($totalKelasUmum ?? 5); ?></h1>
                            <h6 class="text-muted mb-0 fw-normal"><i class="bx bx-grid-alt me-1"></i> Kelas Terapis</h6>
                        </div>

                        
                        <div class="col-md-4 col-12 border-end mb-3 mb-md-0">
                            <h1 class="fw-bold text-dark mb-2"><?php echo e($total1on1 ?? 7); ?></h1>
                            <h6 class="text-muted mb-0 fw-normal"><i class="bx bx-user-voice me-1"></i> Sesi 1 on 1</h6>
                        </div>

                        
                        <div class="col-md-4 col-12">
                            <h1 class="fw-bold text-dark mb-2"><?php echo e($totalGroupShadow ?? 2); ?></h1>
                            <h6 class="text-muted mb-0 fw-normal"><i class="bx bx-group me-1"></i> Group Shadow Teacher</h6>
                        </div>

                    </div>
                </div>
            </div>

        </div>

    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>