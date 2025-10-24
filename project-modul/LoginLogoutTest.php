<?php
use PHPUnit\Framework\TestCase;

require 'login.php';
require 'logout.php';

class LoginLogoutTest extends TestCase
{
    public function testLoginSuccess()
    {
        $this->assertTrue(login('siswa@belajar.id', 'password123'));
    }

    public function testLoginEmptyFields()
    {
        $this->assertFalse(login('', ''));
    }

    public function testLogoutSuccess()
    {
        $_SESSION['logged_in'] = true;
        $result = logout();

        $this->assertFalse(isset($_SESSION['logged_in']));
        $this->assertTrue($result);
    }
}
