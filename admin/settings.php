<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$msg = '';
$msg_type = '';

if (isset($_POST['update_settings'])) {
    $footer = $_POST['footer_text'];
    $title = $_POST['site_title'];
    $desc = $_POST['site_desc'];
    $icon = $_POST['site_icon'];
    $m_mode = isset($_POST['maintenance_mode']) ? '1' : '0';
    $m_msg = $_POST['maintenance_message'];
    $discord_url = $_POST['discord_webhook_url'];
    
    // Message Templates
    $d_success_t = $_POST['discord_msg_success_title'] ?? ''; $d_success_d = $_POST['discord_msg_success_desc'] ?? '';
    $d_fail_t = $_POST['discord_msg_fail_title'] ?? '';       $d_fail_d = $_POST['discord_msg_fail_desc'] ?? '';
    $d_create_t = $_POST['discord_msg_create_title'] ?? '';   $d_create_d = $_POST['discord_msg_create_desc'] ?? '';
    
    // Advanced Settings
    $d_footer = $_POST['discord_footer_text'] ?? 'Webui Key System';
    $d_notify_success = isset($_POST['discord_notify_success']) ? '1' : '0';
    $d_notify_fail = isset($_POST['discord_notify_fail']) ? '1' : '0';
    $d_notify_create = isset($_POST['discord_notify_create']) ? '1' : '0';

    $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'footer_text'")->execute([$footer]);
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('site_title', ?)")->execute([$title]);
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('site_desc', ?)")->execute([$desc]);
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('site_icon', ?)")->execute([$icon]);
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('maintenance_mode', ?)")->execute([$m_mode]);
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('maintenance_message', ?)")->execute([$m_msg]);
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('discord_webhook_url', ?)")->execute([$discord_url]);
    
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('discord_msg_success_title', ?)")->execute([$d_success_t]);
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('discord_msg_success_desc', ?)")->execute([$d_success_d]);
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('discord_msg_fail_title', ?)")->execute([$d_fail_t]);
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('discord_msg_fail_desc', ?)")->execute([$d_fail_d]);
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('discord_msg_create_title', ?)")->execute([$d_create_t]);
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('discord_msg_create_desc', ?)")->execute([$d_create_d]);

    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('discord_footer_text', ?)")->execute([$d_footer]);
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('discord_notify_success', ?)")->execute([$d_notify_success]);
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('discord_notify_fail', ?)")->execute([$d_notify_fail]);
    $pdo->prepare("REPLACE INTO settings (setting_key, setting_value) VALUES ('discord_notify_create', ?)")->execute([$d_notify_create]);
    
    $msg = "Site ayarları güncellendi."; $msg_type = 'success';
}

$footer_text = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'footer_text'")->fetchColumn() ?: "Yapımcı Webui";
$site_title = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'site_title'")->fetchColumn() ?: "Webui Key System";
$site_desc = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'site_desc'")->fetchColumn() ?: "Key System";
$site_icon = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'site_icon'")->fetchColumn() ?: "";
$maintenance_mode = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'maintenance_mode'")->fetchColumn() ?: "0";
$maintenance_message = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'maintenance_message'")->fetchColumn() ?: "Sitemiz şu anda bakım çalışması nedeniyle hizmet dışıdır.";
$discord_webhook_url = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_webhook_url'")->fetchColumn() ?: "";

// Fetch Templates
$d_success_t = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_msg_success_title'")->fetchColumn() ?: "✅ Başarılı Giriş";
$d_success_desc = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_msg_success_desc'")->fetchColumn() ?: "Kullanıcı **{key}** anahtarı ile giriş yaptı.";
$d_fail_t = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_msg_fail_title'")->fetchColumn() ?: "⚠️ Hatalı Giriş Denemesi";
$d_fail_desc = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_msg_fail_desc'")->fetchColumn() ?: "Kayıtlı olmayan bir anahtar denendi.\nDenenen: **{key}**";
$d_create_t = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_msg_create_title'")->fetchColumn() ?: "🔑 Yeni Key Oluşturuldu";
$d_create_desc = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_msg_create_desc'")->fetchColumn() ?: "Yeni bir anahtar üretildi.";

