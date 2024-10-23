<?php
// Include koneksi database
include 'config.php';

// Pastikan ada ID yang dikirim via URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data kegiatan berdasarkan ID
    $sql = "SELECT * FROM kegiatan WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $kegiatan = $stmt->fetch();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Proses update deskripsi dan file jika diupload ulang
        $deskripsi = $_POST['deskripsi'];
        $foto = $kegiatan['foto']; // Default ke foto lama

        // Proses upload file baru jika ada
        if (!empty($_FILES['foto']['name'])) {
            $target_dir = "uploads/";
            $target_file = $target_dir . basename($_FILES["foto"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Upload file baru
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                $foto = $_FILES["foto"]["name"]; // Update nama file baru
            }
        }

        // Update deskripsi dan foto di database
        $sql = "UPDATE kegiatan SET deskripsi = :deskripsi, foto = :foto WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['deskripsi' => $deskripsi, 'foto' => $foto, 'id' => $id]);

        // Redirect setelah update
        header("Location: daftar_kegiatan.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kegiatan</title>
</head>

<body>
    <h1>Edit Kegiatan</h1>
    <form action="edit_kegiatan.php?id=<?= $id ?>" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi Kegiatan</label>
            <textarea class="form-control" id="deskripsi" name="deskripsi"
                rows="3"><?= htmlspecialchars($kegiatan['deskripsi']) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="foto" class="form-label">Ganti Foto (opsional)</label>
            <input type="file" class="form-control" id="foto" name="foto">
            <p>Foto saat ini: <img src="uploads/<?= htmlspecialchars($kegiatan['foto']) ?>" alt="Foto Kegiatan"
                    width="100"></p>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</body>

</html>