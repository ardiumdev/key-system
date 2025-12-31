<?php
require_once 'db.php';

try {
    $current_ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare("SELECT * FROM banned_ips WHERE ip_address = ?");
    $stmt->execute([$current_ip]);
    
    if ($stmt->rowCount() > 0) {
        $ban = $stmt->fetch();
        ?>
        <!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Erişim Engellendi</title>
            <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <style>
                :root { --danger: #ef4444; --bg: #09090b; --card-bg: rgba(255, 255, 255, 0.03); --border: rgba(255, 255, 255, 0.1); }
                body { margin: 0; padding: 0; background: var(--bg); color: #fff; font-family: 'Outfit', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
                .container { 
                    background: var(--card-bg); border: 1px solid var(--border); padding: 3rem; border-radius: 24px; 
                    max-width: 500px; width: 90%; text-align: center; box-shadow: 0 20px 40px rgba(0,0,0,0.4);
                }
                .icon { color: var(--danger); margin-bottom: 1.5rem; animation: shake 0.5s ease-in-out; }
                h1 { margin: 0 0 1rem; color: var(--danger); }
                p { opacity: 0.8; line-height: 1.6; }
                .reason { background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); padding: 1rem; border-radius: 12px; margin-top: 1.5rem; font-family: monospace; }
                @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-5px); } 75% { transform: translateX(5px); } }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                </div>
                <h1>Erişim Engellendi</h1>
                <p>Bu siteye erişiminiz IP adresiniz üzerinden engellenmiştir.</p>
                <?php if($ban['reason']): ?>
                    <div class="reason">Sebep: <?php echo htmlspecialchars($ban['reason']); ?></div>
                <?php endif; ?>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
} catch (Exception $e) {
    // Fail silently on DB error to avoid breaking site
}
?>
