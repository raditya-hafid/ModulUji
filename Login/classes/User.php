<?php
// file: classes/User.php

class User {
    private $conn;
    private $table = 'users';

    // Properti user
    public $id;
    public $username;
    public $email;
    public $password;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Cek apakah username atau email ada
    public function checkUserExists($identifier) {
        // Query untuk mencari user berdasarkan username atau email
        $query = 'SELECT id, username, password FROM ' . $this->table . ' WHERE username = :identifier OR email = :identifier LIMIT 1';

        // Prepared statement
        $stmt = $this->conn->prepare($query);

        // Bind parameter
        $stmt->bindParam(':identifier', $identifier);

        // Eksekusi query
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->password = $row['password']; // Ini adalah password hash dari DB
            return true;
        }

        return false;
    }

    // Verifikasi password
    public function verifyPassword($inputPassword) {
        // Menggunakan fungsi password_verify untuk membandingkan password input dengan hash di database
        return password_verify($inputPassword, $this->password);
    }
}