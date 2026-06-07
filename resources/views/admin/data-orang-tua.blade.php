@extends('layouts.admin')

@section('content')
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Manajemen Pengguna /</span> Data Orang Tua</h4>

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
            <h5 class="mb-0 text-white">Daftar Data Orang Tua</h5>
            {{-- <button type="button" class="btn btn-sm btn-light text-primary" data-bs-toggle="modal"
                data-bs-target="#modalTambahOrtu">
                <i class="bx bx-plus me-1"></i> Tambah Orang Tua
            </button> --}}
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Orang Tua</th>
                        <th>Email (Akun Login)</th>
                        <th>No. HP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($parents as $index => $parent)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $parent->name }}</strong></td>
                            <td>{{ $parent->email }}</td>
                            <td>{{ $parent->phone ?? '-' }}</td>

                            {{-- KOLOM AKSI (Mulai) --}}
                            <td>
                                <button type="button" class="btn btn-sm btn-icon btn-info" data-bs-toggle="modal"
                                    data-bs-target="#modalDetailOrtu{{ $parent->id }}" title="Lihat Detail">
                                    <i class="bx bx-show"></i>
                                </button>

                                <button type="button" class="btn btn-sm btn-icon btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#modalEditOrtu{{ $parent->id }}" title="Edit">
                                    <i class="bx bx-edit-alt"></i>
                                </button>

                                <form action="{{ route('koor.destroyOrangTua', $parent->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Kamu yakin ingin menghapus data Bapak/Ibu {{ $parent->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus"><i
                                            class="bx bx-trash"></i></button>
                                </form>

                                <x-modal id="modalDetailOrtu{{ $parent->id }}" title="Detail Orang Tua: {{ $parent->name }}">
                                    <div class="modal-body text-wrap">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td width="35%" class="ps-0"><strong>Nama Lengkap</strong></td>
                                                <td>: {{ $parent->name }}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0"><strong>Email Login</strong></td>
                                                <td>: {{ $parent->email }}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0"><strong>No. HP (WA)</strong></td>
                                                <td>: {{ $parent->phone ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0"><strong>Status Akun</strong></td>
                                                <td>: <span class="badge bg-label-success">Aktif</span></td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0"><strong>Terdaftar Pada</strong></td>
                                                <td>: {{ $parent->created_at ? $parent->created_at->format('d M Y') : '-' }}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="modal-footer border-top pt-3">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </x-modal>

                                <x-modal id="modalEditOrtu{{ $parent->id }}" title="Edit Data Orang Tua">
                                    <form action="{{ route('koor.updateOrangTua', $parent->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Lengkap <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ $parent->name }}"
                                                    required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control"
                                                    value="{{ $parent->email }}" required>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label">No. HP (WhatsApp)</label>
                                                <input type="text" name="phone" class="form-control"
                                                    value="{{ $parent->phone }}">
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
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada data orang tua.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-modal id="modalTambahOrtu" title="Tambah Akun Orang Tua">
        <form action="{{ route('koor.storeOrangTua') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required>
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