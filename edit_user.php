<?php
session_start();
if (!isset($_SESSION['userType']) || $_SESSION['userType'] != 'admin') {
    header("Location: login.php");
    exit();
}

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

// Ambil data user berdasarkan ID
if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User tidak ditemukan.");
    }
}

// Update data user jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $userType = $_POST['user_type'];

    $updateStmt = $pdo->prepare("UPDATE users SET username = :username,  user_type = :user_type WHERE id = :id");
    $updateStmt->bindParam(':username', $username);
    $updateStmt->bindParam(':user_type', $userType);
    $updateStmt->bindParam(':id', $userId);
    $updateStmt->execute();

    header("Location: data_user.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h1>Edit User</h1>
        <form action="edit_user.php?id=<?php echo $user['id']; ?>" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username"
                    value="<?php echo $user['username']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="user_type" class="form-label">User Type</label>
                <select class="form-select" id="user_type" name="user_type" required>
                    <option value="admin" <?php if ($user['user_type'] == 'admin') echo 'selected'; ?>>Admin</option>
                    <option value="kader" <?php if ($user['user_type'] == 'kader') echo 'selected'; ?>>Kader</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</body>

</html>