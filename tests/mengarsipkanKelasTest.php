<?php
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../modul-kelas/mengarsipkanKelasLogic.php';

class MengarsipkanKelasTest extends TestCase {
    private $kelasList;

    protected function setUp(): void {
        $this->kelasList = [
            ["id" => 1, "nama" => "Matematika Dasar", "arsip" => false],
            ["id" => 2, "nama" => "Bahasa Inggris", "arsip" => false],
            ["id" => 3, "nama" => "Fisika Modern", "arsip" => true],
        ];
    }

    public function testGuruBisaArsipkanKelas() {
        $updated = kelolaArsip($this->kelasList, 2, "arsip", "guru");
        $this->assertTrue($updated[1]['arsip']);
    }

    public function testGuruBisaKembalikanKelas() {
        $updated = kelolaArsip($this->kelasList, 3, "kembalikan", "guru");
        $this->assertFalse($updated[2]['arsip']);
    }

    public function testSiswaTidakBisaArsipkanKelas() {
        $updated = kelolaArsip($this->kelasList, 1, "arsip", "siswa");
        $this->assertFalse($updated[0]['arsip']);
    }

    public function testIdKelasTidakAdaTidakError() {
        $updated = kelolaArsip($this->kelasList, 99, "arsip", "guru");
        $this->assertEquals($this->kelasList, $updated); // harus sama
    }
}