// Fetch Advanced Settings
$d_footer = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_footer_text'")->fetchColumn() ?: "Webui Key System";
$d_notify_success = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_notify_success'")->fetchColumn() ?: "1";
$d_notify_fail = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_notify_fail'")->fetchColumn() ?: "1";
$d_notify_create = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_notify_create'")->fetchColumn() ?: "1";

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Ayarları | Webui</title>
    <?php if($site_icon): ?><link rel="icon" href="<?php echo htmlspecialchars($site_icon); ?>"><?php endif; ?>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    
    <aside class="sidebar">
        <div class="brand">
            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            <span>Webui Admin</span>
        </div>
        <nav class="nav-menu">
            <a href="dashboard.php" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                <span>Genel Bakış</span>
            </a>
            <a href="keys.php" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
                <span>Anahtarlar</span>
            </a>
            <a href="logs.php" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                <span>Log Kayıtları</span>
            </a>
            <a href="bans.php" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                <span>IP Ban / Kara Liste</span>
            </a>
            <a href="settings.php" class="nav-item active">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.1a2 2 0 0 1-1-1.72v-.51a2 2 0 0 1 1-1.74l-.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                <span>Site Ayarları</span>
            </a>
            <a href="../index.php" target="_blank" class="nav-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                <span>Siteyi Görüntüle</span>
            </a>
            <div style="flex-grow: 1;"></div>
            <a href="../logout.php" class="nav-item" style="color: #ef4444;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                <span>Çıkış Yap</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-header animate-slide-up">
            <div class="page-title">
                <h1>Site Ayarları</h1>
                <p>Website başlığı, açıklama ve diğer temel ayarları yapılandırın.</p>
            </div>
            <button class="theme-toggle" aria-label="Temayı Değiştir" style="position: relative; top:0; right:0;"></button>
        </div>

        <div class="admin-card animate-slide-up delay-100" style="max-width: 800px;">
            <div class="admin-title">Genel Yapılandırma</div>
            <form method="POST" action="">
                <input type="hidden" name="update_settings" value="1">
                
                <div class="form-group">
                    <label class="form-label">Site Başlığı</label>
                    <input type="text" name="site_title" class="modern-input" value="<?php echo htmlspecialchars($site_title); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Açıklama</label>
                    <input type="text" name="site_desc" class="modern-input" value="<?php echo htmlspecialchars($site_desc); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Logo / İkon URL</label>
                    <input type="text" name="site_icon" class="modern-input" value="<?php echo htmlspecialchars($site_icon); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Footer Metni</label>
                    <input type="text" name="footer_text" class="modern-input" value="<?php echo htmlspecialchars($footer_text); ?>">
                </div>

                <div class="admin-title" style="margin-top: 2rem; margin-bottom: 1rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem;">Bildirim Ayarları</div>
                
                <div class="form-group">
                    <label class="form-label">Discord Webhook URL</label>
                    <input type="url" name="discord_webhook_url" class="modern-input" placeholder="https://discord.com/api/webhooks/..." value="<?php echo htmlspecialchars($discord_webhook_url); ?>">
                    <p style="font-size: 0.8rem; opacity: 0.5; margin-top: 5px;">Bildirim almak istediğiniz kanalın webhook adresini girin.</p>
                </div>

                <div class="form-group">
                    <label class="form-label">Discord Footer Yazısı</label>
                    <input type="text" name="discord_footer_text" class="modern-input" value="<?php echo htmlspecialchars($d_footer); ?>">
                </div>

                <div class="admin-title" style="margin-top: 2rem; margin-bottom: 1rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem;">Bildirim Şablonları</div>
                <p style="font-size: 0.85rem; opacity: 0.6; margin-bottom: 1rem;">Kullanılabilir değişkenler: <code>{key}</code>, <code>{ip}</code>, <code>{ua}</code> (Sadece giriş), <code>{duration}</code>, <code>{url}</code> (Sadece oluşturma)</p>

                <div class="form-group" style="padding: 1.5rem; border: 1px solid var(--border-color); border-radius: 16px; margin-bottom: 1.5rem; background: var(--card-bg);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
                        <label class="form-label" style="color: #4ade80; margin:0; font-size: 1rem; font-weight: 600;">Başarılı Giriş Bildirimi</label>
                        <label class="switch">
                            <input type="checkbox" name="discord_notify_success" <?php echo $d_notify_success == '1' ? 'checked' : ''; ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <input type="text" name="discord_msg_success_title" class="modern-input" style="margin-bottom: 10px;" placeholder="Başlık" value="<?php echo htmlspecialchars($d_success_t); ?>">
                    <textarea name="discord_msg_success_desc" class="modern-input" rows="2" placeholder="Açıklama" style="resize: vertical; min-height: 80px;"><?php echo htmlspecialchars($d_success_desc); ?></textarea>
                </div>

                <div class="form-group" style="padding: 1.5rem; border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; margin-bottom: 1.5rem; background: rgba(255,255,255,0.01);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
                        <label class="form-label" style="color: #fbbf24; margin:0; font-size: 1rem; font-weight: 600;">Hatalı Giriş Bildirimi</label>
                        <label class="switch">
                            <input type="checkbox" name="discord_notify_fail" <?php echo $d_notify_fail == '1' ? 'checked' : ''; ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <input type="text" name="discord_msg_fail_title" class="modern-input" style="margin-bottom: 10px;" placeholder="Başlık" value="<?php echo htmlspecialchars($d_fail_t); ?>">
                    <textarea name="discord_msg_fail_desc" class="modern-input" rows="2" placeholder="Açıklama" style="resize: vertical; min-height: 80px;"><?php echo htmlspecialchars($d_fail_desc); ?></textarea>
                </div>

                <div class="form-group" style="padding: 1.5rem; border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; background: rgba(255,255,255,0.01);">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
                        <label class="form-label" style="color: #60a5fa; margin:0; font-size: 1rem; font-weight: 600;">Key Oluşturma Bildirimi</label>
                        <label class="switch">
                            <input type="checkbox" name="discord_notify_create" <?php echo $d_notify_create == '1' ? 'checked' : ''; ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <input type="text" name="discord_msg_create_title" class="modern-input" style="margin-bottom: 10px;" placeholder="Başlık" value="<?php echo htmlspecialchars($d_create_t); ?>">
                    <textarea name="discord_msg_create_desc" class="modern-input" rows="2" placeholder="Açıklama" style="resize: vertical; min-height: 80px;"><?php echo htmlspecialchars($d_create_desc); ?></textarea>
                </div>

                <div class="admin-title" style="margin-top: 2rem; margin-bottom: 1rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem;">Bakım Modu Ayarları</div>

                <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                    <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 26px;">
                        <input type="checkbox" name="maintenance_mode" style="opacity: 0; width: 0; height: 0;" <?php echo $maintenance_mode == '1' ? 'checked' : ''; ?>>
                        <span class="slider round" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #3f3f46; transition: .4s; border-radius: 34px;"></span>
                        <style>
                            input:checked + .slider { background-color: #6366f1; }
                            input:focus + .slider { box-shadow: 0 0 1px #6366f1; }
                            .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
                            input:checked + .slider:before { transform: translateX(24px); }
                        </style>
                    </label>
                    <span style="font-weight: 500;">Bakım Modunu Aktifleştir</span>
                </div>

                <div class="form-group">
                    <label class="form-label">Bakım Mesajı</label>
                    <textarea name="maintenance_message" class="modern-input" rows="3"><?php echo htmlspecialchars($maintenance_message); ?></textarea>
                </div>
                
                <button type="submit" class="btn-primary" style="width:100%">AYARLARI KAYDET</button>
            </form>
        </div>
    </main>
    <script src="../assets/js/app.js"></script>
    <script>
        <?php if ($msg): ?>
            showToast("<?php echo addslashes($msg); ?>", "<?php echo $msg_type == 'error' ? 'error' : 'success'; ?>");
        <?php endif; ?>
    </script>
</body>
</html>
