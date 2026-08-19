@extends('layouts.admin')

@section('content')
    <style>
        .custom-table-striped tbody tr:nth-of-type(even) {
            background-color: #f4f8ff !important;
        }

        .custom-table-striped tbody tr td {
            border-bottom: none !important;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- =============================================== --}}
        {{-- HEADER: TOMBOL BACK & JUDUL --}}
        {{-- =============================================== --}}
        <div class="d-flex align-items-center mb-4">
            <a href="{{ url()->previous() }}"
                class="btn btn-sm rounded-circle p-2 me-3 shadow-none d-flex justify-content-center align-items-center"
                style="border: none; background-color: #5b9cf6; width: 35px; height: 35px;">
                <i class="bx bx-arrow-back fs-5 text-white"></i>
            </a>
            <h4 class="fw-bold mb-0 text-dark">Detail Laporan Siswa</h4>
        </div>

        {{-- =============================================== --}}
        {{-- KAPSUL PROFIL SISWA --}}
        {{-- =============================================== --}}
        <div class="card bg-white mb-4" style="border-radius: 20px; border: 1px solid #e0ebfc; box-shadow: none;">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="avatar avatar-xl me-4" style="width: 70px; height: 70px;">
                    @if(!empty($siswa->photo))
                        <img src="{{ str_starts_with($siswa->photo, 'http') ? $siswa->photo : asset('storage/' . $siswa->photo) }}"
                            alt="Avatar" class="rounded-circle" style="object-fit: cover; width: 100%; height: 100%;" />
                    @else
                        <span class="avatar-initial rounded-circle fs-2" style="background-color: #cce5ff; color: #5b9cf6;">
                            <i class="bx bx-user"></i>
                        </span>
                    @endif
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-1">{{ $siswa->name ?? 'Nama Siswa' }}</h5>
                    <div class="d-flex align-items-center mt-2">
                        <span class="badge bg-label-primary rounded-pill px-3 me-2">
                            {{ ucwords(str_replace('_', ' ', $siswa->special_needs ?? 'Reguler')) }}
                        </span>
                        <span class="text-muted" style="font-size: 0.85rem;">
                            <i class="bx bx-male-female me-1"></i>
                            {{ ucwords(str_replace('-', ' ', $siswa->gender ?? '-')) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- =============================================== --}}
        {{-- TABEL RIWAYAT RAPORT / DAILY REPORT --}}
        {{-- =============================================== --}}
        <div class="card bg-white"
            style="border-radius: 20px; border: 1px solid #e0ebfc; box-shadow: none; overflow: hidden;">
            <div
                class="card-header bg-white border-bottom p-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
                <h5 class="fw-bold mb-3 mb-md-0 text-dark">Riwayat Laporan</h5>

                <div class="d-flex flex-column flex-md-row gap-2 w-100 justify-content-md-end" style="max-width: 600px;">
                    {{-- Form Search Kapsul --}}
                    {{-- <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center w-100"
                        style="max-width: 400px;">
                        <div class="input-group me-2"
                            style="border-radius: 50px; overflow: hidden; border: 1px solid #a3c7fb; background: white;">
                            <span class="input-group-text bg-transparent border-0 pe-1">
                                <i class="bx bx-search" style="color: #1e293b;"></i>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control border-0 shadow-none px-2" placeholder="Cari terapis, status..."
                                style="background: transparent;">
                        </div>
                        <button type="submit" class="btn rounded-pill px-4 fw-semibold text-white shadow-none flex-shrink-0"
                            style="background-color: #5b9cf6; border: none;">Cari</button>

                        @if(request()->filled('search'))
                        <a href="{{ url()->current() }}"
                            class="btn rounded-pill px-3 fw-semibold text-dark shadow-none d-flex align-items-center flex-shrink-0 ms-2"
                            style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                            <i class="bx bx-x me-1"></i> Reset
                        </a>
                        @endif
                    </form> --}}

                    {{-- Tombol PDF --}}
                    {{-- <button
                        class="btn rounded-pill px-4 fw-semibold text-white shadow-none d-flex align-items-center flex-shrink-0"
                        style="background-color: #4ade80; border: none;">
                        <i class="bx bx-download me-1"></i> Download PDF
                    </button> --}}
                </div>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table custom-table-striped table-borderless">
                    <thead style="border-bottom: 2px solid #f0f4f9;">
                        <tr>
                            <th class="text-center py-3 text-muted fw-semibold">No</th>
                            <th class="py-3 text-muted fw-semibold">Tanggal</th>
                            {{-- <th class="py-3 text-muted fw-semibold">Terapis / Guru</th> --}}
                            <th class="py-3 text-muted fw-semibold">Status Kehadiran</th>
                            <th class="text-center py-3 text-muted fw-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Asumsi data reportnya ada di $siswa->reports atau passing terpisah --}}
                        @forelse ($siswa->reports ?? [] as $index => $report)
                            <tr>
                                <td class="text-center align-middle text-dark fw-medium">
                                    {{ $index + 1 + (($pagination['current_page'] ?? 1) - 1) * 10 }}
                                </td>
                                <td class="align-middle text-dark fw-bold">
                                    {{ \Carbon\Carbon::parse($report->date ?? now())->locale('id')->translatedFormat('d F Y') }}
                                </td>
                                {{-- <td class="align-middle text-dark">{{ $report->teacher->name ?? '-' }}</td> --}}
                                <td class="align-middle">
                                    @php
                                        $status = strtolower($report->attendance_status ?? '');
                                    @endphp

                                    @if($status == 'hadir')
                                        <span class="badge rounded-pill bg-label-success px-3">Hadir</span>
                                    @elseif($status == 'sakit')
                                        <span class="badge rounded-pill bg-label-warning px-3">Sakit</span>
                                    @else
                                        <span class="badge rounded-pill bg-label-danger px-3">Izin</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    {{-- LOGIKA TOMBOL VIEW --}}
                                    @if($status == 'hadir')
                                        {{-- Jika Hadir, arahkan ke route detail daily report --}}
                                        {{-- Pastikan nama route-nya sesuai dengan yang ada di web.php kamu ya --}}
                                        <a href="{{ route('koor.dailyReport.detail', $report->id) }}"
                                            class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none"
                                            style="font-size: 0.75rem; background-color: #5b9cf6; border: none;"
                                            title="Lihat Detail">
                                            <i class="bx bx-info-circle me-1"></i> Baca Laporan
                                        </a>
                                    @else
                                        {{-- Jika Tidak Hadir, tampilkan strip --}}
                                        <span class="text-muted fw-bold">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bx bx-folder-open fs-1 d-block mb-2"></i> Belum ada riwayat laporan untuk siswa
                                    ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
                {{-- PAGINATION UI --}}
                @if(isset($pagination) && $pagination['last_page'] > 1)
                    <div class="card-footer bg-white border-top text-center py-4">
                        <div class="d-inline-flex align-items-center bg-white px-3 py-2 border shadow-sm"
                            style="border-radius: 50px;">
                            {{-- Tombol Previous --}}
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
                @endif
            </div>
        </div>



    </div>
@endsection