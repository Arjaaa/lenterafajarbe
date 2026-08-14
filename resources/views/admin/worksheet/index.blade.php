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

        {{-- HEADER: Judul & Tanggal --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0 text-dark">Teacher Worksheet</h4>

            <div class="bg-white px-4 py-2 d-flex align-items-center"
                style="border-radius: 50px; border: 1px solid #e0ebfc; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <i class="bx bx-calendar text-success me-2 fs-5"
                    style="color: #4ade80 !important; background: #e8f5e9; padding: 5px; border-radius: 50%;"></i>
                <span class="fw-semibold text-dark" style="font-size: 0.9rem;">
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

        {{-- 4 KARTU STATISTIK ATAS --}}
        <div class="row g-3 mb-4 text-center">

            {{-- Card 1: Total Worksheet --}}
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: none;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="mb-2 bg-label-primary rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; background-color: #e0ebfc;">
                            <i class="bx bx-face text-primary fs-3" style="color: #5b9cf6 !important;"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-1">{{ $totalAssigned ?? 0 }}</h2>
                        <span class="text-muted" style="font-size: 0.85rem;">Total WorkSheet diberikan</span>
                    </div>
                </div>
            </div>

            {{-- Card 2: Worksheet Selesai (Donut Chart) --}}
            @php 
                $donePercent = ($totalAssigned ?? 0) > 0 ? round((($totalDone ?? 0) / $totalAssigned) * 100) : 0; 
            @endphp
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: none;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="mb-2 donut-chart"
                            style="background: conic-gradient(#5b9cf6 {{ $donePercent }}%, #e2e8f0 0);">
                            <div class="donut-inner"></div>
                            <span
                                class="fw-bold text-dark donut-text">{{ $totalDone ?? 0 }}/{{ $totalAssigned > 0 ? $totalAssigned : 100 }}</span>
                        </div>
                        <span class="text-muted" style="font-size: 0.85rem;">WorkSheet Selesai Bulan Ini</span>
                    </div>
                </div>
            </div>

            {{-- Card 3: Laporan Diterima (Donut Chart) --}}
            @php 
                $pendingPercent = ($totalAssigned ?? 0) > 0 ? round((($totalPending ?? 0) / $totalAssigned) * 100) : 0; 
            @endphp
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: none;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="mb-2 donut-chart"
                            style="background: conic-gradient(#5b9cf6 {{ $pendingPercent }}%, #e2e8f0 0);">
                            <div class="donut-inner"></div>
                            <span
                                class="fw-bold text-dark donut-text">{{ $totalPending ?? 0 }}/{{ $totalAssigned > 0 ? $totalAssigned : 100 }}</span>
                        </div>
                        <span class="text-muted" style="font-size: 0.85rem;">Laporan di Terima Hari Ini</span>
                    </div>
                </div>
            </div>

            {{-- Card 4: Lorem Ipsum --}}
            <div class="col-lg-3 col-md-6 col-12">
                <div class="card h-100 bg-white"
                    style="border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: none;">
                    <div class="card-body d-flex flex-column align-items-center justify-content-center py-4">
                        <div class="mb-2 rounded d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; background-color: #f3e8ff;">
                            <i class="bx bx-image fs-3" style="color: #a855f7;"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-1">67</h2>
                        <span class="text-muted" style="font-size: 0.85rem;">Lorem Impsum</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- TABEL DAFTAR WORKSHEET --}}
        <div class="card bg-white"
            style="border: 1px solid #a3c7fb; border-radius: 20px; box-shadow: none; overflow: hidden;">

            {{-- HEADER TABLE: Search Bar & Tombol Add --}}
            <div
                class="card-header d-flex flex-column flex-md-row justify-content-between align-items-center bg-white border-bottom p-4">

                {{-- Kiri: Form Search Kapsul --}}
                {{-- Kiri: Form Search Kapsul --}}
                <div class="mb-3 mb-md-0 w-100" style="max-width: 500px;">
                    <form action="{{ url()->current() }}" method="GET" class="d-flex align-items-center mb-3 mb-md-0 w-100">
                        <div class="input-group me-2"
                            style="border-radius: 50px; overflow: hidden; border: 1px solid #a3c7fb; background: white;">
                            <span class="input-group-text bg-transparent border-0 pe-1">
                                <i class="bx bx-search" style="color: #1e293b;"></i>
                            </span>
                            {{-- Tambahkan name="search" dan value --}}
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="form-control border-0 shadow-none px-2" placeholder="Cari judul, murid, guru..."
                                style="background: transparent;">
                        </div>

                        <button type="submit" class="btn rounded-pill px-4 fw-semibold text-white shadow-none flex-shrink-0"
                            style="background-color: #5b9cf6; border: none;">Cari</button>

                        {{-- Tombol Reset (Muncul kalau lagi nyari) --}}
                        @if(request()->filled('search'))
                            <a href="{{ url()->current() }}"
                                class="btn rounded-pill px-3 fw-semibold text-dark shadow-none d-flex align-items-center flex-shrink-0 ms-2"
                                style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                                <i class="bx bx-x me-1"></i> Reset
                            </a>
                        @endif
                    </form>
                </div>


            </div>

            <div class="table-responsive text-nowrap" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
                <table class="table custom-table-striped table-borderless" style="min-width: 1000px;">
                    <thead style="border-bottom: 1px solid #e0ebfc;">
                        <tr>
                            <th class="py-3 text-muted fw-semibold">No</th>
                            <th class="py-3 text-muted fw-semibold text-center">Media Type</th>
                            <th class="py-3 text-muted fw-semibold">Judul</th>
                            <th class="py-3 text-muted fw-semibold">Keterangan</th>
                            <th class="py-3 text-muted fw-semibold">Pembuat</th>
                            <th class="py-3 text-muted fw-semibold">Siswa</th>
                            <th class="text-center py-3 text-muted fw-semibold">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($worksheets ?? [] as $index => $worksheet)
                            <tr>
                                <td class="align-middle text-dark">{{ $index + 1 }}</td>

                                {{-- Teks Media Type --}}
                                <td class="align-middle text-center">
                                    @if(isset($worksheet->file_type) && $worksheet->file_type != '')
                                        <span class="badge bg-label-info fw-bold"
                                            style="border-radius: 8px;">{{ strtoupper($worksheet->file_type) }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>

                                <td class="align-middle text-dark fw-medium">{{ $worksheet->title ?? '-' }}</td>

                                <td class="align-middle text-dark">
                                    {{ \Illuminate\Support\Str::limit($worksheet->description ?? '-', 25) }}
                                </td>

                                <td class="align-middle text-dark">{{ $worksheet->uploaded_by->name ?? '-' }}</td>
                                <td class="align-middle text-dark">{{ $worksheet->student->name ?? '-' }}</td>

                                {{-- Action (View Hijau & Feedback Biru) --}}
                                <td class="text-center align-middle">
                                    <div class="d-flex justify-content-center gap-2">
                                        {{-- Tombol View --}}
                                        @if(isset($worksheet->file_url) && $worksheet->file_url != '')
                                            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none"
                                                style="font-size: 0.75rem; background-color: #4ade80; border: none;"
                                                data-bs-toggle="modal" data-bs-target="#modalPreviewMedia"
                                                onclick="previewMedia('{{ $worksheet->file_url }}', '{{ $worksheet->file_type ?? 'image' }}')">
                                                <i class="bx bx-info-circle me-1" style="font-size: 0.9rem;"></i> View
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none"
                                                style="font-size: 0.75rem; background-color: #4ade80; border: none; opacity: 0.5;"
                                                disabled>
                                                <i class="bx bx-info-circle me-1" style="font-size: 0.9rem;"></i> View
                                            </button>
                                        @endif

                                        {{-- Tombol Beri Feedback --}}
                                        {{-- <button type="button"
                                            class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none"
                                            style="font-size: 0.75rem; background-color: #2ea4ff; border: none;">
                                            <i class="bx bx-message-square-error me-1" style="font-size: 0.9rem;"></i> Beri
                                            Feedback
                                        </button> --}}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- Dummy Data Pengisi jika kosong --}}
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td class="align-middle py-3">{{ $i + 1 }}</td>
                                    <td class="align-middle text-center">
                                        <span class="badge bg-label-secondary fw-bold" style="border-radius: 8px;">PDF</span>
                                    </td>
                                    <td colspan="4" class="py-4"></td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none"
                                                style="font-size: 0.75rem; background-color: #4ade80; border: none;">
                                                <i class="bx bx-info-circle me-1" style="font-size: 0.9rem;"></i> View
                                            </button>
                                            <button type="button" class="btn btn-sm rounded-pill px-3 py-1 text-white shadow-none"
                                                style="font-size: 0.75rem; background-color: #2ea4ff; border: none;">
                                                <i class="bx bx-message-square-error me-1" style="font-size: 0.9rem;"></i> Beri
                                                Feedback
                                            </button>
                                        </div>
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

        {{-- FOOTER COPYRIGHT --}}
        <div class="text-center mt-4 mb-2">
            <small class="text-muted fw-semibold">&copy; 2026 , Sistem Manajemen Sekolah Lentera Fajar.</small>
        </div>

        {{-- ========================================================== --}}
        {{-- SEMUA MODAL DITARUH DI SINI (LUAR TABEL) AGAR LAYOUT AMAN --}}
        {{-- ========================================================== --}}

        {{-- MODAL ASSIGN WORKSHEET --}}
        <div class="modal fade" id="modalAssignWorksheet" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content" style="border-radius: 20px; border: none;">
                    <div class="modal-header border-bottom p-4">
                        <h5 class="modal-title fw-bold text-dark">Assign Worksheet Baru</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <form action="{{ route('koor.worksheet.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body p-4 text-start">
                            <div class="mb-3">
                                <label class="form-label text-dark fw-semibold">Judul Worksheet <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" placeholder="Contoh: Latihan Menulis"
                                    required />
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-dark fw-semibold">Deskripsi Tugas</label>
                                <textarea name="description" class="form-control" rows="3"
                                    placeholder="Instruksi pengerjaan..."></textarea>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-semibold">Pilih Anak <span
                                            class="text-danger">*</span></label>
                                    <select name="student_id" class="form-select" required>
                                        <option value="" selected disabled>-- Pilih Anak --</option>
                                        @foreach($students ?? [] as $student)
                                            @if(isset($student->id))
                                                <option value="{{ $student->id }}">{{ $student->name ?? 'Tanpa Nama' }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-semibold">Assign ke Guru <span
                                            class="text-danger">*</span></label>
                                    <select name="teacher_id" class="form-select" required>
                                        <option value="" selected disabled>-- Pilih Guru --</option>
                                        @foreach($teachers ?? [] as $teacher)
                                            @if(isset($teacher->id))
                                                <option value="{{ $teacher->id }}">{{ $teacher->name ?? 'Tanpa Nama' }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row g-3 mb-0">
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-semibold">Deadline <span
                                            class="text-danger">*</span></label>
                                    <input type="datetime-local" name="deadline" class="form-control" required />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-dark fw-semibold">Upload Lampiran</label>
                                    <input type="file" name="media" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-top p-4 pt-3">
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                                data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4"
                                style="background-color: #5b9cf6; border: none;">Kirim Tugas</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL PREVIEW MEDIA --}}
        <div class="modal fade" id="modalPreviewMedia" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content" style="border-radius: 20px; border: none;">
                    <div class="modal-header border-bottom p-4">
                        <h5 class="modal-title fw-bold text-dark"><i class="bx bx-image me-2 text-primary"></i>Preview Media
                        </h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center p-4 bg-light" id="mediaContainer">
                        {{-- Konten preview JS --}}
                    </div>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="{{ asset('assets/js/worksheet.js') }}"></script>
    @endpush
@endsection