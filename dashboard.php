<?php
session_start();
require_once 'includes/db.php';

// Redirect if not logged in
if (!is_logged_in()) {
    redirect('login.php');
}

// Redirect admins to admin dashboard
if ($_SESSION['role'] === 'admin') {
    redirect('admin/index.php');
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch user's applications
$stmt = $pdo->prepare("
    SELECT a.*, s.name as service_name, s.category 
    FROM applications a 
    JOIN services s ON a.service_id = s.id 
    WHERE a.user_id = ? 
    ORDER BY a.submitted_at DESC
");
$stmt->execute([$user_id]);
$applications = $stmt->fetchAll();

// Fetch unread notifications
$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->execute([$user_id]);
$unread_notifications = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Console | DLCSMS Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .dashboard-container {
            display: grid;
            grid-template-columns: 280px 1fr;
            min-height: calc(100vh - 80px);
            margin-top: 20px;
            gap: 40px;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 20px;
            border-radius: 14px;
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }

        .sidebar-item:hover, .sidebar-item.active {
            background: white;
            color: var(--secondary);
            box-shadow: var(--shadow-sm);
        }

        .sidebar-item svg {
            width: 20px;
            height: 20px;
        }

        .stat-banner {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-box {
            padding: 32px;
            text-align: left;
        }

        .stat-label {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            font-weight: 700;
            margin-bottom: 12px;
        }

        .stat-value {
            font-size: 2.25rem;
            font-weight: 800;
            font-family: 'Outfit', sans-serif;
            color: var(--primary);
        }

        .main-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        .main-table th {
            padding: 16px 24px;
            text-align: left;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            border: none;
        }

        .main-table tr {
            transition: transform 0.2s ease;
        }

        .main-table td {
            padding: 20px 24px;
            background: white;
            border: none;
        }

        .main-table td:first-child { border-radius: 16px 0 0 16px; border-left: 4px solid transparent; }
        .main-table td:last-child { border-radius: 0 16px 16px 0; }

        .main-table tr:hover td:first-child { border-left-color: var(--secondary); }
        .main-table tr:hover { transform: scale(1.01); }

        .notification-bell {
            position: relative;
            width: 44px;
            height: 44px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-sm);
            color: var(--text-muted);
            cursor: pointer;
        }

        .unread-dot {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 10px;
            height: 10px;
            background: var(--error);
            border-radius: 50%;
            border: 2px solid white;
        }
    </style>
</head>
<body class="animate-up">
    <nav class="container navbar">
        <a href="index.php" class="logo">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: var(--secondary);"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            DLC<span>SMS</span>
        </a>
        <div style="display: flex; gap: 24px; align-items: center;">
            <a href="notifications.php" class="notification-bell">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                <?php if ($unread_notifications > 0): ?><span class="unread-dot"></span><?php endif; ?>
            </a>
            <div style="text-align: right;">
                <div style="font-weight: 700; color: var(--primary);"><?= h($username) ?></div>
                <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase;">Citizen</div>
            </div>
            <a href="logout.php" class="btn btn-secondary" style="padding: 10px 18px; font-size: 0.85rem;">Logout</a>
        </div>
    </nav>

    <main class="container">
        <div class="dashboard-container">
            <aside class="sidebar-nav">
                <a href="dashboard.php" class="sidebar-item active">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                    Command Center
                </a>
                <a href="apply.php" class="sidebar-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"></path></svg>
                    New Application
                </a>
                <a href="notifications.php" class="sidebar-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    Notifications
                </a>
                <a href="profile.php" class="sidebar-item">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Identity & Profile
                </a>
            </aside>

            <section class="content-area">
                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px;">
                    <div>
                        <h2 style="font-size: 2.5rem; margin-bottom: 8px;">System Overview</h2>
                        <p class="text-muted">Welcome back to the council digital interface.</p>
                    </div>
                    <a href="apply.php" class="btn btn-primary" style="padding: 16px 32px; border-radius: 12px; font-weight: 700;">Initiate Protocol</a>
                </div>

                <div class="stat-banner">
                    <div class="glass-card stat-box">
                        <div class="stat-label">Applications</div>
                        <div class="stat-value"><?= count($applications) ?></div>
                    </div>
                    <?php
                        $pending_count = 0;
                        foreach($applications as $app) if($app['status'] == 'pending') $pending_count++;
                    ?>
                    <div class="glass-card stat-box">
                        <div class="stat-label">Pending Status</div>
                        <div class="stat-value" style="color: var(--warning);"><?= $pending_count ?></div>
                    </div>
                    <div class="glass-card stat-box">
                        <div class="stat-label">Final Approvals</div>
                        <div class="stat-value" style="color: var(--success);"><?= count($applications) - $pending_count ?></div>
                    </div>
                </div>

                <div style="margin-top: 60px;">
                    <h3 style="margin-bottom: 32px; font-size: 1.5rem;">Recent Telemetry</h3>
                    <?php if (empty($applications)): ?>
                        <div class="glass-card" style="padding: 80px; text-align: center;">
                            <p class="text-muted" style="font-size: 1.1rem; margin-bottom: 32px;">No application records found on this profile.</p>
                            <a href="apply.php" class="btn btn-secondary">Create First Record</a>
                        </div>
                    <?php else: ?>
                        <table class="main-table">
                            <thead>
                                <tr>
                                    <th>Service Node</th>
                                    <th>Category</th>
                                    <th>Submission</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $app): ?>
                                    <tr class="animate-up">
                                        <td style="font-weight: 700; color: var(--primary);"><?= h($app['service_name']) ?></td>
                                        <td><span style="font-weight: 600; color: var(--text-muted); font-size: 0.85rem;"><?= h($app['category']) ?></span></td>
                                        <td style="color: var(--text-muted); font-size: 0.9rem;"><?= date('M d, Y', strtotime($app['submitted_at'])) ?></td>
                                        <td>
                                            <span class="status-pill status-<?= $app['status'] ?>"><?= h($app['status']) ?></span>
                                        </td>
                                        <td style="text-align: right;">
                                            <a href="view_application.php?id=<?= $app['id'] ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 0.8rem; border-radius: 8px; font-weight: 700;">View Node</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </main>

    <footer style="margin-top: 100px;">
        <div class="container text-center">
            <p class="text-muted" style="font-size: 0.9rem; font-weight: 600;">DLCSMS &copy; 2026 | Digital Sovereignty</p>
        </div>
    </footer>
</body>
</html>
