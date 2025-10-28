<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../modul-kelas/KelasManager.php';

class KelasManagerUnitTest extends TestCase {
    private $conn;
    private $manager;

    protected function setUp(): void {
        $this->conn = @new mysqli('localhost', 'root', '', 'db_kelas');
        if ($this->conn->connect_errno) {
            $this->markTestSkipped('Database tidak dapat dihubungkan: ' . $this->conn->connect_error);
        }

        $this->manager = new KelasManager($this->conn);
        $this->conn->query("REPLACE INTO kelas (id, nama, deskripsi, arsip) VALUES (999, 'Dummy Arsip', 'Testing', 1)");
        $this->conn->query("REPLACE INTO kelas (id, nama, deskripsi, arsip) VALUES (1000, 'Dummy Aktif', 'Testing', 0)");
    }

    protected function tearDown(): void {
        @$this->conn->query("DELETE FROM kelas WHERE id IN (999, 1000)");
        $this->conn->close();
    }

    public function testHapusKelasBerhasil() {
        $result = $this->manager->hapusKelas(999);
        $this->assertTrue($result['status']);
        $this->assertEquals('Kelas berhasil dihapus', $result['pesan']);
    }

    public function testHapusKelasGagalKarenaBelumArsip() {
        $result = $this->manager->hapusKelas(1000);
        $this->assertFalse($result['status']);
        $this->assertEquals('Kelas tidak ditemukan atau belum diarsipkan', $result['pesan']);
    }

    public function testHapusKelasDenganIdTidakValid() {
        $result = $this->manager->hapusKelas('abc');
        $this->assertFalse($result['status']);
        $this->assertEquals('ID tidak valid', $result['pesan']);
    }
}
