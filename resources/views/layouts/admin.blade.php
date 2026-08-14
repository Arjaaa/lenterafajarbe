<!doctype html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="{{ asset('assets') }}/"
    data-template="vertical-menu-template-free">
{{-- NProgress CSS & JS (Loading Bar Pucuk Layar) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>

<style>
    /* Ubah warna loading bar sesuai tema biru kamu */
    #nprogress .bar {
        background: #5b9cf6 !important;
        height: 4px !important;
    }

    #nprogress .peg {
        box-shadow: 0 0 10px #5b9cf6, 0 0 5px #5b9cf6 !important;
    }
</style>

<script>
    // Konfigurasi NProgress
    NProgress.configure({ showSpinner: false, speed: 400, minimum: 0.2 });

    // Mulai loading saat klik link (tag <a>)
    document.addEventListener('click', function (e) {
        let target = e.target.closest('a');
        // Cek kalau yang diklik beneran link dan bukan hashtag atau tab baru
        if (target && target.href && !target.href.includes('javascript:') && !target.href.includes('#') && target.target !== '_blank') {
            NProgress.start();
        }
    });

    // Mulai loading saat submit form
    document.addEventListener('submit', function () {
        NProgress.start();
    });

    // Selesai loading saat halaman beres dimuat
    window.addEventListener('load', function () {
        NProgress.done();
    });
