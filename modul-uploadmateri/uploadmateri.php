<?php
session_start();

class UploadMateri
{
    private $uploadDir;
    private $dataFile;
    private $studentsFile;
    private $topicsFile;

    public function __construct(
        $uploadDir = 'uploads/',
        $dataFile = 'materials.json',
        $studentsFile = 'students.json',
        $topicsFile = 'topics.json'
    ) {
        $this->uploadDir = $uploadDir;
        $this->dataFile = $dataFile;
        $this->studentsFile = $studentsFile;
        $this->topicsFile = $topicsFile;

        if (!is_dir($this->uploadDir)) mkdir($this->uploadDir, 0777, true);
    }

    private function getData($file)
    {
        return file_exists($file) ? json_decode(file_get_contents($file), true) ?: [] : [];
    }

    private function saveData($file, $data)
    {
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
    }

    public function getAll()
    {
        return $this->getData($this->dataFile);
    }

    public function upload($title, $description = '', $topic_id = '', $students = '', $classes = [], $files = [])
    {
        $uploadedFiles = [];

        if (!empty($files['name'])) {
            foreach ($files['name'] as $i => $name) {
                if ($files['error'][$i] === 0) {
                    $uploadedFiles[] = [
                        'original' => $name,
                        'stored' => 'virtual_' . uniqid() . '.' . pathinfo($name, PATHINFO_EXTENSION)
                    ];
                }
            }
        }

        $materials = $this->getData($this->dataFile);
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

        $this->saveData($this->dataFile, $materials);
        return ['status' => true, 'msg' => 'Materi berhasil diupload', 'file' => $uploadedFiles];
    }


    public function delete($id)
    {
        $materials = $this->getData($this->dataFile);
        foreach ($materials as $k => $m) {
            if ($m['id'] === $id) {
                foreach ($m['files'] as $f) {
                    $filePath = $this->uploadDir . $f['stored'];
                    if (file_exists($filePath)) unlink($filePath);
                }
                unset($materials[$k]);
            }
        }
        $this->saveData($this->dataFile, array_values($materials));
        return true;
    }
}


$uploadMateri = new UploadMateri();

$siswa = [
    ['id' => '1', 'name' => 'Semua Siswa'],
    ['id' => '2', 'name' => 'Ahmad Rizki'],
    ['id' => '3', 'name' => 'Siti Nurhaliza'],
];

$topik = [
    ['id' => '1', 'name' => 'Bab 1 - Pengenalan'],
    ['id' => '2', 'name' => 'Bab 2 - Dasar-dasar'],
];

$kelas = [
    ['id' => '1', 'name' => 'TI-A'],
    ['id' => '2', 'name' => 'TI-B'],
    ['id' => '3', 'name' => 'TI-C'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'upload') {
        $result = $uploadMateri->upload(
            $_POST['title'],
            $_POST['description'] ?? '',
            $_POST['topic_id'] ?? '',
            $_POST['students'] ?? '',
            $_POST['classes'] ?? [],
            $_FILES['files'] ?? []
        );
        $msg = $result['msg'];
    }

    if ($action === 'delete') {
        $uploadMateri->delete($_POST['id']);
        $msg = 'Materi dihapus';
    }
}

$materials = $uploadMateri->getAll();
$students = $siswa;
$topics = $topik;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Upload Materi</title>
</head>

<body>
    <h1>Upload Materi</h1>

    <?php if (isset($msg)): ?>
        <p><b><?= $msg ?></b></p>
    <?php endif; ?>

    <hr>

    <h2>Upload Materi</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="upload">

        <p>Judul: <input type="text" name="title" required></p>
        <p>Deskripsi: <textarea name="description"></textarea></p>

        <p>Untuk:</p>
        <?php foreach ($kelas as $k): ?>
            <label>
                <input type="checkbox" name="classes[]" value="<?= $k['id'] ?>">
                <?= $k['name'] ?>
            </label><br>
        <?php endforeach; ?>

        <p>Topik:
            <select name="topic_id">
                <option value="">-</option>
                <?php foreach ($topics as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>Tugaskan ke:
            <select name="students" required>
                <option value="">- Pilih Siswa -</option>
                <?php foreach ($students as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p>File: <input type="file" name="files[]" multiple></p>

        <button>Upload</button>
    </form>

    <hr>

    <h2>Daftar Materi</h2>
    <?php foreach (array_reverse($materials) as $m): ?>
        <div>
            <h3><?= $m['title'] ?></h3>
            <?php if ($m['description']): ?>
                <p><?= $m['description'] ?></p>
            <?php endif; ?>
            <?php if (!empty($m['files'])): ?>
                <p>File:</p>
                <ul>
                    <?php foreach ($m['files'] as $i => $f): ?>
                        <li>
                            <?= $f['original'] ?>
                            <a href="?download=1&id=<?= $m['id'] ?>&i=<?= $i ?>">Download</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <form method="POST" onsubmit="return confirm('Hapus?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $m['id'] ?>">
                <button>Hapus</button>
            </form>
            <hr>
        </div>
    <?php endforeach; ?>
</body>

</html>