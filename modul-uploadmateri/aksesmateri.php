<?php
session_start();

class AksesMateri
{
    private $dataFile;

    public function __construct($dataFile = 'materials.json')
    {
        $this->dataFile = $dataFile;
    }

    private function getData()
    {
        if (!file_exists($this->dataFile)) {
            return [];
        }
        return json_decode(file_get_contents($this->dataFile), true) ?: [];
    }

    public function validateStudentAccess($studentId, $classId)
    {
        return !empty($studentId) && !empty($classId);
    }

    public function getAll()
    {
        return $this->getData();
    }

    public function getMateriByStudent($studentId, $classId)
    {
        if (!$this->validateStudentAccess($studentId, $classId)) {
            return [];
        }

        $allMaterials = $this->getData();
        $filteredMaterials = [];

        foreach ($allMaterials as $m) {
            $isForAllStudents = $m['students'] === '1';
            $isForThisStudent = $m['students'] === $studentId;
            $isForThisClass = in_array($classId, $m['classes']);

            if (($isForAllStudents || $isForThisStudent) && $isForThisClass) {
                $filteredMaterials[] = $m;
            }
        }

        return $filteredMaterials;
    }

    public function getMateriById($materiId)
    {
        $allMaterials = $this->getData();

        foreach ($allMaterials as $m) {
            if ($m['id'] === $materiId) {
                return $m;
            }
        }

        return null;
    }

    public function getFileByIndex($materiId, $fileIndex)
    {
        $materi = $this->getMateriById($materiId);

        if ($materi && isset($materi['files'][$fileIndex])) {
            return $materi['files'][$fileIndex];
        }

        return null;
    }
}

$aksesMateri = new AksesMateri();

$currentStudent = [
    'id' => '2',
    'name' => 'Ahmad Rizki',
    'class' => '1'
];

if (isset($_GET['download']) && isset($_GET['id']) && isset($_GET['file_index'])) {
    $file = $aksesMateri->getFileByIndex($_GET['id'], (int)$_GET['file_index']);
    
    if ($file) {
        $message = "Download file: " . htmlspecialchars($file['original']);
    } else {
        $message = "File tidak ditemukan";
    }
}

$materials = $aksesMateri->getMateriByStudent($currentStudent['id'], $currentStudent['class']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Materi - Siswa</title>
</head>
<body>
    <h1>Materi Pembelajaran</h1>
    
    <p>Login sebagai: <strong><?= htmlspecialchars($currentStudent['name']) ?></strong></p>
    
    <?php if (isset($message)): ?>
        <p><strong><?= $message ?></strong></p>
    <?php endif; ?>

    <hr>

    <h2>Daftar Materi Tersedia (<?= count($materials) ?>)</h2>

    <?php if (empty($materials)): ?>
        <p>Belum ada materi yang tersedia untuk kelas Anda</p>
    <?php else: ?>
        <?php foreach (array_reverse($materials) as $m): ?>
            <div>
                <h3><?= htmlspecialchars($m['title']) ?></h3>
                
                <?php if (!empty($m['description'])): ?>
                    <p><?= htmlspecialchars($m['description']) ?></p>
                <?php endif; ?>

                <?php if (!empty($m['files'])): ?>
                    <p><strong>File yang tersedia:</strong></p>
                    <ul>
                        <?php foreach ($m['files'] as $index => $f): ?>
                            <li>
                                <?= htmlspecialchars($f['original']) ?>
                                <a href="?download=1&id=<?= $m['id'] ?>&file_index=<?= $index ?>">Download</a>
                                |
                                <a href="#" onclick="alert('Preview: <?= htmlspecialchars($f['original']) ?>'); return false;">Preview</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Tidak ada file terlampir</p>
                <?php endif; ?>

                <p><small>Dipublikasikan: <?= $m['date'] ?></small></p>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>