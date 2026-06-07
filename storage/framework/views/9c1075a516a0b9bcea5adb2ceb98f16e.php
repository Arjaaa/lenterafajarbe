<?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalDetailAnak'.e($student->id).'','title' => 'Detail Informasi Anak: '.e($student->name).'','size' => 'modal-lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalDetailAnak'.e($student->id).'','title' => 'Detail Informasi Anak: '.e($student->name).'','size' => 'modal-lg']); ?>
    <div class="modal-body text-wrap">
        <div class="row mb-4 align-items-center">
            <div class="col-md-3 text-center">
                <div class="avatar avatar-xl mx-auto mb-2" style="width: 100px; height: 100px;">
                    <?php if($student->photo): ?>
                        <img src="<?php echo e(asset('storage/' . $student->photo)); ?>" alt="Foto <?php echo e($student->name); ?>"
                            class="rounded-circle" style="object-fit: cover;">
                    <?php else: ?>
                        <span class="avatar-initial rounded-circle bg-label-secondary fs-2"><i
                                class="bx bx-user"></i></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-9">
                <h4 class="mb-1 fw-bold text-primary"><?php echo e($student->name); ?></h4>
                <p class="mb-0 text-muted">Anak Didik Lentera Fajar</p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6 border-end">
                <small class="text-light fw-semibold d-block mb-2">Data Pribadi &
                    Sekolah</small>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="ps-0" width="40%"><strong>Tanggal Lahir</strong></td>
                        <td>:
                            <?php echo e($student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') : '-'); ?>

                        </td>
                    </tr>
                    <tr>
                        <td class="ps-0"><strong>Jenis Kelamin</strong></td>
                        <td>: <?php echo e($student->gender ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td class="ps-0"><strong>Asal Sekolah</strong></td>
                        <td>: <?php echo e($student->school_name ?? '-'); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <small class="text-light fw-semibold d-block mb-2">Data Keluarga & Medis</small>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="ps-0" width="40%"><strong>Nama Ayah</strong></td>
                        <td>: <?php echo e($student->father_name ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td class="ps-0"><strong>Nama Ibu</strong></td>
                        <td>: <?php echo e($student->mother_name ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td class="ps-0"><strong>Akun Wali</strong></td>
                        <td>:
                            <?php echo e($student->parent_id && \App\Models\User::find($student->parent_id) ? \App\Models\User::find($student->parent_id)->name : 'Belum Ditautkan'); ?>

                        </td>
                    </tr>
                    <tr>
                        <td class="ps-0"><strong>No. HP Wali</strong></td>
                        <td>: <?php echo e($student->parent_phone ?? '-'); ?></td>
                    </tr>
                    <tr>
                        <td class="ps-0"><strong>Kebutuhan</strong></td>
                        <td>: <span class="badge bg-label-danger"><?php echo e($student->special_needs ?? '-'); ?></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-12 mb-3">
                <strong class="d-block mb-1">Catatan Diagnosis:</strong>
                <div class="p-3 bg-light rounded text-secondary" style="min-height: 60px;">
                    <?php echo nl2br(e($student->diagnosis_notes ?? 'Tidak ada catatan diagnosis.')); ?>

                </div>
            </div>
            <div class="col-12">
                <strong class="d-block mb-1">Alamat Domisili:</strong>
                <div class="p-3 bg-light rounded text-secondary" style="min-height: 60px;">
                    <?php echo e($student->address ?? 'Tidak ada data alamat.'); ?>

                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer border-top pt-3">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
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
<?php endif; ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/anak/_modal-detail.blade.php ENDPATH**/ ?>