<?php
// file: logout.php
require_once 'includes/header.php';

// Hancurkan semua data session
session_unset();
session_destroy();

// Redirect ke halaman login
header("Location: login.php");
exit();
?>