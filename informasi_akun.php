<?php
session_start();

// Pastikan pengguna sudah login dan memiliki session
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id']; // ID user dari session
$userType = $_SESSION['userType']; // Tipe user dari session

// Koneksi ke database
require 'config.php';

// Cek apakah admin melihat akun pengguna lain
if ($userType == 'admin' && isset($_GET['id'])) {
    // Jika admin melihat data user lain berdasarkan ID dari URL
    $userId = $_GET['id']; 
} else {
    // Jika pengguna biasa mengakses halaman akun mereka sendiri
    $userId = $_SESSION['user_id'];
}

// Ambil data pengguna dari database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Upload foto profil jika ada file yang diunggah
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['foto']['tmp_name'];
        $fileName = $_FILES['foto']['name'];
        $fileSize = $_FILES['foto']['size'];
        $fileType = $_FILES['foto']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = ['jpg', 'jpeg', 'png'];
        if (in_array($fileExtension, $allowedfileExtensions)) {
            // Set nama file yang baru berdasarkan user ID untuk menghindari bentrok
            $newFileName = $userId . '_profile.' . $fileExtension;
            $uploadFileDir = './uploads/';
            $dest_path = $uploadFileDir . $newFileName;

            // Pindahkan file yang diupload ke direktori tujuan
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Update foto profil di database
                $stmt = $pdo->prepare("UPDATE users SET foto = :foto WHERE id = :user_id");
                $stmt->execute([':foto' => $newFileName, ':user_id' => $userId]);
                $success = "Foto profil berhasil diunggah.";
            } else {
                $error = 'Terjadi kesalahan saat mengunggah file.';
            }
        } else {
            $error = 'Jenis file yang diperbolehkan hanya .jpg, .jpeg, dan .png.';
        }
    }

    // Update nama, email, dan no telepon
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $no_tlpn = $_POST['no_tlpn'];

    $stmt = $pdo->prepare("UPDATE users SET nama = :nama, email = :email, no_tlpn = :no_tlpn WHERE id = :user_id");
    $stmt->execute([':nama' => $nama, ':email' => $email, ':no_tlpn' => $no_tlpn, ':user_id' => $userId]);

    $success = "Data berhasil diperbarui.";
}

// Ambil ulang data setelah update
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
$stmt->execute();
$userData = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .card-body {
    text-align: initial; /* Mengembalikan pengaturan text-align ke default */
}

        .form-label {
    font-weight: bold;
    text-align: left; /* Menjamin label berada di kiri */
    display: block; /* Menyusun label dalam blok agar berada di atas elemen input */
    margin-bottom: 5px; /* Menambah jarak di bawah label */
}

        .form-control {
            border-radius: 10px;
            border: 1px solid #66b3ff;
        }
        .form-control:focus {
            border-color: #0056b3;
            box-shadow: 0 0 5px rgba(0, 86, 179, 0.5);
        }
        .btn-primary {
            background-color: #66b3ff;
            border: none;
            border-radius: 10px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .no-border {
            border: none;
        }
        .navbar-brand img {
        height: 40px;
        /* Sesuaikan ukuran logo */
        width: auto;
    
    }
    .custom-navbar{
        background-color: #66b3ff; /* Kode warna biru muda */
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



    body {
            padding-top: 70px; /* Sesuaikan nilai ini dengan tinggi navbar */
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
                                <li><a class="dropdown-item" href="informasi_akun.php">Informasi Akun</a></li>

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
    <h2><i class="bi bi-person-lines-fill"></i>Informasi Akun</h2>
    <?php if (isset($success)): ?>
        <div id="successAlert" class="alert alert-success"><?php echo $success; ?></div>
    <?php elseif (isset($error)): ?>
        <div id="errorAlert" class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <div class="card">
        <div class="card-body text-center">
            <img src="uploads/<?php echo htmlspecialchars($userData['foto'] ?? 'default-profile.jpg'); ?>" alt="Foto Profil" class="profile-img">
            <h5 class="card-title"><?php echo htmlspecialchars($userData['nama']); ?></h5>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="foto" class="form-label">Ganti Foto Profil</label>
                    <input type="file" class="form-control" id="foto" name="foto">
                </div>
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($userData['nama']); ?>">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>">
                </div>
                <div class="mb-3">
                    <label for="no_tlpn" class="form-label">No Telepon</label>
                    <input type="number" class="form-control" id="no_tlpn" name="no_tlpn" value="<?php echo htmlspecialchars($userData['no_tlpn']); ?>">
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control no-border" id="username" value="<?php echo htmlspecialchars($userData['username']); ?>" disabled>
                </div>
                <div class="mb-3">
                    <label for="jenis_akun" class="form-label">Jenis Akun</label>
                    <input type="text" class="form-control no-border" id="jenis_akun" value="<?php echo htmlspecialchars($userData['userType']); ?>" disabled>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Hilangkan notifikasi sukses setelah 5 detik
        setTimeout(function() {
            var successAlert = document.getElementById("successAlert");
            if (successAlert) {
                successAlert.style.display = "none";
            }
        }, 5000);

        // Hilangkan border pada kolom input setelah simpan perubahan
        const formElements = document.querySelectorAll('.form-control');
        formElements.forEach((element) => {
            element.addEventListener('input', function() {
                if (element.value.trim() !== '') {
                    element.classList.add('no-border');
                }
            });
        });
    });
</script>
</body>
</html>
