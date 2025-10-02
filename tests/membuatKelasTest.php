<?php
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../modul-kelas/membuatKelasLogic.php';

class MembuatKelasTest extends TestCase {
    public function testNamaKelasKosong() {
        $result = membuatKelas("");
        $this->assertEquals("⚠️ Nama kelas tidak boleh kosong!", $result["error"]);
        $this->assertEmpty($result["success"]);
    }

    public function testNamaKelasValidTanpaDeskripsi() {
        $result = membuatKelas("Matematika");
        $this->assertEmpty($result["error"]);
        $this->assertStringContainsString("✅ Kelas berhasil dibuat!", $result["success"]);
        $this->assertStringContainsString("Nama Kelas: Matematika", $result["success"]);
        $this->assertStringContainsString("(tidak ada deskripsi)", $result["success"]);
    }

    public function testNamaKelasValidDenganDeskripsi() {
        $result = membuatKelas("IPA", "Kelas untuk ilmu pengetahuan alam");
        $this->assertEmpty($result["error"]);
        $this->assertStringContainsString("Nama Kelas: IPA", $result["success"]);
        $this->assertStringContainsString("Kelas untuk ilmu pengetahuan alam", $result["success"]);
    }
}
