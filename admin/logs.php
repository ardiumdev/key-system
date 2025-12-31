<?php
session_start();
require_once '../db.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Clear Logs
if (isset($_GET['clear_logs'])) {
    $pdo->exec("TRUNCATE TABLE key_logs");
    header('Location: logs.php?msg=cleared');
    exit;
}

$msg = '';
if (isset($_GET['msg']) && $_GET['msg'] == 'cleared') {
    $msg = "Tüm loglar temizlendi."; $msg_type = 'success';
}

// Fetch Logs (Last 100)
$logs = $pdo->query("SELECT * FROM key_logs ORDER BY created_at DESC LIMIT 100")->fetchAll();
$site_icon = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'site_icon'")->fetchColumn() ?: "";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanım Logları | Webui</title>
    <?php if($site_icon): ?><link rel="icon" href="<?php echo htmlspecialchars($site_icon); ?>"><?php endif; ?>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="admin-body">
    
    <!-- Sidebar -->
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
            <a href="logs.php" class="nav-item active">
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

    <!-- Main Content -->
    <main class="main-content">
        <div class="top-header animate-slide-up">
            <div class="page-title">
                <h1>Kullanım Logları</h1>
                <p>Son giriş yapan kullanıcıların listesi (Son 100).</p>
            </div>
            <button class="theme-toggle" aria-label="Temayı Değiştir" style="position: relative; top:0; right:0;"></button>
        </div>

        <div class="admin-card animate-slide-up delay-100">
            <div class="admin-title" style="display: flex; justify-content: space-between; align-items: center;">
                <span>Erişim Geçmişi</span>
                <?php if(!empty($logs)): ?>
                    <a href="?clear_logs=1" class="btn-delete" style="width: auto; padding: 0 12px; background: rgba(239, 68, 68, 0.1); color: #ef4444 !important; box-shadow: none;" onclick="return confirm('Tüm logları temizlemek istediğinize emin misiniz?');">
                        Temizle
                    </a>
                <?php endif; ?>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Kullanılan Key</th>
                            <th>IP Adresi</th>
                            <th>Tarayıcı / Cihaz</th>
                            <th>Tarih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($logs as $log): ?>
                        <tr>
                            <td style="font-family: monospace; color: var(--accent-color); font-weight: 600;"><?php echo htmlspecialchars($log['key_code']); ?></td>
                            <td><span class="status-badge" style="background: rgba(96, 165, 250, 0.1); color: #60a5fa; border-color: rgba(96, 165, 250, 0.2);"><?php echo htmlspecialchars($log['ip_address']); ?></span></td>
                            <td style="opacity: 0.7; font-size: 0.85rem; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?php echo htmlspecialchars($log['user_agent']); ?>">
                                <?php echo htmlspecialchars($log['user_agent']); ?>
                            </td>
                            <td style="opacity: 0.6; font-size: 0.85rem;">
                                <?php echo date('d.m.Y H:i', strtotime($log['created_at'])); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($logs)): ?>
                             <div class="empty-state">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                <p>Henüz kayıtlı log bulunmuyor.</p>
                            </div>
                        <?php endif; ?>
                    </tbody>
                </table>
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
