<?php

use PHPUnit\Framework\TestCase;

require_once 'modul-uploadmateri/uploadmateri.php';

class UploadMateriTest extends TestCase
{
    private $uploadMateri;
    private $dataFile = 'materials_test.json';
    private $uploadDir = 'uploads_test/';

    protected function setUp(): void
    {
        // Setup test environment
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        // Clear test data
        if (file_exists($this->dataFile)) {
            unlink($this->dataFile);
        }

        // Initialize UploadMateri class dengan test files
        $this->uploadMateri = new UploadMateri(
            $this->uploadDir,
            $this->dataFile,
            'students_test.json',
            'topics_test.json'
        );
    }

    protected function tearDown(): void
    {
        // Cleanup after tests
        if (file_exists($this->dataFile)) {
            unlink($this->dataFile);
        }

        // Remove uploaded test files
        if (is_dir($this->uploadDir)) {
            $files = glob($this->uploadDir . '*');
            foreach ($files as $file) {
                if (is_file($file)) unlink($file);
            }
            rmdir($this->uploadDir);
        }
    }

    /**
     * TC-34: Upload materi
     * Test upload materi dengan file PDF
     */
    public function testUploadMateri()
    {
        // Arrange
        $files = [
            'name' => ['materi.pdf'],
            'error' => [0],
            'tmp_name' => [''],
            'size' => [1024],
            'type' => ['application/pdf']
        ];

        // Act
        $result = $this->uploadMateri->upload(
            'Materi Bab 1',
            'Deskripsi materi',
            '1',
            '1',
            ['1'],
            $files
        );

        // Assert
        $this->assertTrue($result['status'], 'Materi berhasil diunggah');
        $this->assertEquals('Materi berhasil diupload', $result['msg']);
        $this->assertNotEmpty($result['file'], 'File berhasil diupload');

        $materials = $this->uploadMateri->getAll();
        $this->assertNotEmpty($materials, 'Data materi tidak kosong');
        $this->assertEquals('Materi Bab 1', $materials[0]['title']);
        $this->assertEquals('materi.pdf', $materials[0]['files'][0]['original']);
    }

    /**
     * TC-35: Mengunggah materi
     * Test materi muncul di daftar setelah upload
     */
    public function testMateriMunculDiDaftarSetelahUpload()
    {
        // Arrange
        $files = [
            'name' => ['materi.pdf'],
            'error' => [0],
            'tmp_name' => [''],
            'size' => [1024],
            'type' => ['application/pdf']
        ];

        // Act
        $this->uploadMateri->upload(
            'Materi Bab 1',
            'Test deskripsi',
            '1',
            '1',
            ['1'],
            $files
        );

        $materials = $this->uploadMateri->getAll();

        // Assert
        $this->assertNotEmpty($materials, 'Materi tersimpan di daftar');

        $found = false;
        foreach ($materials as $m) {
            if ($m['title'] === 'Materi Bab 1' && $m['files'][0]['original'] === 'materi.pdf') {
                $found = true;
                break;
            }
        }

        $this->assertTrue($found, 'Materi "Materi Bab 1" dengan file "materi.pdf" ditemukan di daftar');
    }

    /**
     * TC-36: Hapus materi yang sudah diunggah
     * Test menghapus materi dari daftar
     */
    public function testHapusMateri()
    {
        // Arrange - Upload materi terlebih dahulu
        $files = [
            'name' => ['materi.pdf'],
            'error' => [0],
            'tmp_name' => [''],
            'size' => [1024],
            'type' => ['application/pdf']
        ];

        $this->uploadMateri->upload(
            'Materi Bab 1',
            'Materi yang akan dihapus',
            '1',
            '1',
            ['1'],
            $files
        );

        $materials = $this->uploadMateri->getAll();
        $materiId = $materials[0]['id'];

        // Act - Hapus materi
        $deleteResult = $this->uploadMateri->delete($materiId);

        // Assert
        $this->assertTrue($deleteResult, 'Materi berhasil dihapus');

        $materials = $this->uploadMateri->getAll();
        $found = false;
        foreach ($materials as $m) {
            if ($m['id'] === $materiId) {
                $found = true;
                break;
            }
        }

        $this->assertFalse($found, 'Materi terhapus & hilang dari daftar Classwork');
    }

