<?php
// config/database.php

// Database credentials
$host = 'localhost';
$user = 'root';
$pass = 'Sa@123456';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=coachpro;charset=utf8",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
