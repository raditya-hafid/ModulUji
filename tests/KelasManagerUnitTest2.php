<?php
use PHPUnit\Framework\TestCase;
use ModulKelas\KelasManager;

require_once __DIR__ . '/../modul-kelas/KelasManager.php';

class KelasManagerUnitTest2 extends TestCase {

    public function testHapusKelasDenganIdTidakValid() {
        $mockConn = $this->createMock(mysqli::class);
        $manager = new KelasManager($mockConn);
        $result = $manager->hapusKelas('abc');
        $this->assertFalse($result['status']);
        $this->assertEquals('ID tidak valid', $result['pesan']);
    }
}
