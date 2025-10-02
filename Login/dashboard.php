<?php
// file: dashboard.php
require_once 'includes/header.php';

// Cek apakah user sudah login atau belum
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
     <style>
        body { font-family: sans-serif; max-width: 800px; margin: 50px auto; }
    </style>
</head>
<body>
    <h1>Selamat Datang di Dashboard!</h1>
    <p>Halo, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>. Anda berhasil login.</p>
    <a href="logout.php">Logout</a>
</body>
</html>