<?php

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../modul-uploadmateri/uploadmateri.php';
require_once __DIR__ . '/../modul-uploadmateri/aksesmateri.php';

use PHPUnit\Framework\TestCase;

class AksesMateriTest extends TestCase
{
    private $uploadMateri;
    private $aksesMateri;
    private $dataFile = 'materials_test.json';
    private $uploadDir = 'uploads_test/';

    protected function setUp(): void
    {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        if (file_exists($this->dataFile)) {
            unlink($this->dataFile);
        }

        $this->uploadMateri = new UploadMateri($this->uploadDir, $this->dataFile);
        $this->aksesMateri = new AksesMateri($this->dataFile);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->dataFile)) {
            unlink($this->dataFile);
        }

        if (is_dir($this->uploadDir)) {
            $files = glob($this->uploadDir . '*');
            foreach ($files as $file) {
                if (is_file($file)) unlink($file);
            }
            rmdir($this->uploadDir);
        }
    }

    public function testSiswaMelihatDaftarMateriDiKelas()
    {
        $files = [
            'name' => ['materi1.pdf'],
            'error' => [0],
            'tmp_name' => [''],
            'size' => [1024],
            'type' => ['application/pdf']
        ];

        $this->uploadMateri->upload(
            'Materi Kelas TI-A',
            'Materi untuk kelas TI-A',
            '1',
            '1',
            ['1'],
            $files
        );

        $materials = $this->aksesMateri->getMateriByStudent('2', '1');

        $this->assertNotEmpty($materials);
    }

    public function testSiswaMengunduhMateri()
    {
        $files = [
            'name' => ['materi_download.pdf'],
            'error' => [0],
            'tmp_name' => [''],
            'size' => [2048],
            'type' => ['application/pdf']
        ];

        $this->uploadMateri->upload(
            'Materi Download Test',
            'Test download materi',
            '1',
            '1',
            ['1'],
            $files
        );

        $materials = $this->aksesMateri->getAll();
        $materiId = $materials[0]['id'];
        $file = $this->aksesMateri->getFileByIndex($materiId, 0);

        $this->assertNotNull($file);
        $this->assertEquals('materi_download.pdf', $file['original']);
    }

    public function testSiswaMengaksesPreviewMateri()
    {
        $files = [
            'name' => ['materi_preview.pdf'],
            'error' => [0],
            'tmp_name' => [''],
            'size' => [1024],
            'type' => ['application/pdf']
        ];

        $this->uploadMateri->upload(
            'Materi Preview',
            'Deskripsi lengkap untuk preview',
            '1',
            '1',
            ['1'],
            $files
        );

        $materials = $this->aksesMateri->getMateriByStudent('2', '1');
        $materi = $materials[0];

        $this->assertEquals('Materi Preview', $materi['title']);
        $this->assertNotEmpty($materi['files']);
    }

    public function testPembatasanAksesSiswa()
    {
        $files = [
            'name' => ['materi_khusus.pdf'],
            'error' => [0],
            'tmp_name' => [''],
            'size' => [1024],
            'type' => ['application/pdf']
        ];

        $this->uploadMateri->upload(
            'Materi Khusus',
            'Hanya untuk siswa tertentu',
            '1',
            '2',
            ['1'],
            $files
        );

        $materialsForStudent2 = $this->aksesMateri->getMateriByStudent('2', '1');
        $this->assertNotEmpty($materialsForStudent2);

        $materialsForStudent3 = $this->aksesMateri->getMateriByStudent('3', '1');
        $this->assertEmpty($materialsForStudent3);
    }

    public function testMateriDihapusTidakBisaDiakses()
    {
        $files = [
            'name' => ['materi_hapus.pdf'],
            'error' => [0],
            'tmp_name' => [''],
            'size' => [1024],
            'type' => ['application/pdf']
        ];

        $this->uploadMateri->upload(
            'Materi Akan Dihapus',
            'Test hapus materi',
            '1',
            '1',
            ['1'],
            $files
        );

        $materials = $this->aksesMateri->getAll();
        $materiId = $materials[0]['id'];

        $this->uploadMateri->delete($materiId);

        $materi = $this->aksesMateri->getMateriById($materiId);
        $this->assertNull($materi);
    }
}
