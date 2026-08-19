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
        padding-top: 0.4rem !important;
        padding-bottom: 0.4rem !important;
    }
</style>

<div
    class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center bg-white border-bottom p-4">

    
    <form action="<?php echo e(url()->current()); ?>" method="GET" class="d-flex align-items-center gap-2 mb-3 mb-md-0 w-100"
        style="max-width: 500px;">

        <div class="input-group"
            style="border-radius: 50px; overflow: hidden; border: 1px solid #a3c7fb; background: white; max-width: 350px;">
            <span class="input-group-text bg-transparent border-0 pe-1">
                <i class="bx bx-search" style="color: #1e293b;"></i>
            </span>
            <input type="text" name="search" class="form-control border-0 shadow-none px-2"
                placeholder="Cari nama siswa/orang tua..." value="<?php echo e(request('search')); ?>"
                style="background: transparent;">
        </div>

        <button type="submit" class="btn rounded-pill px-4 fw-semibold text-white shadow-none flex-shrink-0"
            style="background-color: #5b9cf6; border: none;">Cari</button>

        
        <?php if(request()->filled('search')): ?>
            <a href="<?php echo e(url()->current()); ?>"
                class="btn rounded-pill px-3 fw-semibold text-dark shadow-none d-flex align-items-center flex-shrink-0"
                style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                <i class="bx bx-x me-1"></i> Reset
            </a>
        <?php endif; ?>
    </form>

    
    <button type="button" class="btn rounded-pill fw-semibold px-4 text-white shadow-none" data-bs-toggle="modal"
        data-bs-target="#modalTambahAnak" style="background-color: #5b9cf6; border: none;">
        <i class="bx bx-plus-circle me-1 fs-5"></i> Add Student
    </button>

</div>
<div class="table-responsive text-nowrap" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">


    <table class="table custom-table-striped table-borderless" style="min-width: 1000px;">
        <thead style="border-bottom: 2px solid #f0f4f9;">
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
            <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $anak): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td class="text-center align-middle text-dark fw-medium"><?php echo e($index + 1); ?></td>

                    
                    <td class="align-middle">
                        <div class="avatar avatar-md">
                            <?php if(!empty($anak->photo)): ?>
                                <img src="<?php echo e(str_starts_with($anak->photo, 'http') ? $anak->photo : asset('storage/' . $anak->photo)); ?>"
                                    alt="Avatar" class="rounded-circle" style="object-fit: cover; width: 100%; height: 100%;"
                                    onerror="this.onerror=null; this.outerHTML='<span class=\'avatar-initial rounded-circle\' style=\'background-color: #cbd5e1; color: white;\'><i class=\'bx bx-user\'></i></span>';" />
                            <?php else: ?>
                                <span class="avatar-initial rounded-circle" style="background-color: #cbd5e1; color: white;">
                                    <i class="bx bx-user"></i>
                                </span>
                            <?php endif; ?>
                        </div>
                    </td>

                    
                    <td class="align-middle text-dark fw-medium"><?php echo e($anak->name ?? 'Ophelia Luna Jasmine'); ?></td>
                    <td class="align-middle text-dark"><?php echo e($anak->gender ?? 'Perempuan'); ?></td>
                    <td class="align-middle text-dark"><?php echo e($anak->special_needs ?? 'ADHD, Autism'); ?></td>
                    <td class="align-middle text-dark"><?php echo e($anak->parent->name ?? $anak->mother_name ?? 'Bambang Surya'); ?>

                    </td>

                    
                    <td class="text-center align-middle">
                        <a href="<?php echo e(route('koor.detailAnak', ['id' => $anak->id ?? 0, 'back_page' => $pagination['current_page'] ?? 1])); ?>"
                            class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                            style="font-size: 0.75rem; background-color: #4ade80; border: none;" title="View">
                            <i class="bx bx-info-circle me-1" style="font-size: 0.9rem;"></i> View
                        </a>

                        
                        <button type="button" class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                            style="font-size: 0.75rem; background-color: #2ea4ff; border: none;" data-bs-toggle="modal"
                            data-bs-target="#modalEditAnak<?php echo e($anak->id ?? 0); ?>" title="Edit">
                            <i class="bx bx-edit me-1" style="font-size: 0.9rem;"></i> Edit
                        </button>

                        
                        <form action="<?php echo e(route('koor.destroyAnak', $anak->id ?? 0)); ?>" method="POST" class="d-inline"
                            onsubmit="return confirm('Kamu yakin ingin menghapus data <?php echo e(addslashes($anak->name ?? 'anak ini')); ?>?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            
                            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none"
                                style="font-size: 0.75rem; background-color: #f87171; border: none;" title="Delete"
                                data-bs-toggle="modal" data-bs-target="#modalDeleteAnak<?php echo e($anak->id ?? 0); ?>">
                                <i class="bx bx-trash me-1" style="font-size: 0.9rem;"></i> Delete
                            </button>
                        </form>

                    </td>
                </tr>

                <?php echo $__env->make('admin.anak._modal-edit', ['anak' => $anak], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                
                <?php for($i = 0; $i < 6; $i++): ?>
                    <tr>
                        <td colspan="7" class="py-4"></td>
                    </tr>
                <?php endfor; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<div class="card-footer bg-white border-top text-center py-4">
    <div class="d-inline-flex align-items-center bg-white px-3 py-2 border shadow-sm" style="border-radius: 50px;">
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




<?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anak): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalDetailAnak'.e($anak->id ?? 0).'','title' => 'Detail Siswa: '.e($anak->name ?? 'Tanpa Nama').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalDetailAnak'.e($anak->id ?? 0).'','title' => 'Detail Siswa: '.e($anak->name ?? 'Tanpa Nama').'']); ?>
        <div class="modal-body text-wrap text-start">
            <div class="text-center mb-4">
                <?php if(!empty($anak->photo)): ?>
                    <img src="<?php echo e(str_starts_with($anak->photo, 'http') ? $anak->photo : asset('storage/' . $anak->photo)); ?>"
                        alt="Foto Anak" class="rounded-circle shadow-sm"
                        style="width: 100px; height: 100px; object-fit: cover;">
                <?php else: ?>
                    <div class="avatar avatar-xl mx-auto">
                        <span class="avatar-initial rounded-circle bg-label-secondary text-muted"
                            style="width: 100px; height: 100px; font-size: 3rem;">
                            <i class="bx bx-user"></i>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
            <table class="table table-sm table-borderless">
                <tr>
                    <td width="40%" class="ps-0"><strong>Nama Lengkap</strong></td>
                    <td>: <?php echo e($anak->name ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="ps-0"><strong>Jenis Kelamin</strong></td>
                    <td>: <?php echo e($anak->gender ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="ps-0"><strong>Kebutuhan Khusus</strong></td>
                    <td>: <?php echo e($anak->special_needs ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="ps-0"><strong>Catatan Diagnosa</strong></td>
                    <td>: <?php echo e($anak->diagnosis_notes ?? '-'); ?></td>
                </tr>
                <tr>
                    <td class="ps-0"><strong>Nama Orang Tua</strong></td>
                    <td>: <?php echo e($anak->parent->name ?? $anak->mother_name ?? '-'); ?></td>
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

    
    <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalEditAnak'.e($anak->id ?? 0).'','title' => 'Edit Data Siswa']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalEditAnak'.e($anak->id ?? 0).'','title' => 'Edit Data Siswa']); ?>
        <form action="<?php echo e(route('koor.updateAnak', $anak->id ?? 0)); ?>" method="POST" enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="modal-body text-start">
                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?php echo e($anak->name ?? ''); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Jenis Kelamin</label>
                    <select name="gender" class="form-select">
                        <option value="Laki-laki" <?php echo e(($anak->gender ?? '') == 'Laki-laki' ? 'selected' : ''); ?>>Laki-laki
                        </option>
                        <option value="Perempuan" <?php echo e(($anak->gender ?? '') == 'Perempuan' ? 'selected' : ''); ?>>Perempuan
                        </option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Nama Ibu (Orang Tua)</label>
                    <input type="text" name="mother_name" class="form-control" value="<?php echo e($anak->mother_name ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label text-dark fw-semibold">Upload Foto Baru (Opsional)</label>
                    <input type="file" name="photo" class="form-control" accept="image/png, image/jpeg, image/jpg">
                </div>
            </div>
            <div class="modal-footer border-top pt-3">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                    data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4"
                    style="background-color: #5b9cf6; border: none;">Simpan</button>
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

    
    
    
    <div class="modal fade" id="modalDeleteAnak<?php echo e($anak->id ?? 0); ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center" style="border-radius: 20px; border: none;">

                
                <div class="modal-body p-4 text-wrap">

                    
                    <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle"
                        style="width: 60px; height: 60px; background-color: #ffe6e6;">
                        <i class="bx bx-trash fs-2" style="color: #ff5b5c;"></i>
                    </div>

                    <h5 class="fw-bold text-dark mb-2">Hapus Data Anak?</h5>

                    
                    <p class="text-muted mb-4 text-wrap" style="font-size: 0.9rem;">
                        Data <strong><?php echo e($anak->name ?? 'Anak ini'); ?></strong> akan dihapus permanen dan tidak dapat
                        dikembalikan.
                    </p>

                    <form action="<?php echo e(route('koor.destroyAnak', $anak->id ?? 0)); ?>" method="POST">
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

<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/anak/_table.blade.php ENDPATH**/ ?>