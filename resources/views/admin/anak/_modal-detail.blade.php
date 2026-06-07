<x-modal id="modalDetailAnak{{ $student->id }}" title="Detail Informasi Anak: {{ $student->name }}" size="modal-lg">
    <div class="modal-body text-wrap">
        <div class="row mb-4 align-items-center">
            <div class="col-md-3 text-center">
                <div class="avatar avatar-xl mx-auto mb-2" style="width: 100px; height: 100px;">
                    @if($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto {{ $student->name }}"
                            class="rounded-circle" style="object-fit: cover;">
                    @else
                        <span class="avatar-initial rounded-circle bg-label-secondary fs-2"><i
                                class="bx bx-user"></i></span>
                    @endif
                </div>
            </div>
            <div class="col-md-9">
                <h4 class="mb-1 fw-bold text-primary">{{ $student->name }}</h4>
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
                            {{ $student->birth_date ? \Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') : '-' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-0"><strong>Jenis Kelamin</strong></td>
                        <td>: {{ $student->gender ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="ps-0"><strong>Asal Sekolah</strong></td>
                        <td>: {{ $student->school_name ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <small class="text-light fw-semibold d-block mb-2">Data Keluarga & Medis</small>
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="ps-0" width="40%"><strong>Nama Ayah</strong></td>
                        <td>: {{ $student->father_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="ps-0"><strong>Nama Ibu</strong></td>
                        <td>: {{ $student->mother_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="ps-0"><strong>Akun Wali</strong></td>
                        <td>:
                            {{ $student->parent_id && \App\Models\User::find($student->parent_id) ? \App\Models\User::find($student->parent_id)->name : 'Belum Ditautkan' }}
                        </td>
                    </tr>
                    <tr>
                        <td class="ps-0"><strong>No. HP Wali</strong></td>
                        <td>: {{ $student->parent_phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="ps-0"><strong>Kebutuhan</strong></td>
                        <td>: <span class="badge bg-label-danger">{{ $student->special_needs ?? '-' }}</span>
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
                    {!! nl2br(e($student->diagnosis_notes ?? 'Tidak ada catatan diagnosis.')) !!}
                </div>
            </div>
            <div class="col-12">
                <strong class="d-block mb-1">Alamat Domisili:</strong>
                <div class="p-3 bg-light rounded text-secondary" style="min-height: 60px;">
                    {{ $student->address ?? 'Tidak ada data alamat.' }}
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer border-top pt-3">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
    </div>
</x-modal>