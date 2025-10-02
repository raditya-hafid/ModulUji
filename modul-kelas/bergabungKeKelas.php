    <?php
    // Inisialisasi variabel
    $kodeKelas = "";
    $pesanError = "";
    $pesanSukses = "";

    // Jika form disubmit
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $kodeKelas = trim($_POST["kode_kelas"]);

        // 1. Kode kelas wajib diisi
        if (empty($kodeKelas)) {
            $pesanError = "⚠️ Kode kelas tidak boleh kosong!";
        }
        // 2. Tidak boleh mengandung karakter spesial (hanya huruf dan angka)
        elseif (!preg_match('/^[a-zA-Z0-9]+$/', $kodeKelas)) {
            $pesanError = "⚠️ Kode kelas tidak boleh mengandung karakter spesial!";
        }
        // 3. Panjang minimal 6 dan maksimal 12 karakter
        elseif (strlen($kodeKelas) < 6 || strlen($kodeKelas) > 12) {
            $pesanError = "⚠️ Panjang kode kelas harus 6-12 karakter!";
        } else {
            // Jika valid → anggap kode selalu ada/valid
            $pesanSukses = "✅ Berhasil bergabung ke kelas dengan kode: <b>" . strtoupper(htmlspecialchars($kodeKelas)) . "</b>";
            // Reset input setelah sukses
            $kodeKelas = "";
        }
    }
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>Bergabung ke Kelas</title>
    </head>
    <body>
        <h2>Bergabung ke Kelas</h2>

        <?php if (!empty($pesanError)): ?>
            <p style="color:red;"><?php echo $pesanError; ?></p>
        <?php endif; ?>

        <?php if (!empty($pesanSukses)): ?>
            <p style="color:green;"><?php echo $pesanSukses; ?></p>
        <?php endif; ?>

        <form method="post" action="">
            <label>Kode Kelas: *</label><br>
            <input type="text" name="kode_kelas" value="<?php echo htmlspecialchars($kodeKelas); ?>"><br><br>

            <button type="submit">Gabung</button>
        </form>
    </body>
    </html>
