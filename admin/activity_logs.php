<?php
require_once 'auth_check.php';
require_once '../includes/functions.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Fetch Logs
$stmt = $pdo->prepare("
    SELECT l.*, u.username, u.role 
    FROM activity_logs l 
    LEFT JOIN users u ON l.user_id = u.id 
    ORDER BY l.created_at DESC 
    LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $perPage, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll();

$totalLogs = $pdo->query("SELECT COUNT(*) FROM activity_logs")->fetchColumn();
$totalPages = ceil($totalLogs / $perPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Logs | DLCSMS Registry</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .log-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }
        .log-table th {
            padding: 16px 24px;
            text-align: left;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            font-weight: 800;
        }
        .log-row {
            background: white;
            transition: transform 0.2s;
        }
        .log-row:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .log-cell {
            padding: 20px 24px;
            font-size: 0.95rem;
        }
        .log-cell:first-child { border-radius: 12px 0 0 12px; }
        .log-cell:last-child { border-radius: 0 12px 12px 0; }
        
        .action-tag {
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            background: rgba(37, 99, 235, 0.1);
            color: var(--secondary);
        }
    </style>
</head>
<body class="">
    <div class="app-container">
        <?php include '../includes/sidebar.php'; ?>

        <div class="content-wrapper">
            <?php include '../includes/header_dashboard.php'; ?>
            
            <main class="main-content animate-up">
                <header style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2 style="font-size: 2.5rem; margin-bottom: 8px;">Audit Logs</h2>
                        <p class="text-muted">Reviewing system activities and security telemetry.</p>
                    </div>
                </header>

                <?php if (empty($logs)): ?>
                    <div class="glass-card" style="padding: 60px; text-align: center;">
                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                        <h3 style="margin-top: 24px;">No activities recorded yet.</h3>
                    </div>
                <?php else: ?>
                    <table class="log-table">
                        <thead>
                            <tr>
                                <th>Timestamp</th>
                                <th>Identity</th>
                                <th>Action Node</th>
                                <th>Protocol Details</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                            <tr class="log-row">
                                <td class="log-cell" style="white-space: nowrap; color: var(--text-muted); font-size: 0.8rem;">
                                    <?= date('M d, H:i:s', strtotime($log['created_at'])) ?>
                                </td>
                                <td class="log-cell">
                                    <div style="font-weight: 700;"><?= h($log['username'] ?? 'Anonymous') ?></div>
                                    <div style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase;"><?= h($log['role'] ?? 'System') ?></div>
                                </td>
                                <td class="log-cell">
                                    <span class="action-tag"><?= h($log['action']) ?></span>
                                </td>
                                <td class="log-cell" style="max-width: 400px; color: #475569;">
                                    <?= h($log['details']) ?>
                                </td>
                                <td class="log-cell" style="font-family: monospace; font-size: 0.8rem; color: #94a3b8;">
                                    <?= h($log['ip_address']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div style="display: flex; gap: 8px; margin-top: 32px; justify-content: center;">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>" class="btn <?= $page === $i ? 'btn-primary' : 'btn-secondary' ?>" style="padding: 8px 16px;"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
            </main>
        </div>
    </div>
</body>
</html>
