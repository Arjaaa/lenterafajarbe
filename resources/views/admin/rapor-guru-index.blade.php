@extends('layouts.admin')

@section('content')
    {{-- CSS Khusus untuk Striping Tabel & Mencegah Teks Turun Baris --}}
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

        /* CSS Donut Chart Murni */
        .donut-chart {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .donut-inner {
            width: 52px;
            height: 52px;
            background: white;
            border-radius: 50%;
            position: absolute;
        }

        .donut-text {
            position: relative;
            z-index: 1;
            font-size: 0.9rem;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HEADER: Judul --}}
        <div class="mb-4">
            <h4 class="fw-bold mb-0 text-dark">Raport Guru</h4>
        </div>

        {{-- NOTIFIKASI SUKSES --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 15px;">
                <strong></strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- NOTIFIKASI ERROR --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 15px;">
                {{ $errors->first('error') ?? $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- 4 KARTU STATISTIK ATAS --}}
        <div class="row g-3 mb-4 text-center">

            {{-- Card 1: Raport Telah Diberi Feedback --}}
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: none;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        @php
                            $fbTotal = $stats['feedback_total'] ?? 0;
                            $fbGiven = $stats['feedback_given'] ?? 0;
                            $fbPercent = $fbTotal > 0 ? round(($fbGiven / $fbTotal) * 100) : 0;
                        @endphp
                        <div class="mb-2 donut-chart"
                            style="background: conic-gradient(#5b9cf6 {{ $fbPercent }}%, #e2e8f0 0);">
                            <div class="donut-inner"></div>
                            <span class="fw-bold text-dark donut-text">{{ $fbGiven }}/{{ $fbTotal }}</span>
                        </div>
                        <span class="text-muted" style="font-size: 0.85rem;">Raport Telah diberi Feedback</span>
                    </div>
                </div>
            </div>

            {{-- Card 2: Rapor Diterima Bulan Lalu --}}
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: none;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        @php
                            $lmReports = $stats['last_month_reports'] ?? 0;
                            $lmFeedback = $stats['last_month_feedback'] ?? 0;
                            $lmPercent = $lmReports > 0 ? round(($lmFeedback / $lmReports) * 100) : 0;
                        @endphp
                        <div class="mb-2 donut-chart"
                            style="background: conic-gradient(#5b9cf6 {{ $lmPercent }}%, #e2e8f0 0);">
                            <div class="donut-inner"></div>
                            <span class="fw-bold text-dark donut-text">{{ $lmFeedback }}/{{ $lmReports }}</span>
                        </div>
                        <span class="text-muted" style="font-size: 0.85rem;">Rapor Diterima Bulan Lalu</span>
                    </div>
                </div>
            </div>

            {{-- Card 3: Total Seluruh Rapor Guru --}}
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: none;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="mb-2 rounded d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; background-color: #e0ebfc;">
                            <i class="bx bx-clipboard fs-3" style="color: #5b9cf6;"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-1">{{ $stats['total_reports'] ?? 0 }}</h2>
                        <span class="text-muted" style="font-size: 0.85rem;">Total Seluruh Rapor Guru</span>
                    </div>
                </div>
            </div>

            {{-- Card 4: Total Seluruh Guru --}}
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: none;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="mb-2 rounded d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; background-color: #f3e8ff;">
                            <i class="bx bx-user-pin fs-3" style="color: #a855f7;"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-1">{{ $stats['total_guru'] ?? 0 }}</h2>
                        <span class="text-muted" style="font-size: 0.85rem;">Total Seluruh Guru</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABEL DAFTAR GURU --}}
        <div class="card bg-white"
            style="border: 1px solid #a3c7fb; border-radius: 20px; box-shadow: none; overflow: hidden;">

            {{-- HEADER TABLE: Search Bar & Filter Button --}}
            <div class="card-header d-flex flex-column flex-md-row align-items-center bg-white border-bottom p-4">
                <div class="d-flex align-items-stretch w-100" style="max-width: 550px; gap: 12px;">
                    <div class="input-group"
                        style="border-radius: 50px; overflow: hidden; border: 1px solid #a3c7fb; background: white; flex-grow: 1;">
                        <span class="input-group-text bg-transparent border-0 pe-1 ps-3">
                            <i class="bx bx-search" style="color: #a3c7fb;"></i>
                        </span>
                        <input type="text" class="form-control border-0 shadow-none px-2" placeholder="Cari..."
                            style="background: transparent;">
                        <button class="btn fw-semibold text-white px-4 m-0 shadow-none"
                            style="background-color: #5b9cf6; border: none; border-radius: 0;">
                            Cari
                        </button>
                    </div>

                    {{-- <button type="button"
                        class="btn rounded-pill fw-semibold text-white shadow-none px-4 d-flex align-items-center"
                        style="background-color: #f97316; border: none;">
                        <i class="bx bx-filter-alt me-1"></i> Filter
                    </button> --}}
                </div>
            </div>

            <div class="table-responsive text-nowrap" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table custom-table-striped table-borderless" style="min-width: 900px;">
                    <thead style="border-bottom: 1px solid #e0ebfc;">
                        <tr>
                            <th class="py-3 text-muted fw-semibold text-center" style="width: 5%;">No</th>
                            {{-- <th class="py-3 text-muted fw-semibold text-center" style="width: 10%;">Photo</th> --}}
                            <th class="py-3 text-muted fw-semibold">Nama</th>
                            <th class="py-3 text-muted fw-semibold">Email / Kontak</th>
                            <th class="py-3 px-4 text-muted fw-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers ?? [] as $index => $teacher)
                            <tr>
                                <td class="align-middle text-dark text-center">{{ $index + 1 }}</td>
                                {{--
                                <td class="align-middle text-center">
                                    <div class="avatar mx-auto" style="width: 40px; height: 40px;">
                                        <span class="avatar-initial rounded-circle" style="background-color: #cbd5e1;"></span>
                                    </div>
                                </td> --}}

                                <td class="align-middle text-dark fw-medium">{{ $teacher->teacher->name ?? '-' }}</td>
                                <td class="align-middle text-dark">{{ $teacher->teacher->phone ?? '-' }}</td>

                                {{-- Action: modal TIDAK ditaruh di sini, cuma tombolnya --}}
                                <td class="align-middle px-4"> {{-- Hapus text-center, ganti pakai px-4 biar ada jarak dari kiri
                                    --}}
                                    <div class="d-flex justify-content-start align-items-center gap-2">

                                        {{-- Tombol View (Dikasih min-width biar seragam) --}}
                                        <a href="{{ route('koor.raporGuruDetail', $teacher->id ?? 0) }}"
                                            class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none d-flex justify-content-center align-items-center"
                                            style="font-size: 0.75rem; background-color: #4ade80; border: none; min-width: 85px;">
                                            <i class="bx bx-info-circle me-1" style="font-size: 0.9rem;"></i> View
                                        </a>

                                        {{-- Tombol Feedback (Akan otomatis ngikut di kanannya kalau ada) --}}
                                        @if (!($teacher->has_feedback ?? false))
                                            <button type="button"
                                                class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none d-flex justify-content-center align-items-center"
                                                style="font-size: 0.75rem; background-color: #2ea4ff; border: none;"
                                                data-bs-toggle="modal" data-bs-target="#feedbackModal{{ $teacher->id }}">
                                                <i class="bx bx-message-square-error me-1" style="font-size: 0.9rem;"></i> Beri
                                                Feedback
                                            </button>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- Dummy Data Pengisi jika kosong --}}
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td class="align-middle text-center py-3">{{ $i + 1 }}</td>
                                    <td class="align-middle text-center">
                                        <div class="avatar mx-auto" style="width: 40px; height: 40px;">
                                            <span class="avatar-initial rounded-circle" style="background-color: #cbd5e1;"></span>
                                        </div>
                                    </td>
                                    <td colspan="2" class="py-4"></td>
                                    <td class="text-center align-middle">
                                        <span class="text-muted" style="font-size: 0.8rem;">-</span>
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

        {{-- ================================================= --}}
        {{-- SEMUA MODAL FEEDBACK DITARUH DI SINI, DI LUAR TABEL --}}
        {{-- Ini WAJIB di luar .table-responsive, kalau nggak modal --}}
        {{-- akan ke-crop sama overflow-x: auto milik tabel --}}
        {{-- ================================================= --}}
        @foreach($teachers ?? [] as $teacher)
            @if (!($teacher->has_feedback ?? false))
                <div class="modal fade" id="feedbackModal{{ $teacher->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content" style="border-radius: 16px;">
                            <form action="{{ route('koor.raporGuru.feedback', $teacher->id) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Feedback untuk {{ $teacher->teacher->name ?? '-' }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-start">
                                    <textarea name="coordinator_recommendation" class="form-control mb-3" rows="4"
                                        placeholder="Tulis rekomendasi untuk guru ini..."
                                        required>{{ $teacher->coordinator_recommendation }}</textarea>

                                    <label class="form-label small text-muted">Indikator Performa (opsional)</label>
                                    <select name="performance_indicator" class="form-select">
                                        <option value="">-- Tidak diubah --</option>
                                        <option value="sangat_baik" {{ ($teacher->performance_indicator ?? '') == 'sangat_baik' ? 'selected' : '' }}>Sangat Baik</option>
                                        <option value="baik" {{ ($teacher->performance_indicator ?? '') == 'baik' ? 'selected' : '' }}>Baik</option>
                                        <option value="cukup" {{ ($teacher->performance_indicator ?? '') == 'cukup' ? 'selected' : '' }}>Cukup</option>
                                        <option value="kurang" {{ ($teacher->performance_indicator ?? '') == 'kurang' ? 'selected' : '' }}>Kurang</option>
                                        <option value="sangat_kurang" {{ ($teacher->performance_indicator ?? '') == 'sangat_kurang' ? 'selected' : '' }}>Sangat Kurang</option>
                                    </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn text-white" style="background-color: #2ea4ff;">Kirim</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach



    </div>
@endsection