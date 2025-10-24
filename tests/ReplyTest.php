<?php
require __DIR__ . '/../vendor/autoload.php';

use AnnouncementModule\Announcement;
use PHPUnit\Framework\TestCase;

class ReplyTest extends TestCase
{
    private Announcement $announcement;
    private $testJson = [];

    protected function setUp(): void
    {
        $this->announcement = new Announcement("", false);
    }

    public function testTc48StudentEmptyComment()
    {
        $this->assertFalse($this->announcement->validateComment(""));
    }
}