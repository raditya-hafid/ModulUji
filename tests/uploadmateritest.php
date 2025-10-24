<?php

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../modul-uploadmateri/uploadmateri.php';

use PHPUnit\Framework\TestCase;

class UploadMateriTest extends TestCase
{
    private $uploadMateri;

    protected function setUp(): void
    {
        $this->uploadMateri = new UploadMateri('uploads_test/', 'materials_test.json');
    }

    public function testJudulKosong()
    {
        $this->assertFalse($this->uploadMateri->validateTitle(''));
    }

    public function testJudulValid()
    {
        $this->assertTrue($this->uploadMateri->validateTitle('Materi Bab 1'));
    }

    public function testFileLebihDari10()
    {
        $files = [
            'name' => array_fill(0, 11, 'file.pdf'),
            'error' => array_fill(0, 11, 0)
        ];

        $this->assertFalse($this->uploadMateri->validateFiles($files));
    }

    public function testFileTanpaUpload()
    {
        $files = ['name' => [''], 'error' => [4]];

        $this->assertTrue($this->uploadMateri->validateFiles($files));
    }
}
