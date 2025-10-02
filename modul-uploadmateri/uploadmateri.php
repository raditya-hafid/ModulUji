<?php
session_start();

$uploadDir = 'uploads/';
$dataFile = 'materials.json';
$studentsFile = 'students.json';
$topicsFile = 'topics.json';

if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);


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

function getData($file)
{
    return file_exists($file) ? json_decode(file_get_contents($file), true) ?: [] : [];
}

function saveData($file, $data)
{
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'upload') {
        $files = [];
        if (isset($_FILES['files'])) {
            foreach ($_FILES['files']['name'] as $i => $name) {
                if ($_FILES['files']['error'][$i] === 0) {
                    $newName = uniqid() . '.' . pathinfo($name, PATHINFO_EXTENSION);
                    move_uploaded_file($_FILES['files']['tmp_name'][$i], $uploadDir . $newName);
                    $files[] = ['original' => $name, 'stored' => $newName];
                }
            }
        }

        $materials = getData($dataFile);
        $materials[] = [
            'id' => uniqid(),
            'title' => $_POST['title'],
            'description' => $_POST['description'] ?? '',
            'topic' => $_POST['topic_id'] ?? '',
            'students' => $_POST['students'] ?? '',
            'classes' => $_POST['classes'] ?? [],
            'files' => $files,
            'date' => date('Y-m-d H:i:s')
        ];
        saveData($dataFile, $materials);
        $msg = 'Materi berhasil diupload';
    }

    if ($action === 'delete') {
        $materials = getData($dataFile);
        foreach ($materials as $k => $m) {
            if ($m['id'] === $_POST['id']) {
                foreach ($m['files'] as $f) unlink($uploadDir . $f['stored']);
                unset($materials[$k]);
            }
        }
        saveData($dataFile, array_values($materials));
        $msg = 'Materi dihapus';
    }
}

if (isset($_GET['download'])) {
    $materials = getData($dataFile);
    foreach ($materials as $m) {
        if ($m['id'] === $_GET['id'] && isset($m['files'][$_GET['i']])) {
            $file = $m['files'][$_GET['i']];
            header('Content-Disposition: attachment; filename="' . $file['original'] . '"');
            readfile($uploadDir . $file['stored']);
            exit;
        }
    }
}

$materials = getData($dataFile);
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