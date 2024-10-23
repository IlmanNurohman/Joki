<?php
require 'config.php'; // Ganti dengan file konfigurasi yang sesuai

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected'])) {
    $selectedIds = $_POST['selected'];

    // Buat query untuk menghapus data berdasarkan ID yang dipilih
    $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
    $stmt = $pdo->prepare("DELETE FROM bayi_balita WHERE id IN ($placeholders)");
    
    // Eksekusi query dengan ID yang dipilih
    $stmt->execute($selectedIds);

    // Redirect kembali ke halaman yang diinginkan setelah menghapus
    header("Location: lihat_data_bayi_balita.php");
    exit();
}
?>
