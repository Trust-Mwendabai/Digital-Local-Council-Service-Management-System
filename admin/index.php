<?php
require_once 'auth_check.php';
require_once '../includes/functions.php';

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

// Fetch Operational Telemetry (Phase 22)
// Average turnaround time in hours per category
$telemetry_stmt = $pdo->query("
    SELECT s.category, AVG(TIMESTAMPDIFF(HOUR, a.submitted_at, a.updated_at)) as avg_hours 
    FROM applications a 
    JOIN services s ON a.service_id = s.id 
    WHERE a.status IN ('approved', 'rejected') 
    GROUP BY s.category
");
$telemetry_data = $telemetry_stmt->fetchAll();

$categories = [];
$avg_times = [];
foreach ($telemetry_data as $row) {
    $categories[] = $row['category'];
    $avg_times[] = round($row['avg_hours'], 1);
}

// Fetch Service Distribution (Phase 23)
$dist_stmt = $pdo->query("
    SELECT s.category, COUNT(*) as count 
    FROM applications a 
    JOIN services s ON a.service_id = s.id 
    GROUP BY s.category
");
$dist_data = $dist_stmt->fetchAll();
$dist_labels = [];
$dist_counts = [];
foreach ($dist_data as $row) {
    $dist_labels[] = $row['category'];
    $dist_counts[] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | DLCSMS Admin Overview</title>
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
                    <div class="glass-card stat-box primary">
                        <div class="stat-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg></div>
                        <div class="stat-label">Total Applications</div>
                        <div class="stat-value"><?= $total_apps ?></div>
                    </div>
                    <div class="glass-card stat-box warning">
                        <div class="stat-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg></div>
                        <div class="stat-label">Awaiting Review</div>
                        <div class="stat-value"><?= $pending_apps ?></div>
                    </div>
                    <div class="glass-card stat-box success">
                        <div class="stat-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg></div>
                        <div class="stat-label">Total Approved</div>
                        <div class="stat-value"><?= $approved_apps ?></div>
                    </div>
                    <div class="glass-card stat-box secondary">
                        <div class="stat-icon"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg></div>
                        <div class="stat-label">Active Citizens</div>
                        <div class="stat-value"><?= $total_users ?></div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 40px;">
                    <div class="glass-card" style="padding: 32px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                            <h3 style="font-size: 1.25rem;">Efficiency Telemetry</h3>
                            <span style="font-size: 0.7rem; font-weight: 800; color: var(--secondary); background: rgba(37, 99, 235, 0.1); padding: 4px 12px; border-radius: 20px;">Avg Hours</span>
                        </div>
                        <div style="height: 250px; position: relative; overflow: hidden;">
                            <canvas id="efficiencyChart"></canvas>
                        </div>
                    </div>
                    <div class="glass-card" style="padding: 32px;">
                        <h3 style="margin-bottom: 24px; font-size: 1.25rem;">Service Distribution</h3>
                        <div style="height: 250px; position: relative; overflow: hidden;">
                            <canvas id="serviceChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="glass-card">
                <div style="padding: 40px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px;">
                        <h3 style="font-size: 1.5rem;">Recent Applications</h3>
                        <a href="manage_applications.php" class="btn btn-secondary" style="font-size: 0.8rem; font-weight: 700;">View All Registry</a>
                    </div>
                
                    <div class="grid-3">
                        <?php foreach($applications as $app): ?>
                        <div class="glass-card data-card">
                            <div class="card-header">
                                <div>
                                    <div class="card-subtitle"><?= h($app['category']) ?></div>
                                    <div class="card-title"><?= h($app['service_name']) ?></div>
                                </div>
                                <span class="status-pill status-<?= $app['status'] ?>"><?= $app['status'] ?></span>
                            </div>
                            
                            <div class="card-body">
                                <div class="meta-info" style="margin-bottom: 8px;">
                                    <div style="width: 20px; height: 20px; color: var(--secondary); background: rgba(37, 99, 235, 0.1); border-radius: 6px; display: flex; align-items: center; justify-content: center; padding: 4px;">
                                        <?= get_category_icon($app['category']) ?>
                                    </div>
                                    <strong style="font-size: 0.95rem;"><?= h($app['username']) ?></strong>
                                </div>
                                <div class="meta-info">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                    <?= h($app['email']) ?>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="meta-info">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <?= date('M d, Y', strtotime($app['submitted_at'])) ?>
                                </div>
                                <a href="review_application.php?id=<?= $app['id'] ?>" class="btn btn-primary" style="padding: 8px 16px; font-size: 0.8rem;">Review Node</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        const efficiencyCtx = document.getElementById('efficiencyChart').getContext('2d');
        new Chart(efficiencyCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($categories) ?>,
                datasets: [{
                    label: 'Hours',
                    data: <?= json_encode($avg_times) ?>,
                    backgroundColor: '#2563eb',
                    borderRadius: 8,
                    barThickness: 32
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    x: { 
                        beginAtZero: true,
                        grid: { display: false },
                        title: { display: true, text: 'Hours', font: { weight: 'bold' } }
                    },
                    y: { 
                        grid: { display: false }
                    }
                }
            }
        });

        const serviceCtx = document.getElementById('serviceChart').getContext('2d');
        new Chart(serviceCtx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode($dist_labels) ?>,
                datasets: [{
                    data: <?= json_encode($dist_counts) ?>,
                    backgroundColor: ['#2563eb', '#06b6d4', '#475569', '#8b5cf6', '#ec4899', '#f59e0b'],
                    borderWidth: 0,
                    hoverOffset: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 15, font: { weight: '600', size: 10 } }
                    }
                }
            }
        });
    </script>
</body>
</html>
