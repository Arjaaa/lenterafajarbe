<?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['id' => 'modalEditAnak'.e($student->id).'','title' => 'Edit Data Anak','size' => 'modal-lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? (array) $attributes->getIterator() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag && $constructor = (new ReflectionClass(Illuminate\View\AnonymousComponent::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes(['id' => 'modalEditAnak'.e($student->id).'','title' => 'Edit Data Anak','size' => 'modal-lg']); ?>
                                    <form action="<?php echo e(route('koor.updateAnak', $student->id)); ?>" method="POST"
                                        enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="modal-body text-start">
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <label class="form-label">Ganti Foto Profil (Biarkan kosong jika tidak
                                                        diubah)</label>
                                                    <input class="form-control" type="file" name="photo" accept="image/*" id="photo_edit_<?php echo e($student->id); ?>">
                                                    <small id="error_photo_edit_<?php echo e($student->id); ?>" class="text-danger d-none mt-1">Oops! Ukuran foto maksimal 2MB ya.</small>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Nama Lengkap <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="<?php echo e($student->name); ?>" required />
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Tanggal Lahir</label>
                                                    <input type="date" name="birth_date" class="form-control"
                                                        value="<?php echo e($student->birth_date); ?>" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Jenis Kelamin</label>
                                                    <select name="gender" class="form-select">
                                                        <option value="">Pilih Jenis Kelamin</option>
                                                        <option value="Laki-laki" <?php echo e($student->gender == 'Laki-laki' ? 'selected' : ''); ?>>Laki-laki</option>
                                                        <option value="Perempuan" <?php echo e($student->gender == 'Perempuan' ? 'selected' : ''); ?>>Perempuan</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Asal Sekolah</label>
                                                    <input type="text" name="school_name" class="form-control"
                                                        value="<?php echo e($student->school_name); ?>" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Nama Ayah</label>
                                                    <input type="text" name="father_name" class="form-control"
                                                        value="<?php echo e($student->father_name); ?>" />
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Nama Ibu</label>
                                                    <input type="text" name="mother_name" class="form-control"
                                                        value="<?php echo e($student->mother_name); ?>" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Kebutuhan Khusus</label>
                                                    <input type="text" name="special_needs" class="form-control"
                                                        value="<?php echo e($student->special_needs); ?>" />
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Tautkan ke Akun Wali (Ortu)</label>
                                                    <select name="parent_id" class="form-select">
                                                        <option value="">-- Pilih Orang Tua --</option>
                                                        <?php $__currentLoopData = $parents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                            <option value="<?php echo e($parent->id); ?>" <?php echo e($student->parent_id == $parent->id ? 'selected' : ''); ?>><?php echo e($parent->name); ?></option>
                                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">No. HP Orang Tua (Darurat)</label>
                                                    <input type="text" name="parent_phone" class="form-control"
                                                        value="<?php echo e($student->parent_phone); ?>" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label class="form-label">Catatan Diagnosis</label>
                                                    <textarea name="diagnosis_notes" class="form-control"
                                                        rows="2"><?php echo e($student->diagnosis_notes); ?></textarea>
                                                </div>
                                                <div class="col-12 mb-0">
                                                    <label class="form-label">Alamat Domisili</label>
                                                    <textarea name="address" class="form-control"
                                                        rows="2"><?php echo e($student->address); ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top pt-3">
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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
<?php endif; ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/anak/_modal-edit.blade.php ENDPATH**/ ?>