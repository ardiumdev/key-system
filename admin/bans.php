<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$msg = '';
$msg_type = '';

if (isset($_POST['ban_ip'])) {
    $ip = trim($_POST['ip_address']);
    $reason = trim($_POST['reason']);
    
    if ($ip) {
        try {
            $pdo->prepare("INSERT INTO banned_ips (ip_address, reason) VALUES (?, ?)")->execute([$ip, $reason]);
            $msg = "IP adresi başarıyla engellendi."; $msg_type = 'success';
        } catch (PDOException $e) {
            $msg = "Bu IP adresi zaten engelli."; $msg_type = 'error';
        }
    }
}

if (isset($_GET['unban'])) {
    $pdo->prepare("DELETE FROM banned_ips WHERE id = ?")->execute([$_GET['unban']]);
    header('Location: bans.php?msg=unbanned'); exit;
}

if (isset($_GET['msg']) && $_GET['msg'] == 'unbanned') {
    $msg = "Engel başarıyla kaldırıldı."; $msg_type = 'success';
}

$bans = $pdo->query("SELECT * FROM banned_ips ORDER BY created_at DESC")->fetchAll();
$site_icon = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'site_icon'")->fetchColumn() ?: "";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IP Ban Yönetimi | Webui</title>
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
             <a href="bans.php" class="nav-item active">
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
                <h1>IP Ban Yönetimi</h1>
                <p>İstenmeyen IP adreslerini buradan engelleyebilirsiniz.</p>
            </div>
            <button class="theme-toggle" aria-label="Temayı Değiştir" style="position: relative; top:0; right:0;"></button>
        </div>

        <div class="dashboard-grid">
            <!-- Left: Table -->
            <div class="admin-card animate-slide-up delay-100" style="min-height: 500px;">
                <div class="admin-title" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>Engellenen IP'ler</span>
                    <span style="font-size: 0.8rem; opacity: 0.5; font-weight: 500;"><?php echo count($bans); ?> kayıt</span>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>IP Adresi</th>
                                <th>Sebep</th>
                                <th>Tarih</th>
                                <th style="text-align: right;">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($bans as $ban): ?>
                            <tr>
                                <td style="font-family: monospace; color: #ef4444; font-weight: 600;"><?php echo htmlspecialchars($ban['ip_address']); ?></td>
                                <td style="opacity: 0.8;"><?php echo htmlspecialchars($ban['reason']); ?></td>
                                <td style="opacity: 0.6; font-size: 0.85rem;">
                                    <?php echo date('d.m.Y H:i', strtotime($ban['created_at'])); ?>
                                </td>
                                <td style="text-align: right;">
                                    <a href="?unban=<?php echo $ban['id']; ?>" class="btn-delete" style="margin-left: auto; width: auto; padding: 6px 12px; font-size: 0.8rem;" onclick="return confirm('Bu IP adresinin engelini kaldırmak istediğinize emin misiniz?');">
                                        Engeli Kaldır
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <?php if(empty($bans)): ?>
                        <div class="empty-state">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                            <p>Henüz engellenen IP bulunmuyor.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Form -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <div class="admin-card animate-slide-up delay-200">
                    <div class="admin-title">IP Engelle</div>
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">IP Adresi</label>
                            <input type="text" name="ip_address" class="modern-input" placeholder="Örn: 192.168.1.1" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Sebep (Opsiyonel)</label>
                            <input type="text" name="reason" class="modern-input" placeholder="Örn: Spam giriş denemesi">
                        </div>
                        <button type="submit" name="ban_ip" class="btn-primary" style="width:100%; display: flex; justify-content: center; align-items: center; gap: 8px; background: #ef4444;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                            ENGELLE
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
