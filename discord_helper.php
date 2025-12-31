<?php

function sendDiscordWebhook($title, $description, $color, $fields = []) {
    global $pdo;
    
    // Fetch Webhook URL
    try {
        $webhook_url = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_webhook_url'")->fetchColumn();
    } catch (Exception $e) {
        return; // DB error
    }

    if (!$webhook_url) return; // No URL configured

    
    try {
        $footer_text = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_footer_text'")->fetchColumn() ?: "Webui Key System";
    } catch (Exception $e) {
        $footer_text = "Webui Key System";
    }

    $timestamp = date("c", strtotime("now"));

    $json_data = json_encode([
        "embeds" => [
            [
                "title" => $title,
                "description" => $description,
                "color" => $color, // Decimal color code
                "timestamp" => $timestamp,
                "footer" => [
                    "text" => $footer_text,
                ],
                "fields" => $fields
            ]
        ]
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // Disable SSL check for XAMPP
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_exec($ch);
    curl_close($ch);
}
?>
