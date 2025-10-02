<?php
function validasiKodeKelas(string $kodeKelas): array {
    $kodeKelas = trim($kodeKelas);

    if (empty($kodeKelas)) {
        return ["error" => "⚠️ Kode kelas tidak boleh kosong!", "success" => ""];
    } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $kodeKelas)) {
        return ["error" => "⚠️ Kode kelas tidak mengandung karakter spesial!", "success" => ""];
    } elseif (strlen($kodeKelas) < 6 || strlen($kodeKelas) > 12) {
        return ["error" => "⚠️ Panjang kode kelas harus 6-12 karakter!", "success" => ""];
    } else {
        return [
            "error" => "",
            "success" => "✅ Berhasil bergabung ke kelas dengan kode: <b>" . strtoupper(htmlspecialchars($kodeKelas)) . "</b>"
        ];
    }
}
