<?php
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../modul-kelas/bergabungKeKelasLogic.php';

class BergabungKeKelasTest extends TestCase {
    public function testKodeKelasKosong() {
        $result = validasiKodeKelas("");
        $this->assertEquals("⚠️ Kode kelas tidak boleh kosong!", $result["error"]);
        $this->assertEmpty($result["Validasi kode kelas tidak boleh kosong - success"]);
    }

    public function testKodeKelasMengandungKarakterSpesial() {
        $result = validasiKodeKelas("abc$123");
        $this->assertEquals("⚠️ Kode kelas tidak mengandung karakter spesial!", $result["error"]);
        $this->assertEmpty($result["Validasi kode kelas mengandung karakter spesial - success"]);
    }

    public function testKodeKelasTerlaluPendek() {
        $result = validasiKodeKelas("abc1");
        $this->assertEquals("⚠️ Panjang kode kelas harus 6-12 karakter!", $result["error"]);
    }

    public function testKodeKelasTerlaluPanjang() {
        $result = validasiKodeKelas("abcdefghijklmno");
        $this->assertEquals("⚠️ Panjang kode kelas harus 6-12 karakter!", $result["error"]);
    }

    public function testKodeKelasValid() {
        $result = validasiKodeKelas("kelas01");
        $this->assertEmpty($result["error"]);
        $this->assertStringContainsString("✅ Berhasil bergabung ke kelas dengan kode:", $result["success"]);
    }
}
