<?php
session_start();

// Simulasi data kelas
if (!isset($_SESSION['kelas'])) {
    $_SESSION['kelas'] = [
        ["id" => 1, "nama" => "Matematika Dasar", "arsip" => false],
        ["id" => 2, "nama" => "Bahasa Inggris", "arsip" => false],
        ["id" => 3, "nama" => "Fisika Modern", "arsip" => true], // contoh sudah diarsipkan
    ];
}

// Pilih role user (guru/siswa)
if (isset($_POST['role'])) {
    $_SESSION['role'] = $_POST['role'];
}

// Aksi guru: arsipkan atau kembalikan
if (isset($_POST['aksi']) && $_SESSION['role'] == "guru") {
    $idKelas = (int)$_POST['id'];
    foreach ($_SESSION['kelas'] as &$kelas) {
        if ($kelas['id'] == $idKelas) {
            if ($_POST['aksi'] == "arsip") {
                $kelas['arsip'] = true;
            } elseif ($_POST['aksi'] == "kembalikan") {
                $kelas['arsip'] = false;
            }
        }
    }
    unset($kelas);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mengelola Arsip Kelas</title>
</head>
<body>
    <h2>Simulasi Mengarsipkan Kelas</h2>

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
        <h3>📚 Kelas Aktif</h3>
        <ul>
        <?php foreach ($_SESSION['kelas'] as $kelas): ?>
            <?php if (!$kelas['arsip']): ?>
                <li>
                    <?php echo htmlspecialchars($kelas['nama']); ?>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $kelas['id']; ?>">
                        <button type="submit" name="aksi" value="arsip">Arsipkan</button>
                    </form>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
        </ul>

        <h3>🗂️ Daftar Arsip Kelas</h3>
        <ul>
        <?php foreach ($_SESSION['kelas'] as $kelas): ?>
            <?php if ($kelas['arsip']): ?>
                <li>
                    <?php echo htmlspecialchars($kelas['nama']); ?> 
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $kelas['id']; ?>">
                        <button type="submit" name="aksi" value="kembalikan">Kembalikan</button>
                    </form>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
        </ul>

    <?php elseif (isset($_SESSION['role']) && $_SESSION['role'] == "siswa"): ?>
        <h3>📚 Kelas Aktif</h3>
        <ul>
        <?php foreach ($_SESSION['kelas'] as $kelas): ?>
            <?php if (!$kelas['arsip']): ?>
                <li><?php echo htmlspecialchars($kelas['nama']); ?> - Anda bisa mengerjakan/upload tugas ✅</li>
            <?php endif; ?>
        <?php endforeach; ?>
        </ul>

        <h3>🗂️ Kelas Arsip</h3>
        <ul>
        <?php foreach ($_SESSION['kelas'] as $kelas): ?>
            <?php if ($kelas['arsip']): ?>
                <li><?php echo htmlspecialchars($kelas['nama']); ?> - Hanya lihat konten ❌ (tidak bisa upload)</li>
            <?php endif; ?>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>


    <?php
    // Logout (hapus session)
    if (isset($_GET['logout'])) {
        session_destroy();
        header("Location: mengarsipkanKelas.php");
        exit;
    }
    ?>
</body>
</html>
