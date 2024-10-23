<?php
session_start();

// Memeriksa apakah userId dan userType ada dalam session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['userType'])) {
    header("Location: login.php");
    exit();
}

include 'config.php'; // Include koneksi database

// Cek apakah form telah disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];  // Tambahkan variabel untuk judul
    $deskripsi = $_POST['deskripsi'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["foto"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["foto"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File yang diupload bukan gambar.";
        $uploadOk = 0;
    }

    if ($_FILES["foto"]["size"] > 5000000) {
        echo "Ukuran file terlalu besar.";
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Hanya file JPG, JPEG, PNG, & GIF yang diperbolehkan.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "File tidak dapat diupload.";
    } else {
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO kegiatan (judul, foto, deskripsi) VALUES (:judul, :foto, :deskripsi)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'judul' => $judul,               // Masukkan judul ke database
                'foto' => $_FILES["foto"]["name"],
                'deskripsi' => $deskripsi
            ]);

            // Redirect ke halaman daftar kegiatan setelah upload berhasil
            header("Location: daftar_kegiatan.php");
            exit();
        } else {
            echo "Terjadi kesalahan saat mengupload file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Informasi Kegiatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <h1>Upload Informasi Kegiatan</h1>
        <form action="informasi_kegiatan.php" method="post" enctype="multipart/form-data">
            <!-- Input Judul Kegiatan -->
            <div class="mb-3">
                <label for="judul" class="form-label">Judul Kegiatan</label>
                <input type="text" class="form-control" id="judul" name="judul" required>
            </div>

            <!-- Input Foto Kegiatan -->
            <div class="mb-3">
                <label for="foto" class="form-label">Upload Foto Kegiatan</label>
                <input type="file" class="form-control" id="foto" name="foto" required>
            </div>

            <!-- Input Deskripsi Kegiatan -->
            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi Kegiatan</label>
                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" required></textarea>
            </div>

            <!-- Tombol Upload -->
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
</body>

</html>