<?php
// file: enter_password.php
require_once 'includes/header.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';

// Jika belum ada session username dari langkah 1, redirect ke login.php
if (!isset($_SESSION['login_username'])) {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['password'])) {
    $database = new Database();
    $db = $database->connect();
    $user = new User($db);

    // Ambil username dari session dan cek lagi user-nya untuk mendapatkan hash password
    if ($user->checkUserExists($_SESSION['login_username'])) {
        $inputPassword = $_POST['password'];

        if ($user->verifyPassword($inputPassword)) {
            // Password benar, login berhasil
            unset($_SESSION['login_username']); // Hapus session sementara

            // Buat session login permanen
            $_SESSION['user_id'] = $user->id;
            $_SESSION['username'] = $user->username;

            header("Location: dashboard.php");
            exit();
        } else {
            $message = '<div style="color: red;">Password yang Anda masukkan salah.</div>';
        }
    } else {
        // Seharusnya tidak terjadi, tapi untuk keamanan
        $message = '<div style="color: red;">Terjadi kesalahan. Silakan coba lagi.</div>';
        unset($_SESSION['login_username']);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Step 2</title>
    <style>
        body { font-family: sans-serif; max-width: 400px; margin: 50px auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; }
        button { padding: 10px 15px; }
    </style>
</head>
<body>
    <h2>Login (Langkah 2 dari 2)</h2>
    <p>Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['login_username']); ?></strong>. Masukkan password Anda.</p>
    <?php echo $message; ?>
    <form action="enter_password.php" method="post">
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    <p><a href="login.php">Kembali</a></p>
</body>
</html>