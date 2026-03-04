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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="">
    <div class="app-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="content-wrapper">
            <?php include '../includes/header_dashboard.php'; ?>
            
            <main class="main-content animate-up">
                <header style="margin-bottom: 40px;">
                    <h2 style="font-size: 2.5rem; margin-bottom: 8px;">System Overview</h2>
                    <p class="text-muted">Monitor and manage all council activities from one central hub.</p>
                </header>

                <div class="grid-4" style="margin-bottom: 40px;">
                    <div class="glass-card stat-box" style="padding: 24px;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase;">Total Applications</div>
                        <div style="font-size: 2rem; font-weight: 800; color: var(--primary); margin-top: 8px;"><?= $total_apps ?></div>
                    </div>
                    <div class="glass-card stat-box" style="padding: 24px;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase;">Awaiting Review</div>
                        <div style="font-size: 2rem; font-weight: 800; color: var(--warning); margin-top: 8px;"><?= $pending_apps ?></div>
                    </div>
                    <div class="glass-card stat-box" style="padding: 24px;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase;">Total Approved</div>
                        <div style="font-size: 2rem; font-weight: 800; color: var(--success); margin-top: 8px;"><?= $approved_apps ?></div>
                    </div>
                    <div class="glass-card stat-box" style="padding: 24px;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 800; text-transform: uppercase;">Active Citizens</div>
                        <div style="font-size: 2rem; font-weight: 800; color: var(--secondary); margin-top: 8px;"><?= $total_users ?></div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px; margin-bottom: 40px;">
                    <div class="glass-card" style="padding: 32px;">
                        <h3 style="margin-bottom: 24px; font-size: 1.25rem;">Application Progress</h3>
                        <canvas id="growthChart" height="200"></canvas>
                    </div>
                    <div class="glass-card" style="padding: 32px;">
                        <h3 style="margin-bottom: 24px; font-size: 1.25rem;">Service Distribution</h3>
                        <canvas id="serviceChart" height="200"></canvas>
                    </div>
                </div>

                <div class="glass-card">
                    <div style="padding: 40px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
                            <h3 style="font-size: 1.5rem;">Recent Applications</h3>
                            <a href="manage_applications.php" class="btn btn-secondary" style="font-size: 0.8rem; font-weight: 700;">View All</a>
                        </div>
                    
                        <table class="main-table" style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="text-align: left; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                    <th style="padding: 16px 10px; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">User Info</th>
                                    <th style="padding: 16px 10px; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Service Name</th>
                                    <th style="padding: 16px 10px; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Date Submitted</th>
                                    <th style="padding: 16px 10px; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">Status</th>
                                    <th style="padding: 16px 10px; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($applications as $app): ?>
                                <tr style="border-bottom: 1px solid rgba(0,0,0,0.02);">
                                    <td style="padding: 20px 10px;" data-label="User">
                                        <div style="font-weight: 700; color: var(--primary);"><?= h($app['username']) ?></div>
                                        <div style="font-size: 0.7rem; color: var(--text-muted);"><?= h($app['email']) ?></div>
                                    </td>
                                    <td style="padding: 20px 10px;" data-label="Service">
                                        <div style="font-weight: 600;"><?= h($app['service_name']) ?></div>
                                        <div style="font-size: 0.7rem; color: var(--secondary); font-weight: 700;"><?= strtoupper(h($app['category'])) ?></div>
                                    </td>
                                    <td style="padding: 20px 10px; font-size: 0.85rem; color: var(--text-muted);" data-label="Date"><?= date('M d, Y', strtotime($app['submitted_at'])) ?></td>
                                    <td style="padding: 20px 10px;" data-label="Status"><span class="status-pill status-<?= $app['status'] ?>"><?= $app['status'] ?></span></td>
                                    <td style="padding: 20px 10px; text-align: right;">
                                        <a href="review_application.php?id=<?= $app['id'] ?>" class="btn btn-secondary" style="padding: 8px 16px; font-size: 0.8rem; font-weight: 700;">Review</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        const growthCtx = document.getElementById('growthChart').getContext('2d');
        new Chart(growthCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Applications',
                    data: [12, 19, 13, 25, 22, 30],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        const serviceCtx = document.getElementById('serviceChart').getContext('2d');
        new Chart(serviceCtx, {
            type: 'doughnut',
            data: {
                labels: ['Permits', 'Certificates', 'Business'],
                datasets: [{
                    data: [45, 25, 30],
                    backgroundColor: ['#2563eb', '#06b6d4', '#475569']
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    </script>
</body>
</html>
