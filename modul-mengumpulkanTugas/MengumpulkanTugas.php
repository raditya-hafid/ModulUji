<?php
class MengumpulkanTugas
{
    public $add;
    public $message;
    public $status;

    public function __construct($file = null, $action = null, $dueDate = null, $submittedAt = null)
    {
        if ($action == 'turn_in') {
            if ($file && isset($file['error']) && $file['error'] == 0) {
                $this->add = $file['name'];

                // cek keterlambatan
                if ($dueDate && $submittedAt && $submittedAt > $dueDate) {
                    $this->status = 'Terlambat';
                    $this->message = '✅ File berhasil diupload terlambat: '.$file['name'];
                } else {
                    $this->status = 'Submitted';
                    $this->message = '✅ File berhasil diupload: '.$file['name'];
                }
            } else {
                $this->add = null;
                $this->status = 'Submitted';
                $this->message = '❌ Tidak ada tugas yang dilampirkan';
            }
        } elseif ($action == 'mark_done') {
            $this->add = null;
            $this->status = 'Submitted';
            $this->message = '✅ Tugas ditandai selesai tanpa upload file.';
        } elseif ($action == 'unsubmit') {
            $this->add = null;
            $this->status = 'Draft';
            $this->message = '↩️ Tugas dibatalkan, kembali ke draft.';
        }
    }
}

// ----------------------------------------------------
// Bagian untuk tampilan HTML
// ----------------------------------------------------
$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? null;
    $file = $_FILES['add'] ?? null;

    if ($action) { // ✅ hanya proses kalau ada action dari tombol
        $dueDate = strtotime('2025-10-02 12:00:00');
        $submittedAt = time();
        $result = new MengumpulkanTugas($file, $action, $dueDate, $submittedAt);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Submit Assignment</title>
</head>
<body>
  <h1>Your Work</h1>
  <form action="" method="POST" enctype="multipart/form-data">
    <div>
      <label for="add">+ Add or create</label><br>
      <input type="file" name="add" id="add">
    </div><br><br>

    <div>
      <button name="action" value="turn_in" type="submit">Turn in</button>
    </div><br>
    <div>
      <button name="action" value="mark_done" type="submit">Mark as done</button>
    </div><br>
    <div>
      <button name="action" value="unsubmit" type="submit">Unsubmit</button>
    </div>
  </form>

  <?php if ($result) { ?>
    <h2>Hasil:</h2>
    <p>Status: <?php echo htmlspecialchars($result->status); ?></p>
    <p>Pesan: <?php echo htmlspecialchars($result->message); ?></p>
  <?php } ?>
</body>
</html>
