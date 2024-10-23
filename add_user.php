<?php
session_start();
if (!isset($_SESSION['userType'])) {
    // Jika tidak ada session userType, redirect ke halaman login
    header("Location: login.php");
    exit();
}

$userType = $_SESSION['userType']; // Mendapatkan tipe user (admin atau kader)

// Koneksi ke database
$host = 'localhost';  // Ganti sesuai host database
$db = 'posmap';     // Ganti sesuai nama database
$user = 'root';       // Ganti dengan username database
$pass = '';           // Ganti dengan password database

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Proses pendaftaran user baru
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $userType = $_POST['userType'];

    // Masukkan data user baru ke database
    $stmt = $pdo->prepare("INSERT INTO users (username, password, userType) VALUES (:username, :password, :userType)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':userType', $userType);
    $stmt->execute();

    // Redirect kembali ke halaman data user
    header("Location: data_user.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .navbar-brand img {
        height: 40px;
        /* Sesuaikan ukuran logo */
        width: auto;
    
    }
        .custom-navbar{
        background-color: #66b3ff; /* Kode warna biru muda */
    }
    </style>
</head>

<body>
     <!-- Navbar -->
     <nav class="navbar navbar-expand-lg navbar-light custom-navbar">
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
                            <li><a class="dropdown-item" href="data_bumil.php">Data Ibu Hamil</a></li>
                            <li><a class="dropdown-item" href="#">Data Pengguna KB</a></li>
                            <li><a class="dropdown-item" href="#">Data Sasaran Vitamin A</a></li>
                        </ul>
                    </li>

                    <!-- Dropdown Jadwal -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="jadwalDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Jadwal
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="jadwalDropdown">
                            <li><a class="dropdown-item" href="#">Jadwal Posyandu</a></li>
                            <li><a class="dropdown-item" href="#">Jadwal Penyuluhan</a></li>
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
                            <li><a class="dropdown-item" href="informasi_kegiatan.php">Informasi Kegiatan</a></li>
                            <li><a class="dropdown-item" href="#">Informasi Pendanaan Dana Sehat</a></li>
                            <?php if ($userType == 'kader'): ?>
                            <li><a class="dropdown-item" href="#">Informasi Akun</a></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a></li>
                        </ul>
                    </li>

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

                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h3><i class="bi bi-person-fill-add"></i>Tambah User</h3>

        <!-- Form tambah user -->
        <form action="add_user.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="userType" class="form-label">Tipe User</label>
                <select class="form-control" id="userType" name="userType" required>
                    <option value="admin">Admin</option>
                    <option value="kader">Kader</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Daftar</button>
            <a href="data_user.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>

</html>