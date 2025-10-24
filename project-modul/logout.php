<?php
session_start();

function logout() {
    if (isset($_SESSION['logged_in'])) {
        session_unset();
        session_destroy();
        return true;
    }
    return false;
}
