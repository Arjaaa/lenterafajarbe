<x-modal id="modalTambahAnak" title="Tambah Data Anak Baru" size="modal-lg">
    <ul class="nav nav-tabs card-header-tabs mb-3" role="tablist">
        <li class="nav-item">
            <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab"
                data-bs-target="#tab-ortu-lama" aria-controls="tab-ortu-lama" aria-selected="true"
                onclick="setOrtuStatus('lama')">
                <i class="bx bx-user-check me-1"></i> Orang Tua Sudah Terdaftar
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tab-ortu-baru"
                aria-controls="tab-ortu-baru" aria-selected="false" onclick="setOrtuStatus('baru')">
                <i class="bx bx-user-plus me-1"></i> Orang Tua Baru / Belum Ada
            </button>
        </li>
    </ul>

    <form action="{{ route('koor.storeAnak') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="ortu_status" id="ortu_status" value="lama">

        <div class="tab-content p-0">

            <div class="tab-pane fade show active" id="tab-ortu-lama" role="tabpanel">
                <div class="modal-body text-start pt-0">
                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary"><i class="bx bx-face me-1"></i> Data Anak
                    </h6>
                    <div class="mb-3">
                        <label class="form-label">Foto Profil Anak (Opsional)</label>
                        <input class="form-control" type="file" name="photo_lama" id="photo_lama" accept="image/*">
                        <small id="error_photo_lama" class="text-danger d-none mt-1">Oops! Ukuran foto maksimal 2MB
                            ya.</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap Anak <span class="text-danger">*</span></label>
                            <input type="text" name="name_lama" id="name_lama" class="form-control"
                                placeholder="Masukkan nama anak" required />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="birth_date_lama" class="form-control" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="gender_lama" class="form-select">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Asal Sekolah</label>
                            <input type="text" name="school_name_lama" class="form-control"
                                placeholder="Contoh: SD Lentera" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kebutuhan Khusus</label>
                            <input type="text" name="special_needs_lama" class="form-control"
                                placeholder="Contoh: Autisme, ADHD" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pilih Akun Hubungan Orang Tua <span
                                    class="text-danger">*</span></label>
                            <select name="parent_id" id="parent_id" class="form-select" required>
                                <option value="">-- Pilih Akun Ortu --</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Catatan Diagnosis</label>
                            <textarea name="diagnosis_notes_lama" class="form-control" rows="2"
                                placeholder="Catatan medis..."></textarea>
                        </div>
                        <div class="col-12 mb-0">
                            <label class="form-label">Alamat Domisili</label>
                            <textarea name="address_lama" class="form-control" rows="2"
                                placeholder="Alamat lengkap..."></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-ortu-baru" role="tabpanel">
                <div class="modal-body text-start pt-0">
                    <h6 class="fw-bold mb-3 border-bottom pb-2 text-primary"><i class="bx bx-face me-1"></i> Data Anak
                    </h6>
                    <div class="mb-3">
                        <label class="form-label">Foto Profil Anak (Opsional)</label>
                        <input class="form-control" type="file" name="photo_baru" id="photo_baru" accept="image/*">
                        <small id="error_photo_baru" class="text-danger d-none mt-1">Oops! Ukuran foto maksimal 2MB
                            ya.</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap Anak <span class="text-danger">*</span></label>
                            <input type="text" name="name_baru" id="name_baru" class="form-control"
                                placeholder="Masukkan nama anak" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="birth_date_baru" class="form-control" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="gender_baru" class="form-select">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Asal Sekolah</label>
                            <input type="text" name="school_name_baru" class="form-control"
                                placeholder="Contoh: SD Lentera" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kebutuhan Khusus</label>
                            <input type="text" name="special_needs_baru" class="form-control"
                                placeholder="Contoh: Autisme, ADHD" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Catatan Diagnosis</label>
                            <textarea name="diagnosis_notes_baru" class="form-control" rows="2"
                                placeholder="Catatan medis..."></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat Domisili</label>
                            <textarea name="address_baru" class="form-control" rows="2"
                                placeholder="Alamat lengkap..."></textarea>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-4 mb-3 border-bottom pb-2 text-primary"><i class="bx bx-group me-1"></i> Form
                        Full Data Orang Tua</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Ayah <span class="text-danger">*</span></label>
                            <input type="text" name="father_name" id="father_name" class="form-control"
                                placeholder="Nama Lengkap Ayah" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Ibu <span class="text-danger">*</span></label>
                            <input type="text" name="mother_name" id="mother_name" class="form-control"
                                placeholder="Nama Lengkap Ibu" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email Akun Login Wali <span class="text-danger">*</span></label>
                            <input type="email" name="parent_email" id="parent_email" class="form-control"
                                placeholder="contoh: ortu.budi@email.com" />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. HP / WhatsApp Wali <span class="text-danger">*</span></label>
                            <input type="text" name="parent_phone" id="parent_phone" class="form-control"
                                placeholder="0812xxxxxxxx" />
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Set Password Akun Ortu <span class="text-danger">*</span></label>
                        <input type="password" name="parent_password" id="parent_password" class="form-control"
                            placeholder="Masukkan password untuk login orang tua" />
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer border-top pt-3">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Data</button>
        </div>
    </form>
</x-modal>