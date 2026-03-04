<?php
require_once 'auth_check.php';

$stmt = $pdo->query("SELECT * FROM users WHERE role = 'citizen' ORDER BY created_at DESC");
$citizens = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizens | DLCSMS Core</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="">
    <div class="app-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="content-wrapper">
            <?php include '../includes/header_dashboard.php'; ?>
            
            <main class="main-content animate-up">
                <header style="margin-bottom: 40px;">
                    <h2 style="font-size: 2.5rem; margin-bottom: 8px;">Citizen Registry</h2>
                    <p class="text-muted">Manage system authentication and profile credentials for all citizens.</p>
                </header>

                <div class="glass-card">
                    <div style="padding: 40px;">
                        <table class="main-table" style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="text-align: left; border-bottom: 1px solid rgba(0,0,0,0.05);">
                                    <th style="padding: 16px 10px;">Username</th>
                                    <th style="padding: 16px 10px;">Email Interface</th>
                                    <th style="padding: 16px 10px;">Join Date</th>
                                    <th style="padding: 16px 10px;">Registry Status</th>
                                    <th style="padding: 16px 10px; text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($citizens as $user): ?>
                                <tr style="border-bottom: 1px solid rgba(0,0,0,0.02);">
                                    <td style="padding: 20px 10px; font-weight: 700; color: var(--primary);"><?= h($user['username']) ?></td>
                                    <td style="padding: 20px 10px; color: var(--text-muted); font-size: 0.9rem;"><?= h($user['email']) ?></td>
                                    <td style="padding: 20px 10px; font-size: 0.85rem; color: var(--text-muted);"><?= date('M d, Y', strtotime($user['created_at'])) ?></td>
                                    <td style="padding: 20px 10px;"><span class="status-pill status-approved">Active</span></td>
                                    <td style="padding: 20px 10px; text-align: right;">
                                        <button class="btn btn-secondary" style="padding: 8px 16px; font-size: 0.8rem; font-weight: 700;">Audit Logs</button>
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
</body>
</html>
