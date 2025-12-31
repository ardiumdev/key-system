<?php
$host = 'localhost';
$dbname = 'key_system_db';
$username = 'root';
$password = '';

try {
    // Shared hosting uyumlu bağlantı (Veritabanı adı DSN içinde olmalı)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Otomatik veritabanı oluşturma komutları shared hosting'de çalışmaz, o yüzden kaldırıldı.
    // $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    // $pdo->exec("USE `$dbname`");
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