    /**
     * TC-37: Update materi (file atau deskripsi)
     * Test memperbarui materi dengan data baru
     * Note: Karena belum ada method update(), test ini simulate manual update
     */
    public function testUpdateMateri()
    {
        // Arrange - Upload materi lama
        $oldFiles = [
            'name' => ['file_lama.pdf'],
            'error' => [0],
            'tmp_name' => [''],
            'size' => [1024],
            'type' => ['application/pdf']
        ];

        $this->uploadMateri->upload(
            'Materi Bab 1',
            'Deskripsi lama',
            '1',
            '1',
            ['1'],
            $oldFiles
        );

        $materials = $this->uploadMateri->getAll();
        $materiId = $materials[0]['id'];

        // Act - Simulate update: hapus lama, upload baru
        $this->uploadMateri->delete($materiId);

        $newFiles = [
            'name' => ['file_baru.pdf'],
            'error' => [0],
            'tmp_name' => [''],
            'size' => [2048],
            'type' => ['application/pdf']
        ];

        $result = $this->uploadMateri->upload(
            'Materi Bab 1 Revisi',
            'Update penjelasan',
            '1',
            '1',
            ['1'],
            $newFiles
        );

        // Assert
        $this->assertTrue($result['status'], 'Materi berhasil diperbarui');

        $materials = $this->uploadMateri->getAll();
        $updatedMateri = $materials[0];

        $this->assertEquals('Materi Bab 1 Revisi', $updatedMateri['title'], 'Judul berhasil diupdate');
        $this->assertEquals('Update penjelasan', $updatedMateri['description'], 'Deskripsi berhasil diupdate');
        $this->assertEquals('file_baru.pdf', $updatedMateri['files'][0]['original'], 'File berhasil diupdate');
    }

    /**
     * Test tambahan: Upload tanpa file
     */
    public function testUploadTanpaFile()
    {
        // Arrange
        $emptyFiles = [
            'name' => [''],
            'error' => [4], 
            'tmp_name' => [''],
            'size' => [0],
            'type' => ['']
        ];

        // Act
        $result = $this->uploadMateri->upload(
            'Materi Tanpa File',
            'Hanya deskripsi',
            '1',
            '1',
            ['1'],
            $emptyFiles
        );

        // Assert
        $this->assertTrue($result['status']);

        $materials = $this->uploadMateri->getAll();
        $this->assertEmpty($materials[0]['files'], 'Tidak ada file yang diupload');
        $this->assertEquals('Materi Tanpa File', $materials[0]['title']);
    }

    /**
     * Test tambahan: Upload multiple files
     */
    public function testUploadMultipleFiles()
    {
        // Arrange
        $files = [
            'name' => ['file1.pdf', 'file2.docx', 'file3.pptx'],
            'error' => [0, 0, 0],
            'tmp_name' => ['', '', ''],
            'size' => [1024, 2048, 3072],
            'type' => ['application/pdf', 'application/docx', 'application/pptx']
        ];

        // Act
        $result = $this->uploadMateri->upload(
            'Materi Multiple Files',
            'Materi dengan banyak file',
            '1',
            '1',
            ['1', '2'],
            $files
        );

        // Assert
        $this->assertTrue($result['status']);
        $this->assertCount(3, $result['file'], '3 file berhasil diupload');

        $materials = $this->uploadMateri->getAll();
        $this->assertCount(3, $materials[0]['files']);
        $this->assertEquals('file1.pdf', $materials[0]['files'][0]['original']);
        $this->assertEquals('file2.docx', $materials[0]['files'][1]['original']);
        $this->assertEquals('file3.pptx', $materials[0]['files'][2]['original']);
    }
}
