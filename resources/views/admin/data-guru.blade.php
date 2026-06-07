@extends('layouts.admin')

@section('content')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Manajemen Pengguna /</span> Data Guru & Terapis</h4>

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
            <h5 class="mb-0 text-white">Daftar Guru dan Terapis</h5>
            <button type="button" class="btn btn-sm btn-light text-primary" data-bs-toggle="modal"
                data-bs-target="#modalTambahGuru">
                <i class="bx bx-plus me-1"></i> Tambah Pegawai
            </button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Pegawai</th>
                        <th>Jabatan (Role)</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($gurus as $index => $guru)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $guru->name }}</strong></td>
                            <td>
                                @if($guru->role == 'shadow_pj') <span class="badge bg-label-primary">PJ Shadow</span>
                                @elseif($guru->role == 'shadow_teacher') <span class="badge bg-label-info">Guru Shadow</span>
                                @elseif($guru->role == 'therapist_homeroom') <span class="badge bg-label-warning">Wali Kelas
                                    (Terapis)</span>
                                @elseif($guru->role == 'therapist') <span class="badge bg-label-success">Terapis</span>
                                @else {{ $guru->role }} @endif
                            </td>
                            <td>{{ $guru->email }}</td>
                            <td>{{ $guru->phone ?? '-' }}</td>

                            {{-- 1. STATUS DI TABEL UTAMA (DINAMIS) --}}
                            <td>
                                @if($guru->is_active)
                                    <span class="badge bg-label-success">Aktif</span>
                                @else
                                    <span class="badge bg-label-danger">Nonaktif / Resign</span>
                                @endif
                            </td>

                            {{-- KOLOM AKSI (Mulai) --}}
                            <td>
                                <button type="button" class="btn btn-sm btn-icon btn-info" data-bs-toggle="modal"
                                    data-bs-target="#modalDetailGuru{{ $guru->id }}" title="Lihat Detail">
                                    <i class="bx bx-show"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalEditGuru{{ $guru->id }}" title="Edit">
                                    <i class="bx bx-edit-alt"></i>
                                </button>

                                <form action="{{ route('koor.destroyGuru', $guru->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Kamu yakin ingin menghapus data Pegawai {{ $guru->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus"><i
                                            class="bx bx-trash"></i></button>
                                </form>

                                {{-- 2. MODAL DETAIL GURU (DINAMIS) --}}
                                <x-modal id="modalDetailGuru{{ $guru->id }}" title="Detail Pegawai: {{ $guru->name }}">
                                    <div class="modal-body text-wrap text-start">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td width="35%" class="ps-0"><strong>Nama Lengkap</strong></td>
                                                <td>: {{ $guru->name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0"><strong>Jabatan</strong></td>
                                                <td>:
                                                    @if($guru->role == 'shadow_pj') PJ Shadow
                                                    @elseif($guru->role == 'shadow_teacher') Guru Shadow
                                                    @elseif($guru->role == 'therapist_homeroom') Wali Kelas (Terapis)
                                                    @elseif($guru->role == 'therapist') Terapis
                                                    @else {{ $guru->role }} @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0"><strong>Email Login</strong></td>
                                                <td>: {{ $guru->email }}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0"><strong>No. HP (WA)</strong></td>
                                                <td>: {{ $guru->phone ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0"><strong>Status Pegawai</strong></td>
                                                <td>:
                                                    @if($guru->is_active)
                                                        <span class="badge bg-label-success">Aktif</span>
                                                    @else
                                                        <span class="badge bg-label-danger">Nonaktif / Resign</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="modal-footer border-top pt-3">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </x-modal>

                                {{-- 3. MODAL EDIT GURU (MENYIMPAN STATUS DINAMIS) --}}
                                <x-modal id="modalEditGuru{{ $guru->id }}" title="Edit Data Guru & Terapis">
                                    <form action="{{ route('koor.updateGuru', $guru->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Lengkap <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ $guru->name }}"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Jabatan (Role) <span
                                                        class="text-danger">*</span></label>
                                                <select name="role" class="form-select" required>
                                                    <option value="shadow_pj" {{ $guru->role == 'shadow_pj' ? 'selected' : '' }}>
                                                        PJ Shadow</option>
                                                    <option value="shadow_teacher" {{ $guru->role == 'shadow_teacher' ? 'selected' : '' }}>Guru Shadow</option>
                                                    <option value="therapist_homeroom" {{ $guru->role == 'therapist_homeroom' ? 'selected' : '' }}>Wali Kelas (Terapis)</option>
                                                    <option value="therapist" {{ $guru->role == 'therapist' ? 'selected' : '' }}>
                                                        Terapis</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control" value="{{ $guru->email }}"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">No. HP (WhatsApp)</label>
                                                <input type="text" name="phone" class="form-control" value="{{ $guru->phone }}">
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label">Status Pegawai <span
                                                        class="text-danger">*</span></label>
                                                <select name="is_active" class="form-select" required>
                                                    <option value="1" {{ $guru->is_active ? 'selected' : '' }}>Aktif
                                                    </option>
                                                    <option value="0" {{ !$guru->is_active ? 'selected' : '' }}>Nonaktif /
                                                        Resign</option>
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
                            {{-- KOLOM AKSI (Selesai) --}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada data guru/terapis.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-modal id="modalTambahGuru" title="Tambah Pegawai Baru">
        <form action="{{ route('koor.storeGuru') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jabatan (Role) <span class="text-danger">*</span></label>
                    <select name="role" class="form-select" required>
                        <option value="">-- Pilih Jabatan --</option>
                        <option value="shadow_pj">PJ Shadow</option>
                        <option value="shadow_teacher">Guru Shadow</option>
                        <option value="therapist_homeroom">Wali Kelas (Terapis)</option>
                        <option value="therapist">Terapis</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-0">
                    <label class="form-label">No. HP (WhatsApp)</label>
                    <input type="text" name="phone" class="form-control">
                </div>
            </div>
            <div class="modal-footer border-top pt-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </x-modal>
@endsection