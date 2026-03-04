<?php
require_once 'includes/db.php';

$id = $_GET['id'] ?? 0;
$app = null;

if ($id) {
    $stmt = $pdo->prepare("
        SELECT a.*, s.name as service_name, s.category, u.username
        FROM applications a 
        JOIN services s ON a.service_id = s.id 
        JOIN users u ON a.user_id = u.id
        WHERE a.id = ? AND a.status = 'approved'
    ");
    $stmt->execute([$id]);
    $app = $stmt->fetch();
}

$form_data = $app ? json_decode($app['form_data'], true) : null;
$cert_no = $app ? "DLCS/" . strtoupper(substr($app['category'], 0, 3)) . "/" . str_pad($app['id'], 6, '0', STR_PAD_LEFT) : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sovereign Registry Verification | DLCSMS</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .verify-card {
            max-width: 500px;
            width: 100%;
            background: white;
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.05);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .verify-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 6px;
            background: <?= $app ? 'var(--success)' : 'var(--error)' ?>;
        }
        .icon-badge {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            background: <?= $app ? 'rgba(34, 197, 94, 0.1)' : 'rgba(239, 68, 68, 0.1)' ?>;
            color: <?= $app ? 'var(--success)' : 'var(--error)' ?>;
        }
        .status-title {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 8px;
            color: #1e293b;
        }
        .status-desc {
            color: #64748b;
            font-size: 0.95rem;
            margin-bottom: 32px;
            line-height: 1.6;
        }
        .data-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
            text-align: left;
        }
        .data-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
        }
        .data-value {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e293b;
        }
        .seal-watermark {
            position: absolute;
            bottom: -20px;
            right: -20px;
            opacity: 0.03;
            transform: rotate(-15deg);
        }
    </style>
</head>
<body>
    <div class="verify-card animate-up">
        <?php if ($app): ?>
            <div class="icon-badge">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
            <h1 class="status-title">Authenticity Verified</h1>
            <p class="status-desc">This document has been issued by the Digital Local Council and its data is synchronized with the Sovereign Registry.</p>
            
            <div style="background: #f8fafc; padding: 20px; border-radius: 16px; margin-bottom: 24px;">
                <div class="data-row">
                    <span class="data-label">Certificate No</span>
                    <span class="data-value"><?= h($cert_no) ?></span>
                </div>
                <div class="data-row">
                    <span class="data-label">Service Type</span>
                    <span class="data-value"><?= h($app['service_name']) ?></span>
                </div>
                <div class="data-row">
                    <span class="data-label">Holder Name</span>
                    <span class="data-value"><?= h($form_data['full_name'] ?? $app['username']) ?></span>
                </div>
                <div class="data-row" style="border-bottom: none;">
                    <span class="data-label">Issue Date</span>
                    <span class="data-value"><?= date('F d, Y', strtotime($app['updated_at'])) ?></span>
                </div>
            </div>

            <div style="font-size: 0.75rem; color: #94a3b8; font-weight: 600;">
                SECURE REGISTRY HANDSHAKE COMPLETED
            </div>
        <?php else: ?>
            <div class="icon-badge">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
            </div>
            <h1 class="status-title">Verification Failed</h1>
            <p class="status-desc">The provided document identifier could not be validated against the Sovereign Registry Authority.</p>
            <a href="index.php" class="btn btn-primary" style="width: 100%;">Return to Home</a>
        <?php endif; ?>

        <div class="seal-watermark">
            <svg width="150" height="150" viewBox="0 0 24 24" fill="currentColor"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"></path></svg>
        </div>
    </div>
</body>
</html>
