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
    $bulan = $_POST['bulan'];
    $jumlah_bayi_laki = $_POST['jumlah_bayi_laki'];
    $jumlah_bayi_perempuan = $_POST['jumlah_bayi_perempuan'];
    $total_bayi = $_POST['total_bayi'];

    // Cek apakah user_id ada di tabel users
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = :user_id");
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $userExists = $stmt->fetchColumn();

    if ($userExists > 0) {
        // Lanjutkan proses insert ke tabel data_vitamin
        $stmt = $pdo->prepare("INSERT INTO data_vitamin (bulan, jumlah_bayi_laki, jumlah_bayi_perempuan, total_bayi, user_id) 
                               VALUES (:bulan, :jumlah_bayi_laki, :jumlah_bayi_perempuan, :total_bayi, :user_id)");
        // Bind values
        if ($stmt->execute([
            ':bulan' => $bulan, 
            ':jumlah_bayi_laki' => $jumlah_bayi_laki, 
            ':jumlah_bayi_perempuan' => $jumlah_bayi_perempuan, 
            ':total_bayi' => $total_bayi, 
            ':user_id' => $userId
        ])) {
            $success = "Data berhasil ditambahkan!";
        } else {
            $error = "Terjadi kesalahan saat menyimpan data!";
        }
    } else {
        $error = "Error: User ID tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Vitamin</title>
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

    .custom-navbar {
        background-color: #66b3ff;
        /* Kode warna biru muda */
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
            <!-- Navbar content here -->
        </div>
    </nav>

    <!-- main content wrapped in an additional container -->
    <div class="container mt-4">
        <div class="container">
            <h4><i class="bi bi-journals"></i> Data Vitamin</h4>
            <?php if (isset($success)): ?>
            <div class="alert alert-success" id="successAlert"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
            <div class="alert alert-danger" id="errorAlert"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Card for the form -->
            <div class="custom-container">
                <a href="lihat_data_vitamin.php" class="btn btn-primary mb-3">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
                <div class="card">
                    <div class="card-body">
                        <form action="data_vitamin.php" method="POST" id="formPendataan">
                            <div class="mb-3">
                                <label for="bulan" class="form-label">Bulan</label><span>*</span>
                                <select class="form-select" id="bulan" name="bulan" required>
                                    <option value="Januari">Januari</option>
                                    <option value="Februari">Februari</option>
                                    <option value="Maret">Maret</option>
                                    <option value="April">April</option>
                                    <option value="Mei">Mei</option>
                                    <option value="Juni">Juni</option>
                                    <option value="Juli">Juli</option>
                                    <option value="Agustus">Agustus</option>
                                    <option value="September">September</option>
                                    <option value="Oktober">Oktober</option>
                                    <option value="November">November</option>
                                    <option value="Desember">Desember</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="jumlah_bayi_laki" class="form-label">Jumlah Bayi
                                    Laki-laki</label><span>*</span>
                                <input type="number" class="form-control" id="jumlah_bayi_laki" name="jumlah_bayi_laki"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label for="jumlah_bayi_perempuan" class="form-label">Jumlah Bayi
                                    Perempuan</label><span>*</span>
                                <input type="number" class="form-control" id="jumlah_bayi_perempuan"
                                    name="jumlah_bayi_perempuan" required>
                            </div>

                            <div class="mb-3">
                                <label for="total_bayi" class="form-label">Total Bayi</label><span>*</span>
                                <input type="text" class="form-control" id="total_bayi" name="total_bayi" required
                                    readonly>
                            </div>

                            <button type="submit" class="btn btn-success">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Fungsi untuk menghitung total bayi
    function hitungTotalBayi() {
        var jumlahBayiLaki = parseInt(document.getElementById("jumlah_bayi_laki").value) || 0;
        var jumlahBayiPerempuan = parseInt(document.getElementById("jumlah_bayi_perempuan").value) || 0;
        var totalBayi = jumlahBayiLaki + jumlahBayiPerempuan;
        document.getElementById("total_bayi").value = totalBayi;
    }

    // Event listener untuk input perubahan
    document.getElementById("jumlah_bayi_laki").addEventListener("input", hitungTotalBayi);
    document.getElementById("jumlah_bayi_perempuan").addEventListener("input", hitungTotalBayi);
    </script>
</body>

</html>