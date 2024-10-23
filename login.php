<?php
session_start();
require 'config.php'; // Koneksi ke database

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cari user berdasarkan username
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    // Jika user ditemukan dan password cocok, verifikasi password
    if ($user && password_verify($password, $user['password'])) {
        // Jika password valid, set session
        $_SESSION['user_id'] = $user['id']; // Menyimpan user_id
        $_SESSION['userType'] = $user['userType'];
        
        header("Location: index.php"); // Redirect ke halaman utama
        exit();
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
    /* CSS untuk menempatkan form login di tengah layar */
    body {
        position: relative;
        background-image: url('img/kecamatan.jpg');
        background-size: cover;
        background-position: center;
        height: 100vh;
        overflow: hidden;
    }

    body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: inherit;
        z-index: 0;
    }

    .login-container {
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        position: relative;
        z-index: 1;
    }

    .login-box {
        width: 100%;
        max-width: 400px;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        background-color: rgba(241, 241, 241, 0.9);
    }

    .login-box img {
        width: 80px;
        height: auto;
        margin-bottom: 20px;
        display: block;
        margin: auto;
    }

    @media (max-width: 576px) {
        body {
            background-attachment: fixed;
        }

        .login-box {
            padding: 15px;
        }
    }
    </style>
</head>

<body>
    <div class="container-fluid login-container">
        <div class="login-box">
            <img src="img/posyandu.png" alt="Logo">
            <h2 class="text-center">POSMAP</h2>
            <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">Login</button>
                <a href="register.php">register</a>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>