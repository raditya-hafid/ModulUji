<?php
// Inisialisasi variabel
$namaKelas = "";
$deskripsi = "";
$pesanError = "";
$pesanSukses = "";

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $namaKelas = trim($_POST["nama_kelas"]);
    $deskripsi = trim($_POST["deskripsi"]);

    // Validasi: nama kelas wajib diisi
    if (empty($namaKelas)) {
        $pesanError = "⚠️ Nama kelas tidak boleh kosong!";
    } else {
        // Jika valid
        $pesanSukses = "✅ Kelas berhasil dibuat!<br> 
                        Nama Kelas: " . htmlspecialchars($namaKelas) . "<br>
                        Deskripsi: " . (!empty($deskripsi) ? htmlspecialchars($deskripsi) : "(tidak ada deskripsi)");
        // Reset input setelah berhasil
        $namaKelas = "";
        $deskripsi = "";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buat Kelas</title>
</head>
<body>
    <h2>Buat Kelas Baru</h2>

    <?php if (!empty($pesanError)): ?>
        <p style="color:red;"><?php echo $pesanError; ?></p>
    <?php endif; ?>

    <?php if (!empty($pesanSukses)): ?>
        <p style="color:green;"><?php echo $pesanSukses; ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label>Nama Kelas: *</label><br>
        <input type="text" name="nama_kelas" value="<?php echo htmlspecialchars($namaKelas); ?>"><br><br>

        <label>Deskripsi (opsional):</label><br>
        <textarea name="deskripsi"><?php echo htmlspecialchars($deskripsi); ?></textarea><br><br>

        <button type="submit">Buat Kelas</button>
    </form>
</body>
</html>
