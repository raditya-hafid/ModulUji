<?php
session_start();

require_once 'aksesmateri.php';

$aksesMateri = new AksesMateri();

if (!isset($_GET['id']) || !isset($_GET['file_index'])) {
    die('File tidak ditemukan');
}

$materiId = $_GET['id'];
$fileIndex = (int)$_GET['file_index'];

$file = $aksesMateri->getFileByIndex($materiId, $fileIndex);

if (!$file) {
    die('File tidak ditemukan');
}

$materi = $aksesMateri->getMateriById($materiId);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview - <?= htmlspecialchars($file['original']) ?></title>
</head>

<body>
    <h1>Preview File</h1>

    <p><strong>Materi:</strong> <?= htmlspecialchars($materi['title']) ?></p>
    <p><strong>File:</strong> <?= htmlspecialchars($file['original']) ?></p>

    <hr>

    <?php
    $extension = strtolower(pathinfo($file['original'], PATHINFO_EXTENSION));

    if (in_array($extension, ['pdf'])):
    ?>
        <p>Preview PDF:</p>
        <iframe src="uploads/<?= htmlspecialchars($file['stored']) ?>" width="100%" height="600px" style="border: 1px solid #ccc;">
            Browser Anda tidak mendukung preview PDF.
        </iframe>

    <?php elseif (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
        <p>Preview Gambar:</p>
        <img src="uploads/<?= htmlspecialchars($file['stored']) ?>" alt="<?= htmlspecialchars($file['original']) ?>" style="max-width: 100%; height: auto;">

    <?php elseif (in_array($extension, ['txt'])): ?>
        <p>Preview Teks:</p>
        <pre style="background: #f5f5f5; padding: 15px; border: 1px solid #ddd; overflow: auto;">
<?php
        $filePath = 'uploads/' . $file['stored'];
        if (file_exists($filePath)) {
            echo htmlspecialchars(file_get_contents($filePath));
        } else {
            echo 'File tidak ditemukan di server';
        }
?>
        </pre>

    <?php else: ?>
        <p>Preview tidak tersedia untuk file tipe: <strong><?= htmlspecialchars($extension) ?></strong></p>
        <p>Silakan download file untuk melihat isinya.</p>
        <p><a href="aksesmateri.php?download=1&id=<?= $materiId ?>&file_index=<?= $fileIndex ?>">Download File</a></p>
    <?php endif; ?>

    <hr>

    <p><a href="aksesmateri.php">Kembali ke Daftar Materi</a></p>
</body>

</html>