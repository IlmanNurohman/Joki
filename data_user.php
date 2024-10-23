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

// Hapus user jika tombol hapus diklik
if (isset($_GET['delete'])) {
    $userId = $_GET['delete'];
    $deleteStmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $deleteStmt->bindParam(':id', $userId);
    $deleteStmt->execute();
    header("Location: data_user.php");
}

// Proses pencarian user
$searchTerm = '';
if (isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username LIKE :search");
    $stmt->bindValue(':search', '%' . $searchTerm . '%');
} else {
    $stmt = $pdo->prepare("SELECT * FROM users");
}

$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
    .navbar-brand img {
        height: 40px;
        /* Sesuaikan ukuran logo */
        width: auto;
    }

    .table td,
    .table th {
        text-align: center;
        vertical-align: middle;
        border: none;
    }

    /* Tambahkan gaya untuk background container abu-abu */
    .custom-container {
        background-color: #f1f1f1;
        padding: 20px;
        border-radius: 10px;
    }

    /* Tambahkan gaya tambahan untuk tabel */
    .table-striped {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        overflow: hidden;
    }

    .table-striped thead {
        background-color: #343a40;
        color: white;
    }

    .table-striped tbody tr:hover {
        background-color: #f5f5f5;
    }

    .table-striped tbody tr {
        transition: background-color 0.3s ease;
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
                            <li><a class="dropdown-item" href="data_kb.php">Data Pengguna KB</a></li>
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
                            <li><a class="dropdown-item" href="infromasi_kegiatan.php">Informasi Kegiatan</a></li>
                            <li><a class="dropdown-item" href="#">Informasi Pendanaan Dana Sehat</a></li>
                            <li><a class="dropdown-item" href="#">Informasi Akun</a></li>
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

    <div class="container mt-4">

        <h2> <i class="bi bi-person-lines-fill"></i>Data User</h2>

        <!-- Form Pencarian -->
        <form class="d-flex mb-3" method="GET" action="data_user.php">
            <input class="form-control me-2" type="search" placeholder="Cari berdasarkan username" aria-label="Search"
                name="search" value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button class="btn btn-outline-success btn-custom" type="submit">Cari</button>
        </form>

        <div class="custom-container">
            <!-- Tambah User Button -->
            <a href="add_user.php" class="btn btn-primary mb-3 btn-custom">
                <i class="bi bi-plus"></i> Tambah User
            </a>


            <!-- Tabel Data User dalam div table-responsive untuk tampilan mobile -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped  table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>User Type</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="4">Tidak ada data user ditemukan.</td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['userType']; ?></td>
                            <td>
                                <div class="gap-2 d-sm-flex justify-content-sm-center">
                                    <a href="informasi_akun.php?id=<?php echo $user['id']; ?>"
                                        class="btn btn-secondary btn-sm mb-2 mb-sm-0 btn-custom">
                                        <i class="bi bi-eye"></i> Lihat
                                    </a>

                                    <a href="edit_user.php?id=<?php echo $user['id']; ?>"
                                        class="btn btn-warning btn-sm mb-2 mb-sm-0 btn-custom">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a href="data_user.php?delete=<?php echo $user['id']; ?>"
                                        class="btn btn-danger btn-sm mb-2 mb-sm-0 btn-custom"
                                        onclick="return confirm('Yakin ingin menghapus user ini?')">
                                        <i class="bi bi-trash3"></i> Hapus
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
        integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous">
    </script>
</body>

</html>