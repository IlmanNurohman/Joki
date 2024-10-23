<?php
session_start();
require 'config.php'; // Koneksi ke database



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    // Enkripsi password menggunakan password_hash()
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $userType = $_POST['userType']; // 'admin' atau 'kader'

    // Simpan data ke database
    $stmt = $pdo->prepare("INSERT INTO users (username, password, userType) VALUES (?, ?, ?)");
    if ($stmt->execute([$username, $password, $userType])) {
        header("Location: login.php");
        exit();
    } else {
        $error = "Pendaftaran gagal!";
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Register</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="userType" class="form-label">User Type</label>
                <select class="form-select" id="userType" name="userType">
                    <option value="admin">Admin</option>
                    <option value="kader">Kader</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</body>
</html>
