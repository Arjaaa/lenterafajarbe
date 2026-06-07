@extends('layouts.admin')

@section('content')
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card bg-primary text-white">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-8">
                        <div class="card-body">
                            <h5 class="card-title text-white">Selamat Datang, {{ auth()->user()->name ?? 'Koordinator' }}!
                                🎉</h5>
                            <p class="mb-4 text-white">
                                Kamu sedang berada di <span class="fw-bold">Pusat Kendali</span>. Semua statistik dan
                                manajemen sistem Lentera Fajar ada di bawah pantauanmu hari ini.
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-4 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            {{-- <img
                                src="https://demos.themeselection.com/sneat-bootstrap-html-admin-template-free/assets/img/illustrations/man-with-laptop-light.png"
                                height="140" alt="View Badge User"> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-face"></i></span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Total Anak</span>
                    <h3 class="card-title mb-2">{{ $totalAnak }}</h3>
                    <small class="text-success fw-semibold"><i class="bx bx-check"></i> Terdaftar</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-success"><i class="bx bx-group"></i></span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Total Orang Tua</span>
                    <h3 class="card-title mb-2">{{ $totalOrtu }}</h3>
                    <small class="text-success fw-semibold"><i class="bx bx-check"></i> Akun Aktif</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-info"><i class="bx bx-user-voice"></i></span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Total Guru & Terapis</span>
                    <h3 class="card-title mb-2">{{ $totalGuru }}</h3>
                    <small class="text-info fw-semibold"><i class="bx bx-briefcase"></i> Siap Bertugas</small>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-6 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                            <span class="avatar-initial rounded bg-label-warning"><i class="bx bx-buildings"></i></span>
                        </div>
                    </div>
                    <span class="fw-semibold d-block mb-1">Total Kelas & Grup</span>
                    <h3 class="card-title mb-2">{{ $totalKelasAktif }}</h3>
                    <small class="text-warning fw-semibold"><i class="bx bx-chalkboard"></i> Sedang Berjalan</small>
                </div>
            </div>
        </div>

        <div class="col-12 order-3 order-md-2">
            <div class="card">
                <div class="card-header d-flex align-items-center justify-content-between pb-3">
                    <h5 class="card-title m-0 me-2">Sebaran Penempatan Saat Ini</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4 border-end">
                            <div class="fs-3 fw-semibold text-primary mb-1">{{ $totalKelasUmum }}</div>
                            <span class="text-muted d-block"><i class="bx bx-chalkboard me-1"></i>Kelas Reguler</span>
                        </div>
                        <div class="col-4 border-end">
                            <div class="fs-3 fw-semibold text-warning mb-1">{{ $totalGroupShadow }}</div>
                            <span class="text-muted d-block"><i class="bx bx-group me-1"></i>Group Shadow</span>
                        </div>
                        <div class="col-4">
                            <div class="fs-3 fw-semibold text-info mb-1">{{ $total1on1 }}</div>
                            <span class="text-muted d-block"><i class="bx bx-user-voice me-1"></i>Sesi 1 on 1</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection