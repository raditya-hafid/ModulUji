<?php
namespace AnnouncementModule;

date_default_timezone_set("Asia/Jakarta");

class Announcement {
    var $dataFile;
    var $announcements;

    public function __construct($dataFile, bool $useRealFile = true) {
        if ($useRealFile) {
            $this->dataFile = $dataFile;

            $this->announcements = json_decode(file_get_contents($this->dataFile), true);
        }
    }

    public function updateAnnouncement() {
        $now = time();

        foreach ($this->announcements as &$a) {
            if ($a['status'] === 'pending' && $a['scheduled_for'] !== null) {
                if (strtotime($a['scheduled_for']) <= $now) {
                    $a['status'] = 'published';
                }
            }
        }
        unset($a);

        file_put_contents($this->dataFile, json_encode($this->announcements, JSON_PRETTY_PRINT));
    }

    public function addAnnouncement() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_announcement'])) {
            $message = trim($_POST['message'] ?? '');
            $schedule = trim($_POST['schedule'] ?? '');
            $status = "published";
            $scheduled_for = null;
            
            if (!$this->validateMessage($message)) die("❌ Pesan tidak boleh kosong!");

            $files = [];
            if (!empty($_FILES['attachments']['name'][0])) {
                if (!$this->validateAttachment($_FILES['attachments']['name'])) {
                    die("❌ Maksimal 20 file yang bisa diupload!");
                }
                
                if (!is_dir("uploads")) mkdir("uploads");

                foreach ($_FILES['attachments']['name'] as $i => $name) {
                    $tmp = $_FILES['attachments']['tmp_name'][$i];
                    if ($tmp) {
                        $dest = "uploads/" . time() . "_" . basename($name);
                        move_uploaded_file($tmp, $dest);
                        $files[] = $dest;
                    }
                }
            }

            if (!$this->validateSchedule($schedule)) {
                die("❌ Jadwal tidak boleh lebih dari 2 tahun dari sekarang!");
            }

            if ($schedule !== '') {
                $scheduled_for = date("Y-m-d H:i:s", strtotime($schedule));
                if (strtotime($scheduled_for) > time()) {
                    $status = "pending"; // belum saatnya
                }
            }

            $this->announcements[] = [
                "message" => htmlspecialchars($message),
                "files" => $files,
                "created_at" => date("Y-m-d H:i:s"),
                "scheduled_for" => $scheduled_for,
                "status" => $status,
                "comments" => []
            ];

            $this->saveData();
        }
    }

    public function addComment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_comment'])) {
            $announcementIndex = intval($_POST['announcement_index']);
            $name = trim($_POST['name'] ?? 'Anonim');
            $comment = trim($_POST['comment'] ?? '');

            if (!$this->validateComment($comment)) die("❌ Komentar tidak boleh kosong!");

            $newComment = [
                "name" => htmlspecialchars($name),
                "comment" => htmlspecialchars($comment),
                "created_at" => date("Y-m-d H:i:s"),
                "replies" => []
            ];

            $this->announcements[$announcementIndex]['comments'][] = $newComment;
            
            $this->saveData();
        }
    }

    public function addReply() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reply_comment'])) {
            $announcementIndex = intval($_POST['announcement_index']);
            $commentIndex = intval($_POST['comment_index']);
            $name = trim($_POST['name'] ?? 'Anonim');
            $comment = trim($_POST['comment'] ?? '');

            if (!$this->validateComment($comment)) die("❌ Balasan tidak boleh kosong!");

            $newReply = [
                "name" => htmlspecialchars($name),
                "comment" => htmlspecialchars($comment),
                "created_at" => date("Y-m-d H:i:s")
            ];

            $this->announcements[$announcementIndex]['comments'][$commentIndex]['replies'][] = $newReply;
            
            $this->saveData();
        }
    }

    public function validateMessage(String $message) {
        if ($message !== '') {
            return true;
        } else {
            return false;
        }
    }
    
    public function validateComment(String $comment) {
        if ($comment !== '') {
            return true;
        } else {
            return false;
        }
    }

    public function validateAttachment(Array $attachment) {
        if (count($attachment) <= 20) {
            return true;
        } else {
            return false;
        }
    }

    public function validateSchedule($schedule) {
        $now = time();
        $two_years = strtotime("+2 years", $now);

        if ($schedule < $two_years) {
            return true;
        } else {
            return false;
        }
    }

    public function displayAnnouncement() { ?>
        <h2>Buat Pengumuman</h2>
        <form method="post" enctype="multipart/form-data" oninput="validateForm()">
            <textarea name="message" id="message" placeholder="Tulis pengumuman..." rows="3" cols="40" required></textarea><br>
            <label>Jadwal Kirim (opsional):</label><br>
            <input type="datetime-local" name="schedule"><br>
            <input type="file" name="attachments[]" id="attachments" multiple onchange="validateForm()"><br>
            <button type="submit" name="new_announcement" id="submitBtn" disabled>Kirim</button>
        </form>

        <h2>Daftar Pengumuman</h2>
        <?php foreach (($this->announcements ?? []) as $aIndex => $a):
            if ($a['status'] !== 'published') continue; ?>
            <div class="announcement">
                <p><strong><?= $a['created_at'] ?></strong><br><?= nl2br($a['message']) ?></p>
                <?php if (!empty($a['files'])): ?>
                    <p>Lampiran:</p>
                    <ul>
                    <?php foreach ($a['files'] as $f): ?>
                        <li><a href="<?= $f ?>" target="_blank"><?= basename($f) ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <h4>Komentar</h4>
                <?php $this->displayComment($aIndex, $a) ?>
            </div>
        <?php endforeach;
    }

    private function displayComment($aIndex, $a) {
        foreach ($a['comments'] as $cIndex => $c): ?>
            <div class="comment">
                <strong><?= $c['name'] ?></strong> (<?= $c['created_at'] ?>)<br>
                <?= nl2br($c['comment']) ?>

                <!-- Tampilkan balasan -->
                <?php $this->displayReply($aIndex, $cIndex, $c) ?>
        <?php endforeach; ?>

        <form method="post">
            <input type="hidden" name="new_comment" value="1">
            <input type="hidden" name="announcement_index" value="<?= $aIndex ?>">
            <input type="text" name="name" placeholder="Nama (opsional)"><br>
            <textarea name="comment" rows="3" placeholder="Tulis komentar..." required></textarea><br>
            <button type="submit">Kirim Komentar</button>
        </form>
    <?php }

    private function displayReply($aIndex, $cIndex, $c) {
        if (!empty($c['replies'])):
            foreach ($c['replies'] as $r): ?>
                <div class="reply">
                    <em><?= $r['name'] ?></em> (<?= $r['created_at'] ?>)<br>
                    <?= nl2br($r['comment']) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Form balas -->
        <form method="post" style="margin-top:5px;">
            <input type="hidden" name="reply_comment" value="1">
            <input type="hidden" name="announcement_index" value="<?= $aIndex ?>">
            <input type="hidden" name="comment_index" value="<?= $cIndex ?>">
            <input type="text" name="name" placeholder="Nama (opsional)"><br>
            <textarea name="comment" rows="2" placeholder="Balas komentar..." required></textarea><br>
            <button type="submit">Balas</button>
        </form>
    <?php }

    private function saveData() {
        file_put_contents($this->dataFile, json_encode($this->announcements, JSON_PRETTY_PRINT));
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}