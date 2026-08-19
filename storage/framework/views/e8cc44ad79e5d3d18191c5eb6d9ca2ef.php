<?php $__env->startSection('content'); ?>

<style>
    .custom-table-striped tbody tr:nth-of-type(even) {
        background-color: #f4f8ff !important;
    }

    .custom-table-striped tbody tr td {
        border-bottom: none !important;
    }

    /* Mencegah teks dan tombol turun baris (bikin baris bengkak ke bawah) */
    .custom-table-striped th,
    .custom-table-striped td {
        white-space: nowrap !important;
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">


    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0 text-dark">Sesi Terapi 1 on 1</h4>

        <div class="bg-white px-4 py-2 d-flex align-items-center"
            style="border-radius: 50px; border: 1px solid #f0f0f0;">
            <i class="bx bx-calendar text-success me-2 fs-5"
                style="color: #4ade80 !important; background: #e8f5e9; padding: 5px; border-radius: 50%;"></i>
            <span class="fw-semibold text-dark">
                <?php echo e(\Carbon\Carbon::now()->locale('id')->translatedFormat('l, d - m - Y')); ?>

            </span>
        </div>
    </div>


    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 15px;">
            <strong> 🎉</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 15px;">
            <strong>Ada yang salah:</strong>
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


    <div class="card bg-white"
        style="border: 2px solid #e0ebfc; border-radius: 20px; box-shadow: none; overflow: hidden;">



        <div
            class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center bg-white border-bottom p-4">


            <form action="<?php echo e(url()->current()); ?>" method="GET"
                class="d-flex align-items-center gap-2 mb-3 mb-md-0 w-100" style="max-width: 500px;">
                <div class="input-group"
                    style="border-radius: 50px; overflow: hidden; border: 1px solid #a3c7fb; background: white; max-width: 350px;">
                    <span class="input-group-text bg-transparent border-0 pe-1">
                        <i class="bx bx-search" style="color: #1e293b;"></i>
                    </span>
                    <input type="text" name="search" value="<?php echo e(request('search')); ?>"
                        class="form-control border-0 shadow-none px-2" placeholder="Cari siswa atau terapis..."
                        style="background: transparent;">
                </div>

                <button type="submit" class="btn rounded-pill px-4 fw-semibold text-white shadow-none flex-shrink-0"
                    style="background-color: #5b9cf6; border: none;">Cari</button>


                <?php if (request()->filled('search')): ?>
                    <a href="<?php echo e(url()->current()); ?>"
                        class="btn rounded-pill px-3 fw-semibold text-dark shadow-none d-flex align-items-center flex-shrink-0"
                        style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                        <i class="bx bx-x me-1"></i> Reset
                    </a>
                <?php endif; ?>
            </form>


            <button type="button" class="btn rounded-pill fw-semibold px-4 text-white shadow-none flex-shrink-0"
                data-bs-toggle="modal" data-bs-target="#modalTambah1on1"
                style="background-color: #5b9cf6; border: none;">
                <i class="bx bx-plus-circle me-1 fs-5"></i> Buat Sesi Baru
            </button>
        </div>


        <div class="table-responsive text-nowrap" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
            <table class="table custom-table-striped table-borderless" style="min-width: 1000px;">
                <thead style="border-bottom: 2px solid #f0f4f9;">
                    <tr>
                        <th class="text-center py-3 text-muted fw-semibold">No</th>
                        <th class="py-3 text-muted fw-semibold">Nama Kelas Terapi</th>
                        <th class="py-3 text-muted fw-semibold">Nama Terapis</th>
                        <th class="py-3 text-muted fw-semibold">Nama Siswa</th>
                        <th class="text-center py-3 text-muted fw-semibold">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true;
                    $__currentLoopData = $oneOnOnes ?? [];
                    $__env->addLoop($__currentLoopData);
                    foreach ($__currentLoopData as $index => $sesi):
                        $__env->incrementLoopIndices();
                        $loop = $__env->getLastLoop();
                        $__empty_1 = false; ?>
                        <tr>
                            <td class="text-center align-middle text-dark fw-medium"><?php echo e($index + 1); ?></td>


                            <td class="align-middle text-dark fw-bold"><?php echo e($sesi->name ?? '-'); ?></td>


                            <td class="align-middle text-dark fw-medium">
                                <?php echo e($sesi->teacher->name ?? 'Data Hilang'); ?>

                            </td>


                            <td class="align-middle text-dark fw-medium">
                                <?php echo e($sesi->student->name ?? 'Data Hilang'); ?>

                            </td>


                            <td class="text-center align-middle">


                                <a href="<?php echo e(route('koor.detail1on1', ['id' => $sesi->id ?? 0, 'back_page' => $pagination['current_page'] ?? 1])); ?>"
                                    class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                    style="font-size: 0.75rem; background-color: #4ade80; border: none;" title="View">
                                    <i class="bx bx-info-circle me-1" style="font-size: 0.9rem;"></i> View
                                </a>


                                <button type="button" class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                    style="font-size: 0.75rem; background-color: #60a5fa; border: none;"
                                    data-bs-toggle="modal" data-bs-target="#modalEdit1on1<?php echo e($sesi->id ?? 0); ?>"
                                    title="Edit">
                                    <i class="bx bx-edit-alt me-1" style="font-size: 0.9rem;"></i> Edit
                                </button>


                                <form action="<?php echo e(route('koor.destroy1on1', $sesi->id ?? 0)); ?>" method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('Kamu yakin ingin menghapus data sesi <?php echo e(addslashes($sesi->name ?? 'ini')); ?>?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none"
                                        style="font-size: 0.75rem; background-color: #f87171; border: none;" title="Delete"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalDelete1on1<?php echo e($sesi->id ?? 0); ?>">
                                        <i class="bx bx-trash me-1" style="font-size: 0.9rem;"></i> Delete
                                    </button>
                                </form>

                            </td>
                        </tr>
                    <?php endforeach;
                    $__env->popLoop();
                    $loop = $__env->getLastLoop();
                    if ($__empty_1): ?>

                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <tr>
                                <td colspan="5" class="py-4"></td>
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




    <?php $__currentLoopData = $oneOnOnes ?? [];
    $__env->addLoop($__currentLoopData);
    foreach ($__currentLoopData as $sesi):
        $__env->incrementLoopIndices();
        $loop = $__env->getLastLoop(); ?>




        <div class="modal fade" id="modalDelete1on1<?php echo e($sesi->id ?? 0); ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content text-center" style="border-radius: 20px; border: none;">


                    <div class="modal-body p-4 text-wrap">


                        <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle"
                            style="width: 60px; height: 60px; background-color: #ffe6e6;">
                            <i class="bx bx-trash fs-2" style="color: #ff5b5c;"></i>
                        </div>

                        <h5 class="fw-bold text-dark mb-2">Hapus Sesi?</h5>


                        <p class="text-muted mb-4 text-wrap" style="font-size: 0.9rem;">
                            Data sesi <strong><?php echo e($sesi->name ?? 'ini'); ?></strong> akan dihapus permanen dan
                            tidak dapat dikembalikan.
                        </p>

                        <form action="<?php echo e(route('koor.destroy1on1', $sesi->id ?? 0)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn rounded-pill px-4 shadow-none" data-bs-dismiss="modal"
                                    style="background-color: #f1f5f9; color: #475569; border: none; font-weight: 600;">
                                    Batal
                                </button>
                                <button type="submit" class="btn rounded-pill px-4 text-white shadow-none"
                                    style="background-color: #ff5b5c; border: none; font-weight: 600;">
                                    Hapus
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($component)) {
            $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component;
        } ?>
        <?php if (isset($attributes)) {
            $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes;
        } ?>
        <?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal', 'data' => ['id' => 'modalDetail1on1' . e($sesi->id ?? 0) . '', 'title' => 'Detail Sesi Terapi']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
        <?php $component->withName('modal'); ?>
        <?php if ($component->shouldRender()): ?>
            <?php $__env->startComponent($component->resolveView(), $component->data()); ?>
            <?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
                <?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
            <?php endif; ?>
            <?php $component->withAttributes(['id' => 'modalDetail1on1' . e($sesi->id ?? 0) . '', 'title' => 'Detail Sesi Terapi']); ?>
            <div class="modal-body text-wrap text-start">
                <h6 class="fw-bold mb-3 text-primary">Informasi Sesi Terapi</h6>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td width="40%" class="ps-0"><strong>Nama Kelas Terapi</strong></td>
                        <td>: <?php echo e($sesi->name ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td class="ps-0"><strong>Nama Terapis (Guru)</strong></td>
                        <td>: <?php echo e($sesi->teacher->name ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td class="ps-0"><strong>Nama Siswa (Anak)</strong></td>
                        <td>: <?php echo e($sesi->student->name ?? '-'); ?></td>
                    </tr>
                </table>
            </div>
            <div class="modal-footer border-top pt-3">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Tutup</button>
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


        <?php if (isset($component)) {
            $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component;
        } ?>
        <?php if (isset($attributes)) {
            $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes;
        } ?>
        <?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal', 'data' => ['id' => 'modalEdit1on1' . e($sesi->id ?? 0) . '', 'title' => 'Edit Sesi 1 on 1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
        <?php $component->withName('modal'); ?>
        <?php if ($component->shouldRender()): ?>
            <?php $__env->startComponent($component->resolveView(), $component->data()); ?>
            <?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
                <?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
            <?php endif; ?>
            <?php $component->withAttributes(['id' => 'modalEdit1on1' . e($sesi->id ?? 0) . '', 'title' => 'Edit Sesi 1 on 1']); ?>
            <form action="<?php echo e(route('koor.update1on1', $sesi->id ?? 0)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Nama Program / Terapi <span
                                class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?php echo e($sesi->name ?? ''); ?>"
                            placeholder="Contoh: Terapi Wicara" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Pilih Anak <span class="text-danger">*</span></label>
                        <select name="student_id" class="form-select" required>
                            <option value="">-- Pilih Anak --</option>
                            <?php $__currentLoopData = $students ?? [];
                            $__env->addLoop($__currentLoopData);
                            foreach ($__currentLoopData as $student):
                                $__env->incrementLoopIndices();
                                $loop = $__env->getLastLoop(); ?>
                                <?php if (!in_array($student->id, $busyStudentIds ?? []) || ($sesi->student_id ?? 0) == $student->id): ?>
                                    <option value="<?php echo e($student->id); ?>" <?php echo e(($sesi->student_id ?? 0) == $student->id ? 'selected' : ''); ?>>
                                        <?php echo e($student->name); ?>

                                    </option>
                                <?php endif; ?>
                            <?php endforeach;
                            $__env->popLoop();
                            $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-dark fw-semibold">Pilih Terapis <span class="text-danger">*</span></label>
                        <select name="teacher_id" class="form-select" required>
                            <option value="">-- Pilih Terapis --</option>
                            <?php $__currentLoopData = $teachers ?? [];
                            $__env->addLoop($__currentLoopData);
                            foreach ($__currentLoopData as $teacher):
                                $__env->incrementLoopIndices();
                                $loop = $__env->getLastLoop(); ?>
                                <?php if (!in_array($teacher->id, $busyTeacherIds ?? []) || ($sesi->teacher_id ?? 0) == $teacher->id): ?>
                                    <option value="<?php echo e($teacher->id); ?>" <?php echo e(($sesi->teacher_id ?? 0) == $teacher->id ? 'selected' : ''); ?>>
                                        <?php echo e($teacher->name); ?>

                                    </option>
                                <?php endif; ?>
                            <?php endforeach;
                            $__env->popLoop();
                            $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top pt-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4"
                        style="background-color: #5b9cf6; border: none;">Simpan Perubahan</button>
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

    <?php endforeach;
    $__env->popLoop();
    $loop = $__env->getLastLoop(); ?>


    <?php if (isset($component)) {
        $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component;
    } ?>
    <?php if (isset($attributes)) {
        $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes;
    } ?>
    <?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal', 'data' => ['id' => 'modalTambah1on1', 'title' => 'Buat Sesi 1 on 1 Baru']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
    <?php $component->withName('modal'); ?>
    <?php if ($component->shouldRender()): ?>
        <?php $__env->startComponent($component->resolveView(), $component->data()); ?>
        <?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
            <?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
        <?php endif; ?>
        <?php $component->withAttributes(['id' => 'modalTambah1on1', 'title' => 'Buat Sesi 1 on 1 Baru']); ?>
        <form action="<?php echo e(route('koor.store1on1')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-body text-start">
                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Nama Program / Terapi <span
                            class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Terapi Okupasi" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Pilih Anak <span class="text-danger">*</span></label>
                    <select name="student_id" class="form-select" required>
                        <option value="">-- Pilih Anak --</option>
                        <?php $__currentLoopData = $students ?? [];
                        $__env->addLoop($__currentLoopData);
                        foreach ($__currentLoopData as $student):
                            $__env->incrementLoopIndices();
                            $loop = $__env->getLastLoop(); ?>
                            <?php if (!in_array($student->id, $busyStudentIds ?? [])): ?>
                                <option value="<?php echo e($student->id); ?>"><?php echo e($student->name); ?></option>
                            <?php endif; ?>
                        <?php endforeach;
                        $__env->popLoop();
                        $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label text-dark fw-semibold">Pilih Terapis <span class="text-danger">*</span></label>
                    <select name="teacher_id" class="form-select" required>
                        <option value="">-- Pilih Terapis --</option>
                        <?php $__currentLoopData = $teachers ?? [];
                        $__env->addLoop($__currentLoopData);
                        foreach ($__currentLoopData as $teacher):
                            $__env->incrementLoopIndices();
                            $loop = $__env->getLastLoop(); ?>
                            <?php if (!in_array($teacher->id, $busyTeacherIds ?? [])): ?>
                                <option value="<?php echo e($teacher->id); ?>"><?php echo e($teacher->name); ?></option>
                            <?php endif; ?>
                        <?php endforeach;
                        $__env->popLoop();
                        $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-top pt-3">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                    data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4"
                    style="background-color: #5b9cf6; border: none;">Buat Sesi</button>
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

</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/data-1on1.blade.php ENDPATH**/ ?>