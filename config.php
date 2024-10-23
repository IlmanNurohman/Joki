<?php
$host = 'localhost'; // Sesuaikan dengan server database Anda
$dbname = 'posmap'; // Nama database
$username = 'root'; // Username MySQL Anda
$password = ''; // Password MySQL Anda


try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>
