<?php

use PHPUnit\Framework\TestCase;

require_once 'MengumpulkanTugas.php';

class MengumpulkanTugasTest extends TestCase
{
    // ✅ Test siswa upload tugas dan klik turn in
    public function testTurnInWithFile()
    {
        $file = ['name' => 'jawaban.docx', 'error' => 0];
        $dueDate = time() + 3600; // deadline 1 jam ke depan
        $submittedAt = time(); // sekarang

        $task = new MengumpulkanTugas($file, 'turn_in', $dueDate, $submittedAt);

        $this->assertEquals('Submitted', $task->status);
        $this->assertStringContainsString('jawaban.docx', $task->message);
    }

    // ✅ Test validasi tanpa file
    public function testTurnInWithoutFile()
    {
        $task = new MengumpulkanTugas(null, 'turn_in');

        $this->assertEquals('Submitted', $task->status);
        $this->assertEquals('❌ Tidak ada tugas yang dilampirkan', $task->message);
    }

    // ✅ Test status pengumpulan terlambat (dinamis)
    public function testLateSubmission()
    {
        $file = ['name' => 'jawaban.pdf', 'error' => 0];
        $dueDate = time() - 60; // deadline lewat 1 menit
        $submittedAt = time(); // sekarang

        $task = new MengumpulkanTugas($file, 'turn_in', $dueDate, $submittedAt);

        $this->assertEquals('Terlambat', $task->status);
    }

    // ✅ Test unsubmit sebelum tenggat
    public function testUnsubmit()
    {
        $task = new MengumpulkanTugas(null, 'unsubmit');

        $this->assertEquals('Draft', $task->status);
        $this->assertEquals('↩️ Tugas dibatalkan, kembali ke draft.', $task->message);
    }
}
