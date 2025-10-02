<?php
function membuatKelas(string $namaKelas, string $deskripsi = ""): array {
    $namaKelas = trim($namaKelas);
    $deskripsi = trim($deskripsi);

    if (empty($namaKelas)) {
        return [
            "error" => "⚠️ Nama kelas tidak boleh kosong!",
            "success" => ""
        ];
    } else {
        return [
            "error" => "",
            "success" => "✅ Kelas berhasil dibuat!<br> 
                          Nama Kelas: " . htmlspecialchars($namaKelas) . "<br>
                          Deskripsi: " . (!empty($deskripsi) ? htmlspecialchars($deskripsi) : "(tidak ada deskripsi)")
        ];
    }
}
