@extends('layouts.admin')

@section('content')
    {{-- CSS Khusus Striping Tabel Sesuai Desain UI Pastel --}}
    <style>
        .custom-table-striped tbody tr:nth-of-type(even) {
            background-color: #f4f8ff !important;
        }

        .custom-table-striped tbody tr td {
            border-bottom: none !important;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HEADER: Judul & Tanggal --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0 text-dark">Daftar Semua Kelas</h4>

            <div class="bg-white px-4 py-2 d-flex align-items-center"
                style="border-radius: 50px; border: 1px solid #f0f0f0;">
                <i class="bx bx-calendar text-success me-2 fs-5"
                    style="color: #4ade80 !important; background: #e8f5e9; padding: 5px; border-radius: 50%;"></i>
                <span class="fw-semibold text-dark">
                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d - m - Y') }}
                </span>
            </div>
        </div>

        {{-- NOTIFIKASI --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 15px;">
                <strong></strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 15px;">
                <strong> Ada masalah:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- CARD UTAMA & TABEL --}}
        <div class="card bg-white"
            style="border: 2px solid #e0ebfc; border-radius: 20px; box-shadow: none; overflow: hidden;">

            {{-- HEADER TABLE: Search Bar & Tombol Add --}}
            <div
                class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center bg-white border-bottom p-4">

                {{-- Kiri: Form Search & Tombol Reset --}}
                <form action="{{ url()->current() }}" method="GET"
                    class="d-flex align-items-center gap-2 mb-3 mb-md-0 w-100" style="max-width: 500px;">
                    <div class="input-group"
                        style="border-radius: 50px; overflow: hidden; border: 1px solid #a3c7fb; background: white; max-width: 350px;">
                        <span class="input-group-text bg-transparent border-0 pe-1">
                            <i class="bx bx-search" style="color: #1e293b;"></i>
                        </span>
                        {{-- FIX: Tambah name="search" dan value --}}
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control border-0 shadow-none px-2" placeholder="Cari nama kelas..."
                            style="background: transparent;">
                    </div>

                    <button type="submit" class="btn rounded-pill px-4 fw-semibold text-white shadow-none flex-shrink-0"
                        style="background-color: #5b9cf6; border: none;">Cari</button>

                    {{-- Tombol Reset --}}
                    @if(request()->filled('search'))
                        <a href="{{ url()->current() }}"
                            class="btn rounded-pill px-3 fw-semibold text-dark shadow-none d-flex align-items-center flex-shrink-0"
                            style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                            <i class="bx bx-x me-1"></i> Reset
                        </a>
                    @endif
                </form>

                {{-- Kanan: Tombol Add --}}
                <button type="button" class="btn rounded-pill fw-semibold px-4 text-white shadow-none"
                    data-bs-toggle="modal" data-bs-target="#modalTambahKelas"
                    style="background-color: #5b9cf6; border: none;">
                    <i class="bx bx-plus-circle me-1 fs-5"></i> Add Class
                </button>
            </div>

            {{-- TABEL DATA --}}
            <div class="table-responsive text-nowrap">
                <table class="table custom-table-striped table-borderless">
                    <thead style="border-bottom: 2px solid #f0f4f9;">
                        <tr>
                            <th class="text-center py-3 text-muted fw-semibold text-capitalize">No</th>
                            <th class="py-3 text-muted fw-semibold text-capitalize">Nama Kelas</th>
                            <th class="py-3 text-muted fw-semibold text-capitalize">Jumlah Siswa</th>
                            <th class="py-3 text-muted fw-semibold text-capitalize">Wali Kelas 1</th>
                            <th class="py-3 text-muted fw-semibold text-capitalize">Wali Kelas 2</th>
                            <th class="text-center py-3 text-muted fw-semibold text-capitalize">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($classes ?? [] as $index => $kelas)
                            <tr>
                                <td class="text-center align-middle text-dark fw-medium">{{ $index + 1 }}</td>
                                <td class="align-middle text-dark fw-bold">{{ $kelas->name ?? '-' }}</td>

                                {{-- Hitung jumlah siswa --}}
                                <td class="align-middle text-dark fw-medium">
                                    {{ isset($kelas->students) ? count((array) $kelas->students) : 0 }} Siswa
                                </td>

                                {{-- Wali Kelas 1 --}}
                                <td class="align-middle text-dark">
                                    {{ $kelas->homeroom_teacher->name ?? '-' }}
                                </td>

                                {{-- Wali Kelas 2 --}}
                                <td class="align-middle text-dark">
                                    {{ $kelas->homeroom_teacher_2->name ?? '-' }}
                                </td>

                                {{-- Kolom Aksi --}}
                                <td class="text-center align-middle">
                                    <a href="{{ route('koor.detailKelas', ['id' => $kelas->id ?? 0, 'back_page' => $pagination['current_page'] ?? 1]) }}"
                                        class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                        style="font-size: 0.75rem; background-color: #4ade80; border: none;" title="View">
                                        <i class="bx bx-info-circle me-1" style="font-size: 0.9rem;"></i> View
                                    </a>
                                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                        style="font-size: 0.75rem; background-color: #60a5fa; border: none;"
                                        data-bs-toggle="modal" data-bs-target="#modalEditKelas{{ $kelas->id }}">
                                        <i class="bx bx-edit-alt me-1" style="font-size: 0.9rem;"></i> Edit
                                    </button>

                                    <form action="{{ route('koor.destroyKelas', $kelas->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Kamu yakin ingin menghapus kelas {{ $kelas->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        {{-- Button Delete (Merah Soft - Pemicu Modal) --}}
                                        {{-- Button Delete (Merah Soft - Pemicu Modal) --}}
                                        <button type="button" class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none"
                                            style="font-size: 0.75rem; background-color: #f87171; border: none;" title="Delete"
                                            data-bs-toggle="modal" data-bs-target="#modalDeleteKelas{{ $kelas->id ?? 0 }}">
                                            <i class="bx bx-trash me-1" style="font-size: 0.9rem;"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bx bx-folder-open fs-1 d-block mb-2"></i>
                                    Belum ada data kelas yang tersedia.
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

        {{-- ========================================================== --}}
        {{-- SEMUA MODAL DITARUH DI LUAR TABEL AGAR LAYOUT TIDAK PECAH --}}
        {{-- ========================================================== --}}

        {{-- 1. MODAL TAMBAH KELAS --}}
        <div class="modal fade" id="modalTambahKelas" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 20px; border: none;">
                    <form action="{{ route('koor.storeKelas') }}" method="POST">
                        @csrf
                        <div class="modal-header border-bottom p-4">
                            <h5 class="modal-title fw-bold text-dark">
                                <i class="bx bx-plus-circle me-2 text-primary"></i>Tambah Kelas Baru
                            </h5>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label text-dark fw-semibold">Nama Kelas <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control shadow-none"
                                    placeholder="Contoh: Kelas Bintang" required>
                            </div>
                            a
                            <div class="mb-0">
                                <label class="form-label text-dark fw-semibold">Wali Kelas 2 <span
                                        class="text-muted fw-normal">(Opsional)</span></label>
                                <select name="homeroom_teacher_2_id" class="form-select shadow-none">
                                    <option value="">-- Kosongkan jika tidak ada --</option>
                                    @foreach ($teachers as $guru)
                                        <option value="{{ $guru->id }}">{{ $guru->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-top pt-3 p-4">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 shadow-none"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-none"
                                style="background-color: #5b9cf6; border: none;">Simpan Kelas</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 2. MODAL EDIT KELAS (Dilakukan Looping) --}}
        @foreach ($classes ?? [] as $kelas)
            <div class="modal fade" id="modalEditKelas{{ $kelas->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 20px; border: none;">
                        <form action="{{ route('koor.updateKelas', $kelas->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-header border-bottom p-4">
                                <h5 class="modal-title fw-bold text-dark">
                                    <i class="bx bx-edit-alt me-2 text-primary"></i>Edit Kelas: {{ $kelas->name }}
                                </h5>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4 text-start">
                                <div class="mb-3">
                                    <label class="form-label text-dark fw-semibold">Nama Kelas <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control shadow-none" value="{{ $kelas->name }}"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-dark fw-semibold">Wali Kelas 1 <span
                                            class="text-danger">*</span></label>
                                    <select name="homeroom_teacher_id" class="form-select shadow-none" required>
                                        <option value="" disabled>-- Pilih Wali Kelas 1 --</option>
                                        @foreach ($teachers as $guru)
                                            <option value="{{ $guru->id }}" {{ ($kelas->homeroom_teacher_id ?? '') == $guru->id ? 'selected' : '' }}>
                                                {{ $guru->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label text-dark fw-semibold">Wali Kelas 2 <span
                                            class="text-muted fw-normal">(Opsional)</span></label>
                                    <select name="homeroom_teacher_2_id" class="form-select shadow-none">
                                        <option value="">-- Kosongkan jika tidak ada --</option>
                                        @foreach ($teachers as $guru)
                                            <option value="{{ $guru->id }}" {{ ($kelas->homeroom_teacher_2_id ?? '') == $guru->id ? 'selected' : '' }}>
                                                {{ $guru->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer border-top pt-3 p-4">
                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 shadow-none"
                                    data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-none"
                                    style="background-color: #5b9cf6; border: none;">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- =============================================== --}}
            {{-- MODAL KONFIRMASI DELETE KELAS --}}
            {{-- =============================================== --}}
            <div class="modal fade" id="modalDeleteKelas{{ $kelas->id ?? 0 }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content text-center" style="border-radius: 20px; border: none;">

                        {{-- Class text-wrap biar teks aman --}}
                        <div class="modal-body p-4 text-wrap">

                            {{-- Icon Trash Merah --}}
                            <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle"
                                style="width: 60px; height: 60px; background-color: #ffe6e6;">
                                <i class="bx bx-trash fs-2" style="color: #ff5b5c;"></i>
                            </div>

                            <h5 class="fw-bold text-dark mb-2">Hapus Kelas?</h5>

                            {{-- Class text-wrap di teks paragraf --}}
                            <p class="text-muted mb-4 text-wrap" style="font-size: 0.9rem;">
                                Kelas <strong>{{ $kelas->name ?? 'ini' }}</strong> akan dihapus permanen dan tidak dapat
                                dikembalikan.
                            </p>

                            <form action="{{ route('koor.destroyKelas', $kelas->id ?? 0) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn rounded-pill px-4 shadow-none" data-bs-dismiss="modal"
                                        style="background-color: #f1f5f9; color: #475569; border: none; font-weight: 600;">
                                        Batal
                                    </button>
                                    <button type="submit" class="btn rounded-pill px-4 text-white shadow-none"
                                        style="background-color: #ff5b5c; border: none; font-weight: 600;">
                                        Hapus
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        @endforeach


    </div>
@endsection