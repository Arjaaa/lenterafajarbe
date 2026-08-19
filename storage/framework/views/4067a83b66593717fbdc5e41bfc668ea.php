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


    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0 text-dark">Data Laporan Harian</h4>


    </div>


    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 15px;">
            <strong></strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 15px;">
            <strong> Ada yang salah:</strong>
            <ul class="mb-0 mt-1">
                <?php $__currentLoopData = $errors->all();
                $__env->addLoop($__currentLoopData);
                foreach ($__currentLoopData as $error):
                    $__env->incrementLoopIndices();
                    $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach;
                $__env->popLoop();
                $loop = $__env->getLastLoop(); ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>


    <div class="row g-3 mb-4 text-center">
        <div class="row g-4 mb-4 text-center">

            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: none;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="mb-2 donut-chart"
                            style="background: conic-gradient(#5b9cf6 <?php echo e($cardStats['persen_hari_ini']); ?>%, #e2e8f0 0);">
                            <div class="donut-inner"></div>
                            <span
                                class="fw-bold text-dark donut-text"><?php echo e($cardStats['hari_ini']); ?>/<?php echo e($cardStats['total_siswa']); ?></span>
                        </div>
                        <span class="text-muted" style="font-size: 0.85rem;">Laporan Diterima Hari Ini</span>
                    </div>
                </div>
            </div>


            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: none;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="mb-2 donut-chart"
                            style="background: conic-gradient(#5b9cf6 <?php echo e($cardStats['persen_bulan_lalu']); ?>%, #e2e8f0 0);">
                            <div class="donut-inner"></div>
                            <span
                                class="fw-bold text-dark donut-text"><?php echo e($cardStats['bulan_lalu']); ?>/<?php echo e($cardStats['total_siswa']); ?></span>
                        </div>
                        <span class="text-muted" style="font-size: 0.85rem;">Laporan Bulan Lalu</span>
                    </div>
                </div>
            </div>


            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: none;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="mb-2 rounded d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; background-color: #e8f5e9;">
                            <i class="bx bx-clipboard fs-3" style="color: #4ade80;"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-1"><?php echo e($cardStats['total_laporan']); ?></h2>
                        <span class="text-muted" style="font-size: 0.85rem;">Total Seluruh Laporan</span>
                    </div>
                </div>
            </div>


            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: none;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="mb-2 bg-label-primary rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; background-color: #e0ebfc;">
                            <i class="bx bx-face text-primary fs-3" style="color: #5b9cf6 !important;"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-1"><?php echo e($cardStats['total_siswa']); ?></h2>
                        <span class="text-muted" style="font-size: 0.85rem;">Total Seluruh Siswa</span>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <div class="card bg-white"
        style="border: 2px solid #e0ebfc; border-radius: 20px; box-shadow: none; overflow: hidden;">


        <div
            class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center bg-white border-bottom p-4">


            <form action="<?php echo e(url()->current()); ?>" method="GET" class="d-flex align-items-center w-100"
                style="max-width: 550px;">


                <?php if (request()->filled('student_id')): ?> <input type="hidden" name="student_id"
                        value="<?php echo e(request('student_id')); ?>"> <?php endif; ?>
                <?php if (request()->filled('date')): ?> <input type="hidden" name="date"
                        value="<?php echo e(request('date')); ?>"> <?php endif; ?>
                <?php if (request()->filled('month')): ?> <input type="hidden" name="month"
                        value="<?php echo e(request('month')); ?>"> <?php endif; ?>

                <div class="input-group me-2"
                    style="border-radius: 50px; overflow: hidden; border: 1px solid #a3c7fb; background: white;">
                    <span class="input-group-text bg-transparent border-0 pe-1">
                        <i class="bx bx-search" style="color: #1e293b;"></i>
                    </span>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                        class="form-control border-0 shadow-none px-2" placeholder="Cari nama siswa/terapis..."
                        style="background: transparent;">
                </div>

                <button type="submit" class="btn rounded-pill px-4 fw-semibold text-white shadow-none me-2"
                    style="background-color: #5b9cf6; border: none;">Cari</button>


                <button type="button"
                    class="btn rounded-pill px-4 fw-semibold text-white shadow-none d-flex align-items-center"
                    style="background-color: #f97316; border: none;" data-bs-toggle="modal"
                    data-bs-target="#modalFilter">
                    <i class="bx bx-filter-alt me-1"></i> Filter
                </button>


                <?php if (request()->filled('search') || request()->filled('student_id') || request()->filled('date') || request()->filled('month')): ?>
                    <a href="<?php echo e(route('koor.dailyReport.index')); ?>"
                        class="btn rounded-pill px-3 fw-semibold text-dark shadow-none ms-2 d-flex align-items-center"
                        style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                        <i class="bx bx-x me-1"></i> Reset
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <div class="table-responsive text-nowrap" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table class="table custom-table-striped table-borderless" style="min-width: 1000px;">
                <thead style="border-bottom: 2px solid #f0f4f9;">
                    <tr>
                        <th class="text-center py-3 text-muted fw-semibold">No</th>
                        <th class="py-3 text-muted fw-semibold">Nama Anak</th>
                        <th class="py-3 text-muted fw-semibold">Kegiatan</th>
                        <th class="py-3 text-muted fw-semibold">Pembuat Laporan</th>
                        <th class="py-3 text-muted fw-semibold">Waktu Submit</th>
                        <th class="text-center py-3 text-muted fw-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true;
                    $__currentLoopData = $reports ?? [];
                    $__env->addLoop($__currentLoopData);
                    foreach ($__currentLoopData as $index => $report):
                        $__env->incrementLoopIndices();
                        $loop = $__env->getLastLoop();
                        $__empty_1 = false; ?>
                        <tr>

                            <td class="text-center align-middle text-dark fw-medium"><?php echo e($index + 1); ?></td>


                            <td class="align-middle text-dark fw-bold">
                                <?php echo e($report->student->name ?? '-'); ?>

                            </td>


                            <td class="align-middle text-dark">
                                <?php echo e(\Illuminate\Support\Str::limit($report->detail->activity_notes ?? '-', 30)); ?>

                            </td>


                            <td class="align-middle">
                                <div class="d-flex flex-column">
                                    <span class="text-dark fw-semibold">
                                        <?php echo e($report->therapist->name ?? $report->shadow_teacher->name ?? '-'); ?>

                                    </span>
                                    <small class="text-muted" style="font-size: 0.75rem;">
                                        <?php echo e(ucfirst($report->therapist->role ?? $report->shadow_teacher->role ?? 'Guru')); ?>

                                    </small>
                                </div>
                            </td>


                            <td class="align-middle">
                                <span class="fw-medium text-dark">
                                    <?php echo e(\Carbon\Carbon::parse($report->created_at)->locale('id')->translatedFormat('d M Y, H:i')); ?>

                                </span>
                            </td>


                            <td class="text-center align-middle">
                                <a href="<?php echo e(route('koor.dailyReport.detail', $report->id)); ?>"
                                    class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none"
                                    style="font-size: 0.75rem; background-color: #4ade80; border: none;"
                                    title="View Detail Laporan">
                                    <i class="bx bx-info-circle me-1" style="font-size: 0.9rem;"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach;
                    $__env->popLoop();
                    $loop = $__env->getLastLoop();
                    if ($__empty_1): ?>

                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <tr>
                                <td colspan="6" class="py-4 text-center text-muted">
                                    <?php if ($i == 2): ?> Belum ada riwayat laporan harian. <?php endif; ?>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card-footer bg-white border-top text-center py-4">
            <?php
            $currentPage = $pagination['current_page'] ?? 1;
            $lastPage = $pagination['last_page'] ?? 1;
            ?>

            <div class="d-inline-flex align-items-center bg-white px-3 py-2 border shadow-sm"
                style="border-radius: 50px;">

                <a href="<?php echo e($currentPage > 1 ? request()->fullUrlWithQuery(['page' => $currentPage - 1]) : 'javascript:void(0)'); ?>"
                    class="text-dark text-decoration-none me-3 <?php echo e($currentPage <= 1 ? 'opacity-50 pe-none' : ''); ?>"
                    title="Halaman Sebelumnya">
                    <i class="bx bx-chevron-left fs-5"></i>
                </a>


                <span class="fw-semibold text-dark mx-2" style="font-size: 0.9rem;">
                    <?php echo e($currentPage); ?> / <?php echo e($lastPage); ?>

                </span>


                <a href="<?php echo e($currentPage < $lastPage ? request()->fullUrlWithQuery(['page' => $currentPage + 1]) : 'javascript:void(0)'); ?>"
                    class="text-dark text-decoration-none ms-3 <?php echo e($currentPage >= $lastPage ? 'opacity-50 pe-none' : ''); ?>"
                    title="Halaman Selanjutnya">
                    <i class="bx bx-chevron-right fs-5"></i>
                </a>

            </div>
        </div>
    </div>

