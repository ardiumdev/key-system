<?php
session_start();
// Session check removed to allow entering new keys/redirecting correctly

require_once 'db.php';
// Fetch footer text from DB
$footer_text = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'footer_text'")->fetchColumn() ?: "Yapımcı <strong>Webui</strong>";

// Check Maintenance Mode
try {
    $m_mode = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'maintenance_mode'")->fetchColumn();
    if ($m_mode == '1') {
        $m_msg = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'maintenance_message'")->fetchColumn();
        ?>
        <!DOCTYPE html>
        <html lang="tr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Bakım Modu</title>
            <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <style>
                :root { --primary: #6366f1; --bg: #09090b; --card-bg: rgba(255, 255, 255, 0.03); --border: rgba(255, 255, 255, 0.1); }
                body { margin: 0; padding: 0; background: var(--bg); color: #fff; font-family: 'Outfit', sans-serif; display: flex; align-items: center; justify-content: center; min-height: 100vh; overflow: hidden; position: relative; }
                
                /* Animated Background */
                .bg-glow { position: absolute; width: 600px; height: 600px; background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%); border-radius: 50%; top: 50%; left: 50%; transform: translate(-50%, -50%); animation: pulse 8s ease-in-out infinite alternate; z-index: -1; }
                @keyframes pulse { 0% { transform: translate(-50%, -50%) scale(1); opacity: 0.5; } 100% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.8; } }
                
                .container { 
                    position: relative; 
                    background: var(--card-bg); 
                    backdrop-filter: blur(20px); 
                    -webkit-backdrop-filter: blur(20px);
                    border: 1px solid var(--border); 
                    padding: 3rem; 
                    border-radius: 24px; 
                    max-width: 500px; 
                    width: 90%; 
                    text-align: center; 
                    box-shadow: 0 20px 40px rgba(0,0,0,0.4);
                }

                .icon-wrapper {
                    width: 80px; height: 80px; margin: 0 auto 1.5rem;
                    background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(168, 85, 247, 0.2));
                    border: 1px solid rgba(255,255,255,0.1);
                    border-radius: 20px;
                    display: flex; align-items: center; justify-content: center;
                    box-shadow: 0 0 30px rgba(99, 102, 241, 0.2);
                    animation: float 6s ease-in-out infinite;
                }
                @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }

                .icon-wrapper svg { color: #fff; filter: drop-shadow(0 0 10px rgba(99,102,241,0.5)); }

                h1 { margin: 0 0 1rem; font-size: 2rem; font-weight: 700; background: linear-gradient(to right, #fff, #a5b4fc); -webkit-background-clip: text; -webkit-text-fill-color: transparent; letter-spacing: -0.5px; }
                
                p { margin: 0; font-size: 1.05rem; line-height: 1.6; color: #a1a1aa; font-weight: 300; }

                .status-badge { 
                    display: inline-flex; align-items: center; gap: 6px;
                    background: rgba(234, 179, 8, 0.1); color: #facc15; 
                    padding: 6px 12px; border-radius: 99px; font-size: 0.85rem; font-weight: 500; 
                    margin-bottom: 1.5rem; border: 1px solid rgba(234, 179, 8, 0.2);
                }
                .status-badge::before { content:''; width: 6px; height: 6px; background: currentColor; border-radius: 50%; box-shadow: 0 0 8px currentColor; }
            </style>
        </head>
        <body>
            <div class="bg-glow"></div>
            <div class="container">
                <div class="status-badge">Sistem Bakımda</div>
                <div class="icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </div>
                <h1>Kısa Bir Mola</h1>
                <p><?php echo nl2br(htmlspecialchars($m_msg)); ?></p>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
} catch (Exception $e) {}


// Site settings
$site_title = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'site_title'")->fetchColumn() ?: "Webui Key System";
$site_desc = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'site_desc'")->fetchColumn() ?: "Gelişmiş key doğrulama sistemi.";
$site_icon = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'site_icon'")->fetchColumn() ?: "";
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo htmlspecialchars($site_desc); ?>">
    <title><?php echo htmlspecialchars($site_title); ?></title>
    <?php if($site_icon): ?><link rel="icon" href="<?php echo htmlspecialchars($site_icon); ?>"><?php endif; ?>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <button class="theme-toggle" aria-label="Temayı Değiştir"></button>

    <div class="container">
        <div class="card">
            <h1><span id="typewriter"></span><span class="cursor">&nbsp;</span></h1>
            <form id="keyForm">
                <div class="input-group">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="input-icon"><path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/></svg>
                    <input type="text" id="keyInput" placeholder="Anahtarı Giriniz" autocomplete="off">
                </div>
                <button type="submit">Devam Et</button>
                <div id="message" class="message"></div>
            </form>
        </div>
    </div>


    <?php
    require_once 'db.php';
    // Fetch Settings
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'footer_text'");
        $stmt->execute();
        $footer_text = $stmt->fetchColumn(); 
    } catch (Exception $e) {
        $footer_text = "Yapımcı <strong>Webui</strong>";
    }
    
    // Fallback if empty or table missing
    if (!$footer_text) $footer_text = "Yapımcı <strong>Webui</strong>";
    ?>
    <div class="footer"><?php echo $footer_text; ?></div>
    <script src="assets/js/app.js"></script>
</body>
</html>
