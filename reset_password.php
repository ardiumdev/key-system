<?php
require_once 'db.php';

try {
    $username = 'admin';
    $password = 'admin123';
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Check if user exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->fetchColumn() > 0) {
        // Update existing
        $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE username = ?");
        $stmt->execute([$hash, $username]);
        echo "Şifre başarıyla güncellendi!<br>";
        echo "Kullanıcı: $username<br>";
        echo "Yeni Şifre: $password<br>";
    } else {
        // Create new
        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hash]);
        echo "Kullanıcı oluşturuldu!<br>";
        echo "Kullanıcı: $username<br>";
        echo "Şifre: $password<br>";
    }
    
    echo "<br><a href='admin/login.php'>Giriş Yap</a>";

} catch (PDOException $e) {
    echo "Hata: " . $e->getMessage();
}
?>
