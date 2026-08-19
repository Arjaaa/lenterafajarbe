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
        {{-- HEADER GROUP SHADOW --}}
        {{-- =============================================== --}}
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('koor.dataShadowGroup', ['page' => $backPage ?? 1]) }}"
                class="btn btn-sm rounded-circle p-2 me-3 shadow-none d-flex justify-content-center align-items-center"
                style="border: none; background-color: #5b9cf6; width: 35px; height: 35px;">
                <i class="bx bx-arrow-back fs-5 text-white"></i>
            </a>
            <h4 class="fw-bold mb-0" style="color: #5b9cf6;">{{ $group->name ?? 'Detail Group Shadow' }}</h4>
        </div>

        {{-- =============================================== --}}
        {{-- 4 KAPSUL INFORMASI --}}
        {{-- =============================================== --}}
        <div class="row align-items-end mb-2">



            {{-- Kapsul 2: Koordinator / PJ --}}
            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <span class="text-muted d-block mb-2 fw-semibold" style="font-size: 0.85rem;">Koordinator / PJ</span>
                <div class="d-flex align-items-center bg-white px-2 py-2"
                    style="border-radius: 50px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                    <div class="avatar avatar-sm me-3" style="width: 45px; height: 45px;">
                        <span class="avatar-initial rounded-circle fw-bold"
                            style="background-color: #e0ebfc; color: #5b9cf6;">
                            <i class="bx bx-user fs-4"></i>
                        </span>
                    </div>
                    <span class="fw-bold text-dark fs-6 text-truncate"
                        style="max-width: 150px;">{{ $group->pic->name ?? '-' }}</span>
                </div>
            </div>

            {{-- Kapsul 3: Guru Partner --}}
            <div class="col-lg-3 col-md-6 col-12 mb-3">
                <span class="text-muted d-block mb-2 fw-semibold" style="font-size: 0.85rem;">Guru Partner</span>
                <div class="d-flex align-items-center bg-white px-2 py-2"
                    style="border-radius: 50px; box-shadow: 0 2px 10px rgba(0,0,0,0.02);">
                    <div class="avatar avatar-sm me-3" style="width: 45px; height: 45px;">
                        <span class="avatar-initial rounded-circle fw-bold"
                            style="background-color: #e0f2fe; color: #0ea5e9;">
                            <i class="bx bx-user-voice fs-4"></i>
                        </span>
                    </div>
                    <span class="fw-bold text-dark fs-6 text-truncate"
                        style="max-width: 150px;">{{ $group->partner->name ?? '-' }}</span>
                </div>
            </div>

            {{-- Kapsul 4: Sekolah --}}
            {{-- Ubah col-lg-3 menjadi col-lg-6 agar ruang kapsul lebih panjang --}}
            <div class="col-lg-6 col-md-12 col-12 mb-3">
                <span class="text-muted d-block mb-2 fw-semibold" style="font-size: 0.85rem;">Sekolah</span>
                
                {{-- d-inline-flex agar kapsul membungkus isi dengan rapi, w-100 membatasi maksimal selebar kolom --}}
                <div class="d-inline-flex align-items-center bg-white px-2 py-2 w-100"
                    style="border-radius: 50px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); max-width: 100%;">
                    
                    {{-- flex-shrink-0 agar icon tidak ikut menyusut kalau teksnya kepanjangan --}}
                    <div class="avatar avatar-sm me-3 flex-shrink-0" style="width: 45px; height: 45px;">
                        <span class="avatar-initial rounded-circle fw-bold d-flex align-items-center justify-content-center w-100 h-100"
                            style="background-color: #fff3e0; color: #f97316;">
                            <i class="bx bx-building-house fs-4"></i>
                        </span>
                    </div>
                    
                    {{-- Hapus max-width: 150px. Gunakan flex-grow-1 dan min-width: 0 agar text-truncate bekerja sempurna di dalam flexbox --}}
                    <span class="fw-bold text-dark fs-6 text-truncate pe-3 flex-grow-1" style="min-width: 0;">
                        {{ $group->school_name ?? '-' }}
                    </span>
                    
                </div>
            </div>
        </div>

        {{-- Link View and Edit --}}
        <div class="text-end mb-4">
            {{-- <a href="javascript:void(0)" class="text-dark fw-semibold" style="font-size: 0.75rem; text-decoration: none;">
                view and edit
            </a> --}}
        </div>

        {{-- =============================================== --}}
        {{-- CARD TABEL SISWA --}}
        {{-- =============================================== --}}
        <div class="card bg-white"
            style="border-radius: 20px; border: 1px solid #5b9cf6; box-shadow: none; overflow: hidden;">

            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">Daftar Siswa</h5>
            </div>

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
                        @if(isset($group->student) && !empty($group->student))
                            <tr>
                                <td class="text-center align-middle text-dark fw-medium">1</td>

                                {{-- Kolom Photo --}}
                                <td class="align-middle">
                                    <div class="avatar avatar-md" style="width: 45px; height: 45px;">
                                        @if(!empty($group->student->photo))
                                            <img src="{{ str_starts_with($group->student->photo, 'http') ? $group->student->photo : asset('storage/' . $group->student->photo) }}"
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
                                <td class="align-middle text-dark fw-medium">{{ $group->student->name ?? '-' }}</td>
                                <td class="align-middle text-dark">
                                    {{ ucwords(str_replace('-', ' ', $group->student->gender ?? '-')) }}</td>
                                <td class="align-middle text-dark">
                                    {{ ucwords(str_replace('_', ' ', $group->student->special_needs ?? 'Reguler')) }}</td>
                                <td class="align-middle text-dark">
                                    {{ $group->student->parent->name ?? $group->student->mother_name ?? '-' }}</td>

                                {{-- Kolom Aksi --}}
                                <td class="text-center align-middle">
                                    <a href="{{ route('koor.detailAnak', $group->student->id ?? 0) }}"
                                        class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                        style="font-size: 0.75rem; background-color: #4ade80; border: none;" title="View">
                                        <i class="bx bx-info-circle me-1"></i> View
                                    </a>

                                    {{-- <button type="button" class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                        style="font-size: 0.75rem; background-color: #f87171; border: none;" title="Remove">
                                        <i class="bx bx-x-circle me-1"></i> Remove
                                    </button> --}}
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bx bx-folder-open fs-1 d-block mb-2"></i>
                                    Belum ada siswa yang didaftarkan di group shadow ini.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection