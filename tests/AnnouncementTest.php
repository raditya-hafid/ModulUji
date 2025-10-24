<?php
require __DIR__ . '/../vendor/autoload.php';

use AnnouncementModule\Announcement;
use PHPUnit\Framework\TestCase;

class AnnouncementTest extends TestCase
{
    private Announcement $announcement;
    private $testJson = [];

    protected function setUp(): void
    {
        $this->announcement = new Announcement("", false);
    }

    public function testTc43NoMessageAndNoAttachment()
    {
        $this->assertFalse($this->announcement->validateMessage("") && 
            $this->announcement->validateAttachment([]));
    }
    
    public function testTc44NoMessageAndOneAttachment()
    {
        $this->assertFalse($this->announcement->validateMessage("") &&
            $this->announcement->validateAttachment(["tes"]));
    }
    
    public function testTc45AttachmentLength()
    {
        $attachmentFiles = [
            "file1", "file2", "file3", "file4", "file5",
            "file6", "file7", "file8", "file9", "file10",
            "file11", "file12", "file13", "file14", "file15",
            "file16", "file17", "file18", "file19", "file20",
            "file21"
        ]; 

        $this->assertFalse($this->announcement->validateAttachment($attachmentFiles));
    }

    public function testTc46ScheduleMoreThan2Years()
    {
        $now = time();

        $this->assertFalse($this->announcement->validateSchedule(strtotime("+25 months", $now)));
    }

    public function testTc47TeacherEmptyReply()
    {
        $this->assertFalse($this->announcement->validateComment(""));
    }
}