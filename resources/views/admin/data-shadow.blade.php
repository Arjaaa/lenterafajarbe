@extends('layouts.admin')


@section('content')

    {{-- CSS Khusus untuk Striping Tabel & Mencegah Teks/Tombol Turun Baris --}}
    <style>
        .custom-table-striped tbody tr:nth-of-type(even) {
            background-color: #f4f8ff !important;
        }

        .custom-table-striped tbody tr td {
            border-bottom: none !important;
        }

        /* Mencegah teks dan tombol turun baris (bikin baris bengkak ke bawah) */
        .custom-table-striped th,
        .custom-table-striped td {
            white-space: nowrap !important;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HEADER: Judul & Tanggal --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0 text-dark">Group Shadow Teacher</h4>

            <div class="bg-white px-4 py-2 d-flex align-items-center" style="border-radius: 50px; border: 1px solid #f0f0f0;">
                <i class="bx bx-calendar text-success me-2 fs-5" style="color: #4ade80 !important; background: #e8f5e9; padding: 5px; border-radius: 50%;"></i>
                <span class="fw-semibold text-dark">
                    {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d - m - Y') }}
                </span>
            </div>
        </div>

        {{-- NOTIFIKASI --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 15px;">
                <strong>Yay! 🎉</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 15px;">
                <strong>Oops! Ada yang salah:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- CARD UTAMA & TABEL --}}
        <div class="card bg-white" style="border: 2px solid #e0ebfc; border-radius: 20px; box-shadow: none; overflow: hidden;">

            {{-- HEADER TABLE: Search Bar & Tombol Add --}}
           {{-- HEADER TABLE: Search Bar & Tombol Add --}}
            <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center bg-white border-bottom p-4">

                {{-- Kiri: Form Search & Tombol Reset --}}
                <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center gap-2 mb-3 mb-md-0 w-100" style="max-width: 500px;">
                    <div class="input-group" style="border-radius: 50px; overflow: hidden; border: 1px solid #a3c7fb; background: white; max-width: 350px;">
                        <span class="input-group-text bg-transparent border-0 pe-1">
                            <i class="bx bx-search" style="color: #1e293b;"></i>
                        </span>
                        <input type="text" name="search" value="{{ request('search') }}" 
                            class="form-control border-0 shadow-none px-2" placeholder="Cari siswa, PJ, atau partner..." 
                            style="background: transparent;">
                    </div>
                    
                    <button type="submit" class="btn rounded-pill px-4 fw-semibold text-white shadow-none flex-shrink-0" 
                        style="background-color: #5b9cf6; border: none;">Cari</button>

                    {{-- Tombol Reset Cuma Muncul Kalau Lagi Nyari Sesuatu --}}
                    @if(request()->filled('search'))
                        <a href="{{ url()->current() }}" class="btn rounded-pill px-3 fw-semibold text-dark shadow-none d-flex align-items-center flex-shrink-0"
                            style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                            <i class="bx bx-x me-1"></i> Reset
                        </a>
                    @endif
                </form>

                {{-- Kanan: Tombol Tambah --}}
                <button type="button" class="btn rounded-pill fw-semibold px-4 text-white shadow-none flex-shrink-0" data-bs-toggle="modal"
                    data-bs-target="#modalTambahShadow" style="background-color: #5b9cf6; border: none;">
                    <i class="bx bx-plus-circle me-1 fs-5"></i> Buat Group Baru
                </button>
            </div>

            {{-- ========================================================== --}}
            {{-- ISI TABEL (DIBUNGKUS TABLE-RESPONSIVE AGAR BISA SCROLL)    --}}
            {{-- ========================================================== --}}
            <div class="table-responsive text-nowrap" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                {{-- Memaksa minimal lebar 1000px agar tabel aman tidak memble --}}
                <table class="table custom-table-striped table-borderless" style="min-width: 1100px;">
                    <thead style="border-bottom: 2px solid #f0f4f9;">
                        <tr>
                            <th class="text-center py-3 text-muted fw-semibold">No</th>
                            <th class="py-3 text-muted fw-semibold">Nama Group</th>
                            <th class="py-3 text-muted fw-semibold">Anak didampingi</th>
                            <th class="py-3 text-muted fw-semibold">PJ Shadow (Koor)</th>
                            <th class="py-3 text-muted fw-semibold">Guru Shadow (Partner)</th>
                            <th class="py-3 text-muted fw-semibold">Lokasi Sekolah</th>
                            <th class="text-center py-3 text-muted fw-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($shadowGroups ?? [] as $index => $group)
                            <tr>
                                <td class="text-center align-middle text-dark fw-medium">{{ $index + 1 }}</td>

                                {{-- Nama Group --}}
                                <td class="align-middle text-dark fw-bold">{{ $group->name ?? '-' }}</td>

                                {{-- Anak Didampingi --}}
                                <td class="align-middle text-dark fw-medium">
                                    {{ $group->student->name ?? 'Data Hilang' }}
                                </td>

                                {{-- PJ Shadow --}}
                                <td class="align-middle text-dark fw-medium">
                                    {{ $group->pic->name ?? 'Data Hilang' }}
                                </td>

                                {{-- Guru Shadow Partner --}}
                                <td class="align-middle text-dark fw-medium">
                                    {{ $group->partner->name ?? 'Data Hilang' }}
                                </td>

                                {{-- Sekolah Tugas --}}
                                <td class="align-middle text-dark fw-medium">
                                    {{ $group->school_name ?? '-' }}
                                </td>

                                {{-- Kolom Aksi (3 Tombol Pastel) --}}
                                <td class="text-center align-middle">

                                    {{-- Button View (Hijau Soft) - Pakai Modal --}}
                                    <a href="{{ route('koor.detailShadowGroup', ['id' => $group->id ?? 0, 'back_page' => $pagination['current_page'] ?? 1]) }}"
                                        class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                        style="font-size: 0.75rem; background-color: #4ade80; border: none;" title="View">
                                        <i class="bx bx-info-circle me-1" style="font-size: 0.9rem;"></i> View
                                    </a>

                                    {{-- Button Edit (Biru Soft) --}}
                                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                        style="font-size: 0.75rem; background-color: #60a5fa; border: none;" data-bs-toggle="modal"
                                        data-bs-target="#modalEditShadow{{ $group->id ?? 0 }}" title="Edit">
                                        <i class="bx bx-edit-alt me-1" style="font-size: 0.9rem;"></i> Edit
                                    </button>

                                    {{-- Button Delete (Merah Soft) --}}
                                    <form action="{{ route('koor.destroyShadowGroup', $group->id ?? 0) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Kamu yakin ingin menghapus grup {{ addslashes($group->name ?? 'ini') }}?');">
                                        @csrf
                                        @method('DELETE')
                                       <button type="button" class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none"
                                            style="font-size: 0.75rem; background-color: #f87171; border: none;" title="Delete"
                                            data-bs-toggle="modal" data-bs-target="#modalDeleteShadowGroup{{ $group->id ?? 0 }}">
                                            <i class="bx bx-trash me-1" style="font-size: 0.9rem;"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            {{-- Dummy data pengisi jika data masih kosong --}}
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td colspan="7" class="py-4"></td>
                                </tr>
                            @endfor
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION BAWAH --}}
            <div class="card-footer bg-white border-top text-center py-4">
                <div class="d-inline-flex align-items-center bg-white px-3 py-2 border shadow-sm" style="border-radius: 50px;">
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
        {{-- SEMUA MODAL DITARUH DI SINI (LUAR TABEL) AGAR LAYOUT AMAN --}}
        {{-- ========================================================== --}}

        @foreach ($shadowGroups ?? [] as $group)

        {{-- =============================================== --}}
{{-- MODAL KONFIRMASI DELETE SHADOW GROUP --}}
{{-- =============================================== --}}
<div class="modal fade" id="modalDeleteShadowGroup{{ $group->id ?? 0 }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center" style="border-radius: 20px; border: none;">
            
            {{-- Class text-wrap biar teks aman di dalam tabel --}}
            <div class="modal-body p-4 text-wrap">
                
                {{-- Icon Trash Merah --}}
                <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-circle"
                    style="width: 60px; height: 60px; background-color: #ffe6e6;">
                    <i class="bx bx-trash fs-2" style="color: #ff5b5c;"></i>
                </div>

                <h5 class="fw-bold text-dark mb-2">Hapus Grup?</h5>
                
                {{-- Class text-wrap di teks paragraf --}}
                <p class="text-muted mb-4 text-wrap" style="font-size: 0.9rem;">
                    Grup <strong>{{ $group->name ?? 'ini' }}</strong> akan dihapus permanen dan tidak dapat dikembalikan.
                </p>

                <form action="{{ route('koor.destroyShadowGroup', $group->id ?? 0) }}" method="POST">
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

            {{-- 1. MODAL DETAIL SHADOW --}}
            <x-modal id="modalDetailShadow{{ $group->id ?? 0 }}" title="Detail Group Shadow">
                <div class="modal-body text-wrap text-start">
                    <h6 class="fw-bold mb-3 text-primary">Informasi Penugasan</h6>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%" class="ps-0"><strong>Nama Group</strong></td>
                            <td>: {{ $group->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="ps-0"><strong>Anak Didampingi</strong></td>
                            <td>: {{ $group->student->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="ps-0"><strong>Koordinator (PJ)</strong></td>
                            <td>: {{ $group->pic->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="ps-0"><strong>Guru Pendamping</strong></td>
                            <td>: {{ $group->partner->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="ps-0"><strong>Lokasi Sekolah</strong></td>
                            <td>: {{ $group->school_name ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer border-top pt-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                </div>
            </x-modal>

            {{-- 2. MODAL EDIT SHADOW --}}
            <x-modal id="modalEditShadow{{ $group->id ?? 0 }}" title="Edit Group Shadow">
                <form action="{{ route('koor.updateShadowGroup', $group->id ?? 0) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body text-start">
                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold">Nama Group <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $group->name ?? '' }}" placeholder="Contoh: Group Bermain A" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold">Anak yang Didampingi <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-select" required>
                                <option value="">-- Pilih Anak --</option>
                                @foreach($students ?? [] as $student)
                                    @if(!in_array($student->id, $busyStudentIds ?? []) || ($group->student_id ?? 0) == $student->id)
                                        <option value="{{ $student->id }}" {{ ($group->student_id ?? 0) == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold">Koordinator (PJ Shadow) <span class="text-danger">*</span></label>
                            <select name="pic_id" class="form-select" required>
                                <option value="">-- Pilih PJ --</option>
                                @foreach($pjs ?? [] as $pj)
                                    @if(!in_array($pj->id, $assignedPicIds ?? []) || ($group->pic_id ?? 0) == $pj->id)
                                        <option value="{{ $pj->id }}" {{ ($group->pic_id ?? 0) == $pj->id ? 'selected' : '' }}>
                                            {{ $pj->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-semibold">Guru Pendamping (Partner) <span class="text-danger">*</span></label>
                            <select name="partner_id" class="form-select" required>
                                <option value="">-- Pilih Guru Shadow --</option>
                                @foreach($partners ?? [] as $partner)
                                    @if(!in_array($partner->id, $assignedPartnerIds ?? []) || ($group->partner_id ?? 0) == $partner->id)
                                        <option value="{{ $partner->id }}" {{ ($group->partner_id ?? 0) == $partner->id ? 'selected' : '' }}>
                                            {{ $partner->name }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-dark fw-semibold">Lokasi Sekolah <span class="text-danger">*</span></label>
                            <input type="text" name="school_name" class="form-control" value="{{ $group->school_name ?? '' }}" placeholder="Contoh: TK Tunas Bangsa" required>
                        </div>
                    </div>
                    <div class="modal-footer border-top pt-3">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #5b9cf6; border: none;">Simpan Perubahan</button>
                    </div>
                </form>
            </x-modal>
        @endforeach

        {{-- 3. MODAL TAMBAH GROUP SHADOW BARU --}}
        <x-modal id="modalTambahShadow" title="Buat Group Shadow Baru">
            <form action="{{ route('koor.storeShadowGroup') }}" method="POST">
                @csrf
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Nama Group <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Group Bermain A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Anak yang Didampingi <span class="text-danger">*</span></label>
                        <select name="student_id" class="form-select" required>
                            <option value="">-- Pilih Anak --</option>
                            @foreach($students ?? [] as $student)
                                @if(!in_array($student->id, $busyStudentIds ?? []))
                                    <option value="{{ $student->id }}">{{ $student->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Koordinator (PJ Shadow) <span class="text-danger">*</span></label>
                        <select name="pic_id" class="form-select" required>
                            <option value="">-- Pilih PJ --</option>
                            @foreach($pjs ?? [] as $pj)
                                @if(!in_array($pj->id, $assignedPicIds ?? []))
                                    <option value="{{ $pj->id }}">{{ $pj->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-dark fw-semibold">Guru Pendamping (Partner) <span class="text-danger">*</span></label>
                        <select name="partner_id" class="form-select" required>
                            <option value="">-- Pilih Guru Shadow --</option>
                            @foreach($partners ?? [] as $partner)
                                @if(!in_array($partner->id, $assignedPartnerIds ?? []))
                                    <option value="{{ $partner->id }}">{{ $partner->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-dark fw-semibold">Lokasi Sekolah <span class="text-danger">*</span></label>
                        <input type="text" name="school_name" class="form-control" placeholder="Contoh: TK Tunas Bangsa" required>
                    </div>
                </div>
                <div class="modal-footer border-top pt-3">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" style="background-color: #5b9cf6; border: none;">Buat Group</button>
                </div>
            </form>
        </x-modal>

    </div>
@endsection