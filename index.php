<?php
session_start();

// Memeriksa apakah user_id dan userType ada dalam session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['userType'])) {
    // Jika tidak ada session user_id atau userType, redirect ke halaman login
    header("Location: login.php");
    exit();
}

// Jika session ada, simpan user_id ke dalam variabel
$userId = $_SESSION['user_id'];
$userType = $_SESSION['userType']; // Mendapatkan tipe user (admin atau kader)
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Utama</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
    body {
        padding-top: 5%;
    }

    .navbar-brand img {
        height: 40px;
        /* Sesuaikan ukuran logo */
        width: auto;

    }

    .custom-navbar {
        background-color: #66b3ff;
        /* Kode warna biru muda */
    }

    .modal-content {
        border-radius: 10px;
        border: 1px solid #dee2e6;
    }

    .modal-header {
        background-color: #f8f9fa;
    }

    .modal-footer {
        background-color: #f8f9fa;
    }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light custom-navbar fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#" style="font-family: Georgia, 'Times New Roman', Times, serif;">
                <img src="img/posyandu.png" alt="" />
                POSMAP
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.php">Dashboard</a>
                    </li>

                    <!-- Dropdown Pendataan -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="pendataanDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Pendataan
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="pendataanDropdown">
                            <li><a class="dropdown-item" href="lihat_data_bayi_balita.php">Data Bayi/Balita</a></li>
                            <li><a class="dropdown-item" href="lihat_data_bumil.php">Data Ibu Hamil</a></li>
                            <li><a class="dropdown-item" href="lihat_data_kb.php">Data Pengguna KB</a></li>
                            <li><a class="dropdown-item" href="lihat_data_vitamin.php">Data Sasaran Vitamin A</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Jadwal -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="jadwalDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Jadwal
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="jadwalDropdown">
                            <li><a class="dropdown-item" href="jadwal_posyandu.php">Jadwal Posyandu</a></li>

                            <!-- Cek tipe user, jika admin arahkan ke jadwal_penyuluhan.php, jika bukan admin (kader) arahkan ke lihat_data_jadwal.php -->
                            <li>
                                <?php if (isset($_SESSION['userType']) && $_SESSION['userType'] == 'admin'): ?>
                                <a class="dropdown-item" href="jadwal_penyuluhan.php">Jadwal Penyuluhan</a>
                                <?php else: ?>
                                <a class="dropdown-item" href="lihat_data_jadwal.php">Jadwal Penyuluhan</a>
                                <?php endif; ?>
                            </li>

                            <li><a class="dropdown-item" href="#">Jadwal Pemberian Makanan Tambahan</a></li>
                        </ul>

                    </li>

                    <!-- Dropdown Informasi -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="informasiDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Informasi
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="informasiDropdown">
                            <?php if ($userType == 'admin'): ?>
                            <li><a class="dropdown-item" href="daftar_kegiatan.php">Informasi Kegiatan</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="#">Informasi Pendanaan Dana Sehat</a></li>
                            <?php if ($userType == 'kader'): ?>
                            <li><a class="dropdown-item" href="informasi_akun.php">Informasi Akun</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                    data-bs-target="#logoutModal">Logout</a></li>
                        </ul>
                    </li>



                    <!-- Dropdown Data (Tampilkan jika userType adalah admin) -->
                    <?php if ($userType == 'admin'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="dataDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Data
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dataDropdown">
                            <li><a class="dropdown-item" href="data_user.php">Data User</a></li>
                            <li><a class="dropdown-item" href="#">Data Kader</a></li>
                            <li><a class="dropdown-item" href="#">Data Posyandu</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <?php if ($userType == 'kader'): ?>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="">Laporan</a>
                    </li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </nav>
    <!-- Bootstrap Modal untuk konfirmasi logout -->
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutModalLabel">Konfirmasi Logout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin keluar?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <!-- Tombol Ya akan mengarahkan ke halaman logout.php -->
                    <a href="logout_proces.php" class="btn btn-primary">Ya</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mt-4">
        <div class="container-fluid mt-4">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Selamat Datang di Website Saya</h5>
                            <p class="card-text">
                                Ini adalah halaman utama website Anda. Gunakan menu di atas untuk
                                menavigasi ke halaman yang diinginkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid mt-4">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Dokumentasi Kegiatan</h5>

                            <div class="row">
                                <?php
    include 'config.php'; // Pastikan untuk menyertakan file koneksi

    // Ambil data kegiatan dari database
    $query = $pdo->query("SELECT foto, deskripsi FROM kegiatan");
    $kegiatanList = $query->fetchAll(PDO::FETCH_ASSOC);

    foreach ($kegiatanList as $kegiatan) : ?>
                                <div class="col-md-4">
                                    <div class="card mb-4">
                                        <img src="uploads/<?php echo htmlspecialchars($kegiatan['foto']); ?>"
                                            class="card-img-top" alt="Kegiatan Image">
                                        <div class="card-body">
                                            <p class="card-text"><?php echo htmlspecialchars($kegiatan['deskripsi']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid mt-4">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Selamat Datang di Website Saya</h5>
                            <p class="card-text">
                                Ini adalah halaman utama website Anda. Gunakan menu di atas untuk
                                menavigasi ke halaman yang diinginkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>

</body>

</html>