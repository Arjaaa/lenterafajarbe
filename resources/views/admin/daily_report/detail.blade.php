@extends('layouts.admin')

@section('content')
    <style>
        /* Styling Konsisten dengan Index */
        .custom-card {
            border-radius: 20px;
            border: 1px solid #f0f4f9;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
            background-color: #ffffff;
        }

        .icon-box {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
        }

        .note-box {
            background-color: #f4f8ff;
            border-radius: 12px;
            padding: 15px;
            border-left: 4px solid #5b9cf6;
        }

        .note-box-danger {
            background-color: #fef2f2;
            border-radius: 12px;
            padding: 15px;
            border-left: 4px solid #f87171;
        }

        .note-box-success {
            background-color: #e8f5e9;
            border-radius: 12px;
            padding: 15px;
            border-left: 4px solid #4ade80;
        }

        .text-label {
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 500;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">

        {{-- HEADER --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div class="d-flex align-items-center">
                <a href="{{ route('koor.dailyReport.index') }}"
                    class="btn btn-sm rounded-circle p-2 me-3 shadow-none d-flex justify-content-center align-items-center"
                    style="background-color: #5b9cf6; border: none; width: 38px; height: 38px; transition: 0.2s;">
                    <i class="bx bx-arrow-back fs-5 text-white"></i>
                </a>
                <h4 class="fw-bold mb-0" style="color: #1e293b;">Detail Laporan Harian</h4>
            </div>
            <div class="bg-white px-4 py-2 d-flex align-items-center custom-card"
                style="border-radius: 50px; padding-top: 8px !important; padding-bottom: 8px !important;">
                <div class="d-flex align-items-center justify-content-center me-2"
                    style="width: 32px; height: 32px; background-color: #e8f5e9; border-radius: 50%;">
                    <i class="bx bx-calendar text-success fs-6"></i>
                </div>
                <span class="fw-bold text-dark" style="font-size: 0.95rem;">
                    {{ \Carbon\Carbon::parse($report->created_at ?? now())->locale('id')->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>

        {{-- 1. IDENTITAS SISWA & TERAPIS --}}
        {{-- 1. IDENTITAS SISWA & TERAPIS --}}
        <div class="card custom-card mb-4 p-3">
            <div class="card-body d-flex flex-column flex-md-row align-items-md-center p-2">

                @php
                    // Logika Dinamis Warna Status Kehadiran (Tanpa Icon)
                    $rawStatus = strtolower($report->attendance_status ?? 'hadir');

                    if ($rawStatus === 'sakit') {
                        $statusColor = 'warning';       // Kuning
                    } elseif (in_array($rawStatus, ['izin', 'excused'])) {
                        $statusColor = 'info';          // Biru
                    } elseif (in_array($rawStatus, ['alfa', 'absent'])) {
                        $statusColor = 'danger';        // Merah
                    } else {
                        $statusColor = 'success';       // Hijau (Default Hadir)
                    }
                @endphp

                <div class="avatar avatar-xl me-md-4 mb-4 mb-md-0" style="width: 90px; height: 90px;">
                    @if(!empty($report->student?->photo))
                        <img src="{{ str_starts_with($report->student?->photo, 'http') ? $report->student?->photo : asset('storage/' . $report->student?->photo) }}"
                            alt="Avatar" class="rounded-circle"
                            style="object-fit: cover; width: 100%; height: 100%; border: 3px solid #e0ebfc;" />
                    @else
                        <span class="avatar-initial rounded-circle fs-1"
                            style="background-color: #f1f5f9; color: #94a3b8; border: 3px solid #e0ebfc;"><i
                                class="bx bx-user"></i></span>
                    @endif
                </div>

                <div class="flex-grow-1">
                    <h4 class="fw-bold text-dark mb-3">{{ $report->student?->name ?? '-' }}</h4>
                    <div class="row g-3">
                        <div class="col-md-4 col-6">
                            <span class="text-label d-block mb-1">Status Kehadiran</span>
                            {{-- Text Badge Dinamis --}}
                            <span
                                class="badge bg-label-{{ $statusColor }} rounded-pill px-3 py-1 fw-bold">{{ $report->attendance_status_label ?? 'Hadir' }}</span>
                        </div>
                        <div class="col-md-4 col-6 border-start ps-md-4">
                            <span class="text-label d-block mb-1">Terapis / Guru</span>
                            <span
                                class="text-dark fw-bold fs-6">{{ $report->therapist?->name ?? $report->teacher?->name ?? '-' }}</span>
                        </div>
                        <div class="col-md-4 col-12 border-start ps-md-4 mt-3 mt-md-0">
                            <span class="text-label d-block mb-1">Waktu Submit</span>
                            <span
                                class="text-dark fw-bold fs-6">{{ \Carbon\Carbon::parse($report->created_at ?? now())->locale('id')->translatedFormat('H:i') }}
                                WIB</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. GRID KONDISI (FISIK, ENERGI, KEMANDIRIAN) --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card custom-card h-100 p-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box me-3" style="background-color: #e0ebfc;"><i class="bx bx-pulse fs-3"
                                    style="color: #5b9cf6;"></i></div>
                            <h6 class="fw-bold mb-0 text-dark fs-5">Kondisi Fisik</h6>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-light">
                            <span class="text-label">Saat Datang</span>
                            <span
                                class="text-dark fw-bold">{{ $report->detail?->physical_condition_arrival_label ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-label">Saat Pulang</span>
                            <span
                                class="text-dark fw-bold">{{ $report->detail?->physical_condition_end_label ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card custom-card h-100 p-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box me-3" style="background-color: #e8f5e9;"><i class="bx bx-bolt-circle fs-3"
                                    style="color: #4ade80;"></i></div>
                            <h6 class="fw-bold mb-0 text-dark fs-5">Energi Fisik</h6>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom border-light">
                            <span class="text-label">Saat Datang</span>
                            <span
                                class="text-dark fw-bold">{{ $report->detail?->physical_energy_arrival_label ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-label">Saat Pulang</span>
                            <span class="text-dark fw-bold">{{ $report->detail?->physical_energy_end_label ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card custom-card h-100 p-2">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box me-3" style="background-color: #f3e8ff;"><i class="bx bx-body fs-3"
                                    style="color: #a855f7;"></i></div>
                            <h6 class="fw-bold mb-0 text-dark fs-5">Kemandirian</h6>
                        </div>
                        <div class="mt-auto note-box text-center" style="border-left: none; background-color: #f8faff;">
                            <span class="fw-bold fs-6" style="color: #5b9cf6; white-space: normal;">
                                {{ $report->detail?->independence_label ?? 'Belum ada data' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. PERILAKU, RESPON, TANTANGAN --}}
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card custom-card h-100 p-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box me-3" style="background-color: #fff7e6;"><i class="bx bx-smile fs-3"
                                    style="color: #f59e0b;"></i></div>
                            <h6 class="fw-bold mb-0 text-dark fs-5">Perilaku & Mood</h6>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <span class="text-label" style="width: 100px;">Mood Datang</span>
                            <span class="fs-5 me-2">{{ $report->detail?->mood_arrival_emoji ?? '' }}</span>
                            <span class="text-dark fw-bold">{{ $report->detail?->mood_arrival_label ?? '-' }}</span>
                        </div>
                        <div class="d-flex align-items-center pb-3 border-bottom border-light mb-3">
                            <span class="text-label" style="width: 100px;">Mood Pulang</span>
                            <span class="fs-5 me-2">{{ $report->detail?->mood_end_emoji ?? '' }}</span>
                            <span class="text-dark fw-bold">{{ $report->detail?->mood_end_label ?? '-' }}</span>
                        </div>

                        <div class="mb-3">
                            <span
                                class="badge bg-label-warning rounded-pill px-3 py-2 fw-bold">{{ $report->detail?->behavior_label ?? 'Tidak ada label' }}</span>
                        </div>

                        @if(!empty($report->detail?->behavior_notes) && $report->detail?->behavior_notes !== '-')
                            <div class="note-box" style="border-left-color: #f59e0b; background-color: #fffaf0; padding: 12px;">
                                <span class="text-label d-block mb-1">Catatan Tambahan</span>
                                <span class="text-dark fw-medium">{{ $report->detail?->behavior_notes }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card custom-card h-100 p-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box me-3" style="background-color: #ffe6e6;"><i class="bx bx-flag fs-3"
                                    style="color: #f87171;"></i></div>
                            <h6 class="fw-bold mb-0 text-dark fs-5">Tantangan</h6>
                        </div>

                        <div class="mb-3">
                            <span
                                class="badge bg-label-danger rounded-pill px-3 py-2 fw-bold">{{ $report->detail?->challenge_label ?? 'Aman' }}</span>
                        </div>

                        @if(!empty($report->detail?->challenge_other) && trim($report->detail?->challenge_other) !== trim($report->detail?->challenge_label))
                            <div class="note-box-danger p-3 mt-2">
                                <span class="text-label d-block mb-1" style="color: #ef4444;">Detail Tantangan</span>
                                <span class="text-dark fw-medium">{{ $report->detail?->challenge_other }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card custom-card h-100 p-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="icon-box me-3" style="background-color: #e8f5e9;"><i class="bx bx-bulb fs-3"
                                    style="color: #4ade80;"></i></div>
                            <h6 class="fw-bold mb-0 text-dark fs-5">Solusi / Tindakan</h6>
                        </div>
                        <div class="note-box-success p-3 h-auto">
                            <p class="text-dark fw-medium mb-0">
                                {{ $report->detail?->solution_notes ?? 'Tidak ada catatan solusi.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. AKTIVITAS, RESPON SOSIAL & DOKUMENTASI (FOTO) --}}
        <div class="row g-4 mb-4">
            {{-- Kolom Catatan Aktivitas & Respon Sosial --}}
            <div class="col-md-5">
                <div class="card custom-card h-100 p-2">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <i class="bx bx-book-open fs-4 me-2" style="color: #5b9cf6;"></i>
                            <h6 class="fw-bold mb-0 text-dark fs-5">Aktivitas Hari Ini</h6>
                        </div>
                        <div class="note-box mb-4">
                            <p class="text-dark fw-medium mb-0" style="min-height: 40px;">
                                {{ $report->detail?->activity_notes ?? 'Belum ada catatan aktivitas.' }}
                            </p>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <i class="bx bx-group fs-4 me-2" style="color: #a855f7;"></i>
                            <h6 class="fw-bold mb-0 text-dark fs-5">Interaksi & Respon</h6>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex justify-content-between align-items-center border-bottom border-light pb-2">
                                <span class="text-label">Respon Umum</span>
                                <span class="text-dark fw-bold">{{ $report->detail?->response_label ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center border-bottom border-light pb-2">
                                <span class="text-label">Interaksi ke Guru</span>
                                <span
                                    class="text-dark fw-bold">{{ $report->detail?->social_with_teacher_label ?? '-' }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-label">Interaksi ke Teman</span>
                                <span
                                    class="text-dark fw-bold">{{ $report->detail?->social_with_peers_label ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kolom Dokumentasi Foto --}}
            <div class="col-md-7">
                <div class="card custom-card h-100 p-2">
                    <div class="card-body">
                        <div
                            class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between mb-4 pb-3 border-bottom border-light gap-2">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-images fs-4 me-2" style="color: #5b9cf6;"></i>
                                <h6 class="fw-bold mb-0 text-dark fs-5">Dokumentasi Laporan</h6>
                            </div>
                            @if(!empty($report->detail?->achievement_tag_label))
                                <span class="badge rounded-pill px-3 py-2 fw-bold"
                                    style="background-color: #fff7e6; color: #f59e0b; border: 1px solid #fde68a;">
                                    <i class="bx bxs-star me-1"></i> {{ $report->detail?->achievement_tag_label }}
                                </span>
                            @endif
                        </div>

                        <div class="row g-3">
                            @php
                                $rawPhotos = $report->documentations ?? $report->photos ?? $report->detail?->photo_activity ?? [];
                                $validPhotos = [];

                                if (is_iterable($rawPhotos)) {
                                    foreach ($rawPhotos as $photo) {
                                        $url = is_object($photo) ? ($photo->url ?? $photo->file_path ?? $photo->file_url ?? '') : $photo;
                                        if (!empty(trim($url)) && strlen(trim($url)) > 5) {
                                            $validPhotos[] = trim($url);
                                        }
                                    }
                                }
                            @endphp

                            @if(count($validPhotos) > 0)
                                @foreach($validPhotos as $imgUrl)
                                    <div class="col-6 col-sm-4 col-md-3">
                                        <a href="{{ $imgUrl }}" target="_blank" class="d-block overflow-hidden"
                                            style="border-radius: 12px; border: 1px solid #f0f4f9;">
                                            <img src="{{ $imgUrl }}" alt="Dokumentasi" class="img-fluid w-100"
                                                style="height: 120px; object-fit: cover; transition: transform 0.3s ease;"
                                                onmouseover="this.style.transform='scale(1.05)'"
                                                onmouseout="this.style.transform='scale(1)'">
                                        </a>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12 text-center py-5">
                                    <div class="p-4"
                                        style="background-color: #f8faff; border-radius: 16px; border: 1px dashed #cbd5e1;">
                                        <i class="bx bx-image-alt text-muted d-block mb-2" style="font-size: 3rem;"></i>
                                        <span class="text-muted fw-medium">Belum ada foto yang dilampirkan.</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection