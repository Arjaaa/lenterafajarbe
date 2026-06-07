<x-modal id="modalEditAnak{{ $student->id }}" title="Edit Data Anak" size="modal-lg">
                                    <form action="{{ route('koor.updateAnak', $student->id) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-start">
                                            <div class="row mb-3">
                                                <div class="col-12">
                                                    <label class="form-label">Ganti Foto Profil (Biarkan kosong jika tidak
                                                        diubah)</label>
                                                    <input class="form-control" type="file" name="photo" accept="image/*" id="photo_edit_{{ $student->id }}">
                                                    <small id="error_photo_edit_{{ $student->id }}" class="text-danger d-none mt-1">Oops! Ukuran foto maksimal 2MB ya.</small>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Nama Lengkap <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ $student->name }}" required />
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Tanggal Lahir</label>
                                                    <input type="date" name="birth_date" class="form-control"
                                                        value="{{ $student->birth_date }}" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Jenis Kelamin</label>
                                                    <select name="gender" class="form-select">
                                                        <option value="">Pilih Jenis Kelamin</option>
                                                        <option value="Laki-laki" {{ $student->gender == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                                        <option value="Perempuan" {{ $student->gender == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Asal Sekolah</label>
                                                    <input type="text" name="school_name" class="form-control"
                                                        value="{{ $student->school_name }}" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Nama Ayah</label>
                                                    <input type="text" name="father_name" class="form-control"
                                                        value="{{ $student->father_name }}" />
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Nama Ibu</label>
                                                    <input type="text" name="mother_name" class="form-control"
                                                        value="{{ $student->mother_name }}" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Kebutuhan Khusus</label>
                                                    <input type="text" name="special_needs" class="form-control"
                                                        value="{{ $student->special_needs }}" />
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Tautkan ke Akun Wali (Ortu)</label>
                                                    <select name="parent_id" class="form-select">
                                                        <option value="">-- Pilih Orang Tua --</option>
                                                        @foreach($parents as $parent)
                                                            <option value="{{ $parent->id }}" {{ $student->parent_id == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">No. HP Orang Tua (Darurat)</label>
                                                    <input type="text" name="parent_phone" class="form-control"
                                                        value="{{ $student->parent_phone }}" />
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label class="form-label">Catatan Diagnosis</label>
                                                    <textarea name="diagnosis_notes" class="form-control"
                                                        rows="2">{{ $student->diagnosis_notes }}</textarea>
                                                </div>
                                                <div class="col-12 mb-0">
                                                    <label class="form-label">Alamat Domisili</label>
                                                    <textarea name="address" class="form-control"
                                                        rows="2">{{ $student->address }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top pt-3">
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </x-modal>