</div>




<div class="modal fade" id="modalFilter" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">


            <form action="<?php echo e(route('koor.dailyReport.index')); ?>" method="GET">

                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bx bx-filter-alt me-2 text-warning"></i>Filter Laporan
                    </h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark">Pilih Anak</label>
                        <select name="student_id" class="form-select shadow-none rounded-3"
                            style="border-color: #cbd5e1;">
                            <option value="">Semua Anak</option>
                            <?php if (isset($studentsList) && count($studentsList) > 0): ?>
                                <?php $__currentLoopData = $studentsList;
                                $__env->addLoop($__currentLoopData);
                                foreach ($__currentLoopData as $student):
                                    $__env->incrementLoopIndices();
                                    $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($student->id); ?>" <?php echo e(request('student_id') == $student->id ? 'selected' : ''); ?>>
                                        <?php echo e($student->name); ?>

                                    </option>
                                <?php endforeach;
                                $__env->popLoop();
                                $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <option value="" disabled>Data siswa tidak ditemukan / Gagal dimuat</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Filter Bulan</label>
                            <select name="month" class="form-select shadow-none rounded-3"
                                style="border-color: #cbd5e1;">
                                <option value="">Semua Bulan</option>
                                <?php
                                // Bikin array nama bulan pakai PHP
                                $months = [
                                    '01' => 'Januari',
                                    '02' => 'Februari',
                                    '03' => 'Maret',
                                    '04' => 'April',
                                    '05' => 'Mei',
                                    '06' => 'Juni',
                                    '07' => 'Juli',
                                    '08' => 'Agustus',
                                    '09' => 'September',
                                    '10' => 'Oktober',
                                    '11' => 'November',
                                    '12' => 'Desember'
                                ];
                                $currentYear = date('Y');
                                ?>

                                <?php $__currentLoopData = $months;
                                $__env->addLoop($__currentLoopData);
                                foreach ($__currentLoopData as $num => $name):
                                    $__env->incrementLoopIndices();
                                    $loop = $__env->getLastLoop(); ?>
                                    <?php
                                    // Format value-nya jadi YYYY-MM (contoh: 2026-07) sesuai permintaan Backend
                                    $monthValue = $currentYear . '-' . $num;
                                    ?>
                                    <option value="<?php echo e($monthValue); ?>" <?php echo e(request('month') == $monthValue ? 'selected' : ''); ?>>
                                        <?php echo e($name); ?>     <?php echo e($currentYear); ?>

                                    </option>
                                <?php endforeach;
                                $__env->popLoop();
                                $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>


                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark">Tanggal Spesifik</label>
                            <input type="date" name="date" class="form-control shadow-none rounded-3"
                                style="border-color: #cbd5e1;" value="<?php echo e(request('date')); ?>">
                            <small class="text-muted" style="font-size: 0.75rem;">(Opsional) Abaikan jika filter per
                                bulan</small>
                        </div>
                    </div>
                </div>


                <div class="modal-footer border-top p-4 pt-3">
                    <button type="button" class="btn fw-semibold px-4 shadow-none" data-bs-dismiss="modal"
                        style="border-radius: 50px; border: 1px solid #cbd5e1; color: #64748b; background-color: white;">
                        Batal
                    </button>
                    <button type="submit" class="btn fw-semibold px-4 text-white shadow-none d-flex align-items-center"
                        style="border-radius: 50px; background-color: #5b9cf6; border: none;">
                        Terapkan Filter
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/daily_report/index.blade.php ENDPATH**/ ?>