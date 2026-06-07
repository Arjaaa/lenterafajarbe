@extends('layouts.admin')

@section('content')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Manajemen Kelas /</span> Data Kelas Terapis</h4>

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
            <h5 class="mb-0 text-white">Daftar Ruang Kelas</h5>
            <button type="button" class="btn btn-sm btn-light text-primary" data-bs-toggle="modal"
                data-bs-target="#modalTambahKelas">
                <i class="bx bx-plus me-1"></i> Tambah Kelas
            </button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kelas</th>
                        <th>Wali Kelas 1</th>
                        <th>Wali Kelas 2</th>
                        <th>Jumlah Anak</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($classes as $index => $kelas)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $kelas->name }}</strong></td>

                            <td>
                                @if($kelas->homeroomTeacher)
                                    <span class="text-primary fw-semibold">{{ $kelas->homeroomTeacher->name }}</span>
                                @else
                                    <span class="text-danger"><i>Belum Ditentukan</i></span>
                                @endif
                            </td>

                            <td>
                                @if($kelas->homeroomTeacher2)
                                    <span class="text-info fw-semibold">{{ $kelas->homeroomTeacher2->name }}</span>
                                @else
                                    <span class="text-muted"><i>Tidak Ada</i></span>
                                @endif
                            </td>

                            <td>
                                <span class="badge bg-label-info">{{ $kelas->students_count }} Siswa</span>
                            </td>

                            <td>
                                <a href="{{ route('koor.detailKelas', $kelas->id) }}" class="btn btn-sm btn-icon btn-info"
                                    title="Kelola Isi Kelas">
                                    <i class="bx bx-show"></i>
                                </a>

                                <button type="button" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalEditKelas{{ $kelas->id }}" title="Edit">
                                    <i class="bx bx-edit-alt"></i>
                                </button>

                                <form action="{{ route('koor.destroyKelas', $kelas->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Kamu yakin ingin menghapus {{ $kelas->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus"><i
                                            class="bx bx-trash"></i></button>
                                </form>

                                <x-modal id="modalEditKelas{{ $kelas->id }}" title="Edit Ruang Kelas">
                                    <form action="{{ route('koor.updateKelas', $kelas->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ $kelas->name }}"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Wali Kelas 1 (Utama) <span
                                                        class="text-danger">*</span></label>
                                                <select name="homeroom_teacher_id" class="form-select" required>
                                                    <option value="">-- Pilih Terapis 1 --</option>
                                                    @foreach($teachers as $teacher)
                                                        {{-- LOGIKA EDIT: Tampilkan jika guru belum punya kelas ATAU dia memang wali
                                                        di kelas INI --}}
                                                        @if(!in_array($teacher->id, $assignedTeacherIds) || $kelas->homeroom_teacher_id == $teacher->id || $kelas->homeroom_teacher_2_id == $teacher->id)
                                                            <option value="{{ $teacher->id }}" {{ $kelas->homeroom_teacher_id == $teacher->id ? 'selected' : '' }}>
                                                                {{ $teacher->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label">Wali Kelas 2 (Pendamping)</label>
                                                <select name="homeroom_teacher_2_id" class="form-select">
                                                    <option value="">-- Pilih Terapis 2 (Opsional) --</option>
                                                    @foreach($teachers as $teacher)
                                                        {{-- LOGIKA EDIT: Tampilkan jika guru belum punya kelas ATAU dia memang wali
                                                        di kelas INI --}}
                                                        @if(!in_array($teacher->id, $assignedTeacherIds) || $kelas->homeroom_teacher_id == $teacher->id || $kelas->homeroom_teacher_2_id == $teacher->id)
                                                            <option value="{{ $teacher->id }}" {{ $kelas->homeroom_teacher_2_id == $teacher->id ? 'selected' : '' }}>
                                                                {{ $teacher->name }}
                                                            </option>
                                                        @endif
                                                    @endforeach
                                                </select>
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
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada data kelas yang dibuat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-modal id="modalTambahKelas" title="Buat Ruang Kelas Baru">
        <form action="{{ route('koor.storeKelas') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Kelas Bintang" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Wali Kelas 1 (Utama) <span class="text-danger">*</span></label>
                    <select name="homeroom_teacher_id" class="form-select" required>
                        <option value="">-- Pilih Terapis 1 --</option>
                        @foreach($teachers as $teacher)
                            {{-- LOGIKA TAMBAH: Hanya tampilkan guru yang sama sekali belum masuk kelas mana pun --}}
                            @if(!in_array($teacher->id, $assignedTeacherIds))
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="mb-0">
                    <label class="form-label">Wali Kelas 2 (Pendamping)</label>
                    <select name="homeroom_teacher_2_id" class="form-select">
                        <option value="">-- Pilih Terapis 2 (Opsional) --</option>
                        @foreach($teachers as $teacher)
                            {{-- LOGIKA TAMBAH: Hanya tampilkan guru yang sama sekali belum masuk kelas mana pun --}}
                            @if(!in_array($teacher->id, $assignedTeacherIds))
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="modal-footer border-top pt-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Buat Kelas</button>
            </div>
        </form>
    </x-modal>
@endsection