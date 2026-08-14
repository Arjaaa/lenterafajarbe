@extends('layouts.admin')

@section('content')
    <style>
        .custom-table-striped tbody tr:nth-of-type(even) { background-color: #f4f8ff !important; }
        .custom-table-striped tbody tr td { border-bottom: none !important; }
        .custom-table-striped th, .custom-table-striped td { white-space: nowrap !important; }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- =============================================== --}}
        {{-- HEADER SESI 1 ON 1 --}}
        {{-- =============================================== --}}
        <div class="d-flex align-items-center mb-4">
            {{-- Tombol Back Kembali ke Halaman List Berdasarkan Page Sebelumnya --}}
            <a href="{{ route('koor.data1on1', ['page' => $backPage ?? 1]) }}"
                class="btn btn-sm rounded-circle p-2 me-3 shadow-none d-flex justify-content-center align-items-center"
                style="border: none; background-color: #5b9cf6; width: 35px; height: 35px;">
                <i class="bx bx-arrow-back fs-5 text-white"></i>
            </a>
            <h4 class="fw-bold mb-0" style="color: #5b9cf6;">{{ $sesi->name ?? 'Nama Program Terapi' }}</h4>
        </div>

        {{-- =============================================== --}}
        {{-- TERAPIS INFO (KAPSUL) --}}
        {{-- =============================================== --}}
        <div class="row align-items-end mb-4">
            <div class="col-md-4 col-12 mb-3 mb-md-0">
                <span class="text-muted d-block mb-2 fw-semibold" style="font-size: 0.85rem;">Terapis 1 on 1</span>
                <div class="d-flex align-items-center bg-white px-2 py-2" style="border-radius: 50px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); border: 1px solid #f0f4f9;">
                    <div class="avatar avatar-sm me-3" style="width: 45px; height: 45px;">
                        <span class="avatar-initial rounded-circle fw-bold" style="background-color: #e8f5e9; color: #4ade80;">
                            <i class="bx bx-user-voice fs-4"></i>
                        </span>
                    </div>
                    {{-- Pakai ?-> mencegah crash kalau teacher null --}}
                    <span class="fw-bold text-dark fs-6">{{ $sesi->teacher?->name ?? 'Belum Diatur' }}</span>
                </div>
            </div>

            <div class="col-md-8 col-12 text-md-end text-start pb-2">
                <a href="javascript:void(0)" class="text-dark fw-semibold" style="font-size: 0.75rem; text-decoration: none;">
                    view and edit
                </a>
            </div>
        </div>

        {{-- =============================================== --}}
        {{-- CARD DAFTAR SISWA & TABEL --}}
        {{-- =============================================== --}}
        <div class="card bg-white" style="border-radius: 20px; border: 1px solid #5b9cf6; box-shadow: none; overflow: hidden;">
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
                        {{-- Pengaman looping baris siswa --}}
                        @if(!empty($sesi->student))
                            <tr>
                                <td class="text-center align-middle text-dark fw-medium">1</td>

                                {{-- Kolom Photo --}}
                                <td class="align-middle">
                                    <div class="avatar avatar-md" style="width: 45px; height: 45px;">
                                        @if(!empty($sesi->student?->photo))
                                            <img src="{{ str_starts_with($sesi->student?->photo, 'http') ? $sesi->student?->photo : asset('storage/' . $sesi->student?->photo) }}"
                                                alt="Avatar" class="rounded-circle" style="object-fit: cover; width: 100%; height: 100%;" />
                                        @else
                                            <span class="avatar-initial rounded-circle" style="background-color: #cbd5e1; color: white;">
                                                <i class="bx bx-user"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Data Siswa (Diproteksi penuh dengan ?->) --}}
                                <td class="align-middle text-dark fw-medium">{{ $sesi->student?->name ?? '-' }}</td>
                                <td class="align-middle text-dark">
                                    {{ ucwords(str_replace('-', ' ', $sesi->student?->gender ?? '-')) }}
                                </td>
                                <td class="align-middle text-dark">
                                    {{ ucwords(str_replace('_', ' ', $sesi->student?->special_needs ?? 'Reguler')) }}
                                </td>
                                <td class="align-middle text-dark">
                                    {{ $sesi->student?->parent?->name ?? $sesi->student?->mother_name ?? '-' }}
                                </td>

                                {{-- Kolom Aksi --}}
                                <td class="text-center align-middle">
                                    <a href="{{ route('koor.detailAnak', $sesi->student?->id ?? 0) }}"
                                        class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none"
                                        style="font-size: 0.75rem; background-color: #4ade80; border: none;" title="View">
                                        <i class="bx bx-info-circle me-1"></i> View
                                    </a>
                                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 me-1 text-white shadow-none" style="font-size: 0.75rem; background-color: #f87171; border: none;" title="Remove">
                                        <i class="bx bx-x-circle me-1"></i> Remove
                                    </button>
                                </td>
                            </tr>
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bx bx-folder-open fs-1 d-block mb-2"></i> Belum ada siswa yang ditugaskan ke sesi ini.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection