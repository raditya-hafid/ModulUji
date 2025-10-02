<?php
session_start();

// Koneksi database
$host = "localhost";
$user = "root"; // sesuaikan
$pass = "";     // sesuaikan
$db   = "db_kelas";
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Pilih role user
if (isset($_POST['role'])) {
    $_SESSION['role'] = $_POST['role'];
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: simulasiKelas.php");
    exit;
}

// Tambah kelas (oleh guru)
if (isset($_POST['aksi']) && $_POST['aksi'] == "tambah" && $_SESSION['role'] == "guru") {
    $nama = trim($_POST['nama']);
    $desk = trim($_POST['deskripsi']);

    if (empty($nama)) {
        $pesan = "⚠️ Nama kelas tidak boleh kosong!";
    } else {
        $stmt = $conn->prepare("INSERT INTO kelas (nama, deskripsi, arsip) VALUES (?, ?, 0)");
        $stmt->bind_param("ss", $nama, $desk);
        $stmt->execute();
        $pesan = "✅ Kelas berhasil dibuat!";
    }
}

// Arsipkan kelas
if (isset($_POST['aksi']) && $_POST['aksi'] == "arsip" && $_SESSION['role'] == "guru") {
    $id = (int)$_POST['id'];
    $conn->query("UPDATE kelas SET arsip=1 WHERE id=$id");
}

// Kembalikan kelas
if (isset($_POST['aksi']) && $_POST['aksi'] == "kembalikan" && $_SESSION['role'] == "guru") {
    $id = (int)$_POST['id'];
    $conn->query("UPDATE kelas SET arsip=0 WHERE id=$id");
}

// Hapus kelas
if (isset($_POST['aksi']) && $_POST['aksi'] == "hapus") {
    $id = (int)$_POST['id'];
    $conn->query("DELETE FROM kelas WHERE id=$id AND arsip=1");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simulasi Kelas</title>
</head>
<body>
    <h2>Simulasi Sistem Kelas</h2>

    <!-- Pilih Role -->
    <?php if (!isset($_SESSION['role'])): ?>
        <form method="post">
            <label>Pilih peran Anda:</label><br>
            <button type="submit" name="role" value="guru">Masuk sebagai Guru</button>
            <button type="submit" name="role" value="siswa">Masuk sebagai Siswa</button>
        </form>
    <?php else: ?>
        <p>Anda masuk sebagai: <b><?php echo ucfirst($_SESSION['role']); ?></b></p>
        <a href="?logout=1">Keluar</a>
        <hr>
    <?php endif; ?>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "guru"): ?>
        <h3>➕ Buat Kelas Baru</h3>
        <?php if (!empty($pesan)) echo "<p style='color:green;'>$pesan</p>"; ?>
        <form method="post">
            <input type="hidden" name="aksi" value="tambah">
            <label>Nama Kelas: *</label><br>
            <input type="text" name="nama"><br><br>
            <label>Deskripsi (opsional):</label><br>
            <textarea name="deskripsi"></textarea><br><br>
            <button type="submit">Buat Kelas</button>
        </form>
        <hr>

        <h3>📚 Kelas Aktif</h3>
        <ul>
        <?php
        $result = $conn->query("SELECT * FROM kelas WHERE arsip=0");
        while ($row = $result->fetch_assoc()):
        ?>
            <li>
                <?php echo htmlspecialchars($row['nama']); ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="aksi" value="arsip">Arsipkan</button>
                </form>
            </li>
        <?php endwhile; ?>
        </ul>

        <h3>🗂️ Daftar Arsip Kelas</h3>
        <ul>
        <?php
        $result = $conn->query("SELECT * FROM kelas WHERE arsip=1");
        while ($row = $result->fetch_assoc()):
        ?>
            <li>
                <?php echo htmlspecialchars($row['nama']); ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="aksi" value="kembalikan">Kembalikan</button>
                    <button type="submit" name="aksi" value="hapus">Hapus</button>
                </form>
            </li>
        <?php endwhile; ?>
        </ul>

    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == "siswa"): ?>
        <h3>📚 Kelas Aktif</h3>
        <ul>
        <?php
        $result = $conn->query("SELECT * FROM kelas WHERE arsip=0");
        while ($row = $result->fetch_assoc()):
        ?>
            <li><?php echo htmlspecialchars($row['nama']); ?> - Anda bisa mengerjakan tugas ✅</li>
        <?php endwhile; ?>
        </ul>

        <h3>🗂️ Kelas Arsip</h3>
        <ul>
        <?php
        $result = $conn->query("SELECT * FROM kelas WHERE arsip=1");
        while ($row = $result->fetch_assoc()):
        ?>
            <li>
                <?php echo htmlspecialchars($row['nama']); ?> - Hanya lihat konten ❌
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="aksi" value="hapus">Hapus</button>
                </form>
            </li>
        <?php endwhile; ?>
        </ul>
    <?php endif; ?>

</body>
</html>
