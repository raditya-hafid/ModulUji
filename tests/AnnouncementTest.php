<?php
require __DIR__ . '/../vendor/autoload.php';

use AnnouncementModule\Announcement;
use PHPUnit\Framework\TestCase;

class AnnouncementTest extends TestCase
{
    public function testNoMessageAndNoAttachment()
    {
        $announcement = new Announcement("../modul-pengumuman/announcement.json");

        $this->assertFalse($announcement->validateMessage("") && $announcement->validateAttachment([]));
    }
    
    public function testNoMessageAndOneAttachment()
    {
        $announcement = new Announcement("../modul-pengumuman/announcement.json");

        $this->assertFalse($announcement->validateMessage("") && $announcement->validateAttachment(["tes"]));
    }
    
    public function testAttachmentLength()
    {
        $announcement = new Announcement("../modul-pengumuman/announcement.json");
        $attachmentFiles = [
            "file1", "file2", "file3", "file4", "file5",
            "file6", "file7", "file8", "file9", "file10",
            "file11", "file12", "file13", "file14", "file15",
            "file16", "file17", "file18", "file19", "file20",
            "file21"
        ]; 

        $this->assertFalse($announcement->validateAttachment($attachmentFiles));
    }

    public function testScheduleMoreThan2Years()
    {
        $announcement = new Announcement("../modul-pengumuman/announcement.json");

        $this->assertFalse($announcement->validateSchedule(strtotime("2027-11-02 09:28:00")));
    }

    public function testTeacherEmptyReply()
    {
        $announcement = new Announcement("../modul-pengumuman/announcement.json");

        $this->assertFalse($announcement->validateComment(""));
    }

    public function testStudentEmptyComment()
    {
        $announcement = new Announcement("../modul-pengumuman/announcement.json");

        $this->assertFalse($announcement->validateComment(""));
    }
}
