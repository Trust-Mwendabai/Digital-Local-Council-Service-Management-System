<?php
session_start();
require_once 'includes/db.php';

if (!is_logged_in()) {
    redirect('login.php');
}

if ($_SESSION['role'] === 'admin') {
    redirect('admin/index.php');
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$stmt = $pdo->prepare("
    SELECT a.*, s.name as service_name, s.category 
    FROM applications a 
    JOIN services s ON a.service_id = s.id 
    WHERE a.user_id = ? 
    ORDER BY a.submitted_at DESC
");
$stmt->execute([$user_id]);
$applications = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Console | DLCSMS Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="">
    <div class="app-container">
        <?php include 'includes/sidebar.php'; ?>

        <div class="content-wrapper">
            <?php include 'includes/header_dashboard.php'; ?>
            
            <main class="main-content animate-up">
                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px;">
                    <div>
                        <h2 style="font-size: 2.5rem; margin-bottom: 8px;">Citizen Console</h2>
                        <p class="text-muted">Welcome back to your digital council interface.</p>
                    </div>
                    <a href="apply.php" class="btn btn-primary" style="padding: 16px 32px; border-radius: 12px; font-weight: 700;">New Application</a>
                </div>

                <div class="grid-3" style="margin-bottom: 40px;">
                    <div class="glass-card stat-box" style="padding: 24px;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase;">Applications Initiated</div>
                        <div style="font-size: 2.25rem; font-weight: 800; color: var(--primary);"><?= count($applications) ?></div>
                    </div>
                    <?php
                        $pending_count = 0;
                        foreach($applications as $app) if($app['status'] == 'pending') $pending_count++;
                    ?>
                    <div class="glass-card stat-box" style="padding: 24px;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase;">Awaiting Adjudication</div>
                        <div style="font-size: 2.25rem; font-weight: 800; color: var(--warning);"><?= $pending_count ?></div>
                    </div>
                    <div class="glass-card stat-box" style="padding: 24px;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase;">Final Certifications</div>
                        <div style="font-size: 2.25rem; font-weight: 800; color: var(--success);"><?= count($applications) - $pending_count ?></div>
                    </div>
                </div>

                <div class="glass-card">
                    <div style="padding: 40px;">
                        <h3 style="margin-bottom: 32px; font-size: 1.5rem;">Recent Telemetry</h3>
                        <?php if (empty($applications)): ?>
                            <div style="padding: 40px; text-align: center;">
                                <p class="text-muted" style="font-size: 1.1rem; margin-bottom: 24px;">No application records found.</p>
                                <a href="apply.php" class="btn btn-secondary">Initialize First Protocol</a>
                            </div>
                        <?php else: ?>
                            <table class="main-table" style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="text-align: left; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                        <th style="padding: 16px 10px;">Service Node</th>
                                        <th style="padding: 16px 10px;">Category</th>
                                        <th style="padding: 16px 10px;">Timestamp</th>
                                        <th style="padding: 16px 10px;">Status</th>
                                        <th style="padding: 16px 10px; text-align: right;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applications as $app): ?>
                                        <tr style="border-bottom: 1px solid rgba(0,0,0,0.02);">
                                            <td style="padding: 20px 10px; font-weight: 700; color: var(--primary);"><?= h($app['service_name']) ?></td>
                                            <td style="padding: 20px 10px;"><span style="font-weight: 600; color: var(--text-muted); font-size: 0.85rem;"><?= h($app['category']) ?></span></td>
                                            <td style="padding: 20px 10px; color: var(--text-muted); font-size: 0.9rem;"><?= date('M d, Y', strtotime($app['submitted_at'])) ?></td>
                                            <td style="padding: 20px 10px;"><span class="status-pill status-<?= $app['status'] ?>"><?= h($app['status']) ?></span></td>
                                            <td style="padding: 20px 10px; text-align: right;">
                                                <a href="view_application.php?id=<?= $app['id'] ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 0.8rem; font-weight: 700;">View Node</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
