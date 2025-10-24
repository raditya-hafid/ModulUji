<?php
session_start();

function login($username, $password) {
    $validEmail = 'siswa@belajar.id';
    $validPassword = 'password123';

    if (empty($username) || empty($password)) {
        return false;
    }

    if ($username === $validEmail && $password === $validPassword) {
        $_SESSION['logged_in'] = true;
        return true;
    }

    return false;
}
