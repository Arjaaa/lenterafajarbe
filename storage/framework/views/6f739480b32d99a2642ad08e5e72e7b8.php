<div class="table-responsive text-nowrap">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Foto</th>
                <th>Nama Anak</th>
                <th>Akun Wali (Ortu)</th>
                <th>Asal Sekolah</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody class="table-border-bottom-0">
            <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td>
                        <div class="avatar avatar-md">
                            <?php if($student->photo): ?>
                                <img src="<?php echo e(asset('storage/' . $student->photo)); ?>" alt="Foto <?php echo e($student->name); ?>"
                                    class="rounded-circle" style="object-fit: cover;">
                            <?php else: ?>
                                <span class="avatar-initial rounded-circle bg-label-secondary"><i class="bx bx-user"></i></span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td><strong><?php echo e($student->name); ?></strong></td>
                    <td>
                        <?php echo e($student->parent_id && \App\Models\User::find($student->parent_id) ? \App\Models\User::find($student->parent_id)->name : 'Belum Ditautkan'); ?>

                    </td>
                    <td><?php echo e($student->school_name ?? '-'); ?></td>
                    <td><span class="badge bg-label-success me-1">Aktif</span></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-icon btn-info" data-bs-toggle="modal"
                            data-bs-target="#modalDetailAnak<?php echo e($student->id); ?>" title="Lihat Detail">
                            <i class="bx bx-show"></i>
                        </button>

                        <button type="button" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="modal"
                            data-bs-target="#modalEditAnak<?php echo e($student->id); ?>" title="Edit">
                            <i class="bx bx-edit-alt"></i>
                        </button>

                        <form action="<?php echo e(route('koor.destroyAnak', $student->id)); ?>" method="POST" class="d-inline"
                            onsubmit="return confirm('Kamu yakin ingin menghapus data anak <?php echo e($student->name); ?>?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus"><i
                                    class="bx bx-trash"></i></button>
                        </form>

                        
                        <?php echo $__env->make('admin.anak._modal-detail', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <?php echo $__env->make('admin.anak._modal-edit', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Belum ada data anak di database.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/anak/_table.blade.php ENDPATH**/ ?>