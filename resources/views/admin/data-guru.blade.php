@extends('layouts.admin')

@section('content')
    {{-- CSS Khusus Striping Tabel Sesuai Desain --}}
    <style>
        .custom-table-striped tbody tr:nth-of-type(even) {
            background-color: #f4f8ff !important;
        }

        .custom-table-striped tbody tr td {
            border-bottom: none !important;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- ======================================
        HEADER: Judul & Tanggal Hari Ini
        ====================================== --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0 text-dark">Daftar Semua Guru</h4>
            <div class="bg-white px-4 py-2 d-flex align-items-center"
                style="border-radius: 50px; border: 1px solid #f0f0f0;">
                <i class="bx bx-calendar text-success me-2 fs-5"
                    style="color: #4ade80 !important; background: #e8f5e9; padding: 5px; border-radius: 50%;"></i>
                <span class="fw-semibold text-dark">
                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d - m - Y') }}
                </span>
            </div>
        </div>


        <div class="row g-4 mb-4 text-center">
            {{-- Card 1: Wali Kelas --}}
            <div class="col-md-3 col-6">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); border: 1px solid #f0f4f9;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="mb-3 rounded d-flex align-items-center justify-content-center"
                            style="width: 55px; height: 55px; background-color: #ffe6e6;">
                            <i class="bx bx-chalkboard fs-3" style="color: #ff5b5c;"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-1">{{ $cardStats['total_wali_kelas'] ?? 0 }}</h2>
                        <span class="text-muted fw-medium" style="font-size: 0.85rem;">Total Wali Kelas</span>
                    </div>
                </div>
            </div>

            {{-- Card 2: Terapis 1 on 1 --}}
            <div class="col-md-3 col-6">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); border: 1px solid #f0f4f9;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="mb-3 rounded d-flex align-items-center justify-content-center"
                            style="width: 55px; height: 55px; background-color: #e8f5e9;">
                            <i class="bx bx-user-voice fs-3" style="color: #4ade80;"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-1">{{ $cardStats['total_terapis'] ?? 0 }}</h2>
                        <span class="text-muted fw-medium" style="font-size: 0.85rem;">Total Terapis 1 on 1</span>
                    </div>
                </div>
            </div>

            {{-- Card 3: Shadow Teacher --}}
            <div class="col-md-3 col-6">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); border: 1px solid #f0f4f9;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="mb-3 rounded d-flex align-items-center justify-content-center"
                            style="width: 55px; height: 55px; background-color: #f3e8ff;">
                            <i class="bx bx-id-card fs-3" style="color: #a855f7;"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-1">{{ $cardStats['total_shadow'] ?? 0 }}</h2>
                        <span class="text-muted fw-medium" style="font-size: 0.85rem;">Total Shadow Teacher</span>
                    </div>
                </div>
            </div>

            {{-- Card 4: Belum Ada Role / Inactive (BARU) --}}
            <div class="col-md-3 col-6">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); border: 1px solid #f0f4f9;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="mb-3 rounded d-flex align-items-center justify-content-center"
                            style="width: 55px; height: 55px; background-color: #fff7ed;">
                            <i class="bx bx-user-x fs-3" style="color: #f59e0b;"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-1">{{ $cardStats['total_belum_ditugaskan'] ?? 0 }}</h2>
                        <span class="text-muted fw-medium" style="font-size: 0.85rem;">Belum Ditugaskan</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ======================================
        NOTIFIKASI ERROR / SUCCESS
        ====================================== --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 15px;">
                <strong></strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 15px;">
                <strong>Ada yang salah:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- ======================================
        BUNGKUS CARD UTAMA & TABEL
        ====================================== --}}
        <div class="card bg-white"
            style="border: 2px solid #e0ebfc; border-radius: 20px; box-shadow: none; overflow: hidden;">

            {{-- HEADER TABLE: Search Bar, Filter Role & Tombol Add --}}

            <div
                class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center bg-white border-bottom p-4">

                {{-- Form untuk Search --}}
                {{-- Form untuk Search & Bawa Filter --}}
                <form action="{{ route('koor.dataGuru') }}" method="GET"
                    class="d-flex align-items-center gap-2 mb-3 mb-md-0 w-100" style="max-width: 600px;">

                    {{-- Bawa parameter role & status kalau lagi ngetik pencarian --}}
                    @if(request()->filled('role'))
                        <input type="hidden" name="role" value="{{ request('role') }}">
                    @endif
                    @if(request()->filled('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif

                    <div class="input-group"
                        style="border-radius: 50px; overflow: hidden; border: 1px solid #a3c7fb; background: white; max-width: 350px;">
                        <span class="input-group-text bg-transparent border-0 pe-1">
                            <i class="bx bx-search" style="color: #1e293b;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-0 shadow-none px-2"
                            placeholder="Cari nama/email..." value="{{ request('search') }}"
                            style="background: transparent;">
                    </div>

                    <button type="submit" class="btn rounded-pill px-4 fw-semibold text-white shadow-none flex-shrink-0"
                        style="background-color: #5b9cf6; border: none;">Cari</button>

                    <button type="button" class="btn rounded-pill px-4 fw-semibold text-white shadow-none flex-shrink-0"
                        style="background-color: #f59e0b; border: none;" data-bs-toggle="modal"
                        data-bs-target="#modalFilterGuru">
                        <i class="bx bx-filter-alt me-1"></i> Filter
                    </button>

                    {{-- FIX: Tombol Reset Muncul Kalau Ada Filter Apapun --}}
                    @if(request()->filled('role') || request()->filled('search') || request()->filled('status'))
                        <a href="{{ route('koor.dataGuru') }}"
                            class="btn rounded-pill px-3 fw-semibold text-dark shadow-none d-flex align-items-center"
                            style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                            <i class="bx bx-x me-1"></i> Reset
                        </a>
                    @endif
                </form>

                <button type="button" class="btn rounded-pill fw-semibold px-4 text-white shadow-none"
                    data-bs-toggle="modal" data-bs-target="#modalTambahGuru"
                    style="background-color: #5b9cf6; border: none;">
                    <i class="bx bx-plus-circle me-1 fs-5"></i> Add Teacher
                </button>
            </div>

            {{-- ISI TABEL --}}
            <div class="table-responsive text-nowrap">
                <table class="table custom-table-striped table-borderless">
                    <thead style="border-bottom: 2px solid #f0f4f9;">
                        <tr>
                            <th class="text-center py-3 text-muted fw-semibold">No</th>
                            <th class="py-3 text-muted fw-semibold">Nama Pegawai</th>
                            <th class="py-3 text-muted fw-semibold">Jabatan (Role)</th>
                            <th class="py-3 text-muted fw-semibold">Email</th>
                            <th class="py-3 text-muted fw-semibold">Status</th>
                            <th class="text-center py-3 text-muted fw-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($gurus as $index => $guru)
                            <tr>
                                <td class="text-center align-middle text-dark fw-medium">{{ $index + 1 }}</td>
                                <td class="align-middle text-dark fw-bold">{{ $guru->name ?? '-' }}</td>
                                <td class="align-middle text-dark">
                                    @if(($guru->role ?? '') == 'shadow_pj') PJ Shadow
                                    @elseif(($guru->role ?? '') == 'shadow_teacher') Guru Shadow
                                    @elseif(($guru->role ?? '') == 'therapist_homeroom') Wali Kelas (Terapis)
                                    @elseif(($guru->role ?? '') == 'therapist') Terapis
                                    @elseif(!empty($guru->role)) <span
                                        class="text-capitalize">{{ str_replace('_', ' ', $guru->role) }}</span>
                                    @else <span class="text-muted fst-italic">Belum ada role</span>
                                    @endif
                                </td>
                                <td class="align-middle text-dark">{{ $guru->email ?? '-' }}</td>

                                {{-- FIX 1: KOLOM STATUS --}}
                                <td class="align-middle">
                                    @if(!empty($guru->role) || !empty($guru->is_active))
                                        <span class="badge rounded-pill bg-label-success px-3">Aktif</span>
                                    @else
                                        <span class="badge rounded-pill px-3"
                                            style="background-color: #ffe6e6; color: #ff5b5c;">Inactive</span>
                                    @endif
                                </td>

                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                        style="font-size: 0.75rem; background-color: #4ade80; border: none;"
                                        data-bs-toggle="modal" data-bs-target="#modalDetailGuru{{ $guru->id ?? 0 }}"
                                        title="Lihat Detail">
                                        <i class="bx bx-info-circle me-1" style="font-size: 0.9rem;"></i> View
                                    </button>

                                    {{-- FIX 2: LOGIKA TOMBOL ACTION --}}
                                    @if(empty($guru->role))
                                        {{-- Wujud Tombol Jika Belum Di-assign (Warna Orange) --}}
                                        <button type="button" class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                            style="font-size: 0.75rem; background-color: #f59e0b; border: none;"
                                            data-bs-toggle="modal" data-bs-target="#modalEditGuru{{ $guru->id ?? 0 }}"
                                            title="Assign Role & Aktifkan">
                                            <i class="bx bx-check-shield me-1" style="font-size: 0.9rem;"></i> Aktivasi
                                        </button>
                                    @else
                                        {{-- Wujud Tombol Jika Sudah Aktif (Warna Biru Soft) --}}
                                        <button type="button" class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                            style="font-size: 0.75rem; background-color: #60a5fa; border: none;"
                                            data-bs-toggle="modal" data-bs-target="#modalEditGuru{{ $guru->id ?? 0 }}" title="Edit">
                                            <i class="bx bx-edit-alt me-1" style="font-size: 0.9rem;"></i> Edit
                                        </button>
                                    @endif

                                    <form action="{{ route('koor.destroyGuru', $guru->id ?? 0) }}" method="POST"
                                        class="d-inline" onsubmit="return confirm('Kamu yakin ingin menghapus data Pegawai?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none"
                                            style="font-size: 0.75rem; background-color: #f87171; border: none;" title="Hapus"
                                            data-bs-toggle="modal" data-bs-target="#modalDeleteGuru{{ $guru->id ?? 0 }}">
                                            <i class="bx bx-trash me-1" style="font-size: 0.9rem;"></i> Delete
                                        </button>
                                    </form>

                                    {{-- MODAL DETAIL GURU --}}
                                    <x-modal id="modalDetailGuru{{ $guru->id ?? 0 }}"
                                        title="Detail Pegawai: {{ $guru->name ?? '-' }}">
                                        <div class="modal-body text-wrap text-start">
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td width="35%" class="ps-0"><strong>Nama Lengkap</strong></td>
                                                    <td>: {{ $guru->name ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0"><strong>Jabatan</strong></td>
                                                    <td>:
                                                        @if(($guru->role ?? '') == 'shadow_pj') PJ Shadow
                                                        @elseif(($guru->role ?? '') == 'shadow_teacher') Guru Shadow
                                                        @elseif(($guru->role ?? '') == 'therapist_homeroom') Wali Kelas
                                                            (Terapis)
                                                        @elseif(($guru->role ?? '') == 'therapist') Terapis
                                                        @elseif(!empty($guru->role)) <span
                                                            class="text-capitalize">{{ str_replace('_', ' ', $guru->role) }}</span>
                                                        @else <span class="text-muted fst-italic">Belum ada role</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0"><strong>Email Login</strong></td>
                                                    <td>: {{ $guru->email ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0"><strong>No. HP (WA)</strong></td>
                                                    <td>: {{ $guru->phone ?? '-' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="ps-0"><strong>Status Pegawai</strong></td>
                                                    <td>:
                                                        {{-- FIX 3: STATUS DI MODAL DETAIL --}}
                                                        @if(!empty($guru->role) || !empty($guru->is_active))
                                                            <span class="badge bg-label-success px-3 rounded-pill">Aktif</span>
                                                        @else
                                                            <span class="badge px-3 rounded-pill"
                                                                style="background-color: #ffe6e6; color: #ff5b5c;">Inactive</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="modal-footer border-top pt-3">
                                            <button type="button" class="btn btn-secondary rounded-pill"
                                                data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </x-modal>

                                    {{-- MODAL EDIT GURU --}}
                                    <x-modal id="modalEditGuru{{ $guru->id ?? 0 }}" title="Edit Data Guru & Terapis">
                                        <form action="{{ route('koor.updateGuru', $guru->id ?? 0) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body text-start">
                                                <div class="mb-3">
                                                    <label class="form-label text-dark fw-semibold">Nama Lengkap <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ $guru->name ?? '' }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label text-dark fw-semibold">Jabatan (Role) <span
                                                            class="text-danger">*</span></label>
                                                    <select name="role" class="form-select" required>
                                                        <option value="" {{ empty($guru->role) ? 'selected' : '' }} disabled>--
                                                            Pilih Role --</option>
                                                        <option value="shadow_pj" {{ ($guru->role ?? '') == 'shadow_pj' ? 'selected' : '' }}>PJ Shadow</option>
                                                        <option value="shadow_teacher" {{ ($guru->role ?? '') == 'shadow_teacher' ? 'selected' : '' }}>Guru Shadow</option>
                                                        <option value="therapist_homeroom" {{ ($guru->role ?? '') == 'therapist_homeroom' ? 'selected' : '' }}>Wali Kelas (Terapis)
                                                        </option>
                                                        <option value="therapist" {{ ($guru->role ?? '') == 'therapist' ? 'selected' : '' }}>Terapis</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label text-dark fw-semibold">Email <span
                                                            class="text-danger">*</span></label>
                                                    <input type="email" name="email" class="form-control"
                                                        value="{{ $guru->email ?? '' }}" required>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label text-dark fw-semibold">No. HP (WhatsApp)</label>
                                                    <input type="text" name="phone" class="form-control"
                                                        value="{{ $guru->phone ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top pt-3">
                                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                                                    data-bs-dismiss="modal">Batal</button>
                                                {{-- FIX 4: TEKS TOMBOL DINAMIS --}}
                                                <button type="submit" class="btn btn-primary rounded-pill px-4"
                                                    style="background-color: #5b9cf6; border: none;">
                                                    {{ empty($guru->role) ? 'Simpan & Aktifkan' : 'Simpan Perubahan' }}
                                                </button>
                                            </div>
                                        </form>
                                    </x-modal>

                                    {{-- =============================================== --}}
                                    {{-- MODAL KONFIRMASI DELETE GURU --}}
                                    {{-- =============================================== --}}
                                    <div class="modal fade" id="modalDeleteGuru{{ $guru->id ?? 0 }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered modal-sm">
                                            <div class="modal-content text-center" style="border-radius: 20px; border: none;">
                                                {{-- FIX: Tambahin class text-wrap di modal-body --}}
                                                <div class="modal-body p-4 text-wrap">

                                                    {{-- Icon Trash Merah --}}
                                                    <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle"
                                                        style="width: 60px; height: 60px; background-color: #ffe6e6;">
                                                        <i class="bx bx-trash fs-2" style="color: #ff5b5c;"></i>
                                                    </div>

                                                    <h5 class="fw-bold text-dark mb-2">Hapus Pegawai?</h5>

                                                    {{-- FIX: Tambahin class text-wrap juga di tag p biar makin aman --}}
                                                    <p class="text-muted mb-4 text-wrap" style="font-size: 0.9rem;">
                                                        Data <strong>{{ $guru->name ?? 'Pegawai' }}</strong> akan dihapus
                                                        permanen dan tidak dapat dikembalikan.
                                                    </p>

                                                    <form action="{{ route('koor.destroyGuru', $guru->id ?? 0) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <div class="d-flex justify-content-center gap-2">
                                                            <button type="button" class="btn rounded-pill px-4 shadow-none"
                                                                data-bs-dismiss="modal"
                                                                style="background-color: #f1f5f9; color: #475569; border: none; font-weight: 600;">
                                                                Batal
                                                            </button>
                                                            <button type="submit"
                                                                class="btn rounded-pill px-4 text-white shadow-none"
                                                                style="background-color: #ff5b5c; border: none; font-weight: 600;">
                                                                Hapus
                                                            </button>
                                                        </div>
                                                    </form>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bx bx-folder-open fs-1 d-block mb-2"></i> Belum ada data guru/terapis.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION BAWAH --}}
            <div class="card-footer bg-white border-top text-center py-4">
                <div class="d-inline-flex align-items-center bg-white px-3 py-2 border shadow-sm"
                    style="border-radius: 50px;">
                    <a href="{{ ($pagination['current_page'] ?? 1) > 1 ? request()->fullUrlWithQuery(['page' => ($pagination['current_page'] ?? 1) - 1]) : 'javascript:void(0)' }}"
                        class="text-dark text-decoration-none me-3 {{ ($pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 pe-none' : '' }}">
                        <i class="bx bx-chevron-left fs-5"></i>
                    </a>
                    <span class="fw-semibold text-dark mx-2" style="font-size: 0.9rem;">
                        {{ $pagination['current_page'] ?? 1 }} / {{ $pagination['last_page'] ?? 1 }}
                    </span>
                    <a href="{{ ($pagination['current_page'] ?? 1) < ($pagination['last_page'] ?? 1) ? request()->fullUrlWithQuery(['page' => ($pagination['current_page'] ?? 1) + 1]) : 'javascript:void(0)' }}"
                        class="text-dark text-decoration-none ms-3 {{ ($pagination['current_page'] ?? 1) >= ($pagination['last_page'] ?? 1) ? 'opacity-50 pe-none' : '' }}">
                        <i class="bx bx-chevron-right fs-5"></i>
                    </a>
                </div>
            </div>
        </div>

        @include('admin.guru._modal-tambah')


        {{-- MODAL FILTER ROLE & STATUS GURU --}}
        <div class="modal fade" id="modalFilterGuru" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 20px; border: none;">
                    <form action="{{ route('koor.dataGuru') }}" method="GET">

                        {{-- Bawa parameter search kalau lagi nyari nama --}}
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                        <div class="modal-header border-bottom p-4">
                            <h5 class="modal-title fw-bold text-dark">
                                <i class="bx bx-filter-alt me-2 text-warning"></i>Filter Pegawai
                            </h5>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4">
                            {{-- 1. FILTER ROLE --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-dark">Pilih Role</label>
                                <select name="role" class="form-select shadow-none rounded-3"
                                    style="border-color: #cbd5e1;">
                                    <option value="">Semua Role</option>
                                    <option value="shadow_pj" {{ request('role') == 'shadow_pj' ? 'selected' : '' }}>PJ Shadow
                                    </option>
                                    <option value="shadow_teacher" {{ request('role') == 'shadow_teacher' ? 'selected' : '' }}>Guru Shadow</option>
                                    <option value="therapist_homeroom" {{ request('role') == 'therapist_homeroom' ? 'selected' : '' }}>Wali Kelas (Terapis)</option>
                                    <option value="therapist" {{ request('role') == 'therapist' ? 'selected' : '' }}>Terapis
                                    </option>
                                </select>
                            </div>

                            {{-- 2. FILTER STATUS --}}
                            <div class="mb-0">
                                <label class="form-label fw-semibold text-dark">Status Keaktifan</label>
                                <select name="status" class="form-select shadow-none rounded-3"
                                    style="border-color: #cbd5e1;">
                                    <option value="">Semua Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif (Sudah
                                        Ditugaskan)</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive
                                        (Belum Ditugaskan)</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer border-top p-4 pt-3">
                            <button type="button" class="btn fw-semibold px-4 shadow-none" data-bs-dismiss="modal"
                                style="border-radius: 50px; border: 1px solid #cbd5e1; color: #64748b; background-color: white;">Batal</button>
                            <button type="submit" class="btn fw-semibold px-4 text-white shadow-none"
                                style="border-radius: 50px; background-color: #5b9cf6; border: none;">Terapkan
                                Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection