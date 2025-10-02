<?php

use PHPUnit\Framework\TestCase;

require_once 'MembuatTugas.php';

class MembuatTugasTest extends TestCase
{
    public function testMembuatTugasSuccess()
    {
        $tugas = new Tugas('Tugas Bab 1', 'Kerjakan soal 1-10', '2025-09-20', 'soal.pdf', 100, 'semua');
        $this->assertEquals('Tugas Bab 1', $tugas->judul);
        $this->assertEquals('Kerjakan soal 1-10', $tugas->deskripsi);
        $this->assertEquals('2025-09-20', $tugas->deadline);
        $this->assertEquals('soal.pdf', $tugas->lampiran);
    }

    public function testJudulKosongHarusError()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Judul wajib diisi');

        new Tugas('', 'Tes', '2025-09-21');
    }

    public function testMenentukanPenerimaSemua()
    {
        $tugas = new Tugas('Tugas PPL', 'Desc', '2025-09-22', null, 100, 'semua');
        $this->assertEquals('semua', $tugas->penerima);
    }

    public function testMenentukanPenerimaTertentu()
    {
        $tugas = new Tugas('Tugas PPL', 'Desc', '2025-09-23', null, 100, 'handoyo');
        $this->assertEquals('handoyo', $tugas->penerima);
    }

    public function testMenentukanPoin()
    {
        $tugas = new Tugas('Tugas PPL', 'Desc', '2025-09-24', null, 90, 'semua');
        $this->assertEquals(90, $tugas->poin);
    }
}
