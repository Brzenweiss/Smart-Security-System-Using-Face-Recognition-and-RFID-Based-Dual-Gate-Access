<?php
session_start();
if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Data Kunjungan</title>
    <link rel="stylesheet" href="css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Borel&family=Merriweather:wght@300;400;700&family=Poppins:wght@300;400;600;700&display=swap"
        rel="stylesheet" />
    <script src="https://cdn.syncfusion.com/ej2/dist/ej2.min.js" type="text/javascript"></script>
</head>

<body>
    <div class="container-fluid">
        <div class="row flex-nowrap">
            <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 bg-dark">
                <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 text-white min-vh-100">
                    <a href="/"
                        class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                        <span class="fs-5 d-none d-sm-inline">Menu</span>
                    </a>
                    <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start"
                        id="menu">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link align-middle px-0"> <i class="fs-4 bi-house"></i> <span
                                    class="ms-1 d-none d-sm-inline">Home</span> </a>
                        </li>
                        <li>
                            <a href="kontrol_panel.php" class="nav-link px-0 align-middle"> <i
                                    class="fs-4 bi bi-gear-wide-connected"></i> <span
                                    class="ms-1 d-none d-sm-inline">Kontrol Panel</span></a>
                        </li>
                        <li>
                            <a href="#submenu1" data-bs-toggle="collapse" class="nav-link px-0 align-middle"> <i
                                    class="fs-4 bi-speedometer2"></i> <span class="ms-1 d-none d-sm-inline">Data
                                    Realtime</span> </a>
                            <ul class="collapse show nav flex-column ms-1" id="submenu1" data-bs-parent="#menu">
                                <li class="w-100">
                                    <a href="async_masuk.php" class="nav-link px-4"> <span
                                            class="d-none d-sm-inline">Kunjungan Masuk</span> <i
                                            class="bi bi-box-arrow-right"></i></a>
                                </li>
                                <li>
                                    <a href="async_keluar.php" class="nav-link px-4"> <span
                                            class="d-none d-sm-inline">Kunjungan Keluar</span> <i
                                            class="bi bi-box-arrow-left"></i></a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="mahasiswa.php" class="nav-link px-0 align-middle"> <i
                                    class="fs-4 bi bi-person-circle"></i> <span class="ms-1 d-none d-sm-inline">Data
                                    Mahasiswa</span></a>
                        </li>
                        <li>
                            <a href="cari.php" class="nav-link px-0 align-middle"> <i class="fs-4 bi bi-search"></i>
                                <span class="ms-1 d-none d-sm-inline">Cari</span></a>
                        </li>
                        <li>
                            <a href="#submenu2" data-bs-toggle="collapse" class="nav-link px-0 align-middle"> <i
                                    class="fs-4 bi bi-cloud-download"></i> <span
                                    class="ms-1 d-none d-sm-inline">Unduh</span> </a>
                            <ul class="collapse show nav flex-column ms-1" id="submenu2" data-bs-parent="#menu">
                                <li class="w-100">
                                    <a href="down_masuk.php" class="nav-link px-4"> <span
                                            class="d-none d-sm-inline">Kunjungan Masuk</span> <i
                                            class="bi bi-box-arrow-right"></i></a>
                                </li>
                                <li>
                                    <a href="down_keluar.php" class="nav-link px-4"> <span
                                            class="d-none d-sm-inline">Kunjungan Keluar</span> <i
                                            class="bi bi-box-arrow-left"></i></a>
                                </li>
                            </ul>
                        </li>
                    </ul>

                    <hr />
                    <div class="dropdown pb-4">
                        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                            id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="img/setting.png" alt="hugenerd" width="30" height="30" class="rounded-circle" />
                            <span class="d-none d-sm-inline mx-1">User</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li>
                                <hr class="dropdown-divider" />
                            </li>
                            <li><a class="dropdown-item" href="logout.php">Log out</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="col py-3">