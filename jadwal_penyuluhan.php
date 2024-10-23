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

// Jika bukan admin, redirect ke halaman yang tidak diizinkan
if ($userType !== 'admin') {
    header("Location: unauthorized.php"); // Halaman unauthorized untuk user non-admin
    exit();
}

// Koneksi ke database
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bulan = $_POST['bulan'];
    $materi = $_POST['materi'];
    $tempat = $_POST['tempat'];
    $desa = $_POST['desa'];
    $tanggal = $_POST['tanggal'];
    
    // Cek apakah user_id ada di tabel users
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE id = :user_id");
    $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $userExists = $stmt->fetchColumn();

    if ($userExists > 0) {
        // Lanjutkan proses insert ke tabel jadwal_penyuluhan
        $stmt = $pdo->prepare("INSERT INTO jadwal_penyuluhan (bulan, materi, tempat, desa, tanggal, user_id) 
                               VALUES (:bulan, :materi, :tempat, :desa, :tanggal, :user_id)");
        // Bind values
        if ($stmt->execute([
            ':bulan' => $bulan, 
            ':materi' => $materi, 
            ':tempat' => $tempat, 
            ':desa' => $desa, 
            ':tanggal' => $tanggal,
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
    <title>Jadwal Penyuluhan</title>
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

    <!-- main content wrapped in an additional container -->
    <div class="container mt-4">
        <div class="container">
            <h4><i class="bi bi-journals"></i> Jadwal Penyuluhan</h4>
            <?php if (isset($success)): ?>
            <div class="alert alert-success" id="successAlert"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
            <div class="alert alert-danger" id="errorAlert"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Card for the form -->
            <div class="custom-container">
                <a href="lihat_data_jadwal.php" class="btn btn-primary mb-3">
                    <i class="bi bi-arrow-left"></i> Kembali</a>
                <div class="card">

                    <div class="card-body">

                        <!-- Form Input Data Jadwal Penyuluhan -->
                        <form action="jadwal_penyuluhan.php" method="POST" id="formPendataan">
                            <div class="mb-3">
                                <label for="bulan" class="form-label">Bulan</label><span>*</span>
                                <input type="text" class="form-control" id="bulan" name="bulan" required>
                            </div>
                            <div class="mb-3">
                                <label for="materi" class="form-label">Materi</label><span>*</span>
                                <input type="text" class="form-control" id="materi" name="materi" required>
                            </div>
                            <div class="mb-3">
                                <label for="tempat" class="form-label">Tempat</label><span>*</span>
                                <input type="text" class="form-control" id="tempat" name="tempat" required>
                            </div>
                            <div class="mb-3">
                                <label for="desa" class="form-label">Desa</label><span>*</span>
                                <input type="text" class="form-control" id="desa" name="desa" required>
                            </div>
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal</label><span>*</span>
                                <input type="date" class="form-control" id="tanggal" name="tanggal" required>
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
            }, 500);
        }

        if ("<?php echo isset($error) ? $error : ''; ?>") {
            setTimeout(function() {
                document.getElementById("errorAlert").style.display = "none";
            }, 500);
        }
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>