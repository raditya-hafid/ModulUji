<?php
class MengumpulkanTugas
{
    public $add;
    public $message;
    public $status;
    public $dueDate;
    public $submittedAt;

    public function __construct($file = null, $action = null, $dueDate = null, $submittedAt = null)
    {
        $this->dueDate = $dueDate;
        $this->submittedAt = $submittedAt;

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
session_start();

if (!isset($_SESSION['isLate'])) {
    $_SESSION['isLate'] = false; // default: tidak terlambat
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['toggle_status'])) {
        // Toggle mode keterlambatan
        $_SESSION['isLate'] = !$_SESSION['isLate'];
    } else {
        $action = $_POST['action'] ?? null;
        $file = $_FILES['add'] ?? null;

        if ($action) {
            // kalau mode terlambat, deadline diset 1 jam yang lalu
            // kalau normal, deadline diset 1 jam ke depan
            $dueDate = $_SESSION['isLate'] ? time() - 3600 : time() + 3600;
            $submittedAt = time();

            $result = new MengumpulkanTugas($file, $action, $dueDate, $submittedAt);
        }
    }
}

if (php_sapi_name() !== 'cli') {
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Submit Assignment</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
    form { background: white; padding: 15px; border-radius: 10px; width: 300px; }
    h1 { color: #333; }
    .result { margin-top: 20px; padding: 15px; border-radius: 10px; }
    .Submitted { background: #e3ffe3; border: 1px solid #00b300; color: #006600; }
    .Terlambat { background: #ffe3e3; border: 1px solid #ff4d4d; color: #b30000; }
    .Draft { background: #e0e0e0; border: 1px solid #999; color: #333; }
    .mode { background: #ddd; padding: 8px; border-radius: 6px; display: inline-block; margin-bottom: 10px; }
  </style>
</head>
<body>
  <h1>Your Work</h1>

  <!-- Tampilkan mode -->
  <div class="mode">
    📅 Mode saat ini: <strong><?php echo $_SESSION['isLate'] ? 'Terlambat' : 'Normal'; ?></strong>
  </div>

  <!-- Tombol ubah mode -->
  <form method="POST" style="margin-bottom:15px;">
    <button name="toggle_status" type="submit">
      <?php echo $_SESSION['isLate'] ? 'Ubah ke mode Normal' : 'Ubah ke mode Terlambat'; ?>
    </button>
  </form>

  <form action="" method="POST" enctype="multipart/form-data">
    <div>
      <label for="add">+ Add or create</label><br>
      <input type="file" name="add" id="add">
    </div><br>
    
    <button name="action" value="turn_in" type="submit">Turn in</button><br><br>
    <button name="action" value="mark_done" type="submit">Mark as done</button><br><br>
    <button name="action" value="unsubmit" type="submit">Unsubmit</button>
  </form>

  <?php if (!empty($result)) { ?>
    <div class="result <?php echo htmlspecialchars($result->status); ?>">
      <h2>Status: <?php echo htmlspecialchars($result->status); ?></h2>
      <p><?php echo htmlspecialchars($result->message); ?></p>
      <p><strong>Deadline:</strong> <?php echo date('Y-m-d H:i:s', $result->dueDate); ?></p>
      <p><strong>Submitted at:</strong> <?php echo date('Y-m-d H:i:s', $result->submittedAt); ?></p>
    </div>
  <?php } ?>
</body>
</html>
<?php
}
?>

