@extends('layouts.admin')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Manajemen Pengguna /</span> Data Orang Tua</h4>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong></strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong></strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong> Ada yang salah:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center bg-lentera-blue-light text-white mb-0" style="border-radius: 20px 20px 0 0;">
            <h5 class="mb-0 text-white"><i class="bx bx-group me-2"></i>Daftar Orang Tua</h5>
            <button type="button" class="btn btn-sm btn-light text-primary fw-bold" data-bs-toggle="modal"
                data-bs-target="#modalTambahOrtu">
                <i class="bx bx-plus me-1"></i> Tambah Orang Tua
            </button>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Info Orang Tua</th>
                        <th>Kontak (WA)</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($parents as $index => $parent)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            
                            {{-- Kolom Info Orang Tua (Nama & Email digabung biar modern) --}}
                            <td>
                                <div class="d-flex justify-content-start align-items-center">
                                    <div class="avatar avatar-sm me-3">
                                        <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-user"></i></span>
                                    </div>
                                    <div class="d-flex flex-column">
                                        <strong class="text-dark">{{ $parent->name ?? 'Tanpa Nama' }}</strong>
                                        <small class="text-muted">{{ $parent->email ?? '-' }}</small>
                                    </div>
                                </div>
                            </td>

                            {{-- Kolom Kontak --}}
                            <td>
                                @if(!empty($parent->phone) || !empty($parent->phone_number))
                                    <span class="text-dark"><i class="bx bxl-whatsapp text-success me-1"></i> {{ $parent->phone ?? $parent->phone_number }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            {{-- Kolom Status --}}
                            <td><span class="badge bg-label-primary px-3 py-2">Aktif</span></td>

                            {{-- KOLOM AKSI --}}
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
                                    <button type="submit" class="btn btn-sm btn-icon btn-danger" title="Hapus">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </form>

                                {{-- MODAL DETAIL --}}
                                <x-modal id="modalDetailOrtu{{ $parent->id }}" title="Detail Orang Tua: {{ $parent->name }}">
                                    <div class="modal-body text-wrap text-start">
                                        <table class="table table-sm table-borderless mb-0">
                                            <tr>
                                                <td width="35%" class="ps-0 align-top"><strong>Nama Lengkap</strong></td>
                                                <td class="align-top">: {{ $parent->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 align-top"><strong>Email Login</strong></td>
                                                <td class="align-top">: {{ $parent->email ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 align-top"><strong>No. HP (WA)</strong></td>
                                                <td class="align-top">: {{ $parent->phone ?? $parent->phone_number ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 align-top"><strong>Status Akun</strong></td>
                                                <td class="align-top">: <span class="badge bg-label-success">Aktif</span></td>
                                            </tr>
                                            <tr>
                                                <td class="ps-0 align-top"><strong>Terdaftar Pada</strong></td>
                                                <td class="align-top">: {{ isset($parent->created_at) ? date('d M Y', strtotime($parent->created_at)) : '-' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="modal-footer border-top pt-3">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </x-modal>

                                {{-- MODAL EDIT --}}
                                <x-modal id="modalEditOrtu{{ $parent->id }}" title="Edit Data Orang Tua">
                                    <form action="{{ route('koor.updateOrangTua', $parent->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-start">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ $parent->name ?? '' }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control" value="{{ $parent->email ?? '' }}" required>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label">No. HP (WhatsApp)</label>
                                                <input type="text" name="phone" class="form-control" value="{{ $parent->phone ?? $parent->phone_number ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top pt-3">
                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </x-modal>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bx bx-group fs-1 d-block mb-2"></i>
                                Belum ada data orang tua yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL TAMBAH --}}
    <x-modal id="modalTambahOrtu" title="Tambah Akun Orang Tua">
        <form action="{{ route('koor.storeOrangTua') }}" method="POST">
            @csrf
            <div class="modal-body text-start">
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="Contoh: budi@gmail.com" required>
                </div>
                <div class="mb-0">
                    <label class="form-label">No. HP (WhatsApp)</label>
                    <input type="text" name="phone" class="form-control" placeholder="Contoh: 08123456789">
                </div>
            </div>
            <div class="modal-footer border-top pt-3">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Data</button>
            </div>
        </form>
    </x-modal>
</div>
@endsection