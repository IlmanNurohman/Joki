<?php
// Include koneksi database
include 'config.php';

// Pastikan ada ID yang dikirim via URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus data dari database
    $sql = "DELETE FROM kegiatan WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    // Redirect kembali ke halaman informasi kegiatan setelah penghapusan
    header("Location: daftar_kegiatan.php");
    exit();
}
?>