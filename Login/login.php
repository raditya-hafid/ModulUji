<?php
// file: login.php
require_once 'includes/header.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['identifier'])) {
    $database = new Database();
    $db = $database->connect();

    $user = new User($db);

    // Bersihkan input
    $identifier = htmlspecialchars(strip_tags($_POST['identifier']));

    if ($user->checkUserExists($identifier)) {
        // User ditemukan, simpan username di session untuk langkah berikutnya
        $_SESSION['login_username'] = $user->username;
        header("Location: enter_password.php");
        exit();
    } else {
        $message = '<div style="color: red;">Username atau Email tidak ditemukan.</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Step 1</title>
    <style>
        body { font-family: sans-serif; max-width: 400px; margin: 50px auto; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; }
        button { padding: 10px 15px; }
    </style>
</head>
<body>
    <h2>Login (Langkah 1 dari 2)</h2>
    <p>Silakan masukkan username atau email Anda.</p>
    <?php echo $message; ?>
    <form action="login.php" method="post">
        <div class="form-group">
            <label for="identifier">Username atau Email</label>
            <input type="text" name="identifier" id="identifier" required>
        </div>
        <button type="submit">Lanjutkan</button>
    </form>
</body>
</html>