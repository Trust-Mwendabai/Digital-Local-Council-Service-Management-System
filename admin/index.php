<?php
require_once 'auth_check.php';

// Fetch statistics
$total_apps = $pdo->query("SELECT COUNT(*) FROM applications")->fetchColumn();
$pending_apps = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'pending'")->fetchColumn();
$approved_apps = $pdo->query("SELECT COUNT(*) FROM applications WHERE status = 'approved'")->fetchColumn();
$total_users = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'citizen'")->fetchColumn();

// Fetch latest applications with user info
$stmt = $pdo->query("
    SELECT a.*, u.username, u.email, s.name as service_name, s.category
    FROM applications a 
    JOIN users u ON a.user_id = u.id 
    JOIN services s ON a.service_id = s.id 
    ORDER BY a.submitted_at DESC
    LIMIT 5
");
$applications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Console | DLCSMS Admin Overview</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .admin-layout {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 40px;
            margin-top: 30px;
        }
        .stat-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }
        .admin-stat-box {
            padding: 24px;
            text-align: left;
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
                 <a href="index.php" style="display: flex; align-items: center; gap: 12px; padding: 14px; text-decoration: none; color: var(--secondary); background: rgba(37, 99, 235, 0.05); font-weight: 700; border-radius: 12px;">
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
                <a href="settings.php" style="display: flex; align-items: center; gap: 12px; padding: 14px; text-decoration: none; color: var(--text-muted); font-weight: 600; border-radius: 12px; margin-top: 8px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                    Settings
                </a>
            </div>
        </aside>

        <section>
            <header style="margin-bottom: 40px;">
                <h2 style="font-size: 2.5rem; margin-bottom: 8px;">System Telemetry</h2>
                <p class="text-muted">Real-time oversight of council service nodes and citizen interactions.</p>
            </header>

            <div class="stat-grid">
                <div class="glass-card admin-stat-box">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase;">Global Protocols</div>
                    <div style="font-size: 2rem; font-weight: 800; color: var(--primary); margin-top: 8px;"><?= $total_apps ?></div>
                </div>
                <div class="glass-card admin-stat-box">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase;">Review Queue</div>
                    <div style="font-size: 2rem; font-weight: 800; color: var(--warning); margin-top: 8px;"><?= $pending_apps ?></div>
                </div>
                <div class="glass-card admin-stat-box">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase;">Approved Nodes</div>
                    <div style="font-size: 2rem; font-weight: 800; color: var(--success); margin-top: 8px;"><?= $approved_apps ?></div>
                </div>
                <div class="glass-card admin-stat-box">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase;">Citizen Registry</div>
                    <div style="font-size: 2rem; font-weight: 800; color: var(--secondary); margin-top: 8px;"><?= $total_users ?></div>
                </div>
            </div>

            <div class="glass-card" style="padding: 40px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
                    <h3 style="font-size: 1.5rem;">Latest Telemetry Submissions</h3>
                    <a href="manage_applications.php" class="btn btn-secondary" style="font-size: 0.8rem; font-weight: 700;">Full Registry</a>
                </div>
                
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="text-align: left; border-bottom: 1px solid rgba(0,0,0,0.05);">
                            <th style="padding: 16px 10px; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Citizen Registry</th>
                            <th style="padding: 16px 10px; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Service Node</th>
                            <th style="padding: 16px 10px; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Timestamp</th>
                            <th style="padding: 16px 10px; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Status</th>
                            <th style="padding: 16px 10px; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($applications as $app): ?>
                        <tr style="border-bottom: 1px solid rgba(0,0,0,0.02);">
                            <td style="padding: 20px 10px;">
                                <div style="font-weight: 700; color: var(--primary);"><?= h($app['username']) ?></div>
                                <div style="font-size: 0.7rem; color: var(--text-muted);"><?= h($app['email']) ?></div>
                            </td>
                            <td style="padding: 20px 10px;">
                                <div style="font-weight: 600;"><?= h($app['service_name']) ?></div>
                                <div style="font-size: 0.7rem; color: var(--secondary); font-weight: 700;"><?= strtoupper(h($app['category'])) ?></div>
                            </td>
                            <td style="padding: 20px 10px; font-size: 0.85rem; color: var(--text-muted);"><?= date('M d, Y', strtotime($app['submitted_at'])) ?></td>
                            <td style="padding: 20px 10px;"><span class="status-pill status-<?= $app['status'] ?>"><?= $app['status'] ?></span></td>
                            <td style="padding: 20px 10px; text-align: right;">
                                <a href="review_application.php?id=<?= $app['id'] ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 0.8rem; font-weight: 700;">Review</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($applications)): ?>
                        <tr>
                            <td colspan="5" style="padding: 60px; text-align: center; color: var(--text-muted); font-size: 1.1rem;">No telemetry found in the global registry.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
