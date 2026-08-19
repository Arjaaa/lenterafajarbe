<div class="modal fade" id="modalTambahAnak" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark"><i class="bx bx-user-plus me-2 text-primary"></i>Registrasi
                    Siswa & Orang Tua</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('koor.storeAnak') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">

                    {{-- SEKSI 1: AKUN ORANG TUA --}}
                    <div class="d-flex align-items-center mb-3">
                        <div class="badge bg-label-primary p-2 rounded me-2"><i class="bx bx-shield-quarter"></i></div>
                        <h6 class="fw-bold text-dark mb-0">Informasi Akun & Orang Tua</h6>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Nama Ayah <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="father_name" class="form-control" placeholder="Nama lengkap ayah"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Nama Ibu <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="mother_name" class="form-control" placeholder="Nama lengkap ibu"
                                required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-semibold">No. WA / Telp <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="parent_phone" class="form-control" placeholder="0812..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-semibold">Email Login <span
                                    class="text-danger">*</span></label>
                            <input type="email" name="parent_email" class="form-control" placeholder="email@domain.com"
                                required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-dark fw-semibold">Password Akun <span
                                    class="text-danger">*</span></label>
                            {{-- FIX: Bungkus pakai input-group --}}
                            <div class="input-group">
                                <input type="password" name="parent_password" id="parentPasswordInput"
                                    class="form-control" placeholder="Min. 6 karakter" required>
                                <span class="input-group-text cursor-pointer bg-transparent" id="togglePasswordBtn"
                                    style="cursor: pointer;">
                                    <i class="bx bx-hide" id="togglePasswordIcon"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4" style="border-top: 2px dashed #e0ebfc;">

                    {{-- SEKSI 2: BIODATA ANAK --}}
                    <div class="d-flex align-items-center mb-3">
                        <div class="badge bg-label-success p-2 rounded me-2"><i class="bx bx-face"></i></div>
                        <h6 class="fw-bold text-dark mb-0">Biodata Lengkap Siswa</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Nama Lengkap Anak <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Nama lengkap siswa"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="birth_date" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Jenis Kelamin</label>
                            <select name="gender" class="form-select">
                                <option value="" selected disabled>-- Pilih --</option>
                                <option value="laki-laki">Laki-Laki</option>
                                <option value="perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Nama Sekolah</label>
                            <input type="text" name="school_name" class="form-control" placeholder="Nama sekolah asal">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Kebutuhan Khusus</label>
                            <select name="special_needs" class="form-select">
                                <option value="" selected disabled>-- Pilih Kondisi --</option>
                                <option value="autis">Autis</option>
                                <option value="adhd">ADHD</option>
                                <option value="down_syndrome">Down Syndrome</option>
                                <option value="lambat_belajar">Lambat Belajar</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Foto Anak</label>
                            <input type="file" name="photo" id="inputPhoto" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-dark fw-semibold">Catatan Diagnosis</label>
                            <textarea name="diagnosis_notes" class="form-control" rows="2"
                                placeholder="Tuliskan catatan medis atau hasil diagnosis..."></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-dark fw-semibold">Alamat Domisili</label>
                            <textarea name="address" class="form-control" rows="2"
                                placeholder="Alamat rumah lengkap..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-top p-4 pt-3">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4"
                            style="background-color: #5b9cf6; border: none;">Simpan Perubahan</button>
                    </div>

                </div>

            </form>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('parentPasswordInput');
        const toggleIcon = document.getElementById('togglePasswordIcon');

        if (togglePasswordBtn && passwordInput) {
            togglePasswordBtn.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                if (type === 'text') {
                    toggleIcon.classList.remove('bx-hide');
                    toggleIcon.classList.add('bx-show');
                } else {
                    toggleIcon.classList.remove('bx-show');
                    toggleIcon.classList.add('bx-hide');
                }
            });
        }
    });
</script>
<script>
    document.getElementById('inputPhoto').addEventListener('change', function () {
        const file = this.files[0];

        if (file) {
            // Ukuran file dalam bytes (2MB = 2 * 1024 * 1024 = 2097152 bytes)
            const maxSize = 2 * 1024 * 1024;

            if (file.size > maxSize) {
                // Munculkan alert (Bisa diganti SweetAlert kalau kamu pakai)
                alert(' Ukuran foto terlalu besar. Maksimal ukuran file adalah 2MB.');

                // Reset input file biar kosong lagi
                this.value = '';
            }
        }
    });
</script>