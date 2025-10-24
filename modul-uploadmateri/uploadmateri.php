<?php
session_start();

// ============================================
// CLASS UPLOADMATERI (CLEAN & SIMPLE)
// ============================================
class UploadMateri
{
    private $uploadDir;
    private $dataFile;

    public function __construct($uploadDir = 'uploads/', $dataFile = 'materials.json')
    {
        $this->uploadDir = $uploadDir;
        $this->dataFile = $dataFile;

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    private function getData()
    {
        if (!file_exists($this->dataFile)) {
            return [];
        }
        return json_decode(file_get_contents($this->dataFile), true) ?: [];
    }

    private function saveData($data)
    {
        file_put_contents($this->dataFile, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function validateTitle($title)
    {
        return !empty(trim($title));
    }

    public function validateFiles($files)
    {
        if (empty($files['name'][0])) {
            return true; // File optional
        }
        return count($files['name']) <= 10;
    }

    public function getAll()
    {
        return $this->getData();
    }

    public function upload($title, $description = '', $topic_id = '', $students = '', $classes = [], $files = [])
    {
        if (!$this->validateTitle($title)) {
            return ['status' => false, 'msg' => 'Judul tidak boleh kosong'];
        }

        if (!$this->validateFiles($files)) {
            return ['status' => false, 'msg' => 'Maksimal 10 file'];
        }

        $uploadedFiles = [];
        if (!empty($files['name'][0])) {
            foreach ($files['name'] as $i => $name) {
                if ($files['error'][$i] === 0) {
                    $uploadedFiles[] = [
                        'original' => $name,
                        'stored' => 'virtual_' . uniqid() . '.' . pathinfo($name, PATHINFO_EXTENSION)
                    ];
                }
            }
        }

        $materials = $this->getData();
        $materials[] = [
            'id' => uniqid(),
            'title' => $title,
            'description' => $description,
            'topic' => $topic_id,
            'students' => $students,
            'classes' => $classes,
            'files' => $uploadedFiles,
            'date' => date('Y-m-d H:i:s')
        ];

        $this->saveData($materials);

        return ['status' => true, 'msg' => 'Materi berhasil diupload', 'file' => $uploadedFiles];
    }

    public function delete($id)
    {
        $materials = $this->getData();

        foreach ($materials as $k => $m) {
            if ($m['id'] === $id) {
                unset($materials[$k]);
                break;
            }
        }

        $this->saveData(array_values($materials));
        return true;
    }
}

// ============================================
// INISIALISASI & DATA
// ============================================
$uploadMateri = new UploadMateri();

$kelas = [
    ['id' => '1', 'name' => 'TI-A'],
    ['id' => '2', 'name' => 'TI-B'],
    ['id' => '3', 'name' => 'TI-C'],
];

$siswa = [
    ['id' => '1', 'name' => 'Semua Siswa'],
    ['id' => '2', 'name' => 'Ahmad Rizki'],
    ['id' => '3', 'name' => 'Siti Nurhaliza'],
];

$topik = [
    ['id' => '1', 'name' => 'Bab 1 - Pengenalan'],
    ['id' => '2', 'name' => 'Bab 2 - Dasar-dasar'],
];

// ============================================
// PROSES REQUEST
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'upload') {
        $result = $uploadMateri->upload(
            $_POST['title'] ?? '',
            $_POST['description'] ?? '',
            $_POST['topic_id'] ?? '',
            $_POST['students'] ?? '1',
            $_POST['classes'] ?? [],
            $_FILES['files'] ?? []
        );
        $message = $result['msg'];
        $messageType = $result['status'] ? 'success' : 'error';
    }

    if ($action === 'delete') {
        $uploadMateri->delete($_POST['id']);
        $message = 'Materi berhasil dihapus';
        $messageType = 'success';
    }
}

$materials = $uploadMateri->getAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Materi</title>
</head>

<body>
    <h1>Upload Materi</h1>

    <?php if (isset($message)): ?>
        <p><strong><?= htmlspecialchars($message) ?></strong></p>
    <?php endif; ?>

    <h2>Upload Materi Baru</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="upload">

        <label>Judul:</label>
        <input type="text" name="title" required>
        <br><br>

        <label>Deskripsi:</label>
        <textarea name="description" rows="4" cols="50"></textarea>
        <br><br>

        <label>Untuk: *</label><br>
        <?php foreach ($kelas as $k): ?>
            <input type="checkbox" name="classes[]" value="<?= $k['id'] ?>" id="class_<?= $k['id'] ?>">
            <label for="class_<?= $k['id'] ?>"><?= $k['name'] ?></label><br>
        <?php endforeach; ?>
        <small>(Pilih minimal 1 kelas)</small>
        <br><br>

        <label>Topik:</label>
        <select name="topic_id">
            <option value="">-</option>
            <?php foreach ($topik as $t): ?>
                <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>Tugaskan ke:</label>
        <select name="students">
            <option value="">- Pilih Siswa -</option>
            <?php foreach ($siswa as $s): ?>
                <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label>File:</label>
        <input type="file" name="files[]" multiple>
        <br><br>

        <button type="submit">Upload</button>
    </form>

    <hr>

    <h2>Daftar Materi (<?= count($materials) ?>)</h2>

    <?php if (empty($materials)): ?>
        <p>Belum ada materi yang diupload</p>
    <?php else: ?>
        <?php foreach (array_reverse($materials) as $m): ?>
            <div>
                <h3><?= htmlspecialchars($m['title']) ?></h3>

                <?php if (!empty($m['description'])): ?>
                    <p><?= htmlspecialchars($m['description']) ?></p>
                <?php endif; ?>

                <?php if (!empty($m['classes'])): ?>
                    <p><strong>Untuk kelas:</strong>
                        <?php
                        $kelasNames = [];
                        foreach ($m['classes'] as $classId) {
                            foreach ($kelas as $k) {
                                if ($k['id'] === $classId) {
                                    $kelasNames[] = $k['name'];
                                }
                            }
                        }
                        echo implode(', ', $kelasNames);
                        ?>
                    </p>
                <?php endif; ?>

                <?php if (!empty($m['files'])): ?>
                    <p><strong>File terlampir:</strong></p>
                    <ul>
                        <?php foreach ($m['files'] as $f): ?>
                            <li><?= htmlspecialchars($f['original']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Tidak ada file</p>
                <?php endif; ?>

                <p><small>Diupload: <?= $m['date'] ?></small></p>

                <form method="POST" onsubmit="return confirm('Yakin ingin menghapus materi ini?')">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                    <button type="submit">Hapus</button>
                </form>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</body>

</html>