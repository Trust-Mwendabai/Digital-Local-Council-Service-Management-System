<?php
require_once 'auth_check.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mock settings update
    $success = 'System configuration synchronized successfully.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Settings | DLCSMS Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .admin-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 40px;
            margin-top: 30px;
        }
        .config-section {
            background: white;
            padding: 40px;
            border-radius: 20px;
            margin-bottom: 32px;
            box-shadow: var(--shadow-sm);
        }
    </style>
</head>
<body class="animate-up">
    <nav class="container navbar">
        <a href="../index.php" class="logo">DLC<span>SMS</span> <span style="font-size: 0.5em; background: var(--primary); color: white; padding: 4px 10px; border-radius: 6px; margin-left: 10px;">CORE</span></a>
        <div style="display: flex; gap: 24px; align-items: center;">
            <div style="font-weight: 700; color: var(--primary);"><?= h($_SESSION['username']) ?></div>
            <a href="../logout.php" class="btn btn-secondary" style="padding: 10px 18px;">Exit Console</a>
        </div>
    </nav>

    <main class="container admin-layout">
        <aside>
            <div class="glass-card" style="padding: 24px; position: sticky; top: 120px;">
                 <a href="index.php" style="display: flex; align-items: center; gap: 12px; padding: 14px; text-decoration: none; color: var(--text-muted); font-weight: 600; border-radius: 12px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Dashboard
                </a>
                <a href="manage_applications.php" style="display: flex; align-items: center; gap: 12px; padding: 14px; text-decoration: none; color: var(--text-muted); font-weight: 600; border-radius: 12px; margin-top: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                    Applications
                </a>
                <a href="manage_users.php" style="display: flex; align-items: center; gap: 12px; padding: 14px; text-decoration: none; color: var(--text-muted); font-weight: 600; border-radius: 12px; margin-top: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    Citizens
                </a>
                <a href="settings.php" style="display: flex; align-items: center; gap: 12px; padding: 14px; text-decoration: none; color: var(--secondary); background: rgba(37, 99, 235, 0.05); font-weight: 700; border-radius: 12px; margin-top: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    Settings
                </a>
            </div>
        </aside>

        <section>
            <header style="margin-bottom: 40px;">
                <h2 style="font-size: 2.5rem; margin-bottom: 8px;">System Configuration</h2>
                <p class="text-muted">Global control panel for DLCSMS core modules and system security protocols.</p>
            </header>

            <?php if($success): ?>
            <div style="background: rgba(34, 197, 94, 0.1); color: var(--success); padding: 20px; border-radius: 12px; margin-bottom: 32px; border: 1px solid rgba(34, 197, 94, 0.2); font-weight: 600;">
                <?= $success ?>
            </div>
            <?php endif; ?>

            <form action="settings.php" method="POST">
                <div class="config-section">
                    <h4 style="margin-bottom: 32px; font-size: 1.25rem;">General Parameters</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                        <div class="form-group">
                            <label class="form-label">Platform Name</label>
                            <input type="text" class="form-control" value="DLCSMS" readonly>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Official Domain</label>
                            <input type="text" class="form-control" value="dlcsms.gov.zm" readonly>
                        </div>
                    </div>
                </div>

                <div class="config-section">
                    <h4 style="margin-bottom: 32px; font-size: 1.25rem;">Security Protocol</h4>
                    <div class="form-group">
                        <label class="form-label">Encryption Level</label>
                        <select class="form-control">
                            <option>AES-256-GCM (Standard)</option>
                            <option>RSA-4096 (High Sensitivity)</option>
                        </select>
                    </div>
                    <div style="display: flex; gap: 20px; margin-top: 24px;">
                        <div style="background: var(--surface-muted); padding: 20px; border-radius: 12px; flex: 1;">
                            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); margin-bottom: 8px;">BFA PROTECTION</div>
                            <div style="font-weight: 800; color: var(--success);">ACTIVE</div>
                        </div>
                        <div style="background: var(--surface-muted); padding: 20px; border-radius: 12px; flex: 1;">
                            <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); margin-bottom: 8px;">SSL ENFORCEMENT</div>
                            <div style="font-weight: 800; color: var(--success);">ACTIVE</div>
                        </div>
                    </div>
                </div>

                <div class="config-section">
                    <h4 style="margin-bottom: 32px; font-size: 1.25rem;">Notification Hub</h4>
                    <div class="form-group" style="display: flex; align-items: center; gap: 20px;">
                        <div style="flex: 1;">
                            <label class="form-label">Auto-Notify on Protocol Start</label>
                            <p class="text-muted" style="font-size: 0.85rem;">Automatically alert citizens when their application is logged.</p>
                        </div>
                        <div style="width: 60px; height: 32px; background: var(--secondary); border-radius: 20px; position: relative; cursor: not-allowed;">
                            <div style="width: 24px; height: 24px; background: white; border-radius: 50%; position: absolute; right: 4px; top: 4px;"></div>
                        </div>
                    </div>
                </div>

                <div style="text-align: right; margin-top: 40px;">
                    <button type="submit" class="btn btn-primary" style="padding: 18px 48px; border-radius: 14px;">Synchronize Core Configuration</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
