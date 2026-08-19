{{-- ========================================================== --}}
{{-- MODAL EDIT ANAK & ORTU (API DRIVEN) --}}
{{-- ========================================================== --}}
<div class="modal fade" id="modalEditAnak{{ $anak->id ?? 0 }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-bottom p-4">
                <h5 class="modal-title fw-bold text-dark"><i class="bx bx-edit text-primary me-2"></i>Edit Data Siswa &
                    Orang Tua</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('koor.updateAnak', $anak->id ?? 0) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="modal-body p-4 text-start" style="white-space: normal !important;">

                    {{-- SEKSI 1: DATA ORANG TUA --}}
                    <div class="d-flex align-items-center mb-3">
                        <div class="badge bg-label-primary p-2 rounded me-2"><i class="bx bx-user"></i></div>
                        <h6 class="fw-bold text-dark mb-0">Informasi Orang Tua</h6>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Nama Ayah <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="father_name" class="form-control"
                                value="{{ $anak->father_name ?? '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Nama Ibu <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="mother_name" class="form-control"
                                value="{{ $anak->mother_name ?? '' }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-dark fw-semibold">No. WA / Telp Orang Tua <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="parent_phone" class="form-control"
                                value="{{ $anak->parent_phone ?? $anak->parent->phone ?? '' }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-dark fw-semibold">Password Akun Orang Tua <span
                                    class="text-muted fw-normal">(Opsional)</span></label>
                            <div class="input-group input-group-merge">
                                <input type="password" name="parent_password" id="parentPassword{{ $anak->id ?? 0 }}"
                                    class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
                                <span class="input-group-text cursor-pointer"
                                    onclick="togglePassword('parentPassword{{ $anak->id ?? 0 }}', this)">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                            <small class="text-muted">Minimal 6 karakter jika ingin mengganti password.</small>
                        </div>
                    </div>

                    <hr class="my-4" style="border-top: 2px dashed #e0ebfc;">

                    {{-- SEKSI 2: BIODATA ANAK --}}
                    <div class="d-flex align-items-center mb-3">
                        <div class="badge bg-label-success p-2 rounded me-2"><i class="bx bx-face"></i></div>
                        <h6 class="fw-bold text-dark mb-0">Biodata Siswa</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Nama Lengkap Anak <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $anak->name ?? '' }}"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="birth_date" class="form-control"
                                value="{{ isset($anak->birth_date) ? date('Y-m-d', strtotime($anak->birth_date)) : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Jenis Kelamin</label>
                            <select name="gender" class="form-select">
                                <option value="laki-laki" {{ ($anak->gender ?? '') == 'laki-laki' ? 'selected' : '' }}>
                                    Laki-Laki</option>
                                <option value="perempuan" {{ ($anak->gender ?? '') == 'perempuan' ? 'selected' : '' }}>
                                    Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Nama Sekolah</label>
                            <input type="text" name="school_name" class="form-control"
                                value="{{ $anak->school_name ?? '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-dark fw-semibold">Kebutuhan Khusus</label>
                            <select name="special_needs" class="form-select">
                                <option value="autis" {{ ($anak->special_needs ?? '') == 'autis' ? 'selected' : '' }}>
                                    Autis</option>
                                <option value="adhd" {{ ($anak->special_needs ?? '') == 'adhd' ? 'selected' : '' }}>ADHD
                                </option>
                                <option value="down_syndrome" {{ ($anak->special_needs ?? '') == 'down_syndrome' ? 'selected' : '' }}>Down Syndrome</option>
                                <option value="lambat_belajar" {{ ($anak->special_needs ?? '') == 'lambat_belajar' ? 'selected' : '' }}>Lambat Belajar</option>
                                <option value="lainnya" {{ ($anak->special_needs ?? '') == 'lainnya' ? 'selected' : '' }}>
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
                                rows="2">{{ $anak->diagnosis_notes ?? '' }}</textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-dark fw-semibold">Alamat Domisili</label>
                            <textarea name="address" class="form-control" rows="2">{{ $anak->address ?? '' }}</textarea>
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
</div>{{-- 1. Fungsi untuk Show/Hide Password di Modal Edit --}}
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
{{-- 2. POPUP KREDENSIAL MENGGUNAKAN BOOTSTRAP MODAL --}}
@if(session('parent_credentials'))
    {{-- HTML Modal-nya --}}
    <div class="modal fade" id="modalKredensialBaru" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 20px; border: none;">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="bx bx-check-circle text-success me-2"></i>Berhasil Update Password!
                    </h5>
                    {{-- <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                        aria-label="Close"></button> --}}
                </div>
                <div class="modal-body p-4 text-start">
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 10px; border: 1px dashed #d9dee3;">
                        <p class="mb-2 text-dark">Silakan <strong>screenshot</strong> data ini untuk dikabarkan ke Wali
                            Kelas / Orang Tua:</p>
                        <hr class="my-2">
                        <p class="mb-1 text-dark">
                            <strong>Email:</strong>
                            <span style="color: #5b9cf6;">{{ session('parent_credentials')['email'] }}</span>
                        </p>
                        <p class="mb-0 text-dark">
                            <strong>Password Baru:</strong>
                            <span class="text-success fw-bold">{{ session('parent_credentials')['password'] }}</span>
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

    {{-- Script untuk auto-show dan FIX background nyangkut --}}
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
@endif