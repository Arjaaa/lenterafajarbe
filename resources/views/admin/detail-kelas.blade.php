@extends('layouts.admin')

@section('content')
    <style>
        .custom-table-striped tbody tr:nth-of-type(even) {
            background-color: #f4f8ff !important;
        }

        .custom-table-striped tbody tr td {
            border-bottom: none !important;
        }

        .custom-table-striped th,
        .custom-table-striped td {
            white-space: nowrap !important;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- =============================================== --}}
        {{-- HEADER KELAS --}}
        {{-- =============================================== --}}
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('koor.dataKelas', ['page' => $backPage ?? 1]) }}"
                class="btn btn-sm rounded-circle p-2 me-3 shadow-none d-flex justify-content-center align-items-center"
                style="border: none; background-color: #5b9cf6; width: 35px; height: 35px;">
                <i class="bx bx-arrow-back fs-5 text-white"></i>
            </a>
            <h4 class="fw-bold mb-0" style="color: #5b9cf6;">{{ $kelas->name ?? 'Nama Kelas' }}</h4>
        </div>

        {{-- =============================================== --}}
        {{-- WALI KELAS INFO (KAPSUL) --}}
        {{-- =============================================== --}}
        <div class="row align-items-end mb-4">
            <div class="col-md-4 col-12 mb-3 mb-md-0">
                <span class="text-muted d-block mb-2 fw-semibold" style="font-size: 0.85rem;">Wali Kelas 1</span>
                <div class="d-flex align-items-center bg-white px-2 py-2"
                    style="border-radius: 50px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                    <div class="avatar avatar-sm me-3" style="width: 45px; height: 45px;">
                        <span class="avatar-initial rounded-circle fw-bold"
                            style="background-color: #ffcccb; color: #ff5b5c;">
                            <i class="bx bx-user fs-4"></i>
                        </span>
                    </div>
                    <span class="fw-bold text-dark fs-6">{{ $kelas->homeroom_teacher->name ?? 'Belum Diatur' }}</span>
                </div>
            </div>

            <div class="col-md-4 col-12 mb-3 mb-md-0">
                <span class="text-muted d-block mb-2 fw-semibold" style="font-size: 0.85rem;">Wali Kelas 2</span>
                <div class="d-flex align-items-center bg-white px-2 py-2"
                    style="border-radius: 50px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                    <div class="avatar avatar-sm me-3" style="width: 45px; height: 45px;">
                        <span class="avatar-initial rounded-circle fw-bold"
                            style="background-color: #cce5ff; color: #5b9cf6;">
                            <i class="bx bx-user fs-4"></i>
                        </span>
                    </div>
                    <span class="fw-bold text-dark fs-6">{{ $kelas->homeroom_teacher_2->name ?? '-' }}</span>
                </div>
            </div>
            {{-- =============================================== --}}
            {{-- NOTIFIKASI ERROR / SUCCESS --}}
            {{-- =============================================== --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 15px;">
                    <strong></strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert" style="border-radius: 15px;">
                    <strong>Ada masalah:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            {{--
            <div class="col-md-4 col-12 text-md-end text-start pb-2">
                <a href="javascript:void(0)" class="text-dark fw-semibold"
                    style="font-size: 0.75rem; text-decoration: none;">
                    view and edit
                </a>
            </div> --}}
        </div>

        {{-- =============================================== --}}
        {{-- CARD DAFTAR SISWA & TABEL --}}
        {{-- =============================================== --}}
        <div class="card bg-white"
            style="border-radius: 20px; border: 1px solid #5b9cf6; box-shadow: none; overflow: hidden;">

            {{-- Header Tabel --}}
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">Daftar Siswa</h5>
                {{-- Tombol Add Student yang sudah di-fix --}}
                <button class="btn rounded-pill px-4 fw-semibold text-white shadow-none d-flex align-items-center"
                    style="background-color: #5b9cf6; border: none;" data-bs-toggle="modal"
                    data-bs-target="#modalAddStudent">
                    <i class="bx bx-plus me-1"></i> Add Student
                </button>
            </div>

            {{-- Tabel Siswa --}}
            <div class="table-responsive text-nowrap">
                <table class="table custom-table-striped table-borderless" style="min-width: 1000px;">
                    <thead style="border-bottom: 1px solid #e0ebfc;">
                        <tr>
                            <th class="text-center py-3 text-muted fw-semibold">No</th>
                            <th class="py-3 text-muted fw-semibold">Photo</th>
                            <th class="py-3 text-muted fw-semibold">Nama</th>
                            <th class="py-3 text-muted fw-semibold">Gender</th>
                            <th class="py-3 text-muted fw-semibold">Kebutuhan Khusus</th>
                            <th class="py-3 text-muted fw-semibold">Parent</th>
                            <th class="text-center py-3 text-muted fw-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($kelas->students ?? [] as $index => $siswa)
                            <tr>
                                <td class="text-center align-middle text-dark fw-medium">{{ $index + 1 }}</td>

                                {{-- Kolom Photo --}}
                                <td class="align-middle">
                                    <div class="avatar avatar-md" style="width: 45px; height: 45px;">
                                        @if(!empty($siswa->photo))
                                            <img src="{{ str_starts_with($siswa->photo, 'http') ? $siswa->photo : asset('storage/' . $siswa->photo) }}"
                                                alt="Avatar" class="rounded-circle"
                                                style="object-fit: cover; width: 100%; height: 100%;" />
                                        @else
                                            <span class="avatar-initial rounded-circle"
                                                style="background-color: #cbd5e1; color: white;">
                                                <i class="bx bx-user"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Data Siswa --}}
                                <td class="align-middle text-dark fw-medium">{{ $siswa->name ?? '-' }}</td>
                                <td class="align-middle text-dark">{{ ucwords(str_replace('-', ' ', $siswa->gender ?? '-')) }}
                                </td>
                                <td class="align-middle text-dark">
                                    {{ ucwords(str_replace('_', ' ', $siswa->special_needs ?? 'Reguler')) }}
                                </td>
                                <td class="align-middle text-dark">{{ $siswa->parent->name ?? $siswa->mother_name ?? '-' }}</td>

                                {{-- Kolom Aksi --}}
                                <td class="text-center align-middle">
                                    <a href="{{ route('koor.detailAnak', $siswa->id ?? 0) }}"
                                        class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                        style="font-size: 0.75rem; background-color: #4ade80; border: none;" title="View">
                                        <i class="bx bx-info-circle me-1"></i> View
                                    </a>
                                    {{--
                                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                        style="font-size: 0.75rem; background-color: #2ea4ff; border: none;" title="Edit">
                                        <i class="bx bx-edit me-1"></i> Edit
                                    </button> --}}

                                    <form
                                        action="{{ route('koor.keluarkanMurid', ['classId' => $kelas->id, 'studentId' => $siswa->id]) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Keluarkan siswa ini dari kelas?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none"
                                            style="font-size: 0.75rem; background-color: #f87171; border: none;" title="Remove">
                                            <i class="bx bx-trash me-1"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bx bx-folder-open fs-1 d-block mb-2"></i>
                                    Belum ada siswa di kelas ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


        {{-- ========================================================== --}}
        {{-- MODAL TAMBAH SISWA KE KELAS --}}
        {{-- ========================================================== --}}
        <div class="modal fade" id="modalAddStudent" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 20px; border: none;">
                    {{-- Pastikan action routenya sesuai dengan yang ada di web.php kamu --}}
                    <form action="{{ route('koor.tambahMuridKeKelas', $kelas->id ?? 0) }}" method="POST">
                        @csrf
                        <div class="modal-header border-bottom p-4">
                            <h5 class="modal-title fw-bold text-dark">
                                <i class="bx bx-user-plus me-2 text-primary"></i>Tambah Siswa ke Kelas
                            </h5>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label text-dark fw-semibold">Pilih Siswa <span
                                        class="text-danger">*</span></label>
                                <p class="text-muted mb-3" style="font-size: 0.85rem;">
                                    <i class="bx bx-check-square me-1"></i> Centang kotak di samping nama siswa untuk
                                    menambahkannya ke kelas ini.
                                </p>

                                {{-- Area Scrollable untuk List Checkbox --}}
                                <div class="border rounded p-3"
                                    style="max-height: 250px; overflow-y: auto; background-color: #f8fafc; border-color: #cbd5e1 !important;">

                                    @forelse($allStudents ?? [] as $siswa)
                                        <div class="form-check d-flex align-items-center mb-3">
                                            {{-- Checkbox --}}
                                            <input class="form-check-input shadow-none" type="checkbox" name="student_ids[]"
                                                value="{{ $siswa->id }}" id="student_{{ $siswa->id }}"
                                                style="width: 22px; height: 22px; cursor: pointer; border-color: #94a3b8;">

                                            {{-- Label Nama (Bisa di-klik juga buat nyentang kotaknya) --}}
                                            <label class="form-check-label ms-3 w-100" for="student_{{ $siswa->id }}"
                                                style="cursor: pointer; padding-top: 2px;">
                                                <span class="fw-bold text-dark d-block">{{ $siswa->name }}</span>
                                                <span class="text-muted" style="font-size: 0.75rem;">
                                                    Kebutuhan Khusus:
                                                    {{ ucwords(str_replace('_', ' ', $siswa->special_needs ?? 'Reguler')) }}
                                                </span>
                                            </label>
                                        </div>
                                    @empty
                                        <div class="text-center text-muted py-3">
                                            <i class="bx bx-folder-open fs-3 d-block mb-1"></i>
                                            Belum ada data siswa.
                                        </div>
                                    @endforelse

                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-top pt-3 p-4">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 shadow-none"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-none"
                                style="background-color: #5b9cf6; border: none;">
                                Tambahkan Siswa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection