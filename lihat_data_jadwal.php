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

// Jika bukan admin, redirect ke halaman unauthorized
if ($userType !== 'admin') {
    header("Location: lihat_data_jadwal.php"); // Halaman unauthorized untuk user non-admin
    exit();
}

// Koneksi ke database
require 'config.php';

// Query untuk mendapatkan data dari tabel jadwal_penyuluhan
$stmt = $pdo->prepare("SELECT jp.bulan, jp.materi, jp.tempat, jp.desa, jp.tanggal, u.name as user_name 
                       FROM jadwal_penyuluhan jp 
                       JOIN users u ON jp.user_id = u.id");
$stmt->execute();
$jadwal_penyuluhan = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Jadwal Penyuluhan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
    .custom-container {
        background-color: #f1f1f1;
        padding: 20px;
        border-radius: 10px;
    }

    .table {
        margin-top: 20px;
    }
    </style>
</head>

<body>

    <div class="container mt-4">
        <h4><i class="bi bi-journals"></i> Data Jadwal Penyuluhan</h4>
        <div class="custom-container">
            <a href="jadwal_penyuluhan.php" class="btn btn-primary mb-3">
                <i class="bi bi-plus-circle"></i> Tambah Jadwal
            </a>
            <table class="table table-bordered table-striped">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Bulan</th>
                        <th>Materi</th>
                        <th>Tempat</th>
                        <th>Desa</th>
                        <th>Tanggal</th>
                        <th>User</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($jadwal_penyuluhan) > 0): ?>
                    <?php foreach ($jadwal_penyuluhan as $index => $jadwal): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($jadwal['bulan']); ?></td>
                        <td><?php echo htmlspecialchars($jadwal['materi']); ?></td>
                        <td><?php echo htmlspecialchars($jadwal['tempat']); ?></td>
                        <td><?php echo htmlspecialchars($jadwal['desa']); ?></td>
                        <td><?php echo htmlspecialchars($jadwal['tanggal']); ?></td>
                        <td><?php echo htmlspecialchars($jadwal['user_name']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Belum ada jadwal penyuluhan.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>