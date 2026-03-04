<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

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
                    <div class="glass-card stat-box primary">
                        <div class="stat-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg></div>
                        <div class="stat-label">Applications Initiated</div>
                        <div class="stat-value"><?= count($applications) ?></div>
                    </div>
                    <?php
                        $pending_count = 0;
                        foreach($applications as $app) if($app['status'] == 'pending') $pending_count++;
                    ?>
                    <div class="glass-card stat-box warning">
                        <div class="stat-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg></div>
                        <div class="stat-label">Awaiting Adjudication</div>
                        <div class="stat-value"><?= $pending_count ?></div>
                    </div>
                    <div class="glass-card stat-box success">
                        <div class="stat-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></div>
                        <div class="stat-label">Final Certifications</div>
                        <div class="stat-value"><?= count($applications) - $pending_count ?></div>
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
                            <div class="grid-3">
                                <?php foreach ($applications as $app): ?>
                                    <div class="glass-card data-card">
                                        <div class="card-header">
                                            <div>
                                                <div class="card-subtitle"><?= h($app['category']) ?></div>
                                                <div class="card-title"><?= h($app['service_name']) ?></div>
                                            </div>
                                            <span class="status-pill status-<?= $app['status'] ?>"><?= h($app['status']) ?></span>
                                        </div>

                                        <div class="card-body">
                                            <div class="meta-info">
                                                <div style="width: 20px; height: 20px; color: var(--secondary); background: rgba(37, 99, 235, 0.1); border-radius: 6px; display: flex; align-items: center; justify-content: center; padding: 4px;">
                                                    <?= get_category_icon($app['category']) ?>
                                                </div>
                                                Submitted on <?= date('M d, Y', strtotime($app['submitted_at'])) ?>
                                            </div>
                                        </div>

                                        <div class="card-footer">
                                            <div class="meta-info">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                                                ID: #<?= str_pad($app['id'], 5, '0', STR_PAD_LEFT) ?>
                                            </div>
                                            <a href="view_application.php?id=<?= $app['id'] ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 0.8rem; font-weight: 700;">View Node</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
