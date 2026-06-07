@extends('layouts.admin')

@section('content')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Manajemen Kelas /</span> Group Shadow Teacher</h4>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Yay! 🎉</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Oops! Ada yang salah:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-lentera-blue-light text-white mb-3">
            <h5 class="mb-0 text-white">Daftar Penugasan Shadow Teacher</h5>
            <button type="button" class="btn btn-sm btn-light text-primary" data-bs-toggle="modal"
                data-bs-target="#modalTambahShadow">
                <i class="bx bx-plus me-1"></i> Buat Group Baru
            </button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Group</th>
                        <th>Anak didampingi</th>
                        <th>PJ Shadow (Koor)</th>
                        <th>Guru Shadow (Partner)</th>
                        <th>Lokasi Sekolah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($shadowGroups as $index => $group)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $group->name }}</strong></td>

                            <td><span class="text-primary fw-semibold">{{ $group->student->name ?? 'Data Hilang' }}</span></td>
                            <td><span class="text-info fw-semibold">{{ $group->pic->name ?? 'Data Hilang' }}</span></td>
                            <td><span class="text-warning fw-semibold">{{ $group->partner->name ?? 'Data Hilang' }}</span></td>
                            <td>{{ $group->school_name }}</td>
                           

                            <td>
                                <a href="{{ route('koor.shadow.show', $group->id) }}" class="btn btn-sm btn-icon btn-info" title="Lihat Detail">
                                    <i class="bx bx-show"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalEditShadow{{ $group->id }}" title="Edit">
                                    <i class="bx bx-edit-alt"></i>
                                </button>

                                <form action="{{ route('koor.destroyShadowGroup', $group->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Kamu yakin ingin menghapus grup {{ $group->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus"><i
                                            class="bx bx-trash"></i></button>
                                </form>

                                <x-modal id="modalEditShadow{{ $group->id }}" title="Edit Group Shadow">
                                    <form action="{{ route('koor.updateShadowGroup', $group->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Group <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ $group->name }}"
                                                    placeholder="Contoh: Group Bermain A" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Anak yang Didampingi <span
                                                        class="text-danger">*</span></label>
                                                <select name="student_id" class="form-select" required>
                                                    <option value="">-- Pilih Anak --</option>
                                                    @foreach($students as $student)
                                                        {{-- LOGIKA EDIT ANAK --}}
                                                        @if(!in_array($student->id, $busyStudentIds) || $group->student_id == $student->id)
                                                            <option value="{{ $student->id }}" {{ $group->student_id == $student->id ? 'selected' : '' }}>
                                                                {{ $student->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Penanggung Jawab (PJ Shadow) <span
                                                        class="text-danger">*</span></label>
                                                <select name="pic_id" class="form-select" required>
                                                    <option value="">-- Pilih PJ --</option>
                                                    @foreach($pjs as $pj)
                                                        {{-- LOGIKA EDIT PJ 💡 --}}
                                                        @if(!in_array($pj->id, $assignedPicIds) || $group->pic_id == $pj->id)
                                                            <option value="{{ $pj->id }}" {{ $group->pic_id == $pj->id ? 'selected' : '' }}>
                                                                {{ $pj->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Guru Pendamping (Partner) <span
                                                        class="text-danger">*</span></label>
                                                <select name="partner_id" class="form-select" required>
                                                    <option value="">-- Pilih Guru Shadow --</option>
                                                    @foreach($partners as $partner)
                                                        {{-- LOGIKA EDIT GURU --}}
                                                        @if(!in_array($partner->id, $assignedPartnerIds) || $group->partner_id == $partner->id)
                                                            <option value="{{ $partner->id }}" {{ $group->partner_id == $partner->id ? 'selected' : '' }}>
                                                                {{ $partner->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label">Lokasi Sekolah <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="school_name" class="form-control"
                                                    value="{{ $group->school_name }}" placeholder="Contoh: TK Tunas Bangsa"
                                                    required>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top pt-3">
                                            <button type="button" class="btn btn-outline-secondary"
                                                data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </x-modal>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada Group Shadow Teacher yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-modal id="modalTambahShadow" title="Buat Group Shadow Baru">
        <form action="{{ route('koor.storeShadowGroup') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Group <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Group Bermain A" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Anak yang Didampingi <span class="text-danger">*</span></label>
                    <select name="student_id" class="form-select" required>
                        <option value="">-- Pilih Anak --</option>
                        @foreach($students as $student)
                            {{-- LOGIKA TAMBAH ANAK --}}
                            @if(!in_array($student->id, $busyStudentIds))
                                <option value="{{ $student->id }}">{{ $student->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Penanggung Jawab (PJ Shadow) <span class="text-danger">*</span></label>
                    <select name="pic_id" class="form-select" required>
                        <option value="">-- Pilih PJ --</option>
                        @foreach($pjs as $pj)
                            {{-- LOGIKA TAMBAH PJ 💡 --}}
                            @if(!in_array($pj->id, $assignedPicIds))
                                <option value="{{ $pj->id }}">{{ $pj->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Guru Pendamping (Partner) <span class="text-danger">*</span></label>
                    <select name="partner_id" class="form-select" required>
                        <option value="">-- Pilih Guru Shadow --</option>
                        @foreach($partners as $partner)
                            {{-- LOGIKA TAMBAH GURU --}}
                            @if(!in_array($partner->id, $assignedPartnerIds))
                                <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label">Lokasi Sekolah <span class="text-danger">*</span></label>
                    <input type="text" name="school_name" class="form-control" placeholder="Contoh: TK Tunas Bangsa"
                        required>
                </div>
            </div>
            <div class="modal-footer border-top pt-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Buat Group</button>
            </div>
        </form>
    </x-modal>
@endsection