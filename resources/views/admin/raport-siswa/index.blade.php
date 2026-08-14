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
        {{-- HEADER --}}
        {{-- =============================================== --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0 text-dark">Daftar Raport Siswa</h4>
        </div>

        {{-- =============================================== --}}
        {{-- TABEL DATA & UTALITAS --}}
        {{-- =============================================== --}}
        <div class="card bg-white"
            style="border: 1px solid #a3c7fb; border-radius: 20px; box-shadow: none; overflow: hidden;">

       {{-- SEARCH BAR --}}
            <div class="card-header bg-white border-bottom p-4">
                {{-- FIX: Arahkan ke koor.raportSiswa --}}
                <form action="{{ route('koor.raportSiswa') }}" method="GET" class="d-flex align-items-center mb-3 mb-md-0 w-100" style="max-width: 550px;">
                    <div class="input-group me-2"
                        style="border-radius: 50px; overflow: hidden; border: 1px solid #a3c7fb; background: white; max-width: 350px;">
                        <span class="input-group-text bg-transparent border-0 pe-1">
                            <i class="bx bx-search" style="color: #1e293b;"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-0 shadow-none px-2" 
                            placeholder="Cari nama siswa..." value="{{ request('search') }}" style="background: transparent;">
                    </div>
                    
                    <button type="submit" class="btn rounded-pill px-4 fw-semibold text-white shadow-none me-2"
                        style="background-color: #5b9cf6; border: none;">Cari</button>

                    {{-- Tombol Reset --}}
                    @if(request('search'))
                        <a href="{{ route('koor.raportSiswa') }}" class="btn rounded-pill px-3 fw-semibold text-dark shadow-none d-flex align-items-center" 
                            style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                            <i class="bx bx-x me-1"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table custom-table-striped table-borderless" style="min-width: 800px;">
                    <thead style="border-bottom: 1px solid #e0ebfc;">
                        <tr>
                            <th class="py-3 text-muted fw-semibold text-center" style="width: 5%;">No</th>
                            <th class="py-3 text-muted fw-semibold">Nama Siswa</th>
                            <th class="py-3 text-muted fw-semibold text-center">Status Raport</th>
                            <th class="text-center py-3 text-muted fw-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students ?? [] as $index => $student)
                            <tr>
                                <td class="align-middle text-dark text-center">{{ $index + 1 }}</td>
                                <td class="align-middle text-dark fw-bold">{{ data_get($student, 'name', '-') }}</td>
                                <td class="align-middle text-center">
                                    <span class="badge bg-label-success rounded-pill px-3">Sudah Dinilai</span>
                                </td>
                                <td class="text-center align-middle">
                                    {{-- FIX: Arahkan ke koor.detailRaport --}}
                                    <a href="{{ route('koor.detailRaport', data_get($student, 'id', 0)) }}"
                                        class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none d-inline-flex align-items-center"
                                        style="font-size: 0.75rem; background-color: #5b9cf6; border: none;"
                                        title="Lihat Rapot Siswa">
                                        <i class="bx bx-book-open me-1" style="font-size: 0.9rem;"></i> Lihat Rapot
                                    </a>
                                </td>
                            </tr>
                        @empty
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted border-0">
                                        @if($i == 2) <i class="bx bx-folder-open d-block fs-2 mb-1"></i> Belum ada data siswa. @endif
                                    </td>
                                </tr>
                            @endfor
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- PAGINATION --}}
            <div class="card-footer bg-white border-top text-center py-4">
                <div class="d-inline-flex align-items-center bg-white px-3 py-2 border shadow-sm"
                    style="border-radius: 50px;">
                    
                    {{-- Tombol Prev --}}
                    <a href="{{ ($pagination['current_page'] ?? 1) > 1 ? request()->fullUrlWithQuery(['page' => ($pagination['current_page'] ?? 1) - 1]) : 'javascript:void(0)' }}"
                        class="text-dark text-decoration-none me-3 {{ ($pagination['current_page'] ?? 1) <= 1 ? 'opacity-50 pe-none' : '' }}">
                        <i class="bx bx-chevron-left fs-5"></i>
                    </a>

                    {{-- Info Halaman --}}
                    <span class="fw-semibold text-dark mx-2" style="font-size: 0.9rem;">
                        {{ $pagination['current_page'] ?? 1 }} / {{ $pagination['last_page'] ?? 1 }}
                    </span>

                    {{-- Tombol Next --}}
                    <a href="{{ ($pagination['current_page'] ?? 1) < ($pagination['last_page'] ?? 1) ? request()->fullUrlWithQuery(['page' => ($pagination['current_page'] ?? 1) + 1]) : 'javascript:void(0)' }}"
                        class="text-dark text-decoration-none ms-3 {{ ($pagination['current_page'] ?? 1) >= ($pagination['last_page'] ?? 1) ? 'opacity-50 pe-none' : '' }}">
                        <i class="bx bx-chevron-right fs-5"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>
@endsection