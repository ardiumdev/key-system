<?php
require_once 'db.php';

try {
    // Create keys table
    $sqlKeys = "CREATE TABLE IF NOT EXISTS access_keys (
        id INT AUTO_INCREMENT PRIMARY KEY,
        key_code VARCHAR(255) NOT NULL UNIQUE,
        target_url TEXT NOT NULL,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlKeys);
    echo "Table 'access_keys' created successfully.<br>";

    // Add target_url column if it doesn't exist (for migration)
    try {
        $pdo->exec("ALTER TABLE access_keys ADD COLUMN target_url TEXT NOT NULL AFTER key_code");
        echo "Column 'target_url' added successfully.<br>";
    } catch (PDOException $e) {
        // Ignore error if column already exists
    }

    // Create settings table
    $sqlSettings = "CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(50) NOT NULL UNIQUE,
        setting_value TEXT NOT NULL
    )";
    $pdo->exec($sqlSettings);
    echo "Table 'settings' created successfully.<br>";

    // Insert Default Footer Text
    $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('footer_text', 'Yapımcı <strong>Webui</strong>')");
    $stmt->execute();
    
    // Insert Default Title & Description
    $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('site_title', 'Webui Key System')");
    $stmt->execute();
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO settings (setting_key, setting_value) VALUES ('site_desc', 'Gelişmiş key doğrulama ve yönlendirme sistemi.')");
    $stmt->execute();

    echo "Default settings inserted.<br>";

    // Create admins table
    $sqlAdmins = "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sqlAdmins);
    echo "Table 'admins' created successfully.<br>";

    // Create default admin if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $defaultPass = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES ('admin', ?)");
        $stmt->execute([$defaultPass]);
        echo "Default admin user created (User: admin, Pass: admin123).<br>";
    } else {
        echo "Admin user already exists.<br>";
    }

    echo "Setup completed successfully!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