</script>

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Lentera Fajar - Dashboard Koordinator</title>

    <meta name="description" content="Sistem Manajemen Sekolah Lentera Fajar" />

    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/fonts/iconify-icons.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/custom-colors.css') }}" />

    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/apex-charts/apex-charts.css') }}" />

    <script src="{{ asset('assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('assets/js/config.js') }}"></script>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">

            {{-- SIDEBAR: Ditambah d-flex flex-column agar bisa mendorong profil ke bawah --}}
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme d-flex flex-column">

                {{-- LOGO BRAND (Panah ungu sudah dibuang bersih) --}}
                <div class="app-brand demo">
                    <a href="{{ route('koor.dashboard') }}" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <img src="{{ asset('assets/img/illustrations/WaldenFajar.png') }}" alt="Logo Lentera Fajar"
                                width="40">
                        </span>
                        <span class="app-brand-text demo menu-text fw-bold ms-2 text-lentera-blue-dark">Lentera</span>
                    </a>
                </div>

                <div class="menu-divider mt-0"></div>
                <div class="menu-inner-shadow"></div>

                {{-- MENU LIST: Ditambah flex-grow-1 agar mengambil sisa ruang kosong --}}
                <ul class="menu-inner py-1 flex-grow-1 overflow-auto">

                    <li class="menu-item {{ request()->is('koor/dashboard-koor') ? 'active' : '' }}">
                        <a href="{{ route('koor.dashboard') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div class="text-truncate" data-i18n="Dashboard">Dashboard</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Manajemen Pengguna</span>
                    </li>

                    {{-- <li class="menu-item {{ request()->is('koor/data-orang-tua*') ? 'active' : '' }}">
                        <a href="{{ route('koor.dataOrangTua') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-group"></i>
                            <div class="text-truncate" data-i18n="Data Orang Tua">Data Orang Tua</div>
                        </a>
                    </li> --}}
                    <li class="menu-item {{ request()->is('koor/data-anak*') ? 'active' : '' }}">
                        <a href="{{ route('koor.dataAnak') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user"></i>
                            <div class="text-truncate" data-i18n="Data Anak">Data Semua Anak</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('koor/data-guru*') ? 'active' : '' }}">
                        <a href="{{ route('koor.dataGuru') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-chalkboard"></i>
                            <div class="text-truncate" data-i18n="Data Guru">Data Semua Guru</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Manajemen Kelas & Grup</span>
                    </li>
                    <li class="menu-item {{ request()->is('koor/data-kelas*') ? 'active' : '' }}">
                        <a href="{{ route('koor.dataKelas') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-building-house"></i>
                            <div class="text-truncate" data-i18n="Data Kelas">Data Kelas Terapis</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('koor/data-1on1*') ? 'active' : '' }}">
                        <a href="{{ route('koor.data1on1') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user-voice"></i>
                            <div class="text-truncate" data-i18n="Kelas 1 on 1">Data Kelas 1 on 1</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('koor/data-shadow*') ? 'active' : '' }}">
                        <a href="{{ route('koor.dataShadowGroup') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-group"></i>
                            <div class="text-truncate" data-i18n="Group Shadow">Group Shadow Teacher</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Laporan & Evaluasi</span>
                    </li>
                    <li class="menu-item {{ request()->is('koor/perkembangan-anak*') ? 'active' : '' }}">
                        <a href="{{ route('koor.dailyReport.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-line-chart"></i>
                            <div class="text-truncate" data-i18n="Data Laporan Harian">Data Laporan Harian</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('koor.raportSiswa*') ? 'active' : '' }}">
                        <a href="{{ route('koor.raportSiswa') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-id-card"></i>
                            <div class="text-truncate" data-i18n="Raport Siswa">Raport Siswa</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->is('koor/teacher-worksheet*') ? 'active' : '' }}">
                        <a href="{{ route('koor.worksheet.index') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-task"></i>
                            <div class="text-truncate" data-i18n="Teacher Worksheet">Teacher Worksheet</div>
                        </a>
                    </li>
                    <li class="menu-item {{ request()->routeIs('koor.raporGuru*') ? 'active' : '' }}">
                        <a href="{{ route('koor.raporGuru') }}" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
                            <div data-i18n="Rapor Guru">Rapor Guru</div>
                        </a>
                    </li>

                </ul>

                <div class="sidebar-profile-bottom p-3 border-top mt-auto" style="background: transparent;">
                    <div class="dropup">
                        <a href="#" class="d-flex align-items-center text-decoration-none w-100" id="dropdownProfile"
                            data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;">

                            <div class="avatar avatar-md me-3 shrink-0">
                                <span
                                    class="avatar-initial rounded-circle bg-lentera-blue-light text-white fw-bold fs-5">
                                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                                </span>
                            </div>

                            {{-- NAMA & ROLE DINAMIS --}}
                            <div class="d-flex flex-column text-start grow overflow-hidden">
                                <span class="fw-bold text-truncate text-lentera-blue-dark" style="font-size: 1.05rem;">
                                    {{ auth()->user()->name ?? 'Pengguna' }}
                                </span>
                                <small class="text-muted text-truncate">
                                    @if(auth()->check() && auth()->user()->role)

                                        {{ ucwords(str_replace('_', ' ', auth()->user()->role)) }}
                                    @else
                                        Admin / Koordinator
                                    @endif
                                </small>
                            </div>

                            <i class="bx bx-chevron-up text-lentera-blue-dark ms-2 fs-5"></i>
                        </a>

                        {{-- Dropup Menu (Muncul ke atas) --}}
                        <ul class="dropdown-menu shadow-sm mb-2" aria-labelledby="dropdownProfile"
                            style="border-radius: 12px; min-width: 220px; z-index: 9999;">
                            <li>
                                <h6 class="dropdown-header">Pengaturan Akun</h6>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="bx bx-user me-2"></i> Profil Saya
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger d-flex align-items-center">
                                        <i class="bx bx-power-off me-2"></i> Keluar
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

            </aside>

            <div class="layout-page">

                {{-- MOBILE HEADER (Dengan Tombol Hamburger yang Hidup) --}}
                <div class="layout-mobile-header d-flex align-items-center justify-content-between p-3 px-4 d-xl-none bg-white shadow-sm mb-4"
                    style="border-radius: 0 0 24px 24px; margin: 0;">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset('assets/img/illustrations/WaldenFajar.png') }}" alt="Logo" width="32"
                            class="me-2">
                        <span class="fw-bold text-lentera-blue-dark fs-4">Lentera</span>
                    </div>
                    {{-- Ini dihidupkan lagi biar sidebarnya bisa dibuka di HP --}}
                    <a href="javascript:void(0);" class="layout-menu-toggle cursor-pointer text-dark">
                        <i class="bx bx-menu" style="font-size: 1.8rem;"></i>
                    </a>
                </div>

                {{-- WADAH KONTEN UTAMA --}}
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">

                        @yield('content')

                    </div>

                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl">
                            <div
                                class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                                <div class="mb-2 mb-md-0">
                                    ©
                                    <script>document.write(new Date().getFullYear());</script> , Sistem Manajemen
                                    Sekolah Lentera Fajar.
                                </div>
                            </div>
                        </div>
                    </footer>
                    <div class="content-backdrop fade"></div>
                </div>

            </div>
        </div>

        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <script src="{{ asset('assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/vendor/js/menu.js') }}"></script>

    <script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
    @stack('scripts')
</body>

</html>