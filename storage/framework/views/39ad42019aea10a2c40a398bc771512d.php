


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
            <h4 class="fw-bold mb-0 text-dark">Group Shadow Teacher</h4>

            <div class="bg-white px-4 py-2 d-flex align-items-center" style="border-radius: 50px; border: 1px solid #f0f0f0;">
                <i class="bx bx-calendar text-success me-2 fs-5" style="color: #4ade80 !important; background: #e8f5e9; padding: 5px; border-radius: 50%;"></i>
                <span class="fw-semibold text-dark">
                    <?php echo e(\Carbon\Carbon::now()->locale('id')->translatedFormat('l, d - m - Y')); ?>

                </span>
            </div>
        </div>

        
        <?php if(session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 15px;">
                <strong></strong> <?php echo e(session('success')); ?>

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 15px;">
                <strong> Ada yang salah:</strong>
                <ul class="mb-0 mt-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        
        <div class="card bg-white" style="border: 2px solid #e0ebfc; border-radius: 20px; box-shadow: none; overflow: hidden;">

            
           
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center bg-white border-bottom p-4">

                
                <form action="<?php echo e(url()->current()); ?>" method="GET" class="d-flex align-items-center gap-2 mb-3 mb-md-0 w-100" style="max-width: 500px;">
                    <div class="input-group" style="border-radius: 50px; overflow: hidden; border: 1px solid #a3c7fb; background: white; max-width: 350px;">
                        <span class="input-group-text bg-transparent border-0 pe-1">
                            <i class="bx bx-search" style="color: #1e293b;"></i>
                        </span>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                            class="form-control border-0 shadow-none px-2" placeholder="Cari siswa, PJ, atau partner..." 
                            style="background: transparent;">
                    </div>
                    
                    <button type="submit" class="btn rounded-pill px-4 fw-semibold text-white shadow-none flex-shrink-0" 
                        style="background-color: #5b9cf6; border: none;">Cari</button>

                    
                    <?php if(request()->filled('search')): ?>
                        <a href="<?php echo e(url()->current()); ?>" class="btn rounded-pill px-3 fw-semibold text-dark shadow-none d-flex align-items-center flex-shrink-0"
                            style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                            <i class="bx bx-x me-1"></i> Reset
                        </a>
                    <?php endif; ?>
                </form>

                
                <button type="button" class="btn rounded-pill fw-semibold px-4 text-white shadow-none flex-shrink-0" data-bs-toggle="modal"
                    data-bs-target="#modalTambahShadow" style="background-color: #5b9cf6; border: none;">
                    <i class="bx bx-plus-circle me-1 fs-5"></i> Buat Group Baru
                </button>
            </div>

            
            
            
            <div class="table-responsive text-nowrap" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                
                <table class="table custom-table-striped table-borderless" style="min-width: 1100px;">
                    <thead style="border-bottom: 2px solid #f0f4f9;">
                        <tr>
                            <th class="text-center py-3 text-muted fw-semibold">No</th>
                            <th class="py-3 text-muted fw-semibold">Nama Group</th>
                            <th class="py-3 text-muted fw-semibold">Anak didampingi</th>
                            <th class="py-3 text-muted fw-semibold">PJ Shadow (Koor)</th>
                            <th class="py-3 text-muted fw-semibold">Guru Shadow (Partner)</th>
                            <th class="py-3 text-muted fw-semibold">Lokasi Sekolah</th>
                            <th class="text-center py-3 text-muted fw-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $shadowGroups ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td class="text-center align-middle text-dark fw-medium"><?php echo e($index + 1); ?></td>

                                
                                <td class="align-middle text-dark fw-bold"><?php echo e($group->name ?? '-'); ?></td>

                                
                                <td class="align-middle text-dark fw-medium">
                                    <?php echo e($group->student->name ?? 'Data Hilang'); ?>

                                </td>

                                
                                <td class="align-middle text-dark fw-medium">
                                    <?php echo e($group->pic->name ?? 'Data Hilang'); ?>

                                </td>

                                
                                <td class="align-middle text-dark fw-medium">
                                    <?php echo e($group->partner->name ?? 'Data Hilang'); ?>

                                </td>

                                
                                <td class="align-middle text-dark fw-medium">
                                    <?php echo e($group->school_name ?? '-'); ?>

                                </td>

                                
                                <td class="text-center align-middle">

                                    
                                    <a href="<?php echo e(route('koor.detailShadowGroup', ['id' => $group->id ?? 0, 'back_page' => $pagination['current_page'] ?? 1])); ?>"
                                        class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                        style="font-size: 0.75rem; background-color: #4ade80; border: none;" title="View">
                                        <i class="bx bx-info-circle me-1" style="font-size: 0.9rem;"></i> View
                                    </a>

                                    
                                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                        style="font-size: 0.75rem; background-color: #60a5fa; border: none;" data-bs-toggle="modal"
                                        data-bs-target="#modalEditShadow<?php echo e($group->id ?? 0); ?>" title="Edit">
                                        <i class="bx bx-edit-alt me-1" style="font-size: 0.9rem;"></i> Edit
                                    </button>

                                    
                                    <form action="<?php echo e(route('koor.destroyShadowGroup', $group->id ?? 0)); ?>" method="POST" class="d-inline"
                                        onsubmit="return confirm('Kamu yakin ingin menghapus grup <?php echo e(addslashes($group->name ?? 'ini')); ?>?');">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                       <button type="button" class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none"
                                            style="font-size: 0.75rem; background-color: #f87171; border: none;" title="Delete"
                                            data-bs-toggle="modal" data-bs-target="#modalDeleteShadowGroup<?php echo e($group->id ?? 0); ?>">
                                            <i class="bx bx-trash me-1" style="font-size: 0.9rem;"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            
                            <?php for($i = 0; $i < 5; $i++): ?>
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
        </div>

        
        
        

        <?php $__currentLoopData = $shadowGroups ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        


<div class="modal fade" id="modalDeleteShadowGroup<?php echo e($group->id ?? 0); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center" style="border-radius: 20px; border: none;">
            
            
            <div class="modal-body p-4 text-wrap">
                
                
                <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle"
                    style="width: 60px; height: 60px; background-color: #ffe6e6;">
                    <i class="bx bx-trash fs-2" style="color: #ff5b5c;"></i>
                </div>

                <h5 class="fw-bold text-dark mb-2">Hapus Grup?</h5>
                
                
                <p class="text-muted mb-4 text-wrap" style="font-size: 0.9rem;">
                    Grup <strong><?php echo e($group->name ?? 'ini'); ?></strong> akan dihapus permanen dan tidak dapat dikembalikan.
                </p>

                <form action="<?php echo e(route('koor.destroyShadowGroup', $group->id ?? 0)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <div class="d-flex justify-content-center gap-2">
                        <button type="button" class="btn rounded-pill px-4 shadow-none"
                            data-bs-dismiss="modal"
                            style="background-color: #f1f5f9; color: #475569; border: none; font-weight: 600;">
                            Batal
                        </button>
                        <button type="submit"
                            class="btn rounded-pill px-4 text-white shadow-none"
                            style="background-color: #ff5b5c; border: none; font-weight: 600;">
                            Hapus
                        </button>
                    </div>
                </form>
                
            </div>
        </div>
    </div>
</div>

            
            <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalDetailShadow'.e($group->id ?? 0).'','title' => 'Detail Group Shadow']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalDetailShadow'.e($group->id ?? 0).'','title' => 'Detail Group Shadow']); ?>
                <div class="modal-body text-wrap text-start">
                    <h6 class="fw-bold mb-3 text-primary">Informasi Penugasan</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%" class="ps-0"><strong>Nama Group</strong></td>
                            <td>: <?php echo e($group->name ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td class="ps-0"><strong>Anak Didampingi</strong></td>
                            <td>: <?php echo e($group->student->name ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td class="ps-0"><strong>Koordinator (PJ)</strong></td>
                            <td>: <?php echo e($group->pic->name ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td class="ps-0"><strong>Guru Pendamping</strong></td>
                            <td>: <?php echo e($group->partner->name ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <td class="ps-0"><strong>Lokasi Sekolah</strong></td>
                            <td>: <?php echo e($group->school_name ?? '-'); ?></td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer border-top pt-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalEditShadow'.e($group->id ?? 0).'','title' => 'Edit Group Shadow']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalEditShadow'.e($group->id ?? 0).'','title' => 'Edit Group Shadow']); ?>
                <form action="<?php echo e(route('koor.updateShadowGroup', $group->id ?? 0)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="modal-body text-start">
                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold">Nama Group <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo e($group->name ?? ''); ?>" placeholder="Contoh: Group Bermain A" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold">Anak yang Didampingi <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-select" required>
                                <option value="">-- Pilih Anak --</option>
                                <?php $__currentLoopData = $students ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(!in_array($student->id, $busyStudentIds ?? []) || ($group->student_id ?? 0) == $student->id): ?>
                                        <option value="<?php echo e($student->id); ?>" <?php echo e(($group->student_id ?? 0) == $student->id ? 'selected' : ''); ?>>
                                            <?php echo e($student->name); ?>

                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold">Koordinator (PJ Shadow) <span class="text-danger">*</span></label>
                            <select name="pic_id" class="form-select" required>
                                <option value="">-- Pilih PJ --</option>
                                <?php $__currentLoopData = $pjs ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(!in_array($pj->id, $assignedPicIds ?? []) || ($group->pic_id ?? 0) == $pj->id): ?>
                                        <option value="<?php echo e($pj->id); ?>" <?php echo e(($group->pic_id ?? 0) == $pj->id ? 'selected' : ''); ?>>
                                            <?php echo e($pj->name); ?>

                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold">Guru Pendamping (Partner) <span class="text-danger">*</span></label>
                            <select name="partner_id" class="form-select" required>
                                <option value="">-- Pilih Guru Shadow --</option>
                                <?php $__currentLoopData = $partners ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(!in_array($partner->id, $assignedPartnerIds ?? []) || ($group->partner_id ?? 0) == $partner->id): ?>
                                        <option value="<?php echo e($partner->id); ?>" <?php echo e(($group->partner_id ?? 0) == $partner->id ? 'selected' : ''); ?>>
                                            <?php echo e($partner->name); ?>

                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-dark fw-semibold">Lokasi Sekolah <span class="text-danger">*</span></label>
                            <input type="text" name="school_name" class="form-control" value="<?php echo e($group->school_name ?? ''); ?>" placeholder="Contoh: TK Tunas Bangsa" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top pt-3">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #5b9cf6; border: none;">Simpan Perubahan</button>
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
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        
        <?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalTambahShadow','title' => 'Buat Group Shadow Baru']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalTambahShadow','title' => 'Buat Group Shadow Baru']); ?>
            <form action="<?php echo e(route('koor.storeShadowGroup')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Nama Group <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Group Bermain A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Anak yang Didampingi <span class="text-danger">*</span></label>
                        <select name="student_id" class="form-select" required>
                            <option value="">-- Pilih Anak --</option>
                            <?php $__currentLoopData = $students ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!in_array($student->id, $busyStudentIds ?? [])): ?>
                                    <option value="<?php echo e($student->id); ?>"><?php echo e($student->name); ?></option>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Koordinator (PJ Shadow) <span class="text-danger">*</span></label>
                        <select name="pic_id" class="form-select" required>
                            <option value="">-- Pilih PJ --</option>
                            <?php $__currentLoopData = $pjs ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pj): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!in_array($pj->id, $assignedPicIds ?? [])): ?>
                                    <option value="<?php echo e($pj->id); ?>"><?php echo e($pj->name); ?></option>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Guru Pendamping (Partner) <span class="text-danger">*</span></label>
                        <select name="partner_id" class="form-select" required>
                            <option value="">-- Pilih Guru Shadow --</option>
                            <?php $__currentLoopData = $partners ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $partner): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(!in_array($partner->id, $assignedPartnerIds ?? [])): ?>
                                    <option value="<?php echo e($partner->id); ?>"><?php echo e($partner->name); ?></option>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-dark fw-semibold">Lokasi Sekolah <span class="text-danger">*</span></label>
                        <input type="text" name="school_name" class="form-control" placeholder="Contoh: TK Tunas Bangsa" required>
                    </div>
                </div>
                <div class="modal-footer border-top pt-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #5b9cf6; border: none;">Buat Group</button>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/data-shadow.blade.php ENDPATH**/ ?>