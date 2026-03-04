<?php
session_start();
require_once 'includes/db.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$app_id = $_GET['id'] ?? 0;
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT a.*, s.name as service_name, s.category 
    FROM applications a 
    JOIN services s ON a.service_id = s.id 
    WHERE a.id = ? AND a.user_id = ?
");
$stmt->execute([$app_id, $user_id]);
$app = $stmt->fetch();

if (!$app) {
    redirect('dashboard.php');
}

$form_data = json_decode($app['form_data'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Protocol | DLCSMS</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .view-container {
            max-width: 900px;
            margin: 60px auto;
        }
        .data-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 32px;
        }
        .data-block {
            padding: 24px;
            background: #f8fafc;
            border-radius: 16px;
        }
        .label {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 800;
            color: var(--text-muted);
            margin-bottom: 8px;
        }
        .value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary);
        }
        .comment-box {
            background: rgba(37, 99, 235, 0.05);
            border-left: 4px solid var(--secondary);
            padding: 32px;
            border-radius: 0 16px 16px 0;
            margin-top: 40px;
        }
    </style>
</head>
<body class="animate-up">
    <nav class="container navbar">
        <a href="index.php" class="logo">DLC<span>SMS</span></a>
        <div><a href="dashboard.php" class="btn btn-secondary">Return to Console</a></div>
    </nav>

    <main class="container view-container">
        <header style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 48px;">
            <div>
                <span style="font-size: 0.8rem; font-weight: 800; color: var(--secondary); text-transform: uppercase;">Application Node: #<?= str_pad($app['id'], 5, '0', STR_PAD_LEFT) ?></span>
                <h2 style="font-size: 2.5rem; margin-top: 12px;"><?= h($app['service_name']) ?></h2>
            </div>
            <span class="status-pill status-<?= $app['status'] ?>" style="font-size: 1rem; padding: 10px 24px;"><?= strtoupper(h($app['status'])) ?></span>
        </header>

        <div class="glass-card" style="padding: 48px;">
            <h3 style="font-size: 1.5rem; margin-bottom: 24px;">Telemetry Data Submissions</h3>
            <div class="data-grid">
                <div class="data-block"><div class="label">Legal Full Name</div><div class="value"><?= h($form_data['full_name'] ?? 'N/A') ?></div></div>
                <div class="data-block"><div class="label">NRC Identification</div><div class="value"><?= h($form_data['nrc_number'] ?? 'N/A') ?></div></div>
                <div class="data-block"><div class="label">Service Node Category</div><div class="value"><?= h($app['category']) ?></div></div>
                <div class="data-block"><div class="label">Contact Endpoint</div><div class="value"><?= h($form_data['contact_number'] ?? 'N/A') ?></div></div>
            </div>
            
            <div class="data-block" style="margin-top: 24px; min-height: 120px;">
                <div class="label">Physical Domicile Address</div>
                <div class="value"><?= h($form_data['address'] ?? 'N/A') ?></div>
            </div>

            <?php if(!empty($app['admin_comment'])): ?>
            <div class="comment-box">
                <div class="label" style="color: var(--secondary);">Council Adjudication Findings</div>
                <div class="value" style="font-weight: 500; line-height: 1.6; color: var(--text-main);"><?= nl2br(h($app['admin_comment'])) ?></div>
            </div>
            <?php endif; ?>
        </div>

        <div style="margin-top: 40px; text-align: center;">
            <p class="text-muted" style="font-size: 0.9rem;">Protocol Submitted on <?= date('F d, Y • H:i', strtotime($app['submitted_at'])) ?></p>
        </div>
    </main>
</body>
</html>
