<?php
require_once 'db.php';
require_once 'discord_helper.php';

echo "<h1>Discord Test</h1>";

try {
    $url = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_webhook_url'")->fetchColumn();
    echo "<b>Webhook URL:</b> " . htmlspecialchars($url) . "<br><br>";
    
    if (!$url) {
        echo "❌ URL veritabanında bulunamadı!";
        exit;
    }

    echo "Mesaj gönderiliyor...<br>";
    
    sendDiscordWebhook(
        "🚀 Test Mesajı",
        "Bu mesaj sistem tarafından test amaçlı gönderilmiştir. Eğer bunu görüyorsanız entegrasyon çalışıyor demektir!",
        3447003,
        [["name" => "Durum", "value" => "Başarılı", "inline" => true]]
    );
    
    echo "✅ İşlem tamamlandı! Discord kanalınızı kontrol edin.";

} catch (Exception $e) {
    echo "Hata: " . $e->getMessage();
}
?>
