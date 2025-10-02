<?php
class Tugas
{
    public $judul;
    public $deskripsi;
    public $deadline;
    public $lampiran;
    public $poin;
    public $penerima;

    public function __construct($judul, $deskripsi, $deadline, $lampiran = null, $poin = 100, $penerima = 'semua')
    {
        if (empty(trim($judul))) {
            throw new Exception('Judul wajib diisi');
        }

        $this->judul = $judul;
        $this->deskripsi = $deskripsi;
        $this->deadline = $deadline;
        $this->lampiran = $lampiran;
        $this->poin = $poin;
        $this->penerima = $penerima;
    }
}

// Kalau file ini diakses langsung (bukan lewat PHPUnit)
if (php_sapi_name() !== 'cli') {
    ?>
<!DOCTYPE html>
<html>
<head>
  <title>Make The Assignment</title>
</head>
<body>
  <h1>Make The Assignment</h1>
  <form method="POST" enctype="multipart/form-data">
    <div>
      <label for="title">Title:</label><br>
      <input type="text" name="title" placeholder="Title" required>
    </div><br>

    <div>
      <label for="description">Description:</label><br>
      <textarea name="description" placeholder="Instruction (optional)"></textarea>
    </div><br>

    <div>
      <label for="attach">Attach:</label><br>
      <input type="file" name="attach">
    </div><br>

    <div>
      <label for="assign">Assign to:</label><br>
      <select name="assign">
        <option value="semua">All Students</option>
        <option value="handoyo">Handoyo</option>
      </select>
    </div><br>

    <div>
      <label for="points">Points:</label><br>
      <input type="number" name="points" value="100">
    </div><br>

    <div>
      <label for="due">Due:</label><br>
      <input type="date" name="due">
    </div><br>

    <button type="submit">Post</button>
  </form>

  <?php
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          try {
              $tugas = new Tugas(
                  $_POST['title'] ?? '',
                  $_POST['description'] ?? '',
                  $_POST['due'] ?? '',
                  $_FILES['attach']['name'] ?? null,
                  $_POST['points'] ?? 100,
                  $_POST['assign'] ?? 'semua'
              );
              echo "<p style='color:green;'>Tugas '{$tugas->judul}' berhasil dibuat!</p>";
          } catch (Exception $e) {
              echo "<p style='color:red;'>".$e->getMessage().'</p>';
          }
      }
    ?>
</body>
</html>
<?php
}
