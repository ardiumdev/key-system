<?php
session_start();
require_once 'check_ban.php';
require_once 'db.php';
require_once 'discord_helper.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid method']);
    exit;
}

$key = $_POST['key'] ?? '';

if (empty($key)) {
    echo json_encode(['success' => false, 'message' => 'Empty key']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM access_keys WHERE key_code = ? AND status = 'active'");
    $stmt->execute([$key]);
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch();
        
        // CHECK EXPIRATION
        if ($row['expires_at'] && strtotime($row['expires_at']) < time()) {
             // LOG FAIL (Expired)
             sendDiscordWebhook(
                "❌ Başarısız Giriş Denemesi (Süresi Dolmuş)",
                "Bir kullanıcı süresi dolmuş bir key ile giriş yapmayı denedi.",
                15158332, // Red
                [
                    ["name" => "Key", "value" => $key, "inline" => true],
                    ["name" => "IP Adresi", "value" => $_SERVER['REMOTE_ADDR'], "inline" => true]
                ]
            );
             echo json_encode(['success' => false, 'message' => 'Key expired']);
             exit;
        }

        $_SESSION['access_granted'] = true; 
        
        // LOGGING
        $ip = $_SERVER['REMOTE_ADDR'];
        $ua = $_SERVER['HTTP_USER_AGENT'];
        try {
            $logStmt = $pdo->prepare("INSERT INTO key_logs (key_code, ip_address, user_agent) VALUES (?, ?, ?)");
            $logStmt->execute([$key, $ip, $ua]);

            // DISCORD NOTIFY
            $enabled = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_notify_success'")->fetchColumn() ?: "1";
            
            if ($enabled == '1') {
                $d_title = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_msg_success_title'")->fetchColumn() ?: "✅ Başarılı Giriş";
                $d_desc = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_msg_success_desc'")->fetchColumn() ?: "Kullanıcı {key} ile giriş yaptı.";
                
                // Replacements
                $d_desc = str_replace(['{key}', '{ip}', '{ua}'], [$key, $ip, $ua], $d_desc);
                $d_title = str_replace(['{key}', '{ip}'], [$key, $ip], $d_title);

                sendDiscordWebhook(
                    $d_title,
                    $d_desc,
                    3066993, // Green
                    [
                        ["name" => "Key", "value" => $key, "inline" => true],
                        ["name" => "IP Adresi", "value" => $ip, "inline" => true],
                        ["name" => "Tarayıcı", "value" => $ua, "inline" => false]
                    ]
                );
            }
        } catch(Exception $e) {
            // Ignore logging errors to not break login
        }

        echo json_encode(['success' => true, 'redirect_url' => $row['target_url']]);
    } else {
        // LOG FAIL (Invalid Key)
        $enabled = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_notify_fail'")->fetchColumn() ?: "1";
        
        if ($enabled == '1') {
            $d_title = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_msg_fail_title'")->fetchColumn() ?: "⚠️ Hatalı Giriş";
            $d_desc = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_msg_fail_desc'")->fetchColumn() ?: "Geçersiz key: {key}";
            
            $d_desc = str_replace(['{key}', '{ip}'], [$key, $_SERVER['REMOTE_ADDR']], $d_desc);
            $d_title = str_replace(['{key}'], [$key], $d_title);

            sendDiscordWebhook(
                $d_title,
                $d_desc,
                15105570, // Orange
                [
                    ["name" => "Denenen Key", "value" => $key, "inline" => true],
                    ["name" => "IP Adresi", "value" => $_SERVER['REMOTE_ADDR'], "inline" => true]
                ]
            );
        }
        echo json_encode(['success' => false, 'message' => 'Invalid key']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
}
?>
