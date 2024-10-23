<?php
session_start();

// Memeriksa apakah userId dan userType ada dalam session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['userType'])) {
    // Jika tidak ada session userId atau userType, redirect ke halaman login
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id']; // Mendapatkan user ID dari session
$userType = $_SESSION['userType']; // Mendapatkan tipe user (admin atau kader)

// Koneksi ke database
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $jenis_kb = $_POST['jenis_kb'];
    $tanggal_pendataan = $_POST['tanggal_pendataan'];

    // Cek apakah user_id ada di tabel users
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = :user_id");
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $userExists = $stmt->fetchColumn();

    if ($userExists > 0) {
        // Lanjutkan proses insert ke tabel bayi_balita
        $stmt = $pdo->prepare("INSERT INTO data_kb (nama, jenis_kb, tanggal_pendataan, user_id) 
                               VALUES (:nama, :jenis_kb, :tanggal_pendataan, :user_id)");
        // Bind values
        if ($stmt->execute([
            ':nama' => $nama, 
            ':jenis_kb' => $jenis_kb, 
            ':tanggal_pendataan' => $tanggal_pendataan, 
            ':user_id' => $userId
        ])) {
            $success = "Data berhasil ditambahkan!";
        } else {
            $error = "Terjadi kesalahan saat menyimpan data!";
        }
    } else {
        // Tampilkan pesan error jika user_id tidak ditemukan
        $error = "Error: User ID tidak ditemukan.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Bayi/Balita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

    <style>
    .navbar-brand img {
        height: 40px;
        width: auto;
    }
    .custom-container {
            background-color: #f1f1f1;
            padding: 20px;
            border-radius: 10px;
        }
        .custom-navbar{
        background-color: #66b3ff; /* Kode warna biru muda */
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
                            <li><a class="dropdown-item" href="data_vitamin.php">Data Sasaran Vitamin A</a></li>
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
                            <li><a class="dropdown-item" href="informasi_akun.php">Informasi Akun</a></li>
                            <li><a class="dropdown-item" href="logout.php">Log-Out</a></li>
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

                </ul>
            </div>
        </div>
    </nav>

    <!-- main content wrapped in an additional container -->
    <div class="container mt-4">
        <div class="container">
            <h4><i class="bi bi-journals"></i>Data KB</h4>
            <?php if (isset($success)): ?>
            <div class="alert alert-success" id="successAlert"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
            <div class="alert alert-danger" id="errorAlert"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Card for the form -->
             <div class="custom-container">
             <a href="lihat_data_kb.php" class="btn btn-primary mb-3">
             <i class="bi bi-arrow-left"></i> Kembali
    </a>
            <div class="card">
        
                <div class="card-body">
                
                    <!-- Form Input Data Bayi/Balita (selalu tampil) -->
                    <form action="data_kb.php" method="POST" id="formPendataan">
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama </label><span>*</span>
                            <input type="text" class="form-control" id="nama" name="nama" required>
                        </div>
                        <div class="mb-3">
                            
                            <label for="jenis_kelamin" class="form-label">Jenis Kelamin</label><span>*</span>
                            <select class="form-select" id="jenis_kb" name="jenis_kb" required>
                                <option value="suntik">suntik</option>
                                <option value="UID">UID</option>
                                <option value="implan">implan</option>
                                <option value="pil">pil</option>
                                <option value="Lainnya">Lainnya</option></option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="tanggal_pendataan" class="form-label">Tanggal Pendataan</label><span>*</span>
                            <input type="date" class="form-control" id="tanggal_pendataan" name="tanggal_pendataan"
                                required>
                        </div>
                        
                        <button type="submit" class="btn btn-success">Submit</button>
                    </form>
                </div>
            </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        if ("<?php echo isset($success) ? $success : ''; ?>") {
            document.getElementById("formPendataan").reset();

            setTimeout(function() {
                document.getElementById("successAlert").style.display = "none";
            }, 3000);
        }

        if ("<?php echo isset($error) ? $error : ''; ?>") {
            setTimeout(function() {
                document.getElementById("errorAlert").style.display = "none";
            }, 3000);
        }
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>
