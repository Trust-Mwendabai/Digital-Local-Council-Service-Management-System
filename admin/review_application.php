<?php
require_once 'auth_check.php';
require_once '../includes/functions.php';

$app_id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("
    SELECT a.*, u.username, u.email, s.name as service_name, s.category 
    FROM applications a 
    JOIN users u ON a.user_id = u.id 
    JOIN services s ON a.service_id = s.id 
    WHERE a.id = ?
");
$stmt->execute([$app_id]);
$app = $stmt->fetch();

if (!$app) {
    redirect('index.php');
}

$form_data = json_decode($app['form_data'], true);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $comment = trim($_POST['comment']);
    
    $status = $app['status']; // Default to current status
    if ($action === 'approve') $status = 'approved';
    if ($action === 'reject') $status = 'rejected';
    if ($action === 'request_info') $status = 'more_info';
    if ($action === 'update_comment') $status = $app['status']; // Explicitly keep current

    $stmt = $pdo->prepare("UPDATE applications SET status = ?, admin_comment = ? WHERE id = ?");
    if ($stmt->execute([$status, $comment, $app_id])) {
        // Dispatch multi-channel notification
        $notif_msg = "Application Status Update: Your request for {$app['service_name']} is now " . strtoupper($status);
        dispatch_notification($pdo, $app['user_id'], $notif_msg);
        
        $success = "Application status synchronized and alerts dispatched via Email & SMS.";
        // Refresh app data
        $app['status'] = $status;
        $app['admin_comment'] = $comment;
    } else {
        $error = "Failed to synchronize status with registry.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Protocol | DLCSMS Registry</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .review-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 32px;
        }
        .data-item {
            padding: 24px;
            background: rgba(0,0,0,0.02);
            border-radius: 12px;
            margin-bottom: 16px;
        }
        .data-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 800;
            color: var(--text-muted);
            margin-bottom: 8px;
        }
        .data-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary);
        }
    </style>
</head>
<body class="">
    <div class="app-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="content-wrapper">
            <?php include '../includes/header_dashboard.php'; ?>
            
            <main class="main-content animate-up">
                <header style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: flex-end;">
                    <div>
                        <h2 style="font-size: 2.5rem; margin-bottom: 8px;">Review Protocol</h2>
                        <p class="text-muted">Analyzing citizen telemetry for ID #<?= str_pad($app['id'], 5, '0', STR_PAD_LEFT) ?></p>
                    </div>
                    <span class="status-pill status-<?= $app['status'] ?>" style="font-size: 1rem; padding: 8px 20px;"><?= $app['status'] ?></span>
                </header>

                <?php if($success): ?>
                    <div style="background: rgba(34, 197, 94, 0.1); color: var(--success); padding: 20px; border-radius: 12px; margin-bottom: 32px; border: 1px solid rgba(34, 197, 94, 0.2);"><?= $success ?></div>
                <?php endif; ?>

                <div class="review-grid">
                    <div class="glass-card" style="padding: 40px;">
                        <h3 style="margin-bottom: 32px;">Citizen Data</h3>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="data-item"><div class="data-label">Legal Name</div><div class="data-value"><?= h($form_data['full_name'] ?? 'N/A') ?></div></div>
                            <div class="data-item"><div class="data-label">ID / NRC</div><div class="data-value"><?= h($form_data['nrc_number'] ?? 'N/A') ?></div></div>
                        </div>
                        <div class="data-item"><div class="data-label">Service Node</div><div class="data-value"><?= h($app['service_name']) ?> (<?= h($app['category']) ?>)</div></div>
                        <div class="data-item">
                            <div class="data-label">Raw Telemetry</div>
                            <div class="data-value" style="font-size: 0.9rem; line-height: 1.6; color: var(--text-muted); font-family: monospace;">
                                <?php 
                                    if (is_array($form_data)) {
                                        foreach($form_data as $key => $val) {
                                            echo "<strong>" . ucwords(str_replace('_', ' ', $key)) . ":</strong> " . h($val) . "<br>";
                                        }
                                    } else {
                                        echo h($app['form_data']);
                                    }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="glass-card" style="padding: 40px;">
                        <h3 style="margin-bottom: 32px;">Adjudication</h3>
                        <form action="review_application.php?id=<?= $app_id ?>" method="POST">
                            <div class="form-group">
                                <label class="form-label">Admin Assessment</label>
                                <textarea name="comment" class="form-control" rows="6" placeholder="Enter findings and feedback for the citizen..."><?= h($app['admin_comment']) ?></textarea>
                            </div>
                            <div style="display: grid; gap: 12px; margin-top: 32px;">
                                <button name="action" value="update_comment" class="btn btn-secondary" style="height: 50px; background: rgba(0,0,0,0.05); color: var(--text-main);">Sync Assessment Only</button>
                                <button name="action" value="approve" class="btn btn-primary" style="background: var(--success); height: 50px; border-color: var(--success);">Approve Protocol</button>
                                <button name="action" value="reject" class="btn btn-primary" style="background: var(--error); height: 50px; border-color: var(--error);">Reject Protocol</button>
                                <button name="action" value="request_info" class="btn btn-secondary" style="height: 50px;">Request More Info</button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
