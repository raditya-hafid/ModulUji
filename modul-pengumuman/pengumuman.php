<?php
require __DIR__ . '/../vendor/autoload.php';

use AnnouncementModule\Announcement;

$dataFile = "announcements.json";

if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode([]));
}

$announcement = new Announcement($dataFile);

$announcement->updateAnnouncement();
$announcement->addAnnouncement();
$announcement->addComment();
$announcement->addReply();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pengumuman</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js"></script>
</head>
<body>
    <?php $announcement->displayAnnouncement(); ?>
</body>
</html>
