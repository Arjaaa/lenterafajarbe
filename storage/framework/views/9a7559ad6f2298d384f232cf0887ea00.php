


<div class="modal fade" id="modalEditAnak<?php echo e($anak->id ?? 0); ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark"><i class="bx bx-edit text-primary me-2"></i>Edit Data Siswa &
                    Orang Tua</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?php echo e(route('koor.updateAnak', $anak->id ?? 0)); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="modal-body p-4 text-start" style="white-space: normal !important;">

                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="badge bg-label-primary p-2 rounded me-2"><i class="bx bx-user"></i></div>
                        <h6 class="fw-bold text-dark mb-0">Informasi Orang Tua</h6>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Nama Ayah <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="father_name" class="form-control"
                                value="<?php echo e($anak->father_name ?? ''); ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Nama Ibu <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="mother_name" class="form-control"
                                value="<?php echo e($anak->mother_name ?? ''); ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-dark fw-semibold">No. WA / Telp Orang Tua <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="parent_phone" class="form-control"
                                value="<?php echo e($anak->parent_phone ?? $anak->parent->phone ?? ''); ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-dark fw-semibold">Password Akun Orang Tua <span
                                    class="text-muted fw-normal">(Opsional)</span></label>
                            <div class="input-group input-group-merge">
                                <input type="password" name="parent_password" id="parentPassword<?php echo e($anak->id ?? 0); ?>"
                                    class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                                <span class="input-group-text cursor-pointer"
                                    onclick="togglePassword('parentPassword<?php echo e($anak->id ?? 0); ?>', this)">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                            <small class="text-muted">Minimal 6 karakter jika ingin mengganti password.</small>
                        </div>
                    </div>

                    <hr class="my-4" style="border-top: 2px dashed #e0ebfc;">

                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="badge bg-label-success p-2 rounded me-2"><i class="bx bx-face"></i></div>
                        <h6 class="fw-bold text-dark mb-0">Biodata Siswa</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Nama Lengkap Anak <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo e($anak->name ?? ''); ?>"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="birth_date" class="form-control"
                                value="<?php echo e(isset($anak->birth_date) ? date('Y-m-d', strtotime($anak->birth_date)) : ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Jenis Kelamin</label>
                            <select name="gender" class="form-select">
                                <option value="laki-laki" <?php echo e(($anak->gender ?? '') == 'laki-laki' ? 'selected' : ''); ?>>
                                    Laki-Laki</option>
                                <option value="perempuan" <?php echo e(($anak->gender ?? '') == 'perempuan' ? 'selected' : ''); ?>>
                                    Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Nama Sekolah</label>
                            <input type="text" name="school_name" class="form-control"
                                value="<?php echo e($anak->school_name ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Kebutuhan Khusus</label>
                            <select name="special_needs" class="form-select">
                                <option value="autis" <?php echo e(($anak->special_needs ?? '') == 'autis' ? 'selected' : ''); ?>>
                                    Autis</option>
                                <option value="adhd" <?php echo e(($anak->special_needs ?? '') == 'adhd' ? 'selected' : ''); ?>>ADHD
                                </option>
                                <option value="down_syndrome" <?php echo e(($anak->special_needs ?? '') == 'down_syndrome' ? 'selected' : ''); ?>>Down Syndrome</option>
                                <option value="lambat_belajar" <?php echo e(($anak->special_needs ?? '') == 'lambat_belajar' ? 'selected' : ''); ?>>Lambat Belajar</option>
                                <option value="lainnya" <?php echo e(($anak->special_needs ?? '') == 'lainnya' ? 'selected' : ''); ?>>
                                    Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Ganti Foto Anak <span
                                    class="text-muted fw-normal">(Opsional)</span></label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-dark fw-semibold">Catatan Diagnosis</label>
                            <textarea name="diagnosis_notes" class="form-control"
                                rows="2"><?php echo e($anak->diagnosis_notes ?? ''); ?></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-dark fw-semibold">Alamat Domisili</label>
                            <textarea name="address" class="form-control" rows="2"><?php echo e($anak->address ?? ''); ?></textarea>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-top p-4 pt-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                        data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4"
                        style="background-color: #5b9cf6; border: none;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function togglePassword(inputId, iconContainer) {
        const input = document.getElementById(inputId);
        const icon = iconContainer.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bx-hide');
            icon.classList.add('bx-show');
        } else {
            input.type = 'password';
            icon.classList.remove('bx-show');
            icon.classList.add('bx-hide');
        }
    }
</script>

<?php if(session('parent_credentials')): ?>
    
    <div class="modal fade" id="modalKredensialBaru" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bx bx-check-circle text-success me-2"></i>Berhasil Update Password!
                    </h5>
                    
                </div>
                <div class="modal-body p-4 text-start">
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; border: 1px dashed #d9dee3;">
                        <p class="mb-2 text-dark">Silakan <strong>screenshot</strong> data ini untuk dikabarkan ke Wali
                            Kelas / Orang Tua:</p>
                        <hr class="my-2">
                        <p class="mb-1 text-dark">
                            <strong>Email:</strong>
                            <span style="color: #5b9cf6;"><?php echo e(session('parent_credentials')['email']); ?></span>
                        </p>
                        <p class="mb-0 text-dark">
                            <strong>Password Baru:</strong>
                            <span class="text-success fw-bold"><?php echo e(session('parent_credentials')['password']); ?></span>
                        </p>
                    </div>
                </div>
                <div class="modal-footer border-top p-4 pt-3">
                    <button type="button" class="btn btn-primary w-100 rounded-pill px-4" data-bs-dismiss="modal"
                        style="background-color: #5b9cf6; border: none;">
                        Tutup & Paham
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var modalElement = document.getElementById('modalKredensialBaru');
            var myModal = new bootstrap.Modal(modalElement);
            myModal.show();

            // EVENT LISTENER: Saat modal selesai ditutup (hidden)
            modalElement.addEventListener('hidden.bs.modal', function () {
                // 1. Cari dan hapus paksa elemen background (backdrop) yang nyangkut
                var backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(function (backdrop) {
                    backdrop.remove();
                });

                // 2. Hapus class 'modal-open' dari tag <body>
                document.body.classList.remove('modal-open');

                // 3. Kembalikan style body agar halaman bisa di-scroll kembali
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });
        });
    </script>
<?php endif; ?><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/anak/_modal-edit.blade.php ENDPATH**/ ?>