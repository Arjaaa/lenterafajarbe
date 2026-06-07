<!doctype html>
<html lang="en" class="layout-menu-fixed layout-compact" data-assets-path="<?php echo e(asset('assets')); ?>/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Lentera Fajar - Dashboard Koordinator</title>

    <meta name="description" content="Sistem Manajemen Sekolah Lentera Fajar" />

    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('assets/img/favicon/favicon.ico')); ?>" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/fonts/iconify-icons.css')); ?>" />

    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/css/core.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/demo.css')); ?>" />

    <link rel="stylesheet" href="<?php echo e(asset('assets/css/custom-colors.css')); ?>" />

    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')); ?>" />
    <link rel="stylesheet" href="<?php echo e(asset('assets/vendor/libs/apex-charts/apex-charts.css')); ?>" />

    <script src="<?php echo e(asset('assets/vendor/js/helpers.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/js/config.js')); ?>"></script>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="<?php echo e(route('koor.dashboard')); ?>" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <img src="<?php echo e(asset('assets/img/illustrations/WaldenFajar.png')); ?>" alt="Logo Lentera Fajar"
                                width="40">
                        </span>
                        <span class="app-brand-text demo menu-text fw-bold ms-2 text-lentera-blue-dark">Lentera</span>
                    </a>

                    <a href="javascript:void(0);"
                        class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>

                <div class="menu-divider mt-0"></div>
                <div class="menu-inner-shadow"></div>

                <ul class="menu-inner py-1">

                    <li class="menu-item <?php echo e(request()->is('koor/dashboard-koor') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('koor.dashboard')); ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div class="text-truncate" data-i18n="Dashboard">Dashboard</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Manajemen Pengguna</span>
                    </li>

                    <li class="menu-item <?php echo e(request()->is('koor/data-orang-tua*') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('koor.dataOrangTua')); ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-group"></i>
                            <div class="text-truncate" data-i18n="Data Orang Tua">Data Orang Tua</div>
                        </a>
                    </li>
                    <li class="menu-item <?php echo e(request()->is('koor/data-anak*') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('koor.dataAnak')); ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user"></i>
                            <div class="text-truncate" data-i18n="Data Anak">Data Anak</div>
                        </a>
                    </li>
                    <li class="menu-item <?php echo e(request()->is('koor/data-guru*') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('koor.dataGuru')); ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-chalkboard"></i>
                            <div class="text-truncate" data-i18n="Data Guru">Data Guru</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Manajemen Kelas & Grup</span>
                    </li>
                    <li class="menu-item <?php echo e(request()->is('koor/data-kelas*') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('koor.dataKelas')); ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-building-house"></i>
                            <div class="text-truncate" data-i18n="Data Kelas">Data Kelas Terapis</div>
                        </a>
                    </li>
                    <li class="menu-item <?php echo e(request()->is('koor/data-1on1*') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('koor.data1on1')); ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-user-voice"></i>
                            <div class="text-truncate" data-i18n="Kelas 1 on 1">Kelas 1 on 1</div>
                        </a>
                    </li>
                    <li class="menu-item <?php echo e(request()->is('koor/data-shadow*') ? 'active' : ''); ?>">
                        <a href="<?php echo e(route('koor.dataShadowGroup')); ?>" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-group"></i>
                            <div class="text-truncate" data-i18n="Group Shadow">Group Shadow Teacher</div>
                        </a>
                    </li>

                    <li class="menu-header small text-uppercase">
                        <span class="menu-header-text">Laporan & Evaluasi</span>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-line-chart"></i>
                            <div class="text-truncate" data-i18n="Perkembangan Anak">Perkembangan Anak</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="#" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-task"></i>
                            <div class="text-truncate" data-i18n="Hasil Worksheet">TeacherWorksheet</div>
                        </a>
                    </li>

                </ul>
            </aside>
            <div class="layout-page">
                <nav class="layout-navbar container-xxl navbar-detached navbar navbar-expand-xl align-items-center bg-navbar-theme"
                    id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                            <i class="icon-base bx bx-menu icon-md"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
                        <div class="navbar-nav align-items-center me-auto">
                            <div class="nav-item d-flex align-items-center">
                                <span class="w-px-22 h-px-22"><i class="icon-base bx bx-search icon-md"></i></span>
                                <input type="text"
                                    class="form-control border-0 shadow-none ps-1 ps-sm-2 d-md-block d-none"
                                    placeholder="Search..." aria-label="Search..." />
                            </div>
                        </div>
                        <ul class="navbar-nav flex-row align-items-center ms-md-auto">
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);"
                                    data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="<?php echo e(asset('assets/img/avatars/1.png')); ?>" alt
                                            class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="<?php echo e(asset('assets/img/avatars/1.png')); ?>" alt
                                                            class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0"><?php echo e(Auth::user()->name ?? 'Koordinator'); ?></h6>
                                                    <small class="text-body-secondary">Admin</small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider my-1"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="icon-base bx bx-user icon-md me-3"></i><span>My Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <i class="icon-base bx bx-cog icon-md me-3"></i><span>Settings</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider my-1"></div>
                                    </li>
                                    <li>
                                        <form action="<?php echo e(route('logout')); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="dropdown-item w-100 text-start">
                                                <i class="icon-base bx bx-power-off icon-md me-3 text-danger"></i>
                                                <span class="text-danger">Log Out</span>
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">

                        <?php echo $__env->yieldContent('content'); ?>

                    </div>

                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl">
                            <div
                                class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                                <div class="mb-2 mb-md-0">
                                    ©
                                    <script>document.write(new Date().getFullYear());</script>
                                    , Sistem Manajemen Sekolah Lentera Fajar.
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
    <script src="<?php echo e(asset('assets/vendor/libs/jquery/jquery.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/libs/popper/popper.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/js/bootstrap.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')); ?>"></script>
    <script src="<?php echo e(asset('assets/vendor/js/menu.js')); ?>"></script>

    <script src="<?php echo e(asset('assets/vendor/libs/apex-charts/apexcharts.js')); ?>"></script>

    <script src="<?php echo e(asset('assets/js/main.js')); ?>"></script>

    <script src="<?php echo e(asset('assets/js/dashboards-analytics.js')); ?>"></script>
</body>

</html><?php /**PATH D:\LenteraFajar\lenterafajarbe\resources\views/layouts/admin.blade.php ENDPATH**/ ?>