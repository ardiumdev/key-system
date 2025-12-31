<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$msg = '';
$msg_type = '';

if (isset($_POST['add_key'])) {
    $code = trim($_POST['key_code']);
    $url = trim($_POST['target_url']);
    if ($code && $url) {
        try {
            $expires_at = null;
            $duration = $_POST['duration'] ?? 'unlimited';
            
            if ($duration === '24h') $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
            elseif ($duration === '3d') $expires_at = date('Y-m-d H:i:s', strtotime('+3 days'));
            elseif ($duration === '1w') $expires_at = date('Y-m-d H:i:s', strtotime('+1 week'));
            elseif ($duration === '1m') $expires_at = date('Y-m-d H:i:s', strtotime('+1 month'));

            $pdo->prepare("INSERT INTO access_keys (key_code, target_url, expires_at) VALUES (?, ?, ?)")->execute([$code, $url, $expires_at]);
            
            // DISCORD NOTIFY
            require_once '../discord_helper.php';
            
            $enabled = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_notify_create'")->fetchColumn() ?: "1";

            if ($enabled == '1') {
                $d_title = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_msg_create_title'")->fetchColumn() ?: "🔑 Yeni Key";
                $d_desc = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'discord_msg_create_desc'")->fetchColumn() ?: "Oluşturulan: {key}";
                
                $d_desc = str_replace(['{key}', '{duration}', '{url}'], [$code, $duration, $url], $d_desc);
                
                sendDiscordWebhook(
                    $d_title,
                    $d_desc,
                    3447003, // Blue
                    [
                        ["name" => "Key Kodu", "value" => $code, "inline" => true],
                        ["name" => "Süre", "value" => $duration, "inline" => true],
                        ["name" => "Hedef Link", "value" => $url, "inline" => false]
                    ]
                );
            }

            $msg = "Anahtar başarıyla oluşturuldu."; $msg_type = 'success';
        } catch (PDOException $e) {
            $msg = "Bu anahtar zaten mevcut."; $msg_type = 'error';
        }
    }
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM access_keys WHERE id = ?")->execute([$_GET['delete']]);
    header('Location: keys.php?msg=deleted'); exit;
}

if (isset($_GET['msg']) && $_GET['msg'] == 'deleted') {
    $msg = "Anahtar başarıyla silindi."; $msg_type = 'success';
}

$keys = $pdo->query("SELECT * FROM access_keys ORDER BY created_at DESC")->fetchAll();
$site_icon = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'site_icon'")->fetchColumn() ?: "";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anahtarlar | Webui</title>
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
            <a href="keys.php" class="nav-item active">
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
            <a href="settings.php" class="nav-item">
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

    <main class="main-content">
        <div class="top-header animate-slide-up">
            <div class="page-title">
                <h1>Anahtar Yönetimi</h1>
                <p>Yeni anahtar oluşturun veya mevcutları yönetin.</p>
            </div>
            <button class="theme-toggle" aria-label="Temayı Değiştir" style="position: relative; top:0; right:0;"></button>
        </div>

        <div class="dashboard-grid">
            <!-- Left: Table -->
            <div class="admin-card animate-slide-up delay-100" style="min-height: 500px;">
                <div class="admin-title" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>Aktif Anahtarlar</span>
                    <span style="font-size: 0.8rem; opacity: 0.5; font-weight: 500;"><?php echo count($keys); ?> kayıt</span>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Anahtar Kodu</th>
                                <th>Hedef Link</th>
                                <th>Süre</th>
                                <th>Durum</th>
                                <th style="text-align: right;">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($keys as $key): ?>
                            <tr>
                                <td style="font-family: 'Monaco', 'Consolas', monospace; font-size: 1rem; color: var(--text-color);"><?php echo htmlspecialchars($key['key_code']); ?></td>
                                <td>
                                    <div style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; opacity: 0.7; font-size: 0.9rem;">
                                        <?php echo htmlspecialchars($key['target_url']); ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if($key['expires_at']): ?>
                                        <div style="font-size: 0.85rem; opacity: 0.8;">
                                            <?php 
                                                $days_left = ceil((strtotime($key['expires_at']) - time()) / 86400); 
                                                if ($days_left < 0) echo '<span style="color:#ef4444">Süresi Doldu</span>';
                                                else echo $days_left . ' gün kaldı';
                                            ?>
                                            <div style="font-size: 0.75rem; opacity: 0.5;"><?php echo date('d.m.Y', strtotime($key['expires_at'])); ?></div>
                                        </div>
                                    <?php else: ?>
                                        <span class="status-badge" style="background: rgba(255, 255, 255, 0.1); color: white; border-color: rgba(255, 255, 255, 0.1);">Sınırsız</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="status-badge">Aktif</span></td>
                                <td style="text-align: right;">
                                    <a href="?delete=<?php echo $key['id']; ?>" class="btn-delete" style="margin-left: auto;" onclick="return confirm('Bu anahtarı silmek istediğinize emin misiniz?');">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php if(empty($keys)): ?>
                        <div class="empty-state">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                            <p>Henüz hiç anahtar oluşturulmamış.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Form -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <div class="admin-card animate-slide-up delay-200">
                    <div class="admin-title">Hızlı Anahtar Oluştur</div>
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Anahtar Kodu</label>
                            <div style="position: relative;">
                                <input type="text" name="key_code" class="modern-input" placeholder="Örn: VIP-2024-KEY" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Hedef URL</label>
                            <input type="url" name="target_url" class="modern-input" placeholder="https://..." required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Geçerlilik Süresi</label>
                            <select name="duration" class="modern-input" style="appearance: none; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22rgba(255,255,255,0.5)%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1em;">
                                <option style="background: #18181b;" value="unlimited">Sınırsız</option>
                                <option style="background: #18181b;" value="24h">24 Saat</option>
                                <option style="background: #18181b;" value="3d">3 Gün</option>
                                <option style="background: #18181b;" value="1w">1 Hafta</option>
                                <option style="background: #18181b;" value="1m">1 Ay</option>
                            </select>
                        </div>
                        <button type="submit" name="add_key" class="btn-primary" style="width:100%; display: flex; justify-content: center; align-items: center; gap: 8px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                            OLUŞTUR
                        </button>
                    </form>
                </div>
            </div>
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
