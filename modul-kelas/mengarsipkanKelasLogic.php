<?php
/**
 * Mengarsipkan atau mengembalikan kelas
 * 
 * @param array $kelasList daftar kelas (array asosiatif)
 * @param int   $idKelas   id kelas target
 * @param string $aksi     "arsip" atau "kembalikan"
 * @param string $role     "guru" atau "siswa"
 * @return array kelasList yang sudah diperbarui
 */
function kelolaArsip(array $kelasList, int $idKelas, string $aksi, string $role): array {
    if ($role !== "guru") {
        // siswa tidak bisa mengubah arsip
        return $kelasList;
    }

    foreach ($kelasList as &$kelas) {
        if ($kelas['id'] === $idKelas) {
            if ($aksi === "arsip") {
                $kelas['arsip'] = true;
            } elseif ($aksi === "kembalikan") {
                $kelas['arsip'] = false;
            }
        }
    }
    unset($kelas);
    return $kelasList;
}
