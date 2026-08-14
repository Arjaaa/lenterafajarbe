<div class="modal fade" id="modalTambahGuru" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark"></i>Tambah Data
                    Guru Baru</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="<?php echo e(route('koor.storeGuru')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body p-4 text-start">

                    
                    <div class="alert d-flex align-items-center mb-4"
                        style="border-radius: 15px; background-color: #fff7ed; border: 1px solid #fed7aa; color: #c2410c;"
                        role="alert">
                        <i class="bx bx-error-circle fs-4 me-2" style="color: #ea580c;"></i>
                        <div style="font-size: 0.85rem;">
                            Guru yang baru ditambahkan akan berstatus <strong>Inactive</strong>. Penugasan Role &
                            Aktivasi dilakukan nanti di halaman Detail Guru.
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label text-dark fw-semibold">Nama Lengkap <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control style-input"
                                placeholder="Contoh: Bunga Lestari" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Jenis Kelamin <span
                                    class="text-danger">*</span></label>
                            <select name="gender" class="form-select style-input" required>
                                <option value="" selected disabled>-- Pilih --</option>
                                <option value="male">Laki-Laki (Male)</option>
                                <option value="female">Perempuan (Female)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Nomor HP / WA <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control style-input" placeholder="0812..."
                                required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Email Login <span
                                    class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control style-input"
                                placeholder="email@domain.com" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Password <span
                                    class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control style-input"
                                placeholder="Minimal 6 karakter" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label text-dark fw-semibold">Alamat Domisili <span
                                    class="text-danger">*</span></label>
                            <textarea name="address" class="form-control style-input" rows="2"
                                placeholder="Alamat lengkap..." required></textarea>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-top p-4 pt-3">
                    <button type="button" class="btn fw-semibold px-4 shadow-none" data-bs-dismiss="modal"
                        style="border-radius: 50px; border: 1px solid #cbd5e1; color: #64748b; background-color: white;">
                        Batal
                    </button>
                    <button type="submit" class="btn fw-semibold px-4 text-white shadow-none"
                        style="border-radius: 50px; background-color: #5b9cf6; border: none;">
                        Simpan Guru
                    </button>
                </div>
            </form>
        </div>
    </div>
</div><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/admin/guru/_modal-tambah.blade.php ENDPATH**/ ?